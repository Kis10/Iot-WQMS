/************ LIBRARIES ************/
#include <DHT.h>
#include <WiFi.h>
#include <HTTPClient.h>
#include <WiFiClientSecure.h>
#include <Wire.h>
#include <LiquidCrystal_I2C.h>

/************ LCD SETUP ************/
#define LCD_SDA 21
#define LCD_SCL 22
LiquidCrystal_I2C lcd(0x27, 20, 4);

/************ WIFI CREDENTIALS ************/
const char* ssid     = "NO NETWORK01_2.4";
const char* password = "Cristy_028";

/************ LARAVEL API CONFIGURATION ************/
const char* serverName = "https://aquasense.blog";
const char* deviceToken = "arduino-secret-123";
const char* deviceID    = "ESP32-WQ-01";

/************ PIN DEFINITIONS ************/
const int pHPin        = 34; // Analog Input
const int tdsPin       = 35; // Analog Input
const int turbidityPin = 32; // Analog Input
const int buzzerPin    = 25; // Digital Output
const int dhtPin       = 4;  // Digital I/O
const int powerLedPin  = 27; // Digital Output (PN2222A 6E)
const int wifiLedPin   = 13; // Digital Output

// ★★★ NEW: TDS POWER CONTROL (GPIO 26) ★★★
const int tdsPowerPin  = 26; 

// ★★★ NEW: TURBIDITY POWER CONTROL (GPIO 14) ★★★
const int turbidityPowerPin = 14; 

/************ DHT22 SETUP ************/
#define DHTTYPE DHT22
DHT dht(dhtPin, DHTTYPE);

/************ SENSOR VARIABLES ************/
float slope = -7.575;
float offset = 25.34;
float tdsFactor = 0.5;
const int numSamples = 10;
const int turbiditySmooth = 5;
int turbidityHistory[turbiditySmooth];
float temperature = 25.0;
float humidity = 50.0;

/************ TIMING VARIABLES ************/
unsigned long lastWiFiCheck = 0;
const unsigned long wifiCheckInterval = 30000;
unsigned long lastDataSend = 0;
const unsigned long dataSendInterval = 5000;

/************ NON-BLOCKING SOUND SYSTEM ************/
unsigned long soundStartTime = 0;
int soundStep = 0;
bool soundPlaying = false;
int currentSound = 0;

#define SOUND_NONE 0
#define SOUND_READY 1
#define SOUND_WIFI_CONNECT 2
#define SOUND_WIFI_DISCONNECT 3
#define SOUND_WIFI_RECONNECT 4
#define SOUND_DATA_SENT 5

void startSound(int soundType) {
  if (soundPlaying) {
    noTone(buzzerPin);
    soundPlaying = false;
  }
  soundStep = 0;
  soundStartTime = millis();
  soundPlaying = true;
  currentSound = soundType;
}

void updateSound() {
  if (!soundPlaying) return;
  
  unsigned long now = millis();
  
  switch(currentSound) {
    case SOUND_WIFI_DISCONNECT:
      // Buzz... Buzz... Beeeeep beeeeeep (2s + 2s + 2s + 2s)
      if (soundStep == 0) {
        tone(buzzerPin, 200);
        soundStartTime = now;
        soundStep = 1;
      }
      else if (soundStep == 1 && now - soundStartTime >= 2000) {
        noTone(buzzerPin);
        soundStartTime = now;
        soundStep = 2;
      }
      else if (soundStep == 2 && now - soundStartTime >= 100) {
        tone(buzzerPin, 200);
        soundStartTime = now;
        soundStep = 3;
      }
      else if (soundStep == 3 && now - soundStartTime >= 2000) {
        noTone(buzzerPin);
        soundStartTime = now;
        soundStep = 4;
      }
      else if (soundStep == 4 && now - soundStartTime >= 100) {
        tone(buzzerPin, 100);
        soundStartTime = now;
        soundStep = 5;
      }
      else if (soundStep == 5 && now - soundStartTime >= 2000) {
        noTone(buzzerPin);
        soundStartTime = now;
        soundStep = 6;
      }
      else if (soundStep == 6 && now - soundStartTime >= 100) {
        tone(buzzerPin, 100);
        soundStartTime = now;
        soundStep = 7;
      }
      else if (soundStep == 7 && now - soundStartTime >= 2000) {
        noTone(buzzerPin);
        soundPlaying = false;
      }
      break;
      
    case SOUND_WIFI_RECONNECT:
    case SOUND_READY:
      // Do-Re-Mi-Faaaa!
      if (soundStep == 0) {
        tone(buzzerPin, 1320);
        soundStartTime = now;
        soundStep = 1;
      }
      else if (soundStep == 1 && now - soundStartTime >= 100) {
        noTone(buzzerPin);
        soundStartTime = now;
        soundStep = 2;
      }
      else if (soundStep == 2 && now - soundStartTime >= 40) {
        tone(buzzerPin, 1485);
        soundStartTime = now;
        soundStep = 3;
      }
      else if (soundStep == 3 && now - soundStartTime >= 100) {
        noTone(buzzerPin);
        soundStartTime = now;
        soundStep = 4;
      }
      else if (soundStep == 4 && now - soundStartTime >= 40) {
        tone(buzzerPin, 1760);
        soundStartTime = now;
        soundStep = 5;
      }
      else if (soundStep == 5 && now - soundStartTime >= 100) {
        noTone(buzzerPin);
        soundStartTime = now;
        soundStep = 6;
      }
      else if (soundStep == 6 && now - soundStartTime >= 40) {
        tone(buzzerPin, 1976);
        soundStartTime = now;
        soundStep = 7;
      }
      else if (soundStep == 7 && now - soundStartTime >= 400) {
        noTone(buzzerPin);
        soundPlaying = false;
      }
      break;
      
    case SOUND_WIFI_CONNECT:
      tone(buzzerPin, 2093, 120);
      soundPlaying = false;
      break;
      
    case SOUND_DATA_SENT:
      tone(buzzerPin, 2637, 50);
      soundPlaying = false;
      break;
  }
}

