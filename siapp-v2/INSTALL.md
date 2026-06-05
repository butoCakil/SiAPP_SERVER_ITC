# SiAPP — Panduan Instalasi

**SiAPP** (Sistem Informasi Absensi Pintar dan Pembiasaan) adalah sistem presensi siswa berbasis RFID-IoT yang dibangun di atas Laravel 12. Dokumen ini menjelaskan cara menjalankan SiAPP di server atau PC baru menggunakan Docker.

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

### 2. Jalankan Setup Otomatis

```bash
chmod +x setup.sh
./setup.sh
```

Script `setup.sh` akan:
- Menyalin `.env.example.docker` ke `.env.docker`
- Generate `APP_KEY` otomatis
- Membuat file password MQTT
- Membangun dan menjalankan semua container Docker
- Menunggu database siap lalu menjalankan migrasi

Setelah selesai, akses aplikasi di:
```
http://localhost:8080
```

---

## Instalasi Manual (Alternatif)

Jika ingin konfigurasi manual:

### 2a. Buat File Konfigurasi

```bash
cp .env.example.docker .env.docker
```

Buka `.env.docker` dan sesuaikan:

```env
APP_NAME=SiAPP
APP_KEY=                          # Wajib diisi (lihat di bawah)
APP_PORT=8080                     # Port akses di browser
DB_PASSWORD=ganti_password_anda
DB_ROOT_PASSWORD=ganti_root_password
MQTT_USERNAME=ben
MQTT_PASSWORD=1234
```

Generate `APP_KEY`:
```bash
# Linux/Mac
php -r "echo 'base64:' . base64_encode(random_bytes(32)) . PHP_EOL;"

# Atau tanpa PHP
cat /dev/urandom | base64 | head -c 32 | xargs -I{} echo "base64:{}"
```

### 2b. Buat File Password MQTT

```bash
docker run --rm -v $(pwd)/docker/mqtt:/output \
  eclipse-mosquitto:2 \
  mosquitto_passwd -c -b /output/passwd ben 1234
```

> Ganti `ben` dan `1234` sesuai nilai `MQTT_USERNAME` dan `MQTT_PASSWORD` di `.env.docker`.

### 2c. Jalankan SiAPP

```bash
docker compose up -d
```

---

## Verifikasi

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

Login default:
- **Username:** `Pengembang`
- **Password:** (sesuai yang dikonfigurasi di `init.sql`)

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
| Upload URL | `http://<IP_SERVER>:8080/api/tag` |
| DB URL | `http://<IP_SERVER>:8080/api/db` |

Konfigurasi device dapat dilakukan langsung dari dashboard SiAPP via menu **Device** → pilih device → **Set WiFi / Set URL**.

---

## Troubleshooting

### Container tidak mau start
```bash
docker compose logs siapp_app
```

### Port sudah dipakai
Ubah `APP_PORT` di `.env.docker` ke port lain, misalnya `9080`.

### Database error saat pertama kali
```bash
docker compose down -v  # hapus data lama
docker compose up -d    # mulai ulang
```

> **Peringatan:** `down -v` akan menghapus semua data database.

### MQTT device tidak terhubung
Pastikan port 1883 tidak diblokir firewall. Cek log MQTT:
```bash
docker logs siapp_mqtt
```

---

## Struktur File Docker

```
siapp-v2/
├── Dockerfile                  # Resep build container aplikasi
├── docker-compose.yml          # Konfigurasi semua service
├── setup.sh                    # Script setup otomatis
├── .env.docker                 # Konfigurasi lokal (tidak masuk repo)
├── .env.example.docker         # Template konfigurasi
├── docker/
│   ├── db/
│   │   └── init.sql            # Skema database + seed data awal
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