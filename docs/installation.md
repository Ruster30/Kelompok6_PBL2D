# Installation Guide

## Persyaratan Sistem

* PHP 8.3+
* Composer
* Node.js
* NPM
* MySQL
* Git

---

## Langkah Instalasi

### 1. Clone Repository

```bash
https://github.com/Ruster30/Kelompok6_PBL2D.git
cd Kelompok6_PBL2D
```

### 2. Install Dependency Backend

```bash
composer install
```

### 3. Install Dependency Frontend

```bash
npm install
```

### 4. Setup Environment

```bash
cp .env.example .env
php artisan key:generate
```

### 5. Konfigurasi Database

Edit file .env

```env
DB_DATABASE=alpha_corp
DB_USERNAME=root
DB_PASSWORD=
```

### 6. Jalankan Migration

```bash
php artisan migrate
```

### 7. Jalankan Aplikasi

```bash
php artisan serve
npm run dev
```

---

## Troubleshooting

Jika terjadi error permission:

```bash
chmod -R 775 storage bootstrap/cache
```

Jika cache bermasalah:

```bash
php artisan optimize:clear
```
