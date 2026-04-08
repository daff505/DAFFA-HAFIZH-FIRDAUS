# 🚗 Sistem Parkir Digital Pro

**Sistem Parkir Digital Pro** adalah aplikasi berbasis web yang dirancang untuk mengelola operasional parkir secara modern, cepat, dan transparan. Aplikasi ini mendukung manajemen data master, transaksi masuk/keluar secara real-time, hingga pelaporan eksekutif untuk pemilik (Owner).

---

## ✨ Fitur Utama

### 1. Manajemen Multi-Role (RBAC)
Aplikasi ini memiliki pembagian tugas yang ketat sesuai dengan standar industri:
- **Admin**: Mengelola data master (User, Tarif, Area, Kendaraan), Log Aktivitas, dan Pengaturan Sistem.
- **Petugas**: Fokus pada operasional harian (Kendaraan Masuk, Pembayaran Keluar, Cetak Struk).
- **Owner**: Akses eksklusif ke Dashboard Eksekutif dan Rekap Transaksi lengkap.

### 2. Dashboard Eksekutif (Owner)
Visualisasi performa bisnis secara real-time:
- Statistik Pendapatan Bulanan.
- Volume Transaksi Harian.
- Persentase Okupansi Lahan Parkir.
- Monitoring Transaksi Terbaru.

### 3. Transaksi & Pembayaran Pintar
- **Check-in/Check-out**: Proses cepat dengan validasi plat nomor dan jenis kendaraan.
- **QRIS Ready**: Integrasi dengan Midtrans untuk pembayaran non-tunai yang aman.
- **Biaya Dinamis**: Perhitungan biaya parkir otomatis berdasarkan durasi dan regulasi denda.

### 4. Pelaporan & Cetak Mandiri
- **Ekspor Excel**: Laporan rekap transaksi dapat diunduh dalam format `.xlsx` dengan satu klik.
- **Auto-Receipt**: Pencetakan struk parkir otomatis (PDF/Thermal) segera setelah pembayaran selesai.
- **Design High-Contrast**: Laporan PDF dioptimalkan untuk warna terang agar menghemat tinta dan mudah dibaca (Print-Friendly).

---

## 🛠️ Teknologi yang Digunakan

- **Core**: Laravel 11.x
- **Frontend**: Tailwind CSS & Alpine.js
- **Database**: MySQL (dengan Stored Procedures & Triggers)
- **Integrasi**: Midtrans Snap API (QRIS)
- **Ekspor Data**: SheetJS (XLSX)
- **Styling**: Vanilla CSS (Premium Dark Theme UI)

---

## 🚀 Cara Instalasi

1. **Clone Repository**
   ```bash
   git clone https://github.com/daff505/DAFFA-HAFIZH-FIRDAUS.git
   ```

2. **Instalasi Dependensi**
   ```bash
   composer install
   npm install && npm run dev
   ```

3. **Konfigurasi Environment**
   - Salin `.env.example` menjadi `.env`
   - Sesuaikan `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD`.
   - Masukkan Midtrans Server Key untuk fitur QRIS.

4. **Migrasi Database**
   ```bash
   php artisan migrate --seed
   ```

5. **Jalankan Aplikasi**
   ```bash
   php artisan serve
   ```

---

## 📝 Catatan Tambahan
Aplikasi ini dioptimalkan untuk performa tinggi dengan penggunaan **Stored Procedures** pada MySQL untuk operasi data besar dan pengelolaan log aktivitas sistem.

---
**Developed by [Daffa Hafizh Firdaus](https://github.com/daff505)**
