# SiAPP — Panduan Instalasi

**SiAPP** (Sistem Informasi Absensi Pintar dan Pembiasaan) adalah sistem presensi siswa berbasis RFID-IoT yang dibangun di atas Laravel 12. Dokumen ini menjelaskan cara menjalankan SiAPP di server atau PC baru menggunakan Docker.

---

## Prasyarat

Sebelum memulai, pastikan perangkat memenuhi kebutuhan berikut:

| Kebutuhan | Minimum |
|---|---|
| Sistem Operasi | Ubuntu 20.04+ (direkomendasikan 24.04) |
| RAM | 4 GB |
| Ruang Disk | 10 GB |
| Koneksi Internet | Diperlukan saat instalasi pertama |

### Perangkat lunak yang harus sudah terinstall

- **Docker** versi 24 ke atas
- **Docker Compose** versi v2 ke atas

Panduan instalasi Docker:
```bash
curl -fsSL https://get.docker.com | sudo sh
sudo usermod -aG docker $USER
```
Setelah menjalankan perintah di atas, **logout dan login kembali** agar perubahan grup berlaku.

Verifikasi instalasi:
```bash
docker --version
docker compose version
```

> **Catatan Windows:** instalasi via `setup.sh` memerlukan bash (Git Bash atau WSL) dan Docker Desktop dengan WSL2/Hyper-V backend. Pastikan virtualization (VT-x/AMD-V) aktif di BIOS/hypervisor — beberapa VPS dengan tingkatan "Basic" tidak mengekspos nested virtualization sehingga Docker Desktop tidak dapat berjalan.

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
1. Menyalin `.env.example.docker` ke `.env.docker`
2. Generate `APP_KEY` otomatis
3. Membuat file password MQTT
4. **Meminta konfigurasi akun Admin (Super Admin)** — username, email, dan password
5. Membangun dan menjalankan semua container Docker, menunggu database siap, lalu mengatur akun admin sesuai yang diisi

Saat diminta konfigurasi akun admin, Anda bisa:
- **Mengisi username, email, dan password sendiri**, atau
- **Mengosongkan semua (tekan Enter)** — username default `Pengembang`, email default `admin@siapp.local`, dan password akan **dibuat otomatis secara acak**

> **PENTING:** jika password dibuat otomatis, script akan menampilkannya di akhir proses instalasi. **Catat dan simpan password tersebut**, karena tidak ditampilkan lagi setelahnya.

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

Generate `APP_KEY` (harus menghasilkan 32 byte / 44 karakter base64):
```bash
# Cara 1: menggunakan PHP
php -r "echo 'base64:' . base64_encode(random_bytes(32)) . PHP_EOL;"

# Cara 2: tanpa PHP (32 byte acak, lalu di-encode base64)
echo "base64:$(head -c 32 /dev/urandom | base64 | tr -d '\n')"
```

> **Perhatian:** pastikan urutan operasinya benar — ambil **32 byte acak terlebih dahulu**, baru di-encode base64. Jika urutan dibalik (encode dulu, baru dipotong 32 karakter), hasilnya hanya 24 byte dan akan menyebabkan error `Unsupported cipher or incorrect key length` saat aplikasi berjalan.

### 2b. Buat File Password MQTT

```bash
docker run --rm -v $(pwd)/docker/mqtt:/output \
  eclipse-mosquitto:2 \
  mosquitto_passwd -c -b /output/passwd ben 1234
chmod 644 docker/mqtt/passwd
```

> Ganti `ben` dan `1234` sesuai nilai `MQTT_USERNAME` dan `MQTT_PASSWORD` di `.env.docker`.

### 2c. Jalankan SiAPP

```bash
docker compose up -d
```

### 2d. Atur Akun Admin (Super Admin)

Tunggu hingga container `siapp_db` berstatus `healthy`, lalu jalankan:

```bash
docker compose exec -T db mariadb -u root -p"$(grep '^DB_ROOT_PASSWORD=' .env.docker | cut -d= -f2)" siap -e \
  "UPDATE admin SET username='NamaAnda', email='email@anda.com', password=MD5('password_anda') WHERE id=1;"
```

Ganti `NamaAnda`, `email@anda.com`, dan `password_anda` sesuai keinginan.

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

Login dengan akun Admin (Super Admin) yang dikonfigurasi saat instalasi (lihat bagian **Langkah Instalasi** di atas). Jika lupa, password dapat direset lewat database:

```bash
docker compose exec -T db mariadb -u root -p"$(grep '^DB_ROOT_PASSWORD=' .env.docker | cut -d= -f2)" siap -e \
  "UPDATE admin SET password=MD5('password_baru') WHERE username='Pengembang';"
```

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

# Menghentikan dan menghapus seluruh data (database, storage, MQTT)
docker compose down -v

