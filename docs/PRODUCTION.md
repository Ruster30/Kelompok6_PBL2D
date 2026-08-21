# Panduan Deployment Produksi

Dokumen ini berisi panduan penyiapan environment **produksi** untuk ALPHA.CORP (Laravel 12).

> Semua nilai pada dokumen ini adalah **placeholder** — jangan pernah menulis kredensial,
> password, API key, APP_KEY, atau SMTP password asli ke dalam file yang ter-versioning di Git.

## 1. Konfigurasi Environment Produksi

Buat file `.env` produksi (tidak boleh di-commit ke Git) dengan nilai inti berikut:

```dotenv
APP_NAME="Alpha Organizer"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://<production-domain>

# Database produksi — melalui environment variables
DB_CONNECTION=mysql
DB_HOST=<db-host>
DB_PORT=3306
DB_DATABASE=<db-name>
DB_USERNAME=<db-user>
DB_PASSWORD=<db-password>

# APP_KEY unik, hanya dibuat sekali, khusus produksi
# (generate: php artisan key:generate)
APP_KEY=

# SMTP / mail produksi — melalui environment variables
MAIL_MAILER=smtp
MAIL_HOST=<smtp-host>
MAIL_PORT=587
MAIL_USERNAME=<smtp-username>
MAIL_PASSWORD=<smtp-password>
MAIL_FROM_ADDRESS=<noreply@production-domain>
MAIL_FROM_NAME="Alpha Organizer"

# Logging rotasi harian
LOG_CHANNEL=daily
```

Catatan wajib:

- `APP_ENV=production`, `APP_DEBUG=false` — menonaktifkan stack trace/debug di produksi.
- `APP_URL` harus domain produksi dengan HTTPS.
- Kredensial DB/SMTP dikelola melalui environment variables (tidak pernah di-commit).
- `APP_KEY` dibuat unik per environment dengan `php artisan key:generate`.

## 2. Persiapan Cache, Route, dan View

Jalankan perintah berikut sebagai bagian dari proses deploy (setelah kode di-deploy
dan sebelum aplikasi menerima trafik):

```bash
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

atau perintah gabungan:

```bash
php artisan optimize
```

> `php artisan storage:link` wajib agar PDF/QR di `storage/app/public` dapat diakses publik.

## 3. Deployment Migration

Proses migration **dipisahkan** dari persiapan cache/build.

Jalankan migration produksi secara eksplisit:

```bash
php artisan migrate --force
```

**DILARANG** menjalankan perintah destruktif terhadap produksi:

```bash
# DILARANG:
php artisan migrate:fresh
php artisan migrate:refresh
php artisan migrate:reset
php artisan db:wipe
php artisan db:seed
```

> `db:seed` **dilarang** di produksi karena seeder berisi kredensial development (lihat §6).

## 4. Logging Produksi

Gunakan rotasi harian agar log tidak membesar tanpa batas:

```dotenv
LOG_CHANNEL=daily
```

Tidak ada perubahan arsitektur logging; hanya konfigurasi channel.

## 5. Reverse Proxy / HTTPS

Jika aplikasi berjalan di belakang reverse proxy / load balancer dengan terminasi HTTPS
(nginx, Apache, Cloudflare, dsb.):

- Konfigurasi **trusted proxies** wajib disesuaikan dengan infrastruktur aktual.
- Jangan mengasumsikan IP proxy — set `TRUSTED_PROXIES` sesuai IP/range proxy asli
  pada environment produksi, misalnya:

```dotenv
TRUSTED_PROXIES=<ip-range-atau-list-ip-proxy-actual>
```

- Pastikan semua lalu lintas menggunakan HTTPS dan arahkan HTTP ke HTTPS di level web server.

## 6. Keamanan Seeder

Seeder berikut berisi **kredensial development/demo** (password plaintext):

- `database/seeders/AdminSeeder.php`
- `database/seeders/ClientUserSeeder.php`
- `database/seeders/DirectorSeeder.php`
- `database/seeders/VendorSeeder.php`

Aturan:

- Seeder ini **TIDAK boleh dieksekusi terhadap produksi**.
- Pengguna produksi dibuat melalui prosedur manajemen akun produksi yang disetujui
  (bukan via seeder).
- Kredensial produksi tidak boleh di-commit ke Git.
- Perilaku seeder untuk pengembangan/test **tidak diubah**.

## 7. Backup dan Recovery

### Status Saat Ini

**Belum ada sistem backup otomatis** pada project ini. Tidak ada skrip/job backup
yang terpasang. Bagian berikut adalah **prosedur yang direkomendasikan**, bukan
infrastruktur yang sudah ada.

### 7.1 Backup Database (MySQL)

```bash
mysqldump -u <user> -p <db-name> > backup/db-<tanggal>.sql
```

Rekomendasi frekuensi: **harian** (atau sesuai SLA data).

### 7.2 Backup Storage (PDF, QR, dokumen)

Seluruh artefak tersimpan di `storage/app/public` (subfolder `documents`, `document-qr`,
`proposals`, dll.) serta `storage/app/private`.

```bash
tar -czf backup/storage-<tanggal>.tar.gz storage/app
```

Rekomendasi frekuensi: **harian**, disinkronkan bersama backup database agar konsisten.

### 7.3 Prosedur Restore

1. Restore database: `mysql -u <user> -p <db-name> < backup/db-<tanggal>.sql`
2. Restore storage: ekstrak `storage-<tanggal>.tar.gz` ke lokasi storage aplikasi.
3. Pastikan symlink `public/storage` tetap ada: `php artisan storage:link`.
4. Verifikasi: akses halaman public verification (`/verify/{token}`) dengan dokumen
   yang dipulihkan untuk memastikan PDF/QR terbaca.

### 7.4 Verifikasi Backup

- **Backup hanya valid jika bisa di-restore.** Lakukan uji restore berkala
  (misal bulanan) ke environment staging.
- Periksa integritas backup (ukuran file, jumlah record) setelah setiap pembuatan.
