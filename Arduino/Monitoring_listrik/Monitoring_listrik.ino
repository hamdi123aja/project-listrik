/*
    ESP32 + PZEM-004T V4 + LCD I2C + Laravel API
    Laravel server harus bisa diakses dari ESP32, misalnya:
    php artisan serve --host=0.0.0.0 --port=8000

    Monitoring:
    - Voltage
    - Current
    - Power
    - Energy
    - Frequency
    - Power Factor
  */

  #include <WiFi.h>
  #include <HTTPClient.h>
  #include <PZEM004Tv30.h>

  #include <Wire.h>
  #include <LiquidCrystal_I2C.h>

  // =========================
  // LCD I2C
  // =========================
  LiquidCrystal_I2C lcd(0x27, 16, 2);

  // =========================
  // CONFIG BUZZER
  // =========================
  constexpr uint8_t BUZZER_PIN = 25;
  bool wasWarning = false;


  // =========================
  // WIFI DAN API
  // =========================
  const char* WIFI_SSID = "H";
  const char* WIFI_PASSWORD = "87654321";
  // Ganti ke IP laptop/PC yang menjalankan Laravel di jaringan yang sama.
  // Laravel harus dijalankan dengan host 0.0.0.0 agar ESP32 bisa mengaksesnya.
  const char* API_HOST = "52.20.146.63";
  const uint16_t API_PORT = 80;
  const char* API_PATH = "/api/pzem/readings";
  const char* API_TOKEN = "";

  // =========================
  // PIN PZEM DI ESP32
  // =========================
  // ESP32 RX2(GPIO16) <- TX PZEM
  // ESP32 TX2(GPIO17) -> RX PZEM
  constexpr uint8_t PZEM_RX_PIN = 16;
  constexpr uint8_t PZEM_TX_PIN = 17;

  HardwareSerial pzemSerial(2);
  PZEM004Tv30 pzem(pzemSerial, PZEM_RX_PIN, PZEM_TX_PIN);

  const unsigned long SEND_INTERVAL_MS = 5000;
  const unsigned long WIFI_RETRY_INTERVAL_MS = 10000;

  unsigned long lastSendMs = 0;
  unsigned long lastWiFiRetryMs = 0;

  // =========================
  // CONNECT WIFI
  // =========================
  void connectWiFi() {

    if (WiFi.status() == WL_CONNECTED) {
      return;
    }

    Serial.print("Menghubungkan WiFi ke ");
    Serial.println(WIFI_SSID);

    lcd.clear();
    lcd.setCursor(0, 0);
    lcd.print("Connecting...");
    lcd.setCursor(0, 1);
    lcd.print("WiFi");

    WiFi.disconnect(true);
    delay(1000);

    WiFi.mode(WIFI_STA);
    WiFi.begin(WIFI_SSID, WIFI_PASSWORD);

    unsigned long startMs = millis();

    while (WiFi.status() != WL_CONNECTED &&
          millis() - startMs < 10000) {

      delay(500);
      Serial.print(".");
    }

    Serial.println();

    if (WiFi.status() == WL_CONNECTED) {

      Serial.println("WiFi terhubung!");
      Serial.print("IP Address : ");
      Serial.println(WiFi.localIP());

      lcd.clear();
      lcd.setCursor(0, 0);
      lcd.print("WiFi Connected");
      delay(1500);

    } else {

      Serial.println("WiFi belum terhubung.");

      lcd.clear();
      lcd.setCursor(0, 0);
      lcd.print("WiFi Failed");
    }
  }

  // =========================
  // KIRIM DATA KE API
  // =========================

  bool sendToApi(float voltage,
                float current,
                float power,
                float energy,
                float frequency,
                float pf) {

    if (WiFi.status() != WL_CONNECTED) {
      Serial.println("Lewati kirim API: WiFi belum terhubung.");
      return false;
    }

    bool telegramSent = false;
    HTTPClient http;

    String apiUrl = String("http://") + API_HOST + ":" + String(API_PORT) + API_PATH;

    http.begin(apiUrl);
    http.addHeader("Content-Type", "application/json");

    if (strlen(API_TOKEN) > 0) {
      http.addHeader(
        "Authorization",
        String("Bearer ") + API_TOKEN
      );
    }

    String payload = "{";
    payload += "\"voltage\":" + String(voltage, 2) + ",";
    payload += "\"current\":" + String(current, 3) + ",";
    payload += "\"power\":" + String(power, 2) + ",";
    payload += "\"energy\":" + String(energy, 3) + ",";
    payload += "\"frequency\":" + String(frequency, 1) + ",";
    payload += "\"power_factor\":" + String(pf, 2);
    payload += "}";

    int httpCode = http.POST(payload);

    Serial.print("HTTP Code : ");
    Serial.println(httpCode);

    if (httpCode > 0) {

      String response = http.getString();

      Serial.print("Response  : ");
      Serial.println(response);

      if (response.indexOf("\"telegram_sent\":true") != -1) {
        telegramSent = true;
      }

    } else {

      Serial.print("HTTP Error: ");
      Serial.println(http.errorToString(httpCode));
      Serial.print("API URL    : ");
      Serial.println(apiUrl);
    }

    http.end();
    return telegramSent;
  }

  // =========================
  // SETUP
  // =========================
  void setup() {

    Serial.begin(115200);

    // Setup Buzzer
    pinMode(BUZZER_PIN, OUTPUT);
    digitalWrite(BUZZER_PIN, LOW);

    // LCD
    Wire.begin(21, 22);

    lcd.init();
    lcd.backlight();

    lcd.setCursor(0, 0);
    lcd.print("Monitoring");

    lcd.setCursor(0, 1);
    lcd.print("Listrik IoT");

    delay(2000);
    lcd.clear();

    // PZEM
    pzemSerial.begin(
      9600,
      SERIAL_8N1,
      PZEM_RX_PIN,
      PZEM_TX_PIN
    );

    Serial.println();
    Serial.println("==================================");
    Serial.println("PZEM-004T V4 Monitoring");
    Serial.println("ESP32 + LCD + Laravel");
    Serial.println("==================================");
    Serial.print("API target : http://");
    Serial.print(API_HOST);
    Serial.print(":");
    Serial.print(API_PORT);
    Serial.println(API_PATH);

    WiFi.mode(WIFI_STA);
    WiFi.setAutoReconnect(true);
    WiFi.persistent(true);

    connectWiFi();
  }

  // =========================
  // LOOP
  // =========================
  void loop() {

    // Reconnect WiFi setiap 10 detik jika putus
    if (WiFi.status() != WL_CONNECTED &&
        millis() - lastWiFiRetryMs >= WIFI_RETRY_INTERVAL_MS) {

      lastWiFiRetryMs = millis();
      connectWiFi();
    }

    // Kirim data setiap 5 detik
    if (millis() - lastSendMs < SEND_INTERVAL_MS) {
      return;
    }

    lastSendMs = millis();

    float voltage = pzem.voltage();
    float current = pzem.current();
    float power = pzem.power();
    float energy = pzem.energy();
    float frequency = pzem.frequency();
    float pf = pzem.pf();

    if (isnan(voltage)) {

      Serial.println("ERROR: PZEM tidak terdeteksi!");

      lcd.clear();
      lcd.setCursor(0, 0);
      lcd.print("PZEM ERROR");

      lcd.setCursor(0, 1);
      lcd.print("Check Wiring");

      return;
    }

    Serial.println();
    Serial.println("========= DATA LISTRIK =========");

    Serial.printf("Tegangan    : %.2f V\n", voltage);
    Serial.printf("Arus        : %.3f A\n", current);
    Serial.printf("Daya        : %.2f W\n", power);
    Serial.printf("Energi      : %.3f kWh\n", energy);
    Serial.printf("Frekuensi   : %.1f Hz\n", frequency);
    Serial.printf("PowerFactor : %.2f\n", pf);

    Serial.println("===============================");

    // =====================
    // TAMPILKAN KE LCD
    // =====================
    lcd.clear();

  lcd.setCursor(0,0);
  lcd.print("A:");
  lcd.print(current,3);

  lcd.setCursor(0,1);
  lcd.print("W:");
  lcd.print(power,1);

  Serial.println("LCD UPDATED");

    // =====================
    // KIRIM API & ALARM BUZZER
    // =====================
    bool telegramSent = sendToApi(
      voltage,
      current,
      power,
      energy,
      frequency,
      pf
    );

    if (telegramSent) {
      Serial.println("TELEGRAM SENT! Mengaktifkan buzzer 1 detik...");
      digitalWrite(BUZZER_PIN, HIGH);
      delay(1000);
      digitalWrite(BUZZER_PIN, LOW);
    }
  }
