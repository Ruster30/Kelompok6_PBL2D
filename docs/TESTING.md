# Panduan Testing

Project ini menggunakan [Pest PHP](https://pestphp.com) sebagai framework testing dengan struktur folder yang dikelompokkan berdasarkan modul.

## Struktur Folder

```
tests/
├── Pest.php                 # Konfigurasi global Pest
├── TestCase.php              # Base TestCase untuk PHPUnit
├── Unit/                     # Test unit (tanpa booting penuh Laravel)
│   ├── Models/               # Test untuk model (Event, User, Vendor, dll.)
│   ├── Services/             # Test untuk service (RabService, TimelineAutoFill, dll.)
│   └── ExampleTest.php       # Contoh test unit
└── Feature/                  # Test fitur (dengan booting penuh Laravel)
    ├── Admin/                # Test untuk modul admin
    ├── Auth/                 # Test untuk autentikasi dan registrasi
    ├── Client/               # Test untuk modul client
    ├── Vendor/               # Test untuk modul vendor
    ├── ExampleTest.php       # Contoh test fitur
    └── ...
```

## Menjalankan Seluruh Test

```bash
composer test
```

Perintah ini akan menjalankan semua test (Unit dan Feature) sekaligus.

## Menjalankan Test per Modul

Setiap modul dapat dijalankan secara terpisah menggunakan perintah `composer test:<modul>`:

| Perintah | Modul |
|---|---|
| `composer test:unit` | Semua Unit test |
| `composer test:models` | Unit test untuk Models |
| `composer test:services` | Unit test untuk Services |
| `composer test:feature` | Semua Feature test |
| `composer test:admin` | Feature test untuk Admin |
| `composer test:client` | Feature test untuk Client |
| `composer test:vendor` | Feature test untuk Vendor |
| `composer test:auth` | Feature test untuk Auth |

Atau menggunakan Artisan secara langsung:

```bash
php artisan test --testsuite=Admin
php artisan test --testsuite=Models
```

## Menambahkan Test Baru

1. Tentukan modul yang sesuai dengan fitur yang akan diuji:
   - **Models**: test untuk relasi, accessor, attribute model
   - **Services**: test untuk logika bisnis di service layer
   - **Admin**: test untuk fitur yang membutuhkan role admin
   - **Client**: test untuk fitur yang diakses oleh client
   - **Vendor**: test untuk fitur yang diakses oleh vendor
   - **Auth**: test untuk login, register, verifikasi email, reset password

2. Buat file test di direktori yang sesuai:

   ```bash
   php artisan make:test Models/EventTest --unit
   php artisan make:test Services/RabServiceTest --unit
   php artisan make:test Admin/VendorManagementTest
   ```

3. Untuk test dengan gaya Pest, gunakan fungsi `test()` atau `it()`:

   ```php
   <?php

   use App\Models\User;
   use Illuminate\Foundation\Testing\RefreshDatabase;

   uses(RefreshDatabase::class);

   test('admin dapat melihat daftar vendor', function () {
       // ...
   });
   ```

4. Jalankan test untuk memastikan semuanya berfungsi:

   ```bash
   composer test                  # Semua test
   composer test:admin            # Hanya test admin
   php artisan test --filter=nama_test  # Test spesifik
   ```

## Catatan

- Konfigurasi testsuite terdapat di `phpunit.xml`
- Test database menggunakan SQLite in-memory (`:memory:`)
- Namespace test mengikuti PSR-4 dengan prefix `Tests\`