/************ SETUP ************/
void setup() {
  Serial.begin(115200);

  // Initialize pins
  pinMode(buzzerPin, OUTPUT);
  pinMode(powerLedPin, OUTPUT);
  pinMode(wifiLedPin, OUTPUT);
  
  // ★★★ SENSOR POWER CONTROL ★★★
  pinMode(tdsPowerPin, OUTPUT);
  digitalWrite(tdsPowerPin, LOW); // Start OFF
  
  pinMode(turbidityPowerPin, OUTPUT);
  digitalWrite(turbidityPowerPin, LOW); // Start OFF

  // Default states
  digitalWrite(buzzerPin, LOW);
  digitalWrite(powerLedPin, LOW);
  digitalWrite(wifiLedPin, LOW);

  // LCD
  Wire.begin(LCD_SDA, LCD_SCL);
  lcd.begin();
  lcd.backlight();
  lcd.clear();
  lcd.setCursor(0,0);
  lcd.print("AquaSense Booting");

  // Sensors
  dht.begin();
  delay(1000);
  analogReadResolution(12);

  // LCD - System Ready
  lcd.clear();
  lcd.setCursor(0,0);
  lcd.print("System Ready!");
  lcd.setCursor(0,1);
  lcd.print("AquaSense v1.0");
  
  // 🎵 SYSTEM READY SOUND - NON-BLOCKING
  startSound(SOUND_READY);
  
  // WiFi
  connectToWiFi();
}

