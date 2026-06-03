# SiAPP — Panduan Instalasi

**SiAPP** (Sistem Informasi Absensi Pintar dan Pembiasaan) adalah sistem presensi siswa berbasis RFID-IoT yang dibangun di atas Laravel 11. Dokumen ini menjelaskan cara menjalankan SiAPP di server atau PC baru menggunakan Docker.

---

## Prasyarat

Sebelum memulai, pastikan perangkat memenuhi kebutuhan berikut:

| Kebutuhan | Minimum |
|---|---|
| Sistem Operasi | Ubuntu 20.04+ / Windows 10+ (dengan WSL2) |
| RAM | 4 GB |
| Ruang Disk | 10 GB |
| Koneksi Internet | Diperlukan saat instalasi pertama |

### Perangkat lunak yang harus sudah terinstall

- **Docker** versi 24 ke atas
- **Docker Compose** versi v2 ke atas

Panduan instalasi Docker:
- Linux (Ubuntu): https://docs.docker.com/engine/install/ubuntu/
- Windows: https://docs.docker.com/desktop/install/windows-install/

Verifikasi instalasi:
```bash
docker --version
docker compose version
```

---

## Langkah Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/butoCakil/SiAPP_SERVER_ITC.git
cd SiAPP_SERVER_ITC/siapp-v2
```

### 2. Buat File Konfigurasi

Salin file contoh konfigurasi:

```bash
cp .env.example.docker .env.docker
```

Buka `.env.docker` dengan teks editor, lalu sesuaikan nilai berikut:

```env
# Nama aplikasi
APP_NAME=SiAPP

# Kunci enkripsi — WAJIB diisi, generate dengan perintah di bawah
APP_KEY=

# Port akses aplikasi di browser (default: 8080)
APP_PORT=8080

# Kata sandi database
DB_PASSWORD=ganti_dengan_password_anda
DB_ROOT_PASSWORD=ganti_dengan_root_password_anda

# Kredensial MQTT broker
MQTT_USERNAME=ben
MQTT_PASSWORD=1234
```

Generate `APP_KEY`:
```bash
# Linux
cat /dev/urandom | base64 | head -c 32 | xargs -I{} echo "base64:{}"

# Atau jika sudah ada PHP:
php -r "echo 'base64:' . base64_encode(random_bytes(32)) . PHP_EOL;"
```

### 3. Buat File Password MQTT

```bash
docker run --rm -v $(pwd)/docker/mqtt:/output \
  eclipse-mosquitto:2 \
  mosquitto_passwd -c -b /output/passwd ben 1234
```

> Ganti `ben` dan `1234` sesuai dengan nilai `MQTT_USERNAME` dan `MQTT_PASSWORD` di `.env.docker`.

### 4. Jalankan SiAPP

```bash
docker compose up -d
```

Perintah ini akan:
- Mengunduh image Docker yang diperlukan (pertama kali butuh beberapa menit)
- Membangun container aplikasi
- Menjalankan migrasi database otomatis
- Menjalankan semua service (web, database, MQTT broker)

### 5. Verifikasi

Cek status container:
```bash
docker compose ps
```

Semua container harus berstatus `Up`:
```
NAME         STATUS
siapp_app    Up
siapp_db     Up (healthy)
siapp_mqtt   Up
```

Buka browser dan akses:
```
http://localhost:8080
```

---

## Akses Aplikasi

| URL | Keterangan |
|---|---|
| `http://localhost:8080` | Halaman utama SiAPP |
| `http://localhost:8080/login` | Halaman login admin |

Login menggunakan akun administrator default yang sudah dikonfigurasi saat instalasi.

---

## Port yang Digunakan

| Port | Service | Keterangan |
|---|---|---|
| 8080 | Aplikasi Web | Dapat diubah via `APP_PORT` di `.env.docker` |
| 1883 | MQTT Broker | Digunakan device ESP32/ESP8266 |
| 3306 | MariaDB | Hanya internal container, tidak terbuka ke luar |

---

## Perintah Umum

```bash
# Menjalankan SiAPP
docker compose up -d

# Menghentikan SiAPP
docker compose down

# Melihat log aplikasi
docker logs siapp_app

# Melihat log semua container
docker compose logs -f

# Restart satu container
docker compose restart app

# Update ke versi terbaru
git pull
docker compose down
docker compose build
docker compose up -d
```

---

## Konfigurasi Device (ESP32/ESP8266)

Device RFID perlu dikonfigurasi agar terhubung ke server SiAPP:

| Parameter | Nilai |
|---|---|
| MQTT Host | IP address server SiAPP |
| MQTT Port | 1883 |
| MQTT Username | Sesuai `MQTT_USERNAME` di `.env.docker` |
| MQTT Password | Sesuai `MQTT_PASSWORD` di `.env.docker` |
| Upload URL | `http://<IP_SERVER>:8080/api/upload/...` |

---

## Troubleshooting

### Container tidak mau start
```bash
docker compose logs siapp_app
```

### Port sudah dipakai
Ubah `APP_PORT` di `.env.docker` ke port lain, misalnya `9080`.

### Database error
```bash
docker compose down -v  # hapus data lama
docker compose up -d    # mulai ulang
```

> **Peringatan:** `down -v` akan menghapus semua data database.

---

## Struktur File Docker

```
siapp-v2/
├── Dockerfile                  # Resep build container aplikasi
├── docker-compose.yml          # Konfigurasi semua service
├── .env.docker                 # Konfigurasi lokal (tidak masuk repo)
├── .env.example.docker         # Template konfigurasi
├── docker/
│   ├── app/                    # Config Apache & Supervisor
│   ├── db/
│   │   └── init.sql            # Skema database awal
│   └── mqtt/
│       ├── mosquitto.conf      # Konfigurasi MQTT broker
│       └── passwd              # File password MQTT (tidak masuk repo)
└── docker-config/
    └── app/
        ├── apache.conf         # Virtual host Apache
        ├── supervisord.conf    # Konfigurasi proses background
        └── entrypoint.sh       # Script startup container
```

---

## Informasi Sistem

- **Framework**: Laravel 12
- **PHP**: 8.2
- **Database**: MariaDB 10.4
- **MQTT Broker**: Eclipse Mosquitto 2
- **Web Server**: Apache 2.4
- **Dikembangkan oleh**: SMK Negeri Bansari, Temanggung