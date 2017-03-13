#define CUSTOM_SETTINGS
#define INCLUDE_SMS_SHIELD
#define INCLUDE_PHONE_SHIELD
#define INCLUDE_TWITTER_SHIELD
#define INCLUDE_FACEBOOK_SHIELD
#define INCLUDE_CAMERA_SHIELD
#define INCLUDE_GPS_SHIELD
#define INCLUDE_INTERNET_SHIELD
#define INCLUDE_TERMINAL_SHIELD

/* Include 1Sheeld library. */
#include <OneSheeld.h>
#include <NewPing.h>
#include <TimedAction.h>
#include <toneAC.h>

#define PING_PIN 11 // Arduino pin for both trigger and echo
//Setup a ping
NewPing sonar(PING_PIN, PING_PIN );

 

/*Setup HTTP request for sending data to web host*/
HttpRequest request ("http://pburton.000webhostapp.com/GPSandStatus.php");
/*User and vehicle constant values*/
const char user[] = "-Kf178BOnvu09w88toEY";
const char vehicle[] = "-Kf3n0ws3XVOiDj8rkhQ";
/* Define boolean flag. */
boolean isMessageSent = false;
/*Assign LED's and buttons to a certain pin*/
int ledPin = 13;
int button1 = 12;
int brakeLED = 8;

/*Forward declaration of methods*/
void antiTheft();
void brakingSystem();
/*Create 2 timed actions, one for the anti theft side of things, i.e. upload GPS, get Status, upload pictures etc, and one
for the "braking system". This will allow them to run at timed intervals without interfering with eachother too much*/
TimedAction antiT = TimedAction(10000,antiTheft);
TimedAction brake = TimedAction(200,brakingSystem);
  
void setup()
{
  /* Start communication. */
  OneSheeld.begin();

  /* Setup pins */
  pinMode(ledPin, OUTPUT);
  pinMode(brakeLED, OUTPUT);
  pinMode(button1, INPUT);
   /* Subscribe to success callback for the request. */
  request.setOnSuccess(&onSuccess);
  /* Subscribe to failure callback for the request. */
  request.setOnFailure(&onFailure);
   /* Sunbscribe to setOnNextResponseBytesUpdate to be notified once the bytes is updated in the response object. */
  request.getResponse().setOnNextResponseBytesUpdate(&onBytesUpdate);
  /* Subscribe to response errors. */
  request.getResponse().setOnError(&onError);

}



void antiTheft()
{
  if (digitalRead(button1) == HIGH)
  {
    if (!isMessageSent)
    {
      /* Send the SMS. */
      SMS.send("00353863039062", "Hi there, your car is being robbed! Have a great day ;-)");
      //SMS.send("00353877440517", "Hi there, your car is being robbed! Have a great day ;-)");
      /* Set the flag. */
      isMessageSent = true;

      Camera.setFlash(ON);
      /* Take a photo using the phone's rear camera. */
      Camera.rearCapture();
      /* Delay for 5 seconds. */
      OneSheeld.delay(5000);
      Twitter.tweetLastPicture("Hi there, your car is being robbed! Have a great day ;-)", FROM_ONESHEELD_FOLDER);
      Facebook.postLastPicture("Hi there, your car is being robbed! Have a great day ;-)", FROM_ONESHEELD_FOLDER);
      OneSheeld.delay(5000);
      Phone.call("00353863039062");
      //Phone.call("00353877440517");
    }
  }
  else
  {
    /* Reset the flag. */
    isMessageSent = false;
  }

  /*Code to send data to firebase*/
  char latVal[12];
  /*GPS.getLatitude returns a double. This is then converted to string using dtostrf*/
  dtostrf(GPS.getLatitude(), 4, 7, latVal);
  char longVal[12];
  dtostrf(GPS.getLongitude(), 4, 7, longVal);
  /*Add parameters to the request*/
  request.addParameter("latitude", latVal);
  request.addParameter("longitude", longVal);
  request.addParameter("User", user);
  request.addParameter("Vehicle", vehicle);
  Internet.performGet(request);
}

void brakingSystem(){
  unsigned int uS = sonar.ping(); // Send ping, get ping time in microseconds (uS).
  float distance = uS / US_ROUNDTRIP_CM;
  if (distance < 30){
    digitalWrite(brakeLED, HIGH);
    toneAC();
  }
  else if(distance >= 30 && distance <= 60){
    digitalWrite(brakeLED, LOW);
    toneAC(500);
  }
  else{
    digitalWrite(brakeLED, LOW);
    toneAC();
  }
}



void loop(){
  antiT.check();
  brake.check();
}

void onSuccess(HttpResponse &response)
{
  char* myArray;
  /*This code checks the status from the response and if status is 1, "turn off the engine"*/
  myArray = response.getBytes();
  int i = myArray[0];
  if(i == 49){
    digitalWrite(ledPin, HIGH);
  }
  else{
    digitalWrite(ledPin, LOW);
  }
  response.getNextBytes();
  
}

void onFailure(HttpResponse &response)
{
  /* Print out the status code of failure.*/
  Terminal.println(response.getStatusCode());
  /* Print out the data failure.*/
  Terminal.println(response.getBytes());
}

void onBytesUpdate(HttpResponse &response)
{
  /* Print out the data on the terminal. */
  Terminal.println(response.getBytes());
  /* Check if the reponse is sent till the last byte. */
  if(!response.isSentFully())
    {       
      /* Ask for the next 64 bytes. */
      response.getNextBytes();
    }

}

void onError(int errorNumber)
{
  /* Print out error Number.*/
  Terminal.print("Error:");
  switch(errorNumber)
  {
    case INDEX_OUT_OF_BOUNDS: Terminal.println("INDEX_OUT_OF_BOUNDS");break;
    case RESPONSE_CAN_NOT_BE_FOUND: Terminal.println("RESPONSE_CAN_NOT_BE_FOUND");break;
    case HEADER_CAN_NOT_BE_FOUND: Terminal.println("HEADER_CAN_NOT_BE_FOUND");break;
    case NO_ENOUGH_BYTES: Terminal.println("NO_ENOUGH_BYTES");break;
    case REQUEST_HAS_NO_RESPONSE: Terminal.println("REQUEST_HAS_NO_RESPONSE");break;
    case SIZE_OF_REQUEST_CAN_NOT_BE_ZERO: Terminal.println("SIZE_OF_REQUEST_CAN_NOT_BE_ZERO");break;
    case UNSUPPORTED_HTTP_ENTITY: Terminal.println("UNSUPPORTED_HTTP_ENTITY");break;
    case JSON_KEYCHAIN_IS_WRONG: Terminal.println("JSON_KEYCHAIN_IS_WRONG");break;
  }
}