/************ LOOP ************/
void loop() {
  unsigned long currentMillis = millis();
  static bool wasConnected = false;
  static bool firstConnection = true;
  
  // STATESPACE VARIABLES
  static bool isInitializing = true;
  static unsigned long initStartTime = 0;
  static bool firstReadingSent = false;
  
  updateSound();

  // 1. Maintain WiFi
  if (currentMillis - lastWiFiCheck >= wifiCheckInterval) {
    lastWiFiCheck = currentMillis;

    if (WiFi.status() != WL_CONNECTED) {
      if (wasConnected) {
        Serial.println("[WiFi] Connection lost! 🔴");
        lcd.setCursor(0,3);
        lcd.print("WiFi: LOST!     ");
        digitalWrite(wifiLedPin, HIGH);
        digitalWrite(powerLedPin, LOW);
        startSound(SOUND_WIFI_DISCONNECT);
        wasConnected = false;
        // If we lose connection, we don't reset initialization, just wait to reconnect
      }
      WiFi.disconnect();
      WiFi.reconnect();
    } else {
      if (!wasConnected) {
        digitalWrite(wifiLedPin, LOW);
        digitalWrite(powerLedPin, HIGH);
        // Only update LCD if we are NOT in the special initialization phase display
        if (!isInitializing) {
             lcd.setCursor(0,3);
             lcd.print("WiFi: OK        ");
        }
        
        if (!firstConnection) {
          Serial.println("[WiFi] Reconnected! 🧬");
          startSound(SOUND_WIFI_RECONNECT);
        } else {
          Serial.println("[WiFi] Connected! ✅");
          startSound(SOUND_WIFI_CONNECT);
          
          // START INITIALIZATION TIMER ONCE WIFI CONNECTS
          if (firstConnection) {
              initStartTime = millis();
              isInitializing = true;
          }
          firstConnection = false;
        }
        wasConnected = true;
      }
    }
  }

  // 2. Initialization Phase (1 Minute)
  if (isInitializing && WiFi.status() == WL_CONNECTED) {
      unsigned long elapsedInit = currentMillis - initStartTime;
      int remainingSeconds = 60 - (elapsedInit / 1000);
      
      if (remainingSeconds > 0) {
          lcd.setCursor(0,2);
          lcd.printf("Init Sensors: %ds ", remainingSeconds);
          lcd.setCursor(0,3);
          lcd.print("Please Wait...  ");
          
          // During init, we can still update temp/humid seamlessly
          // Read DHT periodically
           if (currentMillis - lastDataSend >= 2000) {
                float newHum = dht.readHumidity();
                float newTemp = dht.readTemperature();
                if (!isnan(newTemp) && !isnan(newHum)) {
                  temperature = newTemp;
                  humidity = newHum;
                }
                lcd.setCursor(0,0);
                lcd.printf("T:%.1fC H:%d%%   ", temperature, (int)humidity);
                lastDataSend = currentMillis;
           }
           delay(100);
           return; // SKIP READING LOGIC UNTIL INIT DONE
      } else {
          isInitializing = false;
          lcd.setCursor(0,2);
          lcd.print("                "); // Clear line
          // Force immediate read
          lastDataSend = 0; 
      }
  }

  // 3. Read Sensors & Send Data (Every 60 Seconds)
  // Changed interval to 60000ms (1 minute)
  if (!isInitializing && (currentMillis - lastDataSend >= 60000 || lastDataSend == 0)) {
    lastDataSend = currentMillis;

    // ===========================================
    // ★★★ NEW SENSOR READING LOGIC ★★★
    // ===========================================

    // --- STEP 1: FORCE TDS & TURBIDITY OFF! ---
    // This removes the electrical noise so pH can be read.
    digitalWrite(tdsPowerPin, LOW); 
    digitalWrite(turbidityPowerPin, LOW);
    delay(200); // Wait for water voltage to stabilize
    
    // Read DHT first
    float newHum = dht.readHumidity();
    float newTemp = dht.readTemperature();
    if (!isnan(newTemp) && !isnan(newHum)) {
      temperature = newTemp;
      humidity = newHum;
    }

    // --- STEP 2: READ pH (CLEANEST STATE) ---
    long pHAvg = 0;
    for(int i = 0; i < numSamples; i++) { 
      pHAvg += analogRead(pHPin); 
      delay(10); 
    }
    float pHVoltage = (pHAvg / (float)numSamples) * (3.3 / 4095.0);
    float pHValue = slope * pHVoltage + offset;
    
    // --- STEP 3: READ TURBIDITY (Turn ON -> Read -> Turn OFF) ---
    digitalWrite(turbidityPowerPin, HIGH);
    delay(800); // Increased warmup for stability
    long turbSum = 0;
    for(int i = 0; i < 5; i++) { 
      turbSum += analogRead(turbidityPin); 
      delay(10); 
    }
    digitalWrite(turbidityPowerPin, LOW); // Turn OFF immediately
    
    int turbidityRaw = turbSum / 5;
    // Calibrated for your sensor: Max Raw ~2100 = 100% Clarity
    int clarityValue = map(turbidityRaw, 0, 2100, 0, 100); 
    clarityValue = constrain(clarityValue, 0, 100);

    // --- STEP 4: READ TDS (Turn ON -> Read -> Turn OFF) ---
    digitalWrite(tdsPowerPin, HIGH);
    delay(800); // Increased warmup for stability
    
    // Read TDS
    long tdsAvg = 0;
    for(int i = 0; i < numSamples; i++) { 
      tdsAvg += analogRead(tdsPin); 
      delay(10); 
    }
    float tdsVoltage = (tdsAvg / (float)numSamples) * (3.3 / 4095.0);
    digitalWrite(tdsPowerPin, LOW); // Turn OFF immediately 

    // DEBUG: Print Raw TDS
    Serial.print("RAW TDS ANALOG: ");
    Serial.println(tdsAvg / numSamples);
    Serial.print("TDS VOLTAGE: ");
    Serial.println(tdsVoltage); 

    // Calculate TDS
    float compensationCoefficient = 1.0 + 0.02 * (temperature - 25.0);
    float compensatedVoltage = tdsVoltage / compensationCoefficient;
    float tdsValue = (133.42 * pow(compensatedVoltage, 3)
                     -255.86 * pow(compensatedVoltage, 2)
                     +857.39 * compensatedVoltage) * tdsFactor;

    // ===========================================
    
    // LCD Update
    lcd.clear();
    lcd.setCursor(0,0);
    lcd.printf("T:%.1fC H:%d%%", temperature, (int)humidity);
    lcd.setCursor(0,1);
    lcd.printf("pH:%.2f TDS:%d", pHValue, (int)tdsValue);
    lcd.setCursor(0,2);
    lcd.printf("Clarity:%d%%", clarityValue);
    lcd.setCursor(0,3);
    lcd.print(WiFi.status() == WL_CONNECTED ? "WiFi: OK        " : "WiFi: DOWN      ");

    // Send to Cloud
    if (WiFi.status() == WL_CONNECTED) {
      sendToRailway(pHValue, clarityValue, tdsValue, temperature, humidity);
    }
  }
  
  delay(5);
}

