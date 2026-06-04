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

### 2. Sesuaikan Konfigurasi (Opsional)

Secara default SiAPP berjalan di port `8080` (web) dan `1883` (MQTT). Jika port tersebut sudah dipakai, salin file konfigurasi dan sesuaikan:

```bash
cp .env.example.docker .env
```

Buka `.env` dan ubah nilai yang diperlukan:

```env
# Port akses aplikasi di browser (default: 8080)
APP_PORT=8080

# Kredensial MQTT broker
MQTT_USERNAME=ben
MQTT_PASSWORD=1234

# Kata sandi database
DB_PASSWORD=ganti_dengan_password_anda
DB_ROOT_PASSWORD=ganti_dengan_root_password_anda
```

> Jika tidak ada konflik port, langkah ini bisa dilewati — `setup.sh` akan membuat file `.env` otomatis dari template.

### 3. Jalankan Setup Otomatis

```bash
./setup.sh
```

Script ini akan otomatis:
- Membuat file `.env` dari template (jika belum ada)
- Generate `APP_KEY` secara acak
- Membuat file password MQTT
- Menjalankan semua container Docker

Jika berhasil, output akhir akan menampilkan:
```
======================================
  SiAPP siap diakses!
  URL: http://localhost:8080
======================================
```

### 4. Verifikasi

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
| 8080 | Aplikasi Web | Dapat diubah via `APP_PORT` di `.env` |
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
| MQTT Host | IP address server SiAPP di jaringan lokal |
| MQTT Port | 1883 |
| MQTT Username | Sesuai `MQTT_USERNAME` di `.env` |
| MQTT Password | Sesuai `MQTT_PASSWORD` di `.env` |
| Upload URL | `http://<IP_SERVER>:8080/data/uploadPresensi.php` |

---

## Troubleshooting

### Port sudah dipakai
Jika `setup.sh` menampilkan peringatan port, buka file `.env` dan ubah port yang konflik:

```env
# Jika port 8080 sudah dipakai
APP_PORT=9080

# Jika port 1883 sudah dipakai
MQTT_PORT=1884
```

Kemudian jalankan ulang:
```bash
docker compose up -d
```

### Container tidak mau start
```bash
docker compose logs siapp_app
```

### Database error saat pertama kali
```bash
docker compose down -v  # hapus data lama
docker compose up -d    # mulai ulang
```

> **Peringatan:** `down -v` akan menghapus semua data database. Gunakan hanya saat setup awal.

### Device tidak bisa konek MQTT
- Pastikan IP server benar dan bisa dijangkau dari jaringan device
- Pastikan port 1883 tidak diblokir firewall
- Cek username/password MQTT sesuai dengan `.env`

---

## Struktur File Docker

```
siapp-v2/
├── setup.sh                    # Script setup otomatis (jalankan ini pertama kali)
├── Dockerfile                  # Resep build container aplikasi
├── docker-compose.yml          # Konfigurasi semua service
├── .env                        # Konfigurasi lokal (tidak masuk repo)
├── .env.example.docker         # Template konfigurasi
├── docker/
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