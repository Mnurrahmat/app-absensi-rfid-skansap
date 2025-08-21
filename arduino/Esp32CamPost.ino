#include "esp_camera.h"
#include "WiFi.h"
#include "soc/soc.h"
#include "soc/rtc_cntl_reg.h"
#include <HTTPClient.h>

//#define FLASH_GPIO_PIN 4

const char* ssid = "A3";
const char* password = "Ladhago11";
const char* host = "192.168.1.5";
const int port = 80;
const String pathAbsen = "/app-absensi-rfid-skansap/upload.php";
const String pathDaftar = "/app-absensi-rfid-skansap/simpan_rfid_baru.php";

#define CAMERA_MODEL_AI_THINKER
#include "camera_pins.h"

void setup() {
  WRITE_PERI_REG(RTC_CNTL_BROWN_OUT_REG, 0); 
  pinMode(FLASH_GPIO_PIN, OUTPUT);
  digitalWrite(FLASH_GPIO_PIN, LOW);

  Serial.begin(115200);
  Serial.setDebugOutput(true);
  Serial.println();
  
  camera_config_t config;
  config.ledc_channel = LEDC_CHANNEL_0;
  config.ledc_timer = LEDC_TIMER_0;
  config.pin_d0 = Y2_GPIO_NUM;
  config.pin_d1 = Y3_GPIO_NUM;
  config.pin_d2 = Y4_GPIO_NUM;
  config.pin_d3 = Y5_GPIO_NUM;
  config.pin_d4 = Y6_GPIO_NUM;
  config.pin_d5 = Y7_GPIO_NUM;
  config.pin_d6 = Y8_GPIO_NUM;
  config.pin_d7 = Y9_GPIO_NUM;
  config.pin_xclk = XCLK_GPIO_NUM;
  config.pin_pclk = PCLK_GPIO_NUM;
  config.pin_vsync = VSYNC_GPIO_NUM;
  config.pin_href = HREF_GPIO_NUM;
  config.pin_sccb_sda = SIOD_GPIO_NUM;
  config.pin_sccb_scl = SIOC_GPIO_NUM;
  config.pin_pwdn = PWDN_GPIO_NUM;
  config.pin_reset = RESET_GPIO_NUM;
  config.xclk_freq_hz = 20000000;
  config.pixel_format = PIXFORMAT_JPEG;
  config.frame_size = FRAMESIZE_VGA;
  config.jpeg_quality = 12;
  config.fb_count = 1;

  esp_err_t err = esp_camera_init(&config);
  if (err != ESP_OK) {
    Serial.println("FAIL: Camera Init");
    return;
  }

  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("\nTerhubung ke WiFi!");
  Serial.println("ESP32-CAM siap menerima perintah...");
}

void loop() {
  if (Serial.available() > 0) {
    String command = Serial.readStringUntil('\n');
    command.trim();
    
    // Memproses perintah dari Arduino
    if (command.startsWith("ABSEN:")) {
      String uid = command.substring(6);
      prosesAbsen(uid);
    } else if (command.startsWith("DAFTAR:")) {
      String uid = command.substring(7);
      prosesDaftar(uid);
    }
  }
}

void prosesAbsen(String uid) {
  digitalWrite(FLASH_GPIO_PIN, HIGH);
  delay(500); 
  camera_fb_t * fb = esp_camera_fb_get();
  digitalWrite(FLASH_GPIO_PIN, LOW);
  
  if(!fb) {
    Serial.println("FAIL");
    return;
  }
  
  String serverResponse = sendDataAbsen(uid, fb->buf, fb->len);
  
  if (serverResponse.indexOf("HTTP/1.1 200 OK") != -1) {
    Serial.println("SUCCESS");
  } else {
    Serial.println("FAIL");
    Serial.println("Server Response: " + serverResponse);
  }
  
  esp_camera_fb_return(fb);
}

void prosesDaftar(String uid) {
  HTTPClient http;
  String serverPath = "http://" + String(host) + pathDaftar;
  http.begin(serverPath);
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");
  String httpRequestData = "uid=" + uid;
  int httpResponseCode = http.POST(httpRequestData);

  if (httpResponseCode == 200) {
    Serial.println("SUCCESS");
  } else {
    Serial.println("FAIL");
  }
  http.end();
}

String sendDataAbsen(String uid, const uint8_t * imageData, size_t imageSize) {
  WiFiClient client;
  String response = "";
  
  if (!client.connect(host, port)) {
    return "FAIL: Connection";
  }

  String boundary = "----WebKitFormBoundary7MA4YWxkTrZu0gW";
  String head = "--" + boundary + "\r\n" + "Content-Disposition: form-data; name=\"uid\"\r\n\r\n" + uid + "\r\n";
  String tail = "\r\n--" + boundary + "--\r\n";
  String file_header = "--" + boundary + "\r\n" + "Content-Disposition: form-data; name=\"imageFile\"; filename=\"picture.jpg\"\r\n" + "Content-Type: image/jpeg\r\n\r\n";
  
  uint32_t contentLength = head.length() + file_header.length() + imageSize + tail.length();

  client.println("POST " + pathAbsen + " HTTP/1.1");
  client.println("Host: " + String(host));
  client.println("Content-Length: " + String(contentLength));
  client.println("Content-Type: multipart/form-data; boundary=" + boundary);
  client.println();
  client.print(head);
  client.print(file_header);
  client.write(imageData, imageSize);
  client.print(tail);
  
  long timeout = millis();
  while (client.available() == 0) {
    if (millis() - timeout > 10000) {
      client.stop();
      return "FAIL: Timeout";
    }
  }
  
  while(client.available()){
    response += client.readString();
  }
  
  client.stop();
  return response;
}