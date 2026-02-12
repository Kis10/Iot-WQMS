#include <Arduino.h>

// PINS
const int pHPin = 34;
const int tdsPin = 35;
const int tdsPowerPin = 26; // The new power pin

void setup() {
  Serial.begin(115200);
  
  // 1. Turn ON TDS Power PERMANENTLY for this test
  pinMode(tdsPowerPin, OUTPUT);
  digitalWrite(tdsPowerPin, HIGH); 
  
  delay(2000); // Wait 2 seconds for stability
  Serial.println("--- DIAGNOSTIC MODE ---");
  Serial.println("TDS Sensor is POWERED ON (Pin 26 HIGH)");
}

void loop() {
  // Read pH
  long pHSum = 0;
  for(int i=0; i<30; i++) {
    pHSum += analogRead(pHPin);
    delay(5);
  }
  float pHAvg = pHSum / 30.0;
  float pHVoltage = (pHAvg / 4095.0) * 3.3;

  // Read TDS
  long tdsSum = 0;
  for(int i=0; i<30; i++) {
    tdsSum += analogRead(tdsPin);
    delay(5);
  }
  float tdsAvg = tdsSum / 30.0;
  float tdsVoltage = (tdsAvg / 4095.0) * 3.3;

  // Print raw values
  Serial.print("pH Raw: "); Serial.print(pHAvg);
  Serial.print(" | pH Volts: "); Serial.print(pHVoltage, 3);
  Serial.print("V");
  
  Serial.print("  ||  ");
  
  Serial.print("TDS Raw: "); Serial.print(tdsAvg);
  Serial.print(" | TDS Volts: "); Serial.print(tdsVoltage, 3);
  Serial.println("V");
  
  delay(1000);
}
