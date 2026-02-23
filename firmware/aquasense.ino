/************ LIBRARIES ************/
#include <OneWire.h>
#include <DallasTemperature.h>
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
const int ds18b20Pin   = 15; // DS18B20 Water Temperature Sensor
const int powerLedPin  = 27; // Digital Output (PN2222A 6E)
const int wifiLedPin   = 13; // Digital Output

// TDS POWER CONTROL (GPIO 26)
const int tdsPowerPin  = 26; 

// TURBIDITY POWER CONTROL (GPIO 14)
const int turbidityPowerPin = 14; 

/************ DS18B20 WATER TEMPERATURE SETUP ************/
OneWire oneWire(ds18b20Pin);
DallasTemperature waterTempSensor(&oneWire);

/************ SENSOR VARIABLES ************/
float slope = -7.575;
float offset = 28.84;
float tdsFactor = 0.5;
const int numSamples = 10;

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


// STATESPACE VARIABLES
bool isSingleShotDone = false;
bool isInitializing = true;
unsigned long initStartTime = 0;

void setup() {
  Serial.begin(115200);

  // Initialize pins
  pinMode(buzzerPin, OUTPUT);
  pinMode(powerLedPin, OUTPUT);
  pinMode(wifiLedPin, OUTPUT);
  
  // SENSOR POWER INITIAL OFF
  pinMode(tdsPowerPin, OUTPUT);
  digitalWrite(tdsPowerPin, LOW); 
  
  pinMode(turbidityPowerPin, OUTPUT);
  digitalWrite(turbidityPowerPin, LOW); 

  // Default states
  digitalWrite(buzzerPin, LOW);
  digitalWrite(powerLedPin, HIGH); // Power LED ON immediately (Green)
  digitalWrite(wifiLedPin, LOW);   // WiFi LED starts OFF

  // LCD Init
  Wire.begin(LCD_SDA, LCD_SCL);
  lcd.begin();
  lcd.backlight();
  lcd.clear();
  lcd.setCursor(0,0);
  lcd.print("Aquasense Booting");
  
  // 🎵 SYSTEM READY SOUND
  startSound(SOUND_READY);
  
  // DS18B20 Water Temperature Sensor Init
  waterTempSensor.begin();
  delay(1000);
  analogReadResolution(12);

  // WiFi Connection with 30s Timeout & Blink UI
  connectToWiFiWithUI();
}

void loop() {
  updateSound();

  // If already done, just freeze (loop idle)
  if (isSingleShotDone) {
    if (!soundPlaying && currentSound != 0) {
       noTone(buzzerPin); // Security safety silence
    }
    delay(100);
    return;
  }

  // If WiFi lost during operation (unlikely in short run but possible)
  if (WiFi.status() != WL_CONNECTED) {
    lcd.clear();
    lcd.setCursor(0,0);
    lcd.print("Error: WiFi Lost");
    while(1); // Halt
  }

  unsigned long currentMillis = millis();

  // 2. Initialization Phase (1 Minute)
  if (isInitializing) {
      if (initStartTime == 0) initStartTime = currentMillis; // Latch start time

      unsigned long elapsedInit = currentMillis - initStartTime;
      int remainingSeconds = 60 - (elapsedInit / 1000);
      
      if (remainingSeconds > 0) {
          lcd.setCursor(0,0);
          lcd.print(WiFi.localIP().toString());
          
          lcd.setCursor(0,1);
          lcd.print("Initializing Sensors");
          
          lcd.setCursor(0,2);
          lcd.print("Please wait...");
          
          lcd.setCursor(0,3);
          lcd.printf("Countdown: %ds    ", remainingSeconds);
          
          // Background read DS18B20 for warmup
          waterTempSensor.requestTemperatures();
          
          delay(100);
      } else {
          isInitializing = false;
          lcd.clear();
          lcd.setCursor(0,0);
          lcd.print("Processing...");
      }
      return;
  }

  // 3. SINGLE SHOT READING & SENDING
  if (!isSingleShotDone && !isInitializing) {
    performSingleReadingAndSend();
    isSingleShotDone = true; // LOCK
  }
}

void connectToWiFiWithUI() {
  lcd.setCursor(0,1);
  lcd.print("Connecting to:");
  lcd.setCursor(0,2);
  lcd.print(ssid);

  WiFi.mode(WIFI_STA);
  WiFi.begin(ssid, password);

  unsigned long startAttempt = millis();
  bool connected = false;
  
  while (millis() - startAttempt < 30000) {
      if (WiFi.status() == WL_CONNECTED) {
          connected = true;
          break;
      }
      
      int remaining = 30 - ((millis() - startAttempt) / 1000);
      
      // Blink Effect on "Waiting..." AND Red LED
      lcd.setCursor(0, 3);
      if ((millis() / 500) % 2 == 0) {
        lcd.printf("Waiting... %ds    ", remaining);
        digitalWrite(wifiLedPin, HIGH); // LED ON
      } else {
        lcd.print("                "); // Blink off
        digitalWrite(wifiLedPin, LOW);  // LED OFF
      }
      
      Serial.print(".");
      updateSound(); 
      delay(200);
  }

  if (!connected) {
     lcd.clear();
     lcd.setCursor(0,0);
     lcd.print("Aquasense Booting");
     lcd.setCursor(0,1);
     lcd.print("Connection Failed");
     lcd.setCursor(0,2);
     lcd.print("Check Network");
     
     // Stay in "waiting..." with blink forever (Halt)
     while(1) {
        lcd.setCursor(0, 3);
        if ((millis() / 500) % 2 == 0) {
          lcd.print("Waiting...      ");
          digitalWrite(wifiLedPin, HIGH); // LED ON
        } else {
          lcd.print("                ");
          digitalWrite(wifiLedPin, LOW);  // LED OFF
        }
        delay(200);
     }
  }

  // CONNECTED SUCCESS
  digitalWrite(wifiLedPin, LOW); 
  digitalWrite(powerLedPin, HIGH);
  
  lcd.clear();
  lcd.setCursor(0,0);
  lcd.print("WiFi Connected!");
  lcd.setCursor(0,1);
  lcd.print("WELCOME!");
  
  startSound(SOUND_WIFI_CONNECT);
  
  // 5s Countdown before startup
  for(int i=5; i>0; i--) {
     lcd.setCursor(0,3);
     lcd.printf("Starting in %ds... ", i);
     delay(1000);
  }
  lcd.clear();
}

