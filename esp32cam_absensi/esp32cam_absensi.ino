#include "esp_camera.h"
#include "WiFi.h"
#include "WiFiUdp.h"
#include "ESPmDNS.h"
#include "soc/soc.h"
#include "soc/rtc_cntl_reg.h"
#include <HTTPClient.h>
#include <SPI.h>
#include <MFRC522.h>
#include <Wire.h>
#include <LiquidCrystal_I2C.h>

#define CAMERA_MODEL_AI_THINKER
#include "camera_pins.h"

#define SS_PIN     13   
#define RST_PIN    255  
#define BUZZER_PIN 4    
#define BUTTON_PIN 1    
#define FLASH_PIN  4    

const char* ssid     = "V2043"; //ssid wifi
const char* password = "qwertyui"; //pass wifi

const char*  host       = "MuhammadNurRahmat.local"; //localhost komputer atau laptop
const int    port       = 80;
const String pathAbsen  = "/app-absensi-rfid-skansap/upload.php";
const String pathDaftar = "/app-absensi-rfid-skansap/simpan_rfid_baru.php";

#define MODE_ABSENSI     1
#define MODE_PENDAFTARAN 2
int currentMode = MODE_ABSENSI;

unsigned long lastButtonPress = 0;
const long    debounceDelay   = 200;

String        textToScroll     = "        SISTEM ABSENSI SMK NEGERI 1 PANGKEP        ";
int           scrollIndex      = 0;
unsigned long lastScrollMillis = 0;
const int     scrollDelay      = 400;

MFRC522           mfrc522(SS_PIN, RST_PIN);
LiquidCrystal_I2C lcd(0x27, 16, 2);

void buzzerTone(int frekuensi, int durasi) {
  ledcAttach(BUZZER_PIN, frekuensi, 8);
  ledcWrite(BUZZER_PIN, 128);
  delay(durasi);
  ledcWrite(BUZZER_PIN, 0);             
  ledcDetach(BUZZER_PIN);
}

void bunyiSukses() {
  buzzerTone(1000, 150);
  delay(100);
  buzzerTone(1000, 150);
}

void bunyiGagal() {
  buzzerTone(400, 800);
}

void flashOff() {
  pinMode(FLASH_PIN, OUTPUT);
  digitalWrite(FLASH_PIN, LOW);
}

