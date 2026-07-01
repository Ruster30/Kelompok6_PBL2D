
**1. Tujuan**

GitHub Actions digunakan untuk melakukan proses Continuous Integration (CI) secara otomatis setiap kali terdapat perubahan pada repository.

Pipeline memastikan bahwa perubahan kode hasil refactoring tetap memiliki kualitas yang baik sebelum digabungkan ke branch utama.

**2. Workflow**
Developer

↓

Push

↓

GitHub

↓

GitHub Actions

↓

Install Dependencies

↓

PHP Syntax Check

↓

Laravel Tests

↓

Success / Failed
3. Trigger

Workflow dijalankan ketika

push

pull_request

pada branch

main

dev


**4. Tahapan Workflow**

Checkout Repository
actions/checkout

Mengambil source code terbaru.

Setup PHP
shivammathur/setup-php

Menginstall PHP.

Install Composer
composer install

Menginstall seluruh dependency Laravel.

Environment
cp .env.example .env

php artisan key:generate

Membuat environment Laravel.

PHP Lint
find app -name "*.php"

↓

php -l

Memastikan seluruh file PHP tidak memiliki syntax error.

Ini sesuai dengan yang selalu kita cek setiap selesai refactoring.

PHPUnit
php artisan test

Menjalankan seluruh test Laravel.

**5. Hasil**

Jika semua tahapan berhasil

✅ Build Success

Jika terdapat syntax error

❌ Build Failed
6. Workflow Diagram
Push

↓

Checkout

↓

Setup PHP

↓

Composer Install

↓

Generate Key

↓

PHP Lint

↓

Laravel Test

↓

Success

**7. Manfaat GitHub Actions**

Otomatis memeriksa kualitas kode.
Mendeteksi syntax error sebelum merge.
Memastikan seluruh perubahan hasil refactoring tetap berjalan.
Mendukung proses Continuous Integration (CI).
Mengurangi risiko kesalahan pada branch utama.