void performSingleReadingAndSend() {
    // --- STEP 1: FORCE NOISE SOURCES OFF ---
    digitalWrite(tdsPowerPin, LOW); 
    digitalWrite(turbidityPowerPin, LOW);
    delay(1000); // 1s silence
    
    // --- STEP 2: READ WATER TEMPERATURE (DS18B20) ---
    waterTempSensor.requestTemperatures();
    float waterTemp = waterTempSensor.getTempCByIndex(0);
    if (waterTemp == DEVICE_DISCONNECTED_C || waterTemp < -50.0) {
      waterTemp = 25.0; // Fallback if sensor error
      Serial.println("[DS18B20] Sensor error, using fallback 25.0C");
    }

    // --- STEP 3: READ pH ---
    long pHAvg = 0;
    for(int i = 0; i < 20; i++) { 
      pHAvg += analogRead(pHPin); 
      delay(10); 
    }
    float pHVoltage = (pHAvg / 20.0) * (3.3 / 4095.0);
    float pHVal = slope * pHVoltage + offset;
    
    // --- STEP 4: READ TURBIDITY ---
    pinMode(turbidityPowerPin, OUTPUT);
    digitalWrite(turbidityPowerPin, HIGH);
    delay(2000); // Increased warmup for stability
    
    long turbSum = 0;
    for(int i = 0; i < 15; i++) { 
      turbSum += analogRead(turbidityPin); 
      delay(20); 
    }
    digitalWrite(turbidityPowerPin, LOW);
    
    int turbRaw = turbSum / 15;
    Serial.print("DEBUG: Turbidity Raw ADC = "); Serial.println(turbRaw);
    
    // Map Raw to Clarity: 100% is 2100 (or higher)
    int clarity = map(turbRaw, 0, 2100, 0, 100); 
    clarity = constrain(clarity, 0, 100);

    // --- STEP 5: READ TDS ---
    pinMode(tdsPowerPin, OUTPUT);
    digitalWrite(tdsPowerPin, HIGH);
    delay(1500); // Longer warmup for TDS
    
    long tdsSum = 0;
    for(int i = 0; i < 30; i++) { 
      tdsSum += analogRead(tdsPin); 
      delay(10); 
    }
    digitalWrite(tdsPowerPin, LOW);
    
    float tdsRawAvg = tdsSum / 30.0;
    Serial.print("DEBUG: TDS Raw ADC = "); Serial.println(tdsRawAvg);
    float tdsVolt = tdsRawAvg * (3.3 / 4095.0);
    
    // Calculate TDS with actual water temperature compensation
    float compCoeff = 1.0 + 0.02 * (waterTemp - 25.0);
    float compVolt = tdsVolt / compCoeff;
    float tdsVal = (133.42 * pow(compVolt, 3) - 255.86 * pow(compVolt, 2) + 857.39 * compVolt) * tdsFactor;

    // --- SEND DATA ---
    sendToRailway(pHVal, clarity, tdsVal, waterTemp);

    lcd.clear();
    lcd.setCursor(0,1);
    lcd.print("    DATA SENT!    ");
    
    // Custom 4 beeps
    for(int i=0; i<4; i++) {
        digitalWrite(buzzerPin, HIGH);
        delay(200); // Beep ON
        digitalWrite(buzzerPin, LOW);
        delay(300); // Beep OFF
    }
    
    delay(2000); // "Data sent!" 2s duration

    // --- FREEZE RESULT ON LCD ---
    lcd.clear();
    lcd.setCursor(0,0);
    lcd.printf("WaterTemp:%.1fC", waterTemp);
    lcd.setCursor(0,1);
    lcd.printf("pH:%.2f", pHVal);
    lcd.setCursor(9,1);
    lcd.printf("TDS:%d", (int)tdsVal);
    lcd.setCursor(0,2);
    lcd.printf("Clarity:%d%%", clarity);
    lcd.setCursor(0,3);
    lcd.print(" READING COMPLETE ");
}


/************ SEND DATA ************/
void sendToRailway(float pH, int turbidity, float tds, float waterTemp) {
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
    jsonPayload += "\"temperature\":" + String(waterTemp, 1) + ",";
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
