-- Membuat database baru
CREATE DATABASE IF NOT EXISTS `db_aplikasi_parkir`;
USE `db_aplikasi_parkir`;

-- ======================================================
-- TABEL USER
-- ======================================================
CREATE TABLE `tb_user` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `nama_lengkap` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `password_asli` varchar(255) DEFAULT NULL,
  `role` enum('admin','petugas','owner') NOT NULL,
  `status_aktif` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ======================================================
-- TABEL AREA PARKIR
-- ======================================================
CREATE TABLE `tb_area_parkir` (
  `id_area` int(11) NOT NULL AUTO_INCREMENT,
  `kode_area` varchar(10) NOT NULL,
  `nama_area` varchar(50) NOT NULL,
  `lokasi` varchar(100) DEFAULT NULL,
  `kapasitas` int(11) NOT NULL,
  `terisi` int(11) DEFAULT 0,
  `status_area` enum('aktif','nonaktif','perbaikan') DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_area`),
  UNIQUE KEY `kode_area` (`kode_area`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ======================================================
-- TABEL KENDARAAN
-- ======================================================
CREATE TABLE `tb_kendaraan` (
  `id_kendaraan` int(11) NOT NULL AUTO_INCREMENT,
  `plat_nomor` varchar(15) NOT NULL,
  `jenis_kendaraan` enum('motor','mobil','truk','lainnya') NOT NULL,
  `warna` varchar(20) DEFAULT NULL,
  `merk` varchar(50) DEFAULT NULL,
  `pemilik` varchar(100) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_kendaraan`),
  UNIQUE KEY `plat_nomor` (`plat_nomor`),
  KEY `id_user` (`id_user`),
  KEY `idx_kendaraan_plat` (`plat_nomor`),
  CONSTRAINT `tb_kendaraan_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `tb_user` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ======================================================
-- TABEL TARIF
-- ======================================================
CREATE TABLE `tb_tarif` (
  `id_tarif` int(11) NOT NULL AUTO_INCREMENT,
  `jenis_kendaraan` enum('motor','mobil','truk','lainnya') NOT NULL,
  `tarif_per_jam` decimal(10,2) NOT NULL,
  `berlaku_mulai` date NOT NULL,
  `berlaku_hingga` date DEFAULT NULL,
  `dibuat_oleh` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_tarif`),
  KEY `dibuat_oleh` (`dibuat_oleh`),
  KEY `idx_tarif_jenis` (`jenis_kendaraan`),
  CONSTRAINT `tb_tarif_ibfk_1` FOREIGN KEY (`dibuat_oleh`) REFERENCES `tb_user` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ======================================================
