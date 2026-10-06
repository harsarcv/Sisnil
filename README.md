# Sisnil — Sistem Informasi Nilai Sekolah

Sisnil adalah aplikasi web Sistem Informasi Nilai Sekolah yang dirancang untuk membantu sekolah dalam mengelola data siswa, guru, mata pelajaran, nilai, serta laporan akademik dalam satu aplikasi.

Aplikasi ini memiliki beberapa jenis pengguna dengan hak akses yang berbeda, yaitu **Admin, Guru, dan Siswa**.

## Fitur

- Autentikasi pengguna
- Hak akses berdasarkan role
- Pengelolaan data siswa
- Pengelolaan data guru
- Pengelolaan mata pelajaran
- Pengelolaan kelas guru
- Input nilai siswa
- Edit dan pembaruan nilai
- Rekap nilai siswa
- Perhitungan nilai akhir otomatis
- Penentuan status kelulusan
- Perhitungan rata-rata nilai
- Dashboard Admin
- Dashboard Guru
- Dashboard Siswa
- Pencarian data siswa
- Filter siswa berdasarkan kelas
- Filter laporan berdasarkan kelas
- Filter laporan berdasarkan mata pelajaran
- Laporan hasil belajar
- Cetak laporan nilai

## Role & Akses

### Admin

- Melihat dashboard Admin
- Mengelola data siswa
- Menambah, mengubah, dan menghapus data siswa
- Mencari siswa berdasarkan nama atau NIS
- Memfilter siswa berdasarkan kelas
- Mengelola data guru
- Menambah, mengubah, dan menghapus data guru
- Mengelola mata pelajaran
- Menentukan guru pengampu mata pelajaran
- Mengelola kelas yang diajar guru
- Melihat statistik nilai
- Melihat status kelulusan siswa
- Melihat laporan hasil belajar
- Memfilter laporan berdasarkan kelas
- Memfilter laporan berdasarkan mata pelajaran
- Mencetak laporan nilai

### Guru

- Melihat dashboard Guru
- Melihat mata pelajaran yang diajar
- Melihat statistik pengajaran
- Memasukkan nilai siswa
- Mengubah nilai siswa
- Menghapus data nilai
- Menghitung nilai akhir secara otomatis
- Melihat status kelulusan
- Melihat rekap nilai siswa
- Memfilter rekap berdasarkan kelas
- Memfilter rekap berdasarkan mata pelajaran

### Siswa

- Melihat dashboard Siswa
- Melihat informasi pribadi
- Melihat nilai pribadi
- Melihat nilai berdasarkan mata pelajaran
- Melihat nilai Tugas
- Melihat nilai UTS
- Melihat nilai UAS
- Melihat nilai akhir
- Melihat rata-rata nilai
- Melihat status kelulusan

## Sistem Penilaian

Sisnil menggunakan tiga komponen utama dalam perhitungan nilai:

```text
Nilai Akhir =
(30% × Nilai Tugas) +
(30% × Nilai UTS) +
(40% × Nilai UAS)
```

Nilai yang dimasukkan harus berada pada rentang **0–100**.

Sistem menggunakan **KKM 70** sebagai batas kelulusan:

```text
Nilai Akhir ≥ 70 → Lulus
Nilai Akhir < 70 → Tidak Lulus
```

## Alur Pengolahan Nilai

Sisnil menggunakan alur pengolahan nilai sebagai berikut:

```text
Guru
  ↓
Memilih Kelas
  ↓
Memilih Siswa
  ↓
Memilih Mata Pelajaran
  ↓
Menginput Nilai Tugas, UTS, dan UAS
  ↓
Perhitungan Nilai Akhir
  ↓
Penentuan Status Kelulusan
  ↓
Menyimpan Nilai
  ↓
Rekap Nilai
  ↓
Nilai Dapat Dilihat Oleh Siswa
```

Nilai akhir dihitung secara otomatis berdasarkan bobot Tugas, UTS, dan UAS.

## Teknologi yang Digunakan

- **PHP** — Bahasa pemrograman utama aplikasi
- **MySQL** — Basis data
- **MySQLi** — Koneksi dan pengelolaan database
- **HTML5** — Struktur halaman
- **CSS3** — Tampilan aplikasi
- **Bootstrap 5** — Antarmuka pengguna
- **JavaScript** — Interaksi pada halaman
- **jQuery** — Interaksi dinamis
- **Font Awesome** — Ikon antarmuka
- **Git & GitHub** — Version control

## Database

Sisnil menggunakan MySQL dengan beberapa tabel utama:

- Users
- Siswa
- Guru
- Mata Pelajaran
- Nilai
- Guru Kelas

Tabel `Nilai` menyimpan nilai Tugas, UTS, UAS, dan nilai akhir siswa.

Data nilai dihubungkan dengan data siswa, mata pelajaran, dan guru untuk menghasilkan rekap nilai serta laporan hasil belajar.

## Struktur Project

```text
Sisnil/
├── assets/
│   └── css/
│       └── style.css
├── classes/
│   ├── Guru.php
│   ├── Nilai.php
│   └── Siswa.php
├── config/
│   └── database.php
├── functions/
│   └── fungsi.php
├── pages/
│   ├── admin/
│   │   ├── dashboard_admin.php
│   │   ├── data_guru.php
│   │   ├── data_mapel.php
│   │   ├── data_siswa.php
│   │   ├── detail_guru.php
│   │   └── laporan_nilai.php
│   ├── guru/
│   │   ├── dashboard_guru.php
│   │   ├── input_nilai.php
│   │   └── rekap_nilai_guru.php
│   └── siswa/
│       ├── dashboard_siswa.php
│       ├── nilai_siswa.php
│       └── status_siswa.php
├── proses/
│   ├── get_mapel_by_kelas.php
│   ├── get_siswa_by_kelas.php
│   ├── proses_edit_nilai.php
│   ├── proses_guru.php
│   ├── proses_login.php
│   ├── proses_logout.php
│   ├── proses_mapel.php
│   ├── proses_nilai.php
│   └── proses_siswa.php
├── .gitattributes
├── .gitignore
├── index.php
└── README.md
```

## Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/harsarcv/Sisnil.git
cd Sisnil
```

### 2. Konfigurasi Web Server

Sisnil membutuhkan web server yang mendukung PHP dan MySQL.

Lingkungan pengembangan yang dapat digunakan:

- XAMPP
- Laragon

Letakkan folder project pada direktori web server.

Contoh:

```text
htdocs/Sisnil
```

### 3. Konfigurasi Database

Buat database MySQL untuk aplikasi Sisnil.

Kemudian sesuaikan konfigurasi database pada:

```text
config/database.php
```

Sesuaikan konfigurasi berikut dengan database lokal:

```text
Host
Username
Password
Nama Database
```

### 4. Jalankan Web Server

Jika menggunakan XAMPP, aktifkan:

```text
Apache
MySQL
```

### 5. Jalankan Aplikasi

Buka browser dan akses:

```text
http://localhost/Sisnil
```

## Keamanan

- Autentikasi menggunakan session
- Pembatasan akses berdasarkan role
- Halaman Admin dilindungi berdasarkan role
- Halaman Guru dilindungi berdasarkan role
- Halaman Siswa dilindungi berdasarkan role
- Validasi nilai dengan rentang 0–100
- Pembersihan input sebelum diproses
- File konfigurasi lokal tidak disarankan untuk dimasukkan ke repository publik

## Lisensi

Project ini menggunakan lisensi MIT.

## Pembuat

**Harsa Archive**

Full-Stack Developer | PostgreSQL & Database | Web, Mobile & 3D

GitHub: https://github.com/harsarcv
