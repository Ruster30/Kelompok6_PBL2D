# Dependency / Package Laravel  
## Project Website Event Organizer (EO)

Nama Project: **Sistem Event Organizer Alpha.corp**

---

# 1. Laravel Breeze

## What
Laravel Breeze adalah package autentikasi sederhana bawaan Laravel untuk login, register, logout, dan manajemen session pengguna.

## Why
Digunakan untuk mempermudah pembuatan sistem autentikasi pada website EO tanpa membuat login dari awal.

## Who
Digunakan oleh:
- Admin
- Klien
- Vendor

## When
Digunakan saat pengguna ingin masuk atau mendaftar ke sistem.

## Where
Digunakan pada:
- Halaman Login
- Register
- Dashboard pengguna

## How
Install menggunakan Composer:

```bash
composer require laravel/breeze --dev
php artisan breeze:install
npm install && npm run dev
php artisan migrate
```

## Referensi
- https://laravel.com/docs/starter-kits#laravel-breeze

---

# 2. Spatie Laravel Permission

## What
Package untuk mengatur role dan permission pengguna.

## Why
Karena sistem EO memiliki beberapa role:
- Admin
- Klien
- Vendor

## Who
Digunakan oleh admin sistem.

## When
Saat membatasi akses fitur berdasarkan role pengguna.

## Where
Digunakan pada:
- Dashboard admin
- Hak akses fitur
- Manajemen user

## How

```bash
composer require spatie/laravel-permission
```

## Referensi
- https://spatie.be/docs/laravel-permission/v6/introduction

---

# 3. Laravel DomPDF

## What
Package untuk membuat file PDF dari data Laravel.

## Why
Digunakan untuk generate:
- Proposal
- RAB
- Invoice
- Kontrak
- Laporan akhir event

## Who
Digunakan oleh admin dan klien.

## When
Saat sistem menghasilkan dokumen resmi.

## Where
Digunakan pada fitur:
- Generate proposal
- Generate invoice
- Generate laporan

## How

```bash
composer require barryvdh/laravel-dompdf
```

## Referensi
- https://github.com/barryvdh/laravel-dompdf

---

# 4. Intervention Image

## What
Package untuk upload dan manipulasi gambar.

## Why
Digunakan untuk mengelola:
- Dokumentasi event
- Poster event
- Bukti pembayaran

## Who
Digunakan oleh admin dan klien.

## When
Saat upload gambar ke sistem.

## Where
Digunakan pada:
- Dokumentasi event
- Upload pembayaran

## How

```bash
composer require intervention/image
```

## Referensi
- https://image.intervention.io

---

# 5. Laravel Notification

## What
Fitur Laravel untuk mengirim notifikasi.

## Why
Digunakan untuk memberikan informasi:
- Status pembayaran
- Timeline event
- Update event
- Konfirmasi vendor

## Who
Digunakan oleh:
- Admin
- Klien
- Vendor

## When
Saat ada perubahan data atau aktivitas event.

## Where
Digunakan pada sistem notifikasi.

## How

```bash
php artisan make:notification EventNotification
```

## Referensi
- https://laravel.com/docs/notifications

---

# 6. Laravel Excel

## What
Package export/import Excel.

## Why
Digunakan untuk:
- Export data RAB
- Export laporan event
- Rekap pembayaran

## Who
Digunakan admin.

## When
Saat membutuhkan laporan dalam format Excel.

## Where
Digunakan pada menu laporan.

## How

```bash
composer require maatwebsite/excel
```

## Referensi
- https://docs.laravel-excel.com

---

# 7. SweetAlert

## What
Library alert interaktif untuk tampilan modern.

## Why
Digunakan agar notifikasi sistem lebih menarik dan mudah dipahami pengguna.

## Who
Digunakan semua pengguna sistem.

## When
Saat:
- Berhasil login
- Hapus data
- Konfirmasi pembayaran
- Error input

## Where
Digunakan di seluruh halaman website.

## How

```bash
composer require realrashid/sweet-alert
```

## Referensi
- https://github.com/realrashid/sweet-alert

---

# 8. Midtrans PHP SDK

## What
SDK pembayaran online Midtrans.

## Why
Digunakan untuk mempermudah pembayaran event secara online.

## Who
Digunakan klien dan admin.

## When
Saat proses pembayaran event.

## Where
Digunakan pada:
- Invoice
- Pembayaran event

## How

```bash
composer require midtrans/midtrans-php
```

## Referensi
- https://docs.midtrans.com

---

# 9. Laravel File Manager

## What
Package pengelolaan file Laravel.

## Why
Digunakan untuk mengelola:
- Dokumentasi event
- File proposal
- Kontrak
- Invoice

## Who
Digunakan admin.

## When
Saat upload dan manajemen file.

## Where
Digunakan pada dashboard admin.

## How

```bash
composer require unisharp/laravel-filemanager
```

## Referensi
- https://github.com/UniSharp/laravel-filemanager

---

# 10. Carbon

## What
Library manipulasi tanggal dan waktu pada Laravel.

## Why
Digunakan untuk:
- Timeline event
- Jadwal event
- Deadline pembayaran

## Who
Digunakan sistem dan admin.

## When
Saat mengatur waktu dan tanggal event.

## Where
Digunakan pada fitur timeline event.

## How

```php
Carbon::now();
```

## Referensi
- https://carbon.nesbot.com/docs

---

# Kesimpulan

Dependency/package Laravel digunakan untuk mempercepat pengembangan sistem Event Organizer dengan menyediakan fitur siap pakai seperti autentikasi, manajemen role, pembayaran online, notifikasi, export laporan, upload dokumentasi, dan pengelolaan timeline event. Dengan menggunakan package Laravel, proses pengembangan menjadi lebih cepat, aman, dan terstruktur.