void setup() {
  
  WRITE_PERI_REG(RTC_CNTL_BROWN_OUT_REG, 0);

  pinMode(FLASH_PIN, OUTPUT);
  digitalWrite(FLASH_PIN, LOW);
  delay(100);

  Wire.begin(2, 3);
  delay(100);

  lcd.init();
  digitalWrite(FLASH_PIN, LOW); 
  lcd.backlight();
  digitalWrite(FLASH_PIN, LOW);
  lcd.clear();
  digitalWrite(FLASH_PIN, LOW);
  lcd.setCursor(0, 0);
  lcd.print("Inisialisasi...");
  digitalWrite(FLASH_PIN, LOW);
  lcd.setCursor(0, 1);
  lcd.print("Mohon Tunggu...");
  digitalWrite(FLASH_PIN, LOW);

  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print("Init RFID...");
  flashOff();
  SPI.begin(14, 12, 15, 13);
  mfrc522.PCD_Init();
  flashOff();

  pinMode(BUTTON_PIN, INPUT_PULLUP);

  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print("Connecting WiFi.");
  lcd.setCursor(0, 1);
  lcd.print(ssid);
  flashOff();

  WiFi.begin(ssid, password);
  int wifiCoba = 0;
  while (WiFi.status() != WL_CONNECTED && wifiCoba < 40) {
    delay(500);
    wifiCoba++;
    lcd.setCursor(15, 0);
    lcd.print((wifiCoba % 2 == 0) ? "." : " ");
    flashOff();
  }

  lcd.clear();
  if (WiFi.status() == WL_CONNECTED) {
    lcd.setCursor(0, 0);
    lcd.print("WiFi Terhubung!");
    lcd.setCursor(0, 1);
    lcd.print(WiFi.localIP().toString());
    flashOff();
    MDNS.begin("esp32cam");
    bunyiSukses();
    delay(1500);
  } else {
    lcd.setCursor(0, 0);
    lcd.print("WiFi Gagal!");
    lcd.setCursor(0, 1);
    lcd.print("Cek koneksi...");
    flashOff();
    bunyiGagal();
    delay(2000);
  }

  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print("Init Kamera...");
  flashOff();

  camera_config_t config;
  config.ledc_channel = LEDC_CHANNEL_0;
  config.ledc_timer   = LEDC_TIMER_0;
  config.pin_d0       = Y2_GPIO_NUM;
  config.pin_d1       = Y3_GPIO_NUM;
  config.pin_d2       = Y4_GPIO_NUM;
  config.pin_d3       = Y5_GPIO_NUM;
  config.pin_d4       = Y6_GPIO_NUM;
  config.pin_d5       = Y7_GPIO_NUM;
  config.pin_d6       = Y8_GPIO_NUM;
  config.pin_d7       = Y9_GPIO_NUM;
  config.pin_xclk     = XCLK_GPIO_NUM;
  config.pin_pclk     = PCLK_GPIO_NUM;
  config.pin_vsync    = VSYNC_GPIO_NUM;
  config.pin_href     = HREF_GPIO_NUM;
  config.pin_sccb_sda = SIOD_GPIO_NUM;
  config.pin_sccb_scl = SIOC_GPIO_NUM;
  config.pin_pwdn     = PWDN_GPIO_NUM;
  config.pin_reset    = RESET_GPIO_NUM;
  config.xclk_freq_hz = 20000000;
  config.pixel_format = PIXFORMAT_JPEG;
  config.frame_size   = FRAMESIZE_VGA;
  config.jpeg_quality = 12;
  config.fb_count     = 1;

  if (PWDN_GPIO_NUM != -1) {
    pinMode(PWDN_GPIO_NUM, OUTPUT);
    digitalWrite(PWDN_GPIO_NUM, HIGH);
    delay(200);
    digitalWrite(PWDN_GPIO_NUM, LOW);
    delay(200);
  }

  esp_err_t camErr = esp_camera_init(&config);
  if (camErr != ESP_OK) {
    lcd.clear();
    lcd.setCursor(0, 0);
    lcd.print("Kamera GAGAL!");
    lcd.setCursor(0, 1);
    lcd.print("Cek ribbon...");
    flashOff();
    delay(3000);
  } else {
    sensor_t* s = esp_camera_sensor_get();
    if (s) {
      s->set_vflip(s, 1);
      s->set_hmirror(s, 1);
    }
    lcd.clear();
    lcd.setCursor(0, 0);
    lcd.print("Kamera OK!");
    flashOff();
    delay(800);
  }

  updateDisplayMode();
}