-- TABEL TRANSAKSI
-- ======================================================
CREATE TABLE `tb_transaksi` (
  `id_parkir` int(11) NOT NULL AUTO_INCREMENT,
  `nomor_tiket` varchar(20) NOT NULL,
  `id_kendaraan` int(11) NOT NULL,
  `id_area` int(11) NOT NULL,
  `waktu_masuk` datetime NOT NULL,
  `waktu_keluar` datetime DEFAULT NULL,
  `id_tarif` int(11) NOT NULL,
  `durasi_jam` decimal(5,2) DEFAULT NULL,
  `biaya_total` decimal(10,2) DEFAULT NULL,
  `status` enum('masuk','keluar','pending','batal') DEFAULT 'masuk',
  `id_petugas_masuk` int(11) DEFAULT NULL,
  `id_petugas_keluar` int(11) DEFAULT NULL,
  `metode_bayar` enum('tunai','qris','debit','kredit') DEFAULT 'tunai',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_parkir`),
  UNIQUE KEY `nomor_tiket` (`nomor_tiket`),
  KEY `id_kendaraan` (`id_kendaraan`),
  KEY `id_area` (`id_area`),
  KEY `id_tarif` (`id_tarif`),
  KEY `id_petugas_masuk` (`id_petugas_masuk`),
  KEY `id_petugas_keluar` (`id_petugas_keluar`),
  KEY `idx_transaksi_status` (`status`),
  KEY `idx_transaksi_waktu_masuk` (`waktu_masuk`),
  KEY `idx_transaksi_waktu_keluar` (`waktu_keluar`),
  CONSTRAINT `tb_transaksi_ibfk_1` FOREIGN KEY (`id_kendaraan`) REFERENCES `tb_kendaraan` (`id_kendaraan`),
  CONSTRAINT `tb_transaksi_ibfk_2` FOREIGN KEY (`id_area`) REFERENCES `tb_area_parkir` (`id_area`),
  CONSTRAINT `tb_transaksi_ibfk_3` FOREIGN KEY (`id_tarif`) REFERENCES `tb_tarif` (`id_tarif`),
  CONSTRAINT `tb_transaksi_ibfk_4` FOREIGN KEY (`id_petugas_masuk`) REFERENCES `tb_user` (`id_user`),
  CONSTRAINT `tb_transaksi_ibfk_5` FOREIGN KEY (`id_petugas_keluar`) REFERENCES `tb_user` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ======================================================
-- TABEL STRUK
-- ======================================================
CREATE TABLE `tb_struk` (
  `id_struk` int(11) NOT NULL AUTO_INCREMENT,
  `id_transaksi` int(11) NOT NULL,
  `nomor_struk` varchar(30) NOT NULL,
  `konten_struk` text NOT NULL,
  `dicetak_pada` datetime NOT NULL,
  `dicetak_oleh` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_struk`),
  UNIQUE KEY `nomor_struk` (`nomor_struk`),
  KEY `id_transaksi` (`id_transaksi`),
  KEY `dicetak_oleh` (`dicetak_oleh`),
  CONSTRAINT `tb_struk_ibfk_1` FOREIGN KEY (`id_transaksi`) REFERENCES `tb_transaksi` (`id_parkir`),
  CONSTRAINT `tb_struk_ibfk_2` FOREIGN KEY (`dicetak_oleh`) REFERENCES `tb_user` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ======================================================
