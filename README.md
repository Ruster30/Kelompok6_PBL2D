# Sistem Informasi Manajemen Event Alpha.corp

## Deskripsi Proyek

Sistem Informasi Manajemen Event Alpha.corp merupakan aplikasi berbasis web yang dirancang untuk membantu proses pengelolaan layanan event secara terintegrasi antara klien, admin, dan vendor.

Sistem ini mempermudah pengelolaan kebutuhan event mulai dari pengajuan kebutuhan oleh klien, penyusunan proposal dan RAB, proses negosiasi, pengelolaan vendor, pembayaran, hingga pembuatan laporan akhir event.

### Tujuan Aplikasi

* Mempermudah pengelolaan layanan event secara digital.
* Mempercepat komunikasi antara klien, admin, dan vendor.
* Meningkatkan efisiensi proses penyusunan proposal dan RAB.
* Memudahkan monitoring pelaksanaan event secara real-time.

### Masalah yang Diselesaikan

* Proses pengelolaan event yang masih manual.
* Sulitnya memantau progres event.
* Dokumentasi event yang tidak terpusat.
* Komunikasi antara pihak terkait yang kurang efektif.

### Target Pengguna

* Admin Event Organizer
* Klien / Customer
* Vendor / Mitra Event

---

# Features

## Client Features

* Registrasi dan Login
* Mengirim kebutuhan event
* Melihat proposal dan RAB
* Menyetujui proposal
* Melakukan negosiasi
* Upload bukti pembayaran
* Melihat progres event
* Melihat dokumentasi event
* Menerima laporan akhir event

## Admin Features

* Login dan Manajemen Akun
* Kelola Data Klien
* Kelola Layanan Event
* Menyusun Proposal
* Menyusun RAB
* Mengelola Negosiasi
* Mengelola Timeline Event
* Mengelola Dokumentasi Event
* Mengelola Vendor
* Verifikasi Pembayaran
* Generate Kontrak dan Invoice
* Menyusun Laporan Akhir Event

## Vendor Features

* Login Vendor
* Menerima Notifikasi Event
* Melihat Timeline Event
* Melaksanakan Tugas Event

---

# Tech Stack

### Backend

* Laravel 12
* PHP

### Frontend

* Blade Template
* Bootstrap 5
* HTML5
* CSS
* JavaScript

### Database

* MySQL

### Development Tools

* Composer
* Node.js
* NPM
* Git
* GitHub

---

# Instalasi Project

## Clone Repository

```bash
git clone https://github.com/username/alpha-corp.git
```

## Masuk ke Folder Project

```bash
cd Kelompok6_PBL2D
```

## Install Dependency PHP

```bash
composer install
```

## Install Dependency Frontend

```bash
npm install
```

## Copy Environment

```bash
cp .env.example .env
```

## Generate Application Key

```bash
php artisan key:generate
```

## Konfigurasi Database

Edit file `.env`

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=alpha_corp
DB_USERNAME=root
DB_PASSWORD=
```

## Jalankan Migration

```bash
php artisan migrate
```

## Jalankan Server

```bash
php artisan serve
```

## Jalankan Vite

```bash
npm run dev
```

---

# Screenshot Proyek

## Public Website

### Beranda
![Beranda](https://github.com/user-attachments/assets/2dad3a34-65da-4fb9-84eb-259733c660cb)

### Tentang Kami
![Tentang Kami](https://github.com/user-attachments/assets/5a5792f9-d870-4fdd-9364-3395c4a40f35)

### Layanan Event
![Layanan](https://github.com/user-attachments/assets/4a0ae9a1-1baa-4a7e-887e-07e901f0a276)

### Portofolio Event
![Portofolio](https://github.com/user-attachments/assets/cccee5b1-1793-42da-ae40-298e8f37843a)

### Tim Kami
![Tim](https://github.com/user-attachments/assets/75b72ac5-5c06-4af3-a29e-6daa9cb611a4)

## Authentication

### Halaman Login
![Login](https://github.com/user-attachments/assets/cb70990f-53cd-4c21-bc94-a12744e748b7)

### Halaman Register
![Register](https://github.com/user-attachments/assets/eb5ba378-1cdb-4cd8-b175-dfa36cecec41)

## Client Portal

### Dashboard Klien
![Dashboard Klien](https://github.com/user-attachments/assets/6fecf68c-540c-401d-9857-117897dd4f95)

### Pemesanan Layanan Event
![Pemesanan](https://github.com/user-attachments/assets/26b050f0-95bb-4040-bc8e-e8b683ff99b7)

### Surat Penawaran
![Surat Penawaran](https://github.com/user-attachments/assets/c8bbc7b2-f6bb-4778-9191-59222c599c8a)

## Admin Portal

### Dashboard Admin
![Dashboard Admin](https://github.com/user-attachments/assets/4096c388-bca3-477a-ab08-b9f2e0569b54)

### Kelola Layanan Event
![Kelola Event](https://github.com/user-attachments/assets/e671a4f9-9005-49be-8532-ed7b1c11f3be)

### Kelola Vendor
![Kelola Vendor](https://github.com/user-attachments/assets/9e22bfb7-767e-44ee-b5c4-36b4c7c5b199)

### Kelola RAB
![Kelola RAB](https://github.com/user-attachments/assets/12eb2215-d5bd-4fea-86a6-f2763ca7ca90)

## Vendor Portal

### Dashboard Vendor
![Dashboard Vendor](https://github.com/user-attachments/assets/1c3446d6-3e73-4182-924b-a1bfb17d1501)

### Tugas Event
![Tugas Event](https://github.com/user-attachments/assets/19d60594-3d8c-410f-9505-3e7d4b62542b)# Tim Pengembang

* Ahmad Ridho Hadaffi  : Project Manager
* Salwa Febriani       : System Analyst
* Muhammad Rafi        : Lead Programmer
* Restia Amelia        : QA Tester

---

# Status Project

- On Development

Project ini sedang dikembangkan sebagai bagian dari tugas/proyek Sistem Informasi Manajemen Event Alpha.corp.

---

# Pengujian (Testing)

## Konvensi Test Database

Test suite (khususnya DDMS) **wajib menggunakan MySQL**, bukan SQLite.

- Nama database test: `alpha_corp_test`
- Konfigurasi test sudah tersedia di `.env.testing` (MySQL).
- `phpunit.xml` masih mengarah ke `sqlite :memory:`, tetapi **tidak kompatibel** dengan rantai migration yang ada (ada migration MySQL-only, misalnya `ALTER TABLE users MODIFY COLUMN role ENUM(...)`). Jangan ubah migration hanya untuk menyesuaikan SQLite.

## Cara Menjalankan Test

Test harus dijalankan **sekuensial** (jangan paralel terhadap database test yang sama).

Jalankan dengan environment override MySQL agar sesuai `.env.testing`:

```bash
DB_CONNECTION=mysql DB_DATABASE=alpha_corp_test DB_USERNAME=root DB_PASSWORD= \
php artisan test --env=testing
```

Contoh untuk suite spesifik:

```bash
DB_CONNECTION=mysql DB_DATABASE=alpha_corp_test DB_USERNAME=root DB_PASSWORD= \
php artisan test tests/Feature/DDMS --env=testing
```

Catatan:
- Jika MySQL belum aktif, test tidak dapat dijalankan — hidupkan dulu MySQL lokal.
- Jangan mengubah `phpunit.xml` / `.env.testing` / migration untuk memaksa test lewat.
- Belum ada CI di repository ini.

---
