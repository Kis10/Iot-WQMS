#define TdsSensorPin 35
#define VREF 3.3      // analog reference voltage(Volt) of the ADC

void setup()
{
    Serial.begin(115200);
    pinMode(TdsSensorPin, INPUT);
    // Use maximum resolution
    analogReadResolution(12); // ESP32 = 0-4095
}

void loop()
{
    long analogBufferSum = 0;
    // Read 30 times quickly
    for(int i=0; i<30; i++) {
       analogBufferSum += analogRead(TdsSensorPin);
       delay(10);
    }
    float averageVoltage = (analogBufferSum / 30.0) * (3.3 / 4095.0); // read the analog value more stable by the median filtering algorithm, and convert to voltage value
    float temperature = 25.0; // Assume 25C for testing
    
    // Convert to TDS
    float compensationCoefficient = 1.0 + 0.02 * (temperature - 25.0);
    float compensationVoltage = averageVoltage / compensationCoefficient;
    float tdsValue = (133.42 * pow(compensationVoltage, 3) - 255.86 * pow(compensationVoltage, 2) + 857.39 * compensationVoltage) * 0.5; 
    
    Serial.print("RAW Reading: ");
    Serial.print(analogBufferSum / 30);
    Serial.print("   Voltage:");
    Serial.print(averageVoltage,2);
    Serial.print("V   TDS Value:");
    Serial.print(tdsValue,0);
    Serial.println("ppm");
    
    delay(1000);
}