/************ WIFI ************/
void connectToWiFi() {
  Serial.print("[WiFi] Connecting to "); 
  Serial.println(ssid);
  
  lcd.clear();
  lcd.setCursor(0,0);
  lcd.print("Connecting WiFi...");
  lcd.setCursor(0,1);
  lcd.print(ssid);
  
  WiFi.mode(WIFI_STA);
  WiFi.begin(ssid, password);
  
  // 30 Seconds Countdown
  unsigned long startAttempt = millis();
  bool connected = false;
  
  while (millis() - startAttempt < 30000) {
      if (WiFi.status() == WL_CONNECTED) {
          connected = true;
          break;
      }
      
      int remaining = 30 - ((millis() - startAttempt) / 1000);
      lcd.setCursor(0, 3);
      lcd.printf("Waiting... %ds    ", remaining);
      
      Serial.print(".");
      updateSound(); 
      delay(500);
  }
  
  // If still not connected after 30s, keep trying but change display
  if (!connected) {
      lcd.setCursor(0, 3);
      lcd.print("Connecting...       ");
      
      while (WiFi.status() != WL_CONNECTED) {
          Serial.print(".");
          updateSound();
          delay(500);
      }
  }
  
  // Connected!
  Serial.println("\n[WiFi] Connected! ✅");
  Serial.print("[WiFi] IP: ");
  Serial.println(WiFi.localIP().toString());
    
  lcd.clear();
  lcd.setCursor(0,0);
  lcd.print("WiFi Connected!");
  lcd.setCursor(0,1);
  lcd.print(WiFi.localIP().toString());
  lcd.setCursor(0,2);
  lcd.print("AquaSense Ready");
    
  digitalWrite(powerLedPin, HIGH);
  digitalWrite(wifiLedPin, LOW);
    
  // ✅ WIFI CONNECTED - One beep only!
  startSound(SOUND_WIFI_CONNECT);
}

/************ SEND DATA ************/
void sendToRailway(float pH, int turbidity, float tds, float temp, float humid) {
  WiFiClientSecure client;
  client.setInsecure();
  HTTPClient http;
  http.setTimeout(15000);
  
  String url = String(serverName) + "/api/readings";
  
  if(http.begin(client, url)) {
    http.addHeader("Content-Type", "application/json");
    http.addHeader("Accept", "application/json");
    http.addHeader("X-Device-Token", deviceToken);
    
    String jsonPayload = "{";
    jsonPayload += "\"device_id\":\"" + String(deviceID) + "\",";
    jsonPayload += "\"turbidity\":" + String(turbidity) + ",";
    jsonPayload += "\"tds\":" + String(tds, 1) + ",";
    jsonPayload += "\"ph\":" + String(pH, 2) + ",";
    jsonPayload += "\"temperature\":" + String(temp, 1) + ",";
    jsonPayload += "\"humidity\":" + String(humid, 1) + ",";
    jsonPayload += "\"no_water_detected\": false";
    jsonPayload += "}";
    
    int httpResponseCode = http.POST(jsonPayload);
    
    if(httpResponseCode > 0) {
      // 📤 DATA SENT - pip!
      startSound(SOUND_DATA_SENT);
      Serial.println("[HTTP] POST success ✅");
      Serial.print("[HTTP] Response: ");
      Serial.println(httpResponseCode);
      
      // Quick LCD confirmation
      lcd.setCursor(12,3);
      lcd.print("SENT");
      delay(100);
      lcd.setCursor(12,3);
      lcd.print("    ");
    } else {
      Serial.print("[HTTP] POST failed ❌");
      Serial.println(http.errorToString(httpResponseCode).c_str());
    }
    
    http.end();
  }
  
  noTone(buzzerPin);
  digitalWrite(buzzerPin, LOW);
}
