# Aplikasi Manajemen Karyawan

Aplikasi Manajemen Karyawan ini dibangun menggunakan **Filament**, yang memungkinkan pengelolaan karyawan, manajemen waktu, serta pengelolaan payroll secara efisien dan terstruktur. Aplikasi ini dirancang untuk mempermudah pengelolaan data karyawan, absensi, lembur, jadwal, serta pengelolaan gaji secara otomatis.

## Fitur Utama

### 1. **Employee (Database Karyawan)**
   Mengelola data karyawan secara terpusat. Anda dapat melihat, menambah, mengedit, dan menghapus data karyawan. Fitur ini juga mencakup informasi terkait posisi, departemen, dan status kepegawaian.

   - **Menambah Karyawan**: Menambahkan data karyawan baru.
   - **Mengedit Karyawan**: Mengupdate informasi karyawan yang sudah ada.
   - **Menghapus Karyawan**: Menghapus data karyawan jika diperlukan.

### 2. **Time Management**
   Mengelola waktu kerja karyawan dengan berbagai fitur terkait absensi, jadwal, dan lembur.

   - **Time Off (Cuti dan Izin)**: Mengelola permintaan cuti atau izin karyawan.
   - **Attendance (Kehadiran)**: Mencatat dan memonitor kehadiran karyawan setiap harinya.
   - **Overtime (Lembur)**: Mencatat jam lembur yang dikerjakan oleh karyawan.
   - **Schedule (Jadwal Kerja)**: Mengatur dan memonitor jadwal kerja karyawan.

### 3. **Payroll**
   Mengelola proses penggajian karyawan, termasuk pencatatan riwayat gaji, pembaruan gaji, dan menjalankan proses penggajian.

   - **Payroll History (Riwayat Gaji)**: Melihat riwayat pembayaran gaji karyawan.
   - **Update Payroll (Pembaruan Gaji)**: Mengupdate informasi terkait gaji karyawan (misalnya, kenaikan gaji atau perubahan posisi).
   - **Run Payroll (Proses Gaji)**: Menjalankan proses penggajian untuk semua karyawan.

## Prasyarat

Sebelum menggunakan aplikasi ini, pastikan Anda memiliki hal-hal berikut:

- PHP versi 8.0 atau lebih baru
- Composer untuk mengelola dependensi
- Database seperti MySQL atau PostgreSQL
- Filament Admin Panel (untuk pengelolaan admin interface)

## Instalasi

Ikuti langkah-langkah berikut untuk menginstal aplikasi ini:

1. **Clone repository ini**
    ```bash
    git clone https://github.com/username/repository-name.git
    cd repository-name
    ```

2. **Instal dependensi dengan Composer**
    ```bash
    composer install
    ```

3. **Konfigurasi environment**
   Salin file `.env.example` menjadi `.env` dan sesuaikan dengan pengaturan database Anda.
    ```bash
    cp .env.example .env
    ```

4. **Generate kunci aplikasi**
    ```bash
    php artisan key:generate
    ```

5. **Migrasi database**
    ```bash
    php artisan migrate
    ```

6. **Jalankan server**
    ```bash
    php artisan serve
    ```

   Aplikasi sekarang dapat diakses di `http://localhost:8000`.

## Penggunaan

Setelah aplikasi berjalan, Anda dapat mengakses berbagai fitur melalui antarmuka admin Filament. Berikut adalah beberapa tindakan yang dapat dilakukan:

- **Menambah Karyawan**: Melalui menu `Employee`, Anda dapat menambahkan data karyawan baru.
- **Mencatat Kehadiran**: Akses fitur `Attendance` untuk mencatat kehadiran karyawan.
- **Mengatur Jadwal Kerja**: Atur jadwal karyawan di bagian `Schedule`.
- **Memproses Penggajian**: Jalankan fitur `Run Payroll` untuk memproses gaji karyawan.
