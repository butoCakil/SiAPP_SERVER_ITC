#!/bin/bash
set -e
echo "======================================"
echo "  SiAPP — Setup Docker"
echo "======================================"
echo ""
# Cek Docker tersedia
if ! command -v docker &> /dev/null; then
  echo "ERROR: Docker belum terinstall."
  echo "Panduan: https://docs.docker.com/engine/install/"
  exit 1
fi
if ! docker compose version &> /dev/null; then
  echo "ERROR: Docker Compose belum tersedia."
  exit 1
fi
echo "[1/5] Membuat file konfigurasi..."
if [ -f ".env.docker" ]; then
  echo "      File .env.docker sudah ada, dilewati."
else
  cp .env.example.docker .env.docker
  echo "      File .env.docker dibuat dari template."
fi
echo "[2/5] Generate APP_KEY..."
APP_KEY=$(php -r "echo 'base64:' . base64_encode(random_bytes(32));" 2>/dev/null \
  || echo "base64:$(head -c 32 /dev/urandom | base64 | tr -d '\n')")
sed -i "s|^APP_KEY=.*|APP_KEY=${APP_KEY}|" .env.docker
echo "      APP_KEY berhasil di-generate."
echo "[3/5] Membuat password MQTT..."
MQTT_USER=$(grep "^MQTT_USERNAME=" .env.docker | cut -d= -f2)
MQTT_PASS=$(grep "^MQTT_PASSWORD=" .env.docker | cut -d= -f2)
mkdir -p docker/mqtt
rm -f docker/mqtt/passwd
docker run --rm -v "$(pwd)/docker/mqtt:/output" \
  eclipse-mosquitto:2 \
  mosquitto_passwd -c -b /output/passwd "${MQTT_USER}" "${MQTT_PASS}" 2>/dev/null
chmod 644 docker/mqtt/passwd 2>/dev/null || sudo chmod 644 docker/mqtt/passwd
echo "      Password MQTT dibuat untuk user: ${MQTT_USER}"

echo "[4/5] Konfigurasi akun Admin (Super Admin)..."
echo "      Kosongkan untuk pakai default (password akan di-generate otomatis)."
read -p "      Username admin [Pengembang]: " ADMIN_USERNAME
read -p "      Email admin [admin@siapp.local]: " ADMIN_EMAIL
read -s -p "      Password admin (kosongkan untuk random): " ADMIN_PASSWORD
echo ""

ADMIN_USERNAME=${ADMIN_USERNAME:-Pengembang}
ADMIN_EMAIL=${ADMIN_EMAIL:-admin@siapp.local}
GENERATED_PASSWORD=0
if [ -z "$ADMIN_PASSWORD" ]; then
  ADMIN_PASSWORD=$(head -c 12 /dev/urandom | base64 | tr -dc 'a-zA-Z0-9' | head -c 12)
  GENERATED_PASSWORD=1
fi

echo "[5/5] Menjalankan SiAPP..."
APP_PORT=$(grep "^APP_PORT=" .env.docker | cut -d= -f2); APP_PORT=${APP_PORT:-8080}
MQTT_PORT=$(grep "^MQTT_PORT=" .env.docker | cut -d= -f2); MQTT_PORT=${MQTT_PORT:-1883}
if ss -tlnp | grep -q ":${APP_PORT} "; then
  echo ""
  echo "  PERINGATAN: Port ${APP_PORT} sudah dipakai oleh proses lain."
  echo "  Ubah APP_PORT di .env.docker ke port lain, misalnya: APP_PORT=9080"
  echo "  Kemudian jalankan: docker compose up -d"
  exit 1
fi
if ss -tlnp | grep -q ":${MQTT_PORT} "; then
  echo ""
  echo "  PERINGATAN: Port ${MQTT_PORT} sudah dipakai oleh proses lain."
  echo "  Ubah MQTT_PORT di .env.docker ke port lain, misalnya: MQTT_PORT=1884"
  echo "  Kemudian jalankan: docker compose up -d"
  exit 1
fi
docker compose up -d

echo "      Menunggu database siap..."
for i in $(seq 1 30); do
  if docker compose exec -T db mariadb -u root -p"$(grep '^DB_ROOT_PASSWORD=' .env.docker | cut -d= -f2)" -e "SELECT 1 FROM admin LIMIT 1" siap &>/dev/null; then
    break
  fi
  sleep 2
done

DB_ROOT_PASSWORD=$(grep '^DB_ROOT_PASSWORD=' .env.docker | cut -d= -f2)
DB_NAME=$(grep '^DB_DATABASE=' .env.docker | cut -d= -f2); DB_NAME=${DB_NAME:-siap}
docker compose exec -T db mariadb -u root -p"${DB_ROOT_PASSWORD}" "${DB_NAME}" -e \
  "UPDATE admin SET username='${ADMIN_USERNAME}', email='${ADMIN_EMAIL}', password=MD5('${ADMIN_PASSWORD}') WHERE id=1;" &>/dev/null \
  && echo "      Akun admin berhasil dikonfigurasi." \
  || echo "      PERINGATAN: gagal mengatur akun admin, gunakan default seed."

echo ""
echo "======================================"
echo "  SiAPP siap diakses!"
echo "  URL: http://localhost:${APP_PORT}"
echo ""
echo "  Login Admin (Super Admin):"
echo "    Username : ${ADMIN_USERNAME}"
echo "    Email    : ${ADMIN_EMAIL}"
if [ "$GENERATED_PASSWORD" = "1" ]; then
  echo "    Password : ${ADMIN_PASSWORD}  (dibuat otomatis, simpan baik-baik!)"
else
  echo "    Password : (sesuai yang Anda masukkan)"
fi
echo "======================================"
