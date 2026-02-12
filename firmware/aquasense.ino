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
        firstConnection = false;
      }
      WiFi.disconnect();
      WiFi.reconnect();
    } else {
      if (!wasConnected) {
        digitalWrite(wifiLedPin, LOW);
        digitalWrite(powerLedPin, HIGH);
        lcd.setCursor(0,3);
        lcd.print("WiFi: OK        ");
        
        if (!firstConnection) {
          Serial.println("[WiFi] Reconnected! 🧬");
          startSound(SOUND_WIFI_RECONNECT);
        } else {
          Serial.println("[WiFi] Connected! ✅");
          startSound(SOUND_WIFI_CONNECT);
          firstConnection = false;
        }
        wasConnected = true;
      }
    }
  }

  // 2. Read Sensors & Send Data
  if (currentMillis - lastDataSend >= dataSendInterval) {
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
  lcd.print("Connecting WiFi.");
  lcd.setCursor(0,1);
  lcd.print(ssid);
  
  WiFi.mode(WIFI_STA);
  WiFi.begin(ssid, password);
  
  int attempts = 0;
  while(WiFi.status() != WL_CONNECTED && attempts < 30) {
    delay(500);
    Serial.print(".");
    lcd.setCursor(15,3);
    lcd.print(attempts + 1);
    lcd.print("/30");
    attempts++;
    updateSound(); // Keep sounds playing while connecting
  }
  
  if(WiFi.status() == WL_CONNECTED) {
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
  } else {
    Serial.println("\n[WiFi] Failed to connect! ❌");
    
    lcd.clear();
    lcd.setCursor(0,0);
    lcd.print("WiFi Failed!");
    lcd.setCursor(0,1);
    lcd.print("Check Network");
    lcd.setCursor(0,2);
    lcd.print(ssid);
    
    digitalWrite(powerLedPin, LOW);
    digitalWrite(wifiLedPin, HIGH);
  }
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