# Melihat log aplikasi
docker logs siapp_app

# Melihat log semua container
docker compose logs -f

# Restart satu container
docker compose restart app

# Update ke versi terbaru
git pull
docker compose down
docker compose up -d --build
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

## Script Verifikasi Cepat

Setelah instalasi selesai, jalankan perintah-perintah berikut untuk memastikan semuanya berjalan normal.

### 1. Status container dan migration

```bash
docker compose ps
docker exec siapp_app php /var/www/siapp/artisan migrate:status
```

Semua migration harus berstatus `Ran`. Semua container harus `Up` (siapp_db harus `healthy`).

### 2. Ekstensi PHP yang dibutuhkan

```bash
docker exec siapp_app php -m | grep -iE 'calendar|pdo_mysql|gd|zip|mbstring'
```

Kelima ekstensi harus muncul (terutama `calendar`, dibutuhkan oleh halaman Kaldik).

### 3. Cek halaman utama (harus semua 200)

```bash
# Login dulu untuk mendapatkan session
CSRF=$(curl -s -c /tmp/cookies.txt http://127.0.0.1:8080/login | grep -oP 'name="_token" value="\K[^"]+')
curl -s -b /tmp/cookies.txt -c /tmp/cookies.txt -X POST http://127.0.0.1:8080/login \
  -d "_token=${CSRF}&username=USERNAME_ADMIN&password=PASSWORD_ADMIN" \
  -o /dev/null -w "login: %{http_code}\n"

# Cek semua halaman utama
for route in dashboard device device/cards device/registrasi device-ota \
  kaldik log log/sidebar presensi presensi/create presensi/event presensi/ijin \
  presensi/rekap presensi/rekap/semester setting siswa siswa/create siswa/tmprfid \
  akun home; do
  code=$(curl -s -b /tmp/cookies.txt -o /dev/null -w "%{http_code}" "http://127.0.0.1:8080/${route}")
  echo "${route}: ${code}"
done
```

Ganti `USERNAME_ADMIN` dan `PASSWORD_ADMIN` dengan kredensial admin yang dikonfigurasi saat instalasi. Semua baris harus menunjukkan `200`.

### 4. Cek log error terbaru

```bash
docker exec siapp_app sh -c "ls -t /var/www/siapp/storage/logs/*.log 2>/dev/null | head -1 | xargs tail -50"
```

Jika file tidak ditemukan ("No such file or directory"), berarti belum ada error tercatat — kondisi normal untuk instalasi baru. Jika ada error, cari baris berisi `production.ERROR` dan `Exception` untuk detail.

---

## Troubleshooting

### Container tidak mau start
```bash
docker compose logs siapp_app
```

### Port sudah dipakai
Ubah `APP_PORT` (atau `MQTT_PORT`) di `.env.docker` ke port lain, misalnya `9080` / `1884`, lalu jalankan ulang:
```bash
docker compose up -d
```

### Database error saat pertama kali
```bash
docker compose down -v  # hapus data lama
./setup.sh               # mulai ulang dari awal
```

> **Peringatan:** `down -v` akan menghapus semua data database, storage, dan password MQTT.

### Apache di dalam container tidak mau start setelah restart
Jika `docker compose ps` menunjukkan container `siapp_app` terus restart, biasanya disebabkan oleh PID file Apache yang tertinggal. Ini sudah ditangani otomatis oleh `entrypoint.sh`, namun jika masih terjadi:
```bash
docker exec siapp_app rm -f /var/run/apache2/apache2.pid
docker compose restart app
```

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
├── setup.sh                    # Script setup otomatis (interaktif)
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

## Keterbatasan yang Diketahui

- **Endpoint upload device legacy** (`uploadPresensi.php`, `uploadSholat.php`, `uploadIzinSholat.php`) belum dimigrasikan ke Laravel. Device ESP32/ESP8266 yang masih menggunakan endpoint lama perlu firmware/konfigurasi terbaru agar kompatibel dengan instalasi Docker ini.
- Instalasi via Docker ini ditujukan untuk **deployment baru** (server baru / VPS baru). Migrasi dari instalasi produksi berbasis XAMPP yang sudah berjalan memerlukan langkah tambahan (migrasi data) yang belum dicakup dokumen ini.

---

## Informasi Sistem

- **Framework**: Laravel 12
- **PHP**: 8.2 (dengan ekstensi: pdo_mysql, mysqli, mbstring, curl, gd, zip, xml, bcmath, pcntl, posix, calendar)
- **Database**: MariaDB 10.4
- **MQTT Broker**: Eclipse Mosquitto 2
- **Web Server**: Apache 2.4
- **Dikembangkan oleh**: SMK Negeri Bansari, Temanggung
