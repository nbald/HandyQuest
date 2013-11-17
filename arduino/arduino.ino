int sensorPin = A0;
int sensorValue = 0;

int flashPin = A1;

const int numReadings = 10;
int readings[numReadings];
int index = 0;
int total;
int average;

int mini=999;
boolean has_already_flashed=false;

void setup() {
  pinMode(flashPin, OUTPUT);
  digitalWrite(flashPin, LOW);
  delay(1000);
  Serial.begin(9600);
  Serial.println("AT$SS=CACA");
  resetAverage();
}

void resetAverage() {
  for (int thisReading = 0; thisReading < numReadings; thisReading++)
    readings[thisReading] = 999; 
  total = 999*numReadings;
  average = 999;
}

void loop() {
  // subtract the last reading:
  total= total - readings[index];         
  // read from the sensor:  
  readings[index] = analogRead(sensorPin); 
  // add the reading to the total:
  total= total + readings[index];       
  // advance to the next position in the array:  
  index = index + 1;                    

  // if we're at the end of the array...
  if (index >= numReadings)              
    // ...wrap around to the beginning: 
    index = 0;                           

  // calculate the average:
  average = total / numReadings;         
  // send it to the computer as ASCII digits
  
  if (average < mini) mini=average;
  
  if (average < 180 && !has_already_flashed) {
    digitalWrite(flashPin, HIGH);
    delay(10);
    digitalWrite(flashPin, LOW);
    has_already_flashed=true;
  }
  
  if (average > 200 && has_already_flashed) {
    delay(2000); // must delay because sigfox gets jammed by the flash
    char tmp[10];
    sprintf(tmp, "AT$SS=%04x", mini);
    Serial.println(tmp);
    resetAverage();
    has_already_flashed=false;
    mini=999;
    delay(8000);
  }
  
  
  
  delay(1);  
}
