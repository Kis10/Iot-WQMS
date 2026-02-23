#include <Arduino.h>

// PINS
const int pHPin = 34;       // Analog Input (pH Sensor)
const int tdsPin = 35;      // Analog Input (TDS Sensor)
const int tdsPowerPin = 26; // Digital Output (TDS Power Switch)

// CALIBRATION VALUES (From your original sketch)
float slope = -7.575;
float offset = 28.84;
float tdsFactor = 0.5;

void setup() {
  Serial.begin(115200);
  
  // 1. Setup Power Pin for TDS Control
  pinMode(tdsPowerPin, OUTPUT);
  digitalWrite(tdsPowerPin, LOW); // Start with TDS OFF (Clean Water)
  
  // 2. Setup Analog Resolution
  analogReadResolution(12); // 0-4095 range
  
  delay(1000);
  Serial.println("--- REAL READING MONITOR (ISOLATED) ---");
}

void loop() {
  
  // ==========================================
  // STEP 1: READ pH (TDS MUST BE OFF!)
  // ==========================================
  
  // Force TDS OFF
  digitalWrite(tdsPowerPin, LOW);
  delay(200); // Wait for electrical noise to dissipate (Silence)
  
  // Read pH
  long pHSum = 0;
  for(int i=0; i<20; i++) {
    pHSum += analogRead(pHPin);
    delay(5);
  }
  float pHVoltage = (pHSum / 20.0) * (3.3 / 4095.0);
  
  // CALCULATE REAL pH
  // Formula: pH = slope * voltage + offset
  float pHValue = (slope * pHVoltage) + offset;
  
  
  // ==========================================
  // STEP 2: READ TDS (TURN IT ON!)
  // ==========================================
  
  // Turn TDS ON
  digitalWrite(tdsPowerPin, HIGH);
  delay(300); // Wait for sensor to warm up
  
  // Read TDS
  long tdsSum = 0;
  for(int i=0; i<20; i++) {
    tdsSum += analogRead(tdsPin);
    delay(5);
  }
  float tdsVoltage = (tdsSum / 20.0) * (3.3 / 4095.0);
  
  // Turn TDS OFF Immediately
  digitalWrite(tdsPowerPin, LOW); 

  // CALCULATE REAL TDS (Assuming 25.0C temperature)
  float compensationCoefficient = 1.0 + 0.02 * (25.0 - 25.0); 
  float compensatedVoltage = tdsVoltage / compensationCoefficient;
  
  float tdsValue = (133.42 * pow(compensatedVoltage, 3)
                   - 255.86 * pow(compensatedVoltage, 2)
                   + 857.39 * compensatedVoltage) * tdsFactor;
                   
  // ==========================================
  // DISPLAY RESULTS
  // ==========================================
  Serial.print("pH Level: ");
  Serial.print(pHValue, 2);  // 2 decimal places
  Serial.print("   |   ");
  
  Serial.print("TDS Value: ");
  Serial.print(tdsValue, 0); // Whole number
  Serial.println(" ppm");

  delay(2000);
}
