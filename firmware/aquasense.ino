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
struct WiFiNetwork {
  const char* ssid;
  const char* password;
};

WiFiNetwork knownNetworks[] = {
  {"izEf", "OHmMSYnh"},
  {"NO NETWORK01_2.4", "Cristy_028"},
  {"MIS OFFICE", "MISO_Gm@2025"},
  {"kirstine2G", "@012345_2G"}
};

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
// --- pH Calibration ---
float phSlope = -7.575;
float phOffset = 30.126; //(28.24) Set to the user's preferred 30.126

// --- Turbidity Calibration ---
float turbidityOffset = 0.0; // In case turbidity needs its own offset later

// --- TDS Calibration ---
float tdsFactor = 0.5;
float tdsOffset = 0.0;  // In case TDS needs its own offset later

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
  delay(1000);
  Serial.println("\n\n--- AQUASENSE SYSTEM STARTING ---");
  Serial.print("pH Offset: "); Serial.println(phOffset);

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

/************ UPDATED WIFI CONNECTION FUNCTION ************/
void connectToWiFiWithUI() {
  lcd.setCursor(0,1);
  lcd.print("Scanning Networks");
  
  WiFi.mode(WIFI_STA);
  WiFi.disconnect();
  delay(100);
  
  // Scan for available networks
  lcd.setCursor(0,2);
  lcd.print("Scanning...       ");
  int networksFound = WiFi.scanNetworks();
  
  lcd.setCursor(0,2);
  lcd.print("                ");
  
  bool connected = false;
  int networkCount = sizeof(knownNetworks) / sizeof(knownNetworks[0]);
  
  // Try each known network that's available
  for (int attempt = 0; attempt < 3 && !connected; attempt++) { // Multiple attempts
    for (int i = 0; i < networkCount && !connected; i++) {
      
      // Check if this network is available in scan results
      bool networkAvailable = false;
      for (int j = 0; j < networksFound; j++) {
        if (strcmp(WiFi.SSID(j).c_str(), knownNetworks[i].ssid) == 0) {
          networkAvailable = true;
          break;
        }
      }
      
      // Skip if network not found in scan (optional - you can remove this check
      // if you want to try connecting anyway)
      if (!networkAvailable && networksFound > 0) {
        continue;
      }
      
      lcd.setCursor(0,1);
      lcd.print("Connecting to:    ");
      lcd.setCursor(0,2);
      
      // Display SSID (truncate if too long)
      String ssidStr = String(knownNetworks[i].ssid);
      if (ssidStr.length() > 16) {
        ssidStr = ssidStr.substring(0, 13) + "...";
      }
      lcd.print(ssidStr);
      lcd.print("                ");
      
      WiFi.begin(knownNetworks[i].ssid, knownNetworks[i].password);
      
      unsigned long startAttempt = millis();
      while (millis() - startAttempt < 10000) { // 10s per network
        if (WiFi.status() == WL_CONNECTED) {
          connected = true;
          break;
        }
        
        // Blinking animation
        int remaining = 10 - ((millis() - startAttempt) / 1000);
        lcd.setCursor(0, 3);
        if ((millis() / 500) % 2 == 0) {
          lcd.printf("Trying... %ds    ", remaining);
          digitalWrite(wifiLedPin, HIGH);
        } else {
          lcd.print("                ");
          digitalWrite(wifiLedPin, LOW);
        }
        
        Serial.print(".");
        updateSound();
        delay(200);
      }
    }
    
    // Rescan if not connected and we have more attempts
    if (!connected && attempt < 2) {
      lcd.setCursor(0,1);
      lcd.print("Rescanning...     ");
      networksFound = WiFi.scanNetworks();
      delay(1000);
    }
  }
  
  // Handle connection result
  if (!connected) {
    lcd.clear();
    lcd.setCursor(0,0);
    lcd.print("No Known Networks");
    lcd.setCursor(0,1);
    lcd.print("Found");
    lcd.setCursor(0,2);
    lcd.print("Check WiFi &");
    lcd.setCursor(0,3);
    lcd.print("Restart Device");
    
    // Play disconnect sound
    startSound(SOUND_WIFI_DISCONNECT);
    
    // Stay in "waiting..." with blink forever (Halt)
    while(1) {
      lcd.setCursor(0, 3);
      if ((millis() / 500) % 2 == 0) {
        lcd.print("RESTART DEVICE   ");
        digitalWrite(wifiLedPin, HIGH);
      } else {
        lcd.print("                ");
        digitalWrite(wifiLedPin, LOW);
      }
      updateSound();
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
  lcd.print(WiFi.SSID()); // Show which network we connected to
  lcd.setCursor(0,2);
  lcd.print(WiFi.localIP().toString());
  
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

    // Ensure other sensors are OFF to prevent interference in one bottle
    digitalWrite(tdsPowerPin, LOW);
    digitalWrite(turbidityPowerPin, LOW);
    delay(2000); 

    // --- STEP 3: READ pH ---
    long pHAvg = 0;
    for(int i = 0; i < 20; i++) { 
      pHAvg += analogRead(pHPin); 
      delay(10); 
    }
    float pHVoltage = (pHAvg / 20.0) * (3.3 / 4095.0);
    Serial.print("DEBUG: pH Voltage = "); Serial.println(pHVoltage, 3);
    float pHVal = phSlope * pHVoltage + phOffset;
    
    // --- STEP 4: READ TURBIDITY ---
    // Ensure TDS is OFF before starting Turbidity
    digitalWrite(tdsPowerPin, LOW);
    delay(2000); // 2-second settling time for common water container
    
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
    
    // Map Raw to Voltage, then to Clarity
    float turbVoltage = turbRaw * (3.3 / 4095.0);
    
    // Updated Mapping for your specific hardware:
    // In your last test, Purified Water (Clean) resulted in 40% with the old 2.5V limit.
    // This confirms your sensor's peak clean output is roughly 1.3V.
    // We now map 1.3V and above to 100% Clarity, and 0.4V to 0% Clarity.
    int clarity = map(turbVoltage * 1000, 400, 1300, 0, 100);
    clarity = constrain(clarity, 0, 100);
    delay(2000); // Electrical settling for one-bottle test

    // --- STEP 5: READ TDS ---
    pinMode(tdsPowerPin, OUTPUT);
    digitalWrite(tdsPowerPin, HIGH);
    // The TDS sensor (especially DFRobot types) has filtering capacitors that take time to charge.
    // 1500ms was likely too short, causing it to read ~0V (0.20 ppm). We increase this to 3000ms.
    delay(3000); 
    
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
    
    float tdsVal = 0;
    if (compVolt > 0.01) { // Only calculate if we have a real voltage reading
        tdsVal = (133.42 * pow(compVolt, 3) - 255.86 * pow(compVolt, 2) + 857.39 * compVolt) * tdsFactor;
    }

    // --- SEND DATA ---
    sendToRailway(pHVal, clarity, tdsVal, waterTemp);
    
    // allow a brief moment before freezing LCD
    delay(2000);

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
    
    // Sanitize values to prevent JSON strictly failing on NaN/Inf
    if (isnan(pH) || isinf(pH)) pH = 0.0;
    if (isnan(tds) || isinf(tds)) tds = 0.0;
    if (isnan(waterTemp) || isinf(waterTemp)) waterTemp = 0.0;

    String jsonPayload = "{";
    jsonPayload += "\"device_id\":\"" + String(deviceID) + "\",";
    jsonPayload += "\"turbidity\":" + String(turbidity) + ",";
    jsonPayload += "\"tds\":" + String(tds, 1) + ",";
    jsonPayload += "\"ph\":" + String(pH, 2) + ",";
    jsonPayload += "\"temperature\":" + String(waterTemp, 1) + ",";
    jsonPayload += "\"no_water_detected\": false";
    jsonPayload += "}";
    
    int httpResponseCode = http.POST(jsonPayload);
    
    if(httpResponseCode == 200 || httpResponseCode == 201) {
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
    } else if (httpResponseCode > 0) {
      Serial.print("[HTTP] SERVERSIDE REJECT ❌ Code: ");
      Serial.println(httpResponseCode);
      lcd.setCursor(8,3);
      lcd.print("API ERR:");
      lcd.print(httpResponseCode);
    } else {
      Serial.print("[HTTP] POST failed ❌");
      Serial.println(http.errorToString(httpResponseCode).c_str());
    }
    
    http.end();
  }
  
  noTone(buzzerPin);
  digitalWrite(buzzerPin, LOW);
}
