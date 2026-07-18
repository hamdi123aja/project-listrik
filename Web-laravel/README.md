# Monitoring Listrik Rumah Berbasis IoT

Project ini menerima data sensor listrik dari `ESP32 + PZEM-004T`, menyimpannya ke database Laravel, lalu menampilkannya di dashboard web.

## Fitur

- API `POST /api/sensor-readings` untuk menerima data dari ESP32
- API `GET /api/sensor-readings/latest` untuk mengambil data terbaru
- Dashboard web pada `/` untuk melihat data sensor terbaru dan riwayat singkat
- Database default menggunakan `SQLite` agar langsung bisa dijalankan

## Struktur Data yang Dikirim ESP32

```json
{
  "device_id": "esp32-pzem-rumah",
  "voltage": 220.4,
  "current": 0.532,
  "power": 117.2,
  "energy": 1.254,
  "frequency": 50.0,
  "power_factor": 0.98,
  "status": "normal"
}
```

## Cara Menjalankan Laravel

```bash
cd Web-laravel
php artisan migrate
composer run dev
```

Lalu buka:

`http://127.0.0.1:8000`

Kalau ESP32 harus kirim data ke Laravel, server wajib berjalan di jaringan LAN. Script `composer run dev` sudah bind ke `0.0.0.0`, jadi bisa diakses dari IP komputer seperti `http://192.168.x.x:8000`.

## Konfigurasi ESP32

File firmware ada di:

`Kode Arduino/kode.ino`

Yang perlu diubah:

- `WIFI_SSID`
- `WIFI_PASSWORD`
- `SERVER_URL`
- `SENSOR_API_KEY` jika ingin mengamankan API

Contoh `SERVER_URL`:

`http://192.168.1.10:8000/api/sensor-readings`

Gunakan IP komputer yang menjalankan Laravel, bukan `localhost`, karena ESP32 mengakses lewat jaringan WiFi.

## Library Arduino

Install library berikut di Arduino IDE:

- `PZEM004Tv30`
- `WiFi` dan `HTTPClient` sudah tersedia pada board package ESP32

## Uji API Manual

```bash
curl -X POST http://127.0.0.1:8000/api/sensor-readings ^
  -H "Content-Type: application/json" ^
  -d "{\"device_id\":\"esp32-test\",\"voltage\":220.4,\"current\":0.53,\"power\":116.8,\"energy\":1.24,\"frequency\":50.0,\"power_factor\":0.98,\"status\":\"normal\"}"
```

Setelah request sukses, refresh dashboard untuk melihat data masuk.