void loop() {
  checkModeButton();

  if (currentMode == MODE_ABSENSI) {
    if (!mfrc522.PICC_IsNewCardPresent()) {
      updateScrollingText();
      return;
    }
  } else {
    if (!mfrc522.PICC_IsNewCardPresent()) {
      return;
    }
  }

  if (mfrc522.PICC_ReadCardSerial()) {
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
  flashOff();

  String uid = getUIDString();

  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print("Tersenyum :)    ");
  lcd.setCursor(0, 1);
  lcd.print("Mengambil foto..");
  flashOff();
  delay(800);

  digitalWrite(FLASH_PIN, HIGH);
  delay(200);
  bool berhasil = kirimAbsen(uid);
  digitalWrite(FLASH_PIN, LOW);

  lcd.clear();
  lcd.setCursor(0, 0);
  if (berhasil) {
    lcd.print("Absen Berhasil..");
    lcd.setCursor(0, 1);
    lcd.print("UID: " + uid);
    flashOff();
    bunyiSukses();
  } else {
    lcd.print("Absen Gagal.....");
    lcd.setCursor(0, 1);
    lcd.print("Coba lagi...");
    flashOff();
    bunyiGagal();
  }

  delay(1500);
  mfrc522.PICC_HaltA();
  mfrc522.PCD_StopCrypto1();
  updateDisplayMode();
}

void prosesPendaftaran() {
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print("Kartu Didaftar..");
  lcd.setCursor(0, 1);
  lcd.print("Mohon Tunggu...");
  flashOff();

  String uid      = getUIDString();
  bool   berhasil = kirimDaftar(uid);

  lcd.clear();
  lcd.setCursor(0, 0);
  if (berhasil) {
    lcd.print("Daftar Berhasil.");
    lcd.setCursor(0, 1);
    lcd.print("UID: " + uid);
    flashOff();
    bunyiSukses();
  } else {
    lcd.print("Daftar Gagal....");
    lcd.setCursor(0, 1);
    lcd.print("Coba lagi...");
    flashOff();
    bunyiGagal();
  }

  delay(1500);
  mfrc522.PICC_HaltA();
  mfrc522.PCD_StopCrypto1();
  updateDisplayMode();
}

String resolveHost() {
  IPAddress ip = MDNS.queryHost(host);
  if (ip == IPAddress(0, 0, 0, 0)) {

    return String(host);
  }
  return ip.toString();
}

bool kirimAbsen(String uid) {
  
  String serverIP = resolveHost();

 
  camera_fb_t* fb = esp_camera_fb_get();
  if (!fb) return false;

  WiFiClient client;
  if (!client.connect(serverIP.c_str(), port)) {
    esp_camera_fb_return(fb);
    return false;
  }

  String boundary   = "----WebKitFormBoundary7MA4YWxkTrZu0gW";
  String head       = "--" + boundary + "\r\n"
                      "Content-Disposition: form-data; name=\"uid\"\r\n\r\n"
                      + uid + "\r\n";
  String fileHeader = "--" + boundary + "\r\n"
                      "Content-Disposition: form-data; name=\"imageFile\";"
                      " filename=\"picture.jpg\"\r\n"
                      "Content-Type: image/jpeg\r\n\r\n";
  String tail       = "\r\n--" + boundary + "--\r\n";

  uint32_t totalLen = head.length() + fileHeader.length() + fb->len + tail.length();

  client.println("POST " + pathAbsen + " HTTP/1.1");
  client.println("Host: " + String(host));
  client.println("Content-Length: " + String(totalLen));
  client.println("Content-Type: multipart/form-data; boundary=" + boundary);
  client.println();
  client.print(head);
  client.print(fileHeader);
  client.write(fb->buf, fb->len);
  client.print(tail);

  esp_camera_fb_return(fb);

  unsigned long timeout = millis();
  while (client.available() == 0) {
    if (millis() - timeout > 10000) {
      client.stop();
      return false;
    }
  }

  String response = "";
  while (client.available()) {
    response += client.readString();
  }
  client.stop();

  return (response.indexOf("HTTP/1.1 200 OK") != -1);
}

bool kirimDaftar(String uid) {
  String serverIP = resolveHost();
  HTTPClient http;
  http.begin("http://" + serverIP + pathDaftar);
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");
  int httpCode = http.POST("uid=" + uid);
  http.end();
  return (httpCode == 200);
}

void updateDisplayMode() {
  lcd.clear();
  if (currentMode == MODE_ABSENSI) {
    lcd.setCursor(0, 1);
    lcd.print("Scan Kartu RFID");
  } else {
    lcd.setCursor(0, 0);
    lcd.print("MODE DAFTAR RFID");
    lcd.setCursor(0, 1);
    lcd.print("Scan Kartu RFID");
  }
  flashOff();
  scrollIndex      = 0;
  lastScrollMillis = 0;
}

void updateScrollingText() {
  if (millis() - lastScrollMillis >= scrollDelay) {
    lastScrollMillis = millis();
    String visible = textToScroll.substring(scrollIndex, scrollIndex + 16);
    lcd.setCursor(0, 0);
    lcd.print(visible);
    flashOff();
    scrollIndex++;
    if (scrollIndex > (int)(textToScroll.length() - 16)) {
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