-- TABEL PENGATURAN
-- ======================================================
CREATE TABLE `tb_pengaturan` (
  `id_pengaturan` int(11) NOT NULL AUTO_INCREMENT,
  `kunci` varchar(50) NOT NULL,
  `nilai` text DEFAULT NULL,
  `keterangan` varchar(200) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_pengaturan`),
  UNIQUE KEY `kunci` (`kunci`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ======================================================
-- TABEL LOG AKTIVITAS
-- ======================================================
CREATE TABLE `tb_log_aktivitas` (
  `id_log` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `aktivitas` varchar(100) NOT NULL,
  `tabel_terkait` varchar(50) DEFAULT NULL,
  `id_record_terkait` int(11) DEFAULT NULL,
  `detail` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `waktu_aktivitas` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_log`),
  KEY `id_user` (`id_user`),
  KEY `idx_log_waktu` (`waktu_aktivitas`),
  CONSTRAINT `tb_log_aktivitas_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `tb_user` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ======================================================
-- TRIGGER
-- ======================================================

DELIMITER $$

-- Trigger untuk mengurangi kapasitas area saat kendaraan keluar
CREATE TRIGGER `after_transaksi_keluar` 
AFTER UPDATE ON `tb_transaksi` 
FOR EACH ROW
BEGIN
    IF NEW.status = 'keluar' AND OLD.status = 'masuk' THEN
        UPDATE tb_area_parkir 
        SET terisi = terisi - 1 
        WHERE id_area = NEW.id_area;
    END IF;
END$$

-- Trigger untuk menambah kapasitas area saat kendaraan masuk
CREATE TRIGGER `after_transaksi_masuk` 
AFTER INSERT ON `tb_transaksi` 
FOR EACH ROW
BEGIN
    IF NEW.status = 'masuk' THEN
        UPDATE tb_area_parkir 
        SET terisi = terisi + 1 
        WHERE id_area = NEW.id_area;
    END IF;
END$$

-- Stored Procedure untuk menghitung biaya parkir
CREATE PROCEDURE `sp_hitung_biaya` (
    IN `p_id_transaksi` INT, 
    OUT `p_biaya_total` DECIMAL(10,2)
)
BEGIN
    DECLARE v_tarif_per_jam DECIMAL(10,2);
    DECLARE v_durasi_jam DECIMAL(5,2);
    DECLARE v_id_tarif INT;
    
    SELECT id_tarif, durasi_jam INTO v_id_tarif, v_durasi_jam
    FROM tb_transaksi WHERE id_parkir = p_id_transaksi;
    
    SELECT tarif_per_jam INTO v_tarif_per_jam
    FROM tb_tarif WHERE id_tarif = v_id_tarif;
    
    SET p_biaya_total = v_durasi_jam * v_tarif_per_jam;
    
    UPDATE tb_transaksi 
    SET biaya_total = p_biaya_total 
    WHERE id_parkir = p_id_transaksi;
END$$

DELIMITER ;

-- ======================================================
-- VIEW
-- ======================================================

CREATE VIEW `v_rekap_transaksi` AS
SELECT 
    t.id_parkir,
    t.nomor_tiket,
    k.plat_nomor,
    k.jenis_kendaraan,
    a.nama_area,
    t.waktu_masuk,
    t.waktu_keluar,
    t.durasi_jam,
    t.biaya_total,
    t.status,
    u1.nama_lengkap AS petugas_masuk,
    u2.nama_lengkap AS petugas_keluar
FROM tb_transaksi t
JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
JOIN tb_area_parkir a ON t.id_area = a.id_area
LEFT JOIN tb_user u1 ON t.id_petugas_masuk = u1.id_user
LEFT JOIN tb_user u2 ON t.id_petugas_keluar = u2.id_user;

-- ======================================================
-- DATA SEEDING (Data Awal)
-- ======================================================

-- Data User (password: password123 - gunakan bcrypt hash sesuai kebutuhan)
INSERT INTO `tb_user` (`id_user`, `nama_lengkap`, `username`, `password`, `password_asli`, `role`, `status_aktif`) VALUES
(1, 'Administrator Utama', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'password123', 'admin', 1),
(2, 'Petugas Parkir 1', 'petugas1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'password123', 'petugas', 1),
(3, 'Pemilik Parkir', 'owner', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'password123', 'owner', 1);

-- Data Area Parkir
INSERT INTO `tb_area_parkir` (`id_area`, `kode_area`, `nama_area`, `lokasi`, `kapasitas`, `terisi`, `status_area`) VALUES
(1, 'A-01', 'Area Motor', 'Pelataran Samping Gedung A', 50, 0, 'aktif'),
(2, 'B-01', 'Area Mobil', 'Basement 1 - Blok B', 30, 0, 'aktif'),
(3, 'C-01', 'Area Truk', 'Lahan Terbuka Belakang', 10, 0, 'aktif');

-- Data Tarif
INSERT INTO `tb_tarif` (`id_tarif`, `jenis_kendaraan`, `tarif_per_jam`, `berlaku_mulai`, `berlaku_hingga`, `dibuat_oleh`) VALUES
(1, 'motor', 2000.00, CURDATE(), NULL, 1),
(2, 'mobil', 5000.00, CURDATE(), NULL, 1),
(3, 'truk', 10000.00, CURDATE(), NULL, 1);

-- Data Pengaturan
INSERT INTO `tb_pengaturan` (`id_pengaturan`, `kunci`, `nilai`, `keterangan`) VALUES
(1, 'nama_aplikasi', 'Sistem Parkir Digital', 'Nama aplikasi'),
(2, 'waktu_tenggat', '24', 'Waktu maksimal parkir dalam jam'),
(3, 'denda_per_jam', '1000', 'Denda keterlambatan per jam'),
(4, 'struk_header', 'SISTEM PARKIR DIGITAL\nJl. Parkir No. 123', 'Header struk');

-- ======================================================
-- COMMIT
-- ======================================================
COMMIT;