#include <SPI.h>
#include <MFRC522.h>
#include <Wire.h>
#include <LiquidCrystal_I2C.h>

#define SS_PIN 10
#define RST_PIN 9
#define BUZZER_PIN 8
#define BUTTON_PIN 7

#define MODE_ABSENSI 1
#define MODE_PENDAFTARAN 2

MFRC522 mfrc522(SS_PIN, RST_PIN);
LiquidCrystal_I2C lcd(0x27, 16, 2);

int currentMode = MODE_ABSENSI;
unsigned long lastButtonPress = 0;
const long debounceDelay = 200;
String textToScroll = "        SISTEM ABSENSI SMK NEGERI 1 PANGKEP        ";
int scrollIndex = 0;
unsigned long lastScrollMillis = 0;
const int scrollDelay = 400;

void setup() {
  Serial.begin(115200);
  SPI.begin();
  mfrc522.PCD_Init();
  
  pinMode(BUTTON_PIN, INPUT_PULLUP);
  pinMode(BUZZER_PIN, OUTPUT);
  
  lcd.init();
  lcd.backlight();
  updateDisplayMode();
}

void loop() {
  checkModeButton();
  if (currentMode == MODE_ABSENSI) {
    if (!mfrc522.PICC_IsNewCardPresent()) {
      updateScrollingText();
    }
  }

  if (mfrc522.PICC_IsNewCardPresent() && mfrc522.PICC_ReadCardSerial()) {
    if (currentMode == MODE_ABSENSI) {
      prosesAbsensi();
    } else {
      prosesPendaftaran();
    }
  }
}

void checkModeButton() {
  if (digitalRead(BUTTON_PIN) == LOW) {
    if (millis() - lastButtonPress > debounceDelay) {
      lastButtonPress = millis();
      currentMode = (currentMode == MODE_ABSENSI) ? MODE_PENDAFTARAN : MODE_ABSENSI;
      updateDisplayMode();
    }
  }
}

void prosesAbsensi() {
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print("Absen Diproses..");
  lcd.setCursor(0, 1);
  lcd.print("Mohon Tunggu...");
  
  String uid = getUIDString();
  Serial.println("ABSEN:" + uid);
  
  handleResponse("Absen Berhasil...", "Absen Gagal...");
}

void prosesPendaftaran() {
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print("Kartu Didaftar..");
  lcd.setCursor(0, 1);
  lcd.print("Mohon Tunggu...");
  
  String uid = getUIDString();
  Serial.println("DAFTAR:" + uid);
  
  handleResponse("Daftar Berhasil...", "Daftar Gagal...");
}

void handleResponse(String successMsg, String failMsg) {
  String response = waitForResponse();
  lcd.clear();
  if (response == "SUCCESS") {
    lcd.setCursor(0, 0);
    lcd.print(successMsg);
    bunyiSukses();
  } else {
    lcd.setCursor(0, 0);
    lcd.print(failMsg);
    bunyiGagal();
  }
  
  delay(1500);
  updateDisplayMode();
  mfrc522.PICC_HaltA();
  mfrc522.PCD_StopCrypto1();
}

String waitForResponse() {
  long startTime = millis();
  String receivedString = "";
  while (millis() - startTime < 15000) {
    if (Serial.available() > 0) {
      char c = Serial.read();
      receivedString += c;
      if (receivedString.indexOf("SUCCESS") != -1) return "SUCCESS";
      if (receivedString.indexOf("FAIL") != -1) return "FAIL";
    }
  }
  return "TIMEOUT";
}

void updateDisplayMode() {
  lcd.clear();
  lcd.setCursor(0, 1);
  lcd.print("Scan Kartu RFID");
  
  if (currentMode == MODE_ABSENSI) {
  } else {
    lcd.setCursor(0, 0);
    lcd.print("MODE DAFTAR RFID");
  }
  
  scrollIndex = 0;
  lastScrollMillis = 0;
}

void updateScrollingText() {
  if (millis() - lastScrollMillis >= scrollDelay) {
    lastScrollMillis = millis();
    String textToShow = textToScroll.substring(scrollIndex, scrollIndex + 16);
    lcd.setCursor(0, 0);
    lcd.print(textToShow);
    scrollIndex++;
    if (scrollIndex > (textToScroll.length() - 16)) {
      scrollIndex = 0;
    }
  }
}

String getUIDString() {
  String uid = "";
  for (byte i = 0; i < mfrc522.uid.size; i++) {
     uid += (mfrc522.uid.uidByte[i] < 0x10 ? "0" : "");
     uid += String(mfrc522.uid.uidByte[i], HEX);
  }
  uid.toUpperCase();
  return uid;
}

void bunyiSukses() {
  tone(BUZZER_PIN, 1000, 200);
}

void bunyiGagal() {
  tone(BUZZER_PIN, 500, 1000);
}