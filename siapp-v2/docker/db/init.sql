
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` varchar(50) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `wa` varchar(50) DEFAULT NULL,
  `fb` varchar(50) DEFAULT NULL,
  `ig` varchar(50) DEFAULT NULL,
  `foto` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `api`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `api` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode_api` longtext NOT NULL,
  `info_api` varchar(255) DEFAULT NULL,
  `jenis` varchar(50) DEFAULT NULL,
  `masaberlaku` date DEFAULT NULL,
  `status` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cuaca`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cuaca` (
  `jam_cuaca` varchar(20) NOT NULL,
  `kondisi_cuaca` varchar(20) NOT NULL,
  `suhu_cuaca` varchar(20) NOT NULL,
  `kelembapan_cuaca` varchar(20) NOT NULL,
  `kecepatan_angin` varchar(20) NOT NULL,
  `arah_angin` varchar(20) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `daftarijin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `daftarijin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nokartu` varchar(255) NOT NULL,
  `nis` varchar(100) NOT NULL,
  `nama` varchar(30) NOT NULL,
  `info` varchar(50) DEFAULT NULL,
  `jam_keluar` varchar(100) NOT NULL,
  `jam_kembali` varchar(100) DEFAULT NULL,
  `tanggalijin` date NOT NULL,
  `fotodoc` varchar(255) DEFAULT NULL,
  `kode` varchar(10) NOT NULL,
  `timestamp` timestamp NULL DEFAULT current_timestamp(),
  `pushed_at` timestamp NULL DEFAULT NULL,
  `kembali_pushed_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1457 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `daftarketerlambatan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `daftarketerlambatan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nokartu` varchar(100) NOT NULL,
  `tanggal` varchar(255) NOT NULL,
  `nis` varchar(10) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `kelas` varchar(20) NOT NULL,
  `tingkat` varchar(10) NOT NULL,
  `jurusan` varchar(10) NOT NULL,
  `waktu` varchar(255) NOT NULL,
  `waktuterlambat` varchar(255) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1607 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `daftarruang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `daftarruang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode` varchar(10) NOT NULL,
  `inforuang` varchar(100) NOT NULL,
  `keterangan` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `dataguru`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dataguru` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nokartu` varchar(100) NOT NULL,
  `nip` varchar(100) DEFAULT NULL,
  `nama` varchar(100) NOT NULL,
  `nick` varchar(100) NOT NULL,
  `status` varchar(100) DEFAULT NULL,
  `info` varchar(100) DEFAULT NULL,
  `foto` varchar(255) NOT NULL,
  `created_at` varchar(100) DEFAULT NULL,
  `updated_at` varchar(100) DEFAULT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `tglawaldispo` varchar(100) DEFAULT NULL,
  `tglakhirdispo` varchar(100) DEFAULT NULL,
  `docdis` varchar(255) DEFAULT NULL,
  `t_waktu_telat` varchar(100) DEFAULT NULL,
  `poin` varchar(11) DEFAULT NULL,
  `kode` varchar(10) DEFAULT NULL,
  `jabatan` varchar(100) DEFAULT NULL,
  `akses` varchar(100) DEFAULT NULL,
  `ket_akses` varchar(10) DEFAULT NULL,
  `saldo` varchar(100) DEFAULT NULL,
  `total_transaksi` varchar(100) DEFAULT NULL,
  `total_belanja` varchar(100) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `login` varchar(10) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `tentang` longtext DEFAULT NULL,
  `template_pesan` text DEFAULT NULL,
  `level_login` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=88 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `datapresensi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `datapresensi` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nokartu` varchar(100) NOT NULL,
  `nomorinduk` varchar(100) DEFAULT NULL,
  `nama` varchar(100) NOT NULL,
  `info` varchar(100) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `waktumasuk` time DEFAULT NULL,
  `ketmasuk` varchar(25) DEFAULT NULL,
  `a_time` time DEFAULT NULL,
  `waktupulang` time DEFAULT NULL,
  `ketpulang` varchar(25) DEFAULT NULL,
  `b_time` time DEFAULT NULL,
  `tanggal` date NOT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `kode` varchar(10) DEFAULT NULL,
  `infodevice` varchar(100) DEFAULT NULL,
  `infodevice2` varchar(100) DEFAULT NULL,
  `pushed_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `datasiswa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `datasiswa` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nokartu` varchar(100) NOT NULL,
  `nis` varchar(10) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `nick` varchar(100) NOT NULL,
  `kelas` varchar(100) NOT NULL,
  `foto` varchar(255) NOT NULL,
  `kelompok` varchar(20) DEFAULT NULL,
  `t_waktu_telat` time DEFAULT NULL,
  `poin` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `keterangan` varchar(100) DEFAULT NULL,
  `tglawal` date DEFAULT NULL,
  `tglakhir` date DEFAULT NULL,
  `fotodoc` varchar(100) DEFAULT NULL,
  `kode` varchar(10) NOT NULL,
  `tingkat` varchar(10) NOT NULL,
  `jur` varchar(20) DEFAULT NULL,
  `saldo` varchar(50) DEFAULT NULL,
  `total_transaksi` int(11) DEFAULT NULL,
  `total_belanja` int(11) DEFAULT NULL,
  `tentang` text DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `login` varchar(10) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1654 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `device_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `device_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `device_id` varchar(64) DEFAULT NULL,
  `topic` varchar(128) DEFAULT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload`)),
  `received_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_received_at` (`received_at`),
  KEY `idx_device_id` (`device_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1643601 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `device_metrics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `device_metrics` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `device_id` varchar(50) NOT NULL,
  `ram` tinyint(3) unsigned DEFAULT 0,
  `rssi` smallint(6) DEFAULT -100,
  `ping` int(11) NOT NULL DEFAULT 0,
  `buffer` smallint(5) unsigned DEFAULT 0,
  `recorded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_device_recorded` (`device_id`,`recorded_at`)
) ENGINE=InnoDB AUTO_INCREMENT=182742 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `devices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `devices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `device_id` varchar(64) NOT NULL,
  `last_seen` datetime DEFAULT NULL,
  `offline_since` datetime DEFAULT NULL,
  `online_since` datetime DEFAULT NULL,
  `online` tinyint(1) DEFAULT 0,
  `hidden` tinyint(1) NOT NULL DEFAULT 0,
  `fw_version` varchar(32) DEFAULT NULL,
  `last_setting` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`last_setting`)),
  `last_command` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`last_command`)),
  `last_koneksi` text DEFAULT NULL,
  `last_dirlist` text DEFAULT NULL,
  `last_status` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`last_status`)),
  `info` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `device_id` (`device_id`),
  UNIQUE KEY `device_id_2` (`device_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1252769 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `exportdb`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `exportdb` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `db` varchar(255) NOT NULL,
  `link` text NOT NULL,
  `keyapi` text DEFAULT NULL,
  `status` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `jadwalgurujur`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jadwalgurujur` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ruangan` varchar(100) NOT NULL,
  `keterangan_ruang` varchar(100) NOT NULL,
  `nick` varchar(100) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `jur` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `jadwalkbm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jadwalkbm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ruangan` varchar(10) DEFAULT NULL,
  `info` varchar(100) DEFAULT NULL,
  `kelas` varchar(10) DEFAULT NULL,
  `kelompok` varchar(10) DEFAULT NULL,
  `tingkat` varchar(10) DEFAULT NULL,
  `jur` varchar(10) DEFAULT NULL,
  `nick` varchar(100) DEFAULT NULL,
  `tanggal` varchar(20) DEFAULT NULL,
  `mulai_jamke` varchar(10) DEFAULT NULL,
  `sampai_jamke` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `jampelajaran`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jampelajaran` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mode` varchar(100) NOT NULL,
  `waktu_masuk` time NOT NULL,
  `jamke` varchar(10) NOT NULL,
  `mulai` time NOT NULL,
  `selesai` time NOT NULL,
  `info` varchar(100) NOT NULL,
  `keterangan` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `jurnalguru`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jurnalguru` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nick` varchar(100) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `jurnal` longtext NOT NULL,
  `ruangan` varchar(100) NOT NULL,
  `info` varchar(100) NOT NULL,
  `kelas` varchar(100) NOT NULL,
  `kelompok` varchar(100) NOT NULL,
  `jur` varchar(100) NOT NULL,
  `tingkat` varchar(100) NOT NULL,
  `jamke` varchar(100) NOT NULL,
  `sampai_jamke` varchar(100) NOT NULL,
  `tanggal` date NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `kaldik`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kaldik` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tanggal` date NOT NULL,
  `judul` varchar(255) NOT NULL,
  `tipe` enum('libur_nasional','cuti_bersama','libur_semester','kegiatan','daring','force_majeure') NOT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_tanggal` (`tanggal`)
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `kalender`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kalender` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tanggal` varchar(10) NOT NULL,
  `bulan` varchar(10) NOT NULL,
  `info` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `kelompokkelas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kelompokkelas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode` varchar(100) NOT NULL,
  `jurusan` varchar(100) NOT NULL,
  `tingkat` varchar(100) NOT NULL,
  `kelompok` varchar(100) DEFAULT NULL,
  `info` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=113 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `kodeinfo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kodeinfo` (
  `kode` varchar(10) NOT NULL,
  `info` varchar(30) NOT NULL,
  `tingkat` varchar(10) DEFAULT NULL,
  `jur` varchar(10) NOT NULL,
  PRIMARY KEY (`kode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_resets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` int(10) unsigned NOT NULL,
  `token` varchar(64) NOT NULL,
  `otp` varchar(6) DEFAULT NULL,
  `metode` enum('email','wa') NOT NULL DEFAULT 'email',
  `expired_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `password_resets_token_unique` (`token`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pengumuman`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pengumuman` (
  `judul` varchar(50) NOT NULL,
  `isi` longtext NOT NULL,
  `foto` varchar(100) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `presensiEvent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `presensiEvent` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nokartu` varchar(100) NOT NULL,
  `nis` varchar(100) NOT NULL,
  `ruang` varchar(100) NOT NULL,
  `mulai` varchar(100) DEFAULT NULL,
  `selesai` varchar(100) DEFAULT NULL,
  `jam` varchar(100) NOT NULL,
  `tanggal` varchar(100) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `keterangan` varchar(255) DEFAULT NULL,
  `pushed_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33160 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `presensikelas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `presensikelas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nokartu` varchar(255) NOT NULL,
  `nis` varchar(100) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `ruangan` varchar(100) NOT NULL,
  `kelas` varchar(100) DEFAULT NULL,
  `mulai_jamke` varchar(100) DEFAULT NULL,
  `sampai_jamke` varchar(100) DEFAULT NULL,
  `status` varchar(10) DEFAULT NULL,
  `catatan` longtext DEFAULT NULL,
  `aff` varchar(100) DEFAULT NULL,
  `psi` varchar(100) DEFAULT NULL,
  `kog` varchar(100) DEFAULT NULL,
  `plus` varchar(100) DEFAULT NULL,
  `nick_guru` varchar(20) DEFAULT NULL,
  `nama_guru` varchar(255) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=103 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `push_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `push_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `endpoint` varchar(30) NOT NULL,
  `tanggal` date NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `total` int(11) NOT NULL DEFAULT 0,
  `http_status` int(11) DEFAULT NULL,
  `pesan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_endpoint` (`endpoint`),
  KEY `idx_tanggal` (`tanggal`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=2280 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `reg_device`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reg_device` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `chip_id` varchar(255) NOT NULL,
  `no_device` text NOT NULL,
  `kode` varchar(100) NOT NULL,
  `info_device` varchar(255) NOT NULL,
  `status` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `riwayat_topup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `riwayat_topup` (
  `id` int(11) NOT NULL,
  `nokartu` varchar(255) NOT NULL,
  `nominal` int(255) NOT NULL,
  `admin` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `riwayat_transaksi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `riwayat_transaksi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode_item` varchar(255) NOT NULL,
  `nama_item` varchar(255) NOT NULL,
  `nokartu` varchar(255) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `info` varchar(50) NOT NULL,
  `status` varchar(100) NOT NULL,
  `nominal` varchar(50) NOT NULL,
  `sisa_saldo` varchar(100) NOT NULL,
  `jumlah_transaksi` varchar(255) NOT NULL,
  `saldo_sebelumnya` varchar(100) NOT NULL,
  `user` varchar(100) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `akses` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=230 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `statusnya`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `statusnya` (
  `mode` int(11) NOT NULL,
  `wa` time NOT NULL,
  `wta` time NOT NULL,
  `wtp` time NOT NULL,
  `wtp_jumat` time NOT NULL DEFAULT '11:00:00',
  `upload1` time NOT NULL DEFAULT '07:30:00',
  `upload2` time NOT NULL DEFAULT '13:00:00',
  `restart1` time NOT NULL DEFAULT '05:00:00',
  `restart2` time NOT NULL DEFAULT '17:00:00',
  `wp` time NOT NULL,
  `wp_jumat` time NOT NULL DEFAULT '16:00:00',
  `dhuha_start` time DEFAULT '07:00:00',
  `dhuha_end` time DEFAULT '11:00:00',
  `dzuhur_start` time DEFAULT '11:30:00',
  `dzuhur_end` time DEFAULT '13:30:00',
  `ashar_start` time DEFAULT '15:00:00',
  `ashar_end` time DEFAULT '16:30:00',
  `hari_kerja` tinyint(1) NOT NULL DEFAULT 5,
  `tingkat_aktif` varchar(50) DEFAULT '["X","XI","XII"]',
  `log_retention` int(3) DEFAULT 30,
  `timid_presensi_url` text DEFAULT NULL,
  `timid_sholat_url` text DEFAULT NULL,
  `timid_izin_mens_url` text DEFAULT NULL,
  `timid_ijin_url` text DEFAULT NULL,
  `timid_api_key` varchar(255) DEFAULT '',
  `push_interval` int(3) DEFAULT 5,
  `auto_mode` tinyint(1) NOT NULL DEFAULT 1,
  `waktumasuk` time NOT NULL,
  `waktupulang` time NOT NULL,
  `info` varchar(255) NOT NULL,
  `wa_number` varchar(20) DEFAULT '',
  `wa_numbers` text DEFAULT NULL,
  `wa_device_id` varchar(100) DEFAULT '',
  `offline_after` int(11) NOT NULL DEFAULT 120,
  `escalation_after` int(11) NOT NULL DEFAULT 300,
  `notif_quiet_start` int(11) NOT NULL DEFAULT 18,
  `notif_quiet_end` int(11) NOT NULL DEFAULT 6,
  `notif_escalation_start` int(11) NOT NULL DEFAULT 10,
  `notif_escalation_end` int(11) NOT NULL DEFAULT 16,
  `push_auto` tinyint(4) DEFAULT 1,
  PRIMARY KEY (`mode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tempreq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tempreq` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `ip` varchar(50) DEFAULT NULL,
  `req` text NOT NULL,
  `info` varchar(255) DEFAULT NULL,
  `detail` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=368902 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tmprfid`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tmprfid` (
  `nokartu` varchar(50) NOT NULL,
  `nokartu_admin` varchar(50) DEFAULT NULL,
  `nokartu_emanpo` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`nokartu`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

LOCK TABLES `statusnya` WRITE;
/*!40000 ALTER TABLE `statusnya` DISABLE KEYS */;
INSERT INTO `statusnya` (`mode`, `wa`, `wta`, `wtp`, `wtp_jumat`, `upload1`, `upload2`, `restart1`, `restart2`, `wp`, `wp_jumat`, `dhuha_start`, `dhuha_end`, `dzuhur_start`, `dzuhur_end`, `ashar_start`, `ashar_end`, `hari_kerja`, `tingkat_aktif`, `log_retention`, `timid_presensi_url`, `timid_sholat_url`, `timid_izin_mens_url`, `timid_ijin_url`, `timid_api_key`, `push_interval`, `auto_mode`, `waktumasuk`, `waktupulang`, `info`, `wa_number`, `wa_numbers`, `wa_device_id`, `offline_after`, `escalation_after`, `notif_quiet_start`, `notif_quiet_end`, `notif_escalation_start`, `notif_escalation_end`, `push_auto`) VALUES (2,'05:00:00','07:30:00','13:30:00','13:00:00','07:30:00','12:50:00','05:00:00','17:00:00','18:30:00','16:00:00','07:00:00','11:00:00','11:45:00','13:15:00','14:45:00','16:30:00',5,'[\"X\",\"XI\"]',30,NULL,'https://script.google.com/macros/s/AKfycbxraKdCMsaKUwmZto-zmNwJWctJJpr60vPl1uiM1inkSaG94VLAl34c1pFBBqHEjIE/exec',NULL,NULL,NULL,5,1,'07:00:00','15:00:00','5','082241863393','[]','933bbd2c-8931-421c-8432-8e1ba9b3d795',120,300,18,6,10,16,0);
/*!40000 ALTER TABLE `statusnya` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `admin` WRITE;
/*!40000 ALTER TABLE `admin` DISABLE KEYS */;
INSERT INTO `admin` (`id`, `username`, `email`, `password`, `status`, `timestamp`, `wa`, `fb`, `ig`, `foto`) VALUES (1,'Pengembang','bennysurahman@gmail.com ','ab2b7643cbeab5672481f10088482efb','login','2026-05-23 23:10:40','6282241863393','','','EmbeddedImage.png'),(2,'Kepala Sekolah','ks@smknbansari.sch.id','63acb0f064ddb5b0773feb0257986fa3','login','2024-04-22 03:46:30','',NULL,NULL,'default.jpg'),(16,'Super Admin','admin@smknbansari.sch.id','63acb0f064ddb5b0773feb0257986fa3','login','2026-04-29 04:39:52','',NULL,NULL,'default.jpg');
/*!40000 ALTER TABLE `admin` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `api` WRITE;
/*!40000 ALTER TABLE `api` DISABLE KEYS */;
INSERT INTO `api` (`id`, `kode_api`, `info_api`, `jenis`, `masaberlaku`, `status`) VALUES (1,'e807f1fcf82d132f9bb018ca6738a19f',NULL,'lama','2022-12-31','nonaktif'),(2,'bf84b03e04fca268e50fc7698e8d673e','restAPIdb','sim_token','2027-12-31','aktif'),(3,'1234567890987654321','Token Sekolah - Device ESP32','device_token','2027-12-31','aktif'),(4,'3813d2f1bad6c012dac67aa0228aff28','restAPI001','sim_token','2027-12-31','aktif');
/*!40000 ALTER TABLE `api` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

