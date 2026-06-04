# 📘 Panduan Kolaborasi Tim (GitHub Workflow)

## 🎯 Tujuan

Dokumen ini dibuat untuk memastikan semua anggota tim bekerja secara terstruktur, rapi, dan mudah dipantau kontribusinya.

---

# 🧩 Struktur Branch

## 🔹 Branch Utama

* `main` → versi final (production)
* `dev` → penggabungan semua fitur

## 🔹 Branch Per Orang

Setiap anggota memiliki branch masing-masing:

* `rafi`
* `hadaffi`
* `salwa`
* `restia`

👉 Digunakan untuk tracking kontribusi individu.

## 🔹 Branch Feature (Sementara)

Digunakan untuk mengerjakan fitur tertentu:

* `feature/login`
* `feature/register`
* `feature/dashboard`

👉 Akan dihapus setelah selesai.

---

# 🚀 Setup Awal (Sekali Saja)

## 1. Buat branch `dev`

```bash
git checkout main
git pull

git checkout -b dev
git push origin dev
```

## 2. Buat branch per orang

```bash
git checkout dev
git checkout -b dev/nama
git push origin dev/nama
```

---

# 🪜 Alur Kerja Harian

## 1. Ambil update terbaru dari `dev`

```bash
git checkout dev
git pull
```

## 2. Masuk ke branch pribadi

```bash
git checkout nama_branch
git pull
```

## 3. Buat branch fitur

```bash
git checkout -b feature/nama-fitur
```

## 4. Kerjakan fitur

```bash
git add .
git commit -m "deskripsi perubahan"
git push origin feature/nama-fitur
```

---

# 🔗 Proses Pull Request (PR) & Merge

## 🎯 Tujuan

PR digunakan untuk menggabungkan kode dengan aman melalui proses review.

---

## 🪜 Langkah Membuat Pull Request (PR)

### 1. Push branch ke GitHub

```bash
git push origin nama-branch
```

### 2. Buka GitHub

* Masuk ke repository
* Klik **Compare & pull request**

### 3. Atur PR

* **base**: tujuan merge
* **compare**: branch kamu

Contoh:

* `feature/login` → `nama_branch`
* `nama_branch` → `dev`
* `dev` → `main`

---

## 🔄 Alur PR dalam Tim

### 1. Feature → Dev Pribadi

* base: `nama_branch`
* compare: `feature/nama-fitur`

### 2. Dev Pribadi → Dev

* base: `dev`
* compare: `nama_branch`

### 3. Dev → Main

* base: `main`
* compare: `dev`

---

## ✅ Aturan Merge

* PR harus direview minimal 1 orang
* Pastikan tidak ada conflict
* Pastikan fitur sudah berjalan dengan baik
* Gunakan tombol **Merge Pull Request** di GitHub

---

## ⚠️ Jika Terjadi Conflict

1. Checkout branch kamu

```bash
git checkout nama_branch
```

2. Ambil update terbaru

```bash
git pull origin dev
```

3. Perbaiki conflict di file

4. Commit ulang

```bash
git add .
git commit -m "fix conflict"
git push
```

👉 PR akan otomatis ter-update

---

# 🔄 Update Setelah Merge

```bash
git checkout dev
git pull

git checkout nama_branch
git merge dev
git push
```

---

# 🔧 Perbaikan & Perubahan Feature

## 🟢 Jika Feature BELUM di-merge

Gunakan branch feature yang sama (tidak perlu buat baru).

```bash
git checkout feature/nama-fitur
# lakukan perbaikan

git add .
git commit -m "fix / update fitur"
git push
```

👉 Pull Request (PR) akan otomatis ter-update.

---

## 🟡 Jika SUDAH PR tapi diminta revisi

Tetap gunakan branch feature yang sama.

```bash
git checkout feature/nama-fitur
# perbaiki sesuai review

git add .
git commit -m "revisi sesuai review"
git push
```

👉 Tidak perlu buat PR baru.

---

## 🔴 Jika Feature SUDAH di-merge

JANGAN gunakan branch lama.

Buat branch baru dari `dev`:

```bash
git checkout dev
git pull

git checkout -b fix/nama-fitur
```

Lanjutkan:

```bash
git add .
git commit -m "fix bug fitur"
git push origin fix/nama-fitur
```

---

## 📌 Aturan Penting

* Gunakan branch yang sama jika feature belum selesai
* Buat branch baru jika sudah di-merge
* Jangan edit langsung di `dev` atau `main`

---

# 📜 Aturan Wajib

## ❌ Dilarang

* Push langsung ke `main`
* Merge tanpa Pull Request
* Mengubah branch orang lain
* Lompat merge (feature → main)

## ✅ Wajib

* Semua kerja dari `dev`
* Gunakan branch `feature/...`
* Gunakan Pull Request untuk merge
* Commit dengan pesan jelas

---

# 🎯 Alur Singkat

```
main
 ↑
dev
 ↑
nama_branch
 ↑
feature/nama-fitur
```

---

# Struktur Folder
project-eo/
│
├── app/
│   │
│   ├── Http/
│   │   │
│   │   ├── Controllers/
│   │   │   │
│   │   │   ├── LandingController.php
│   │   │   │
│   │   │   ├── Auth/                    # Laravel Breeze
│   │   │   │   ├── AuthenticatedSessionController.php
│   │   │   │   ├── ConfirmablePasswordController.php
│   │   │   │   ├── EmailVerificationNotificationController.php
│   │   │   │   ├── EmailVerificationPromptController.php
│   │   │   │   ├── NewPasswordController.php
│   │   │   │   ├── PasswordController.php
│   │   │   │   ├── PasswordResetLinkController.php
│   │   │   │   ├── RegisteredUserController.php
│   │   │   │   └── VerifyEmailController.php
│   │   │   │
│   │   │   ├── Admin/
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── EventController.php
│   │   │   │   ├── VendorController.php
│   │   │   │   ├── ClientController.php
│   │   │   │   └── NegotiationController.php
│   │   │   │
│   │   │   ├── Client/
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── TimelineController.php
│   │   │   │   ├── OfferLetterController.php
│   │   │   │   └── EventController.php
│   │   │   │
│   │   │   ├── Vendor/
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── ServiceController.php
│   │   │   │   ├── PortfolioController.php
│   │   │   │   └── NegotiationController.php
│   │   │   │
│   │   │   └── Event/
│   │   │       └── EventController.php
│   │   │
│   │   ├── Middleware/
│   │   │   ├── AdminMiddleware.php
│   │   │   ├── ClientMiddleware.php
│   │   │   └── VendorMiddleware.php
│   │   │
│   │   └── Requests/
│   │       ├── EventRequest.php
│   │       ├── VendorRequest.php
│   │       └── ClientRequest.php
│   │
│   ├── Models/
│   │   ├── User.php
│   │   ├── Event.php
│   │   ├── Vendor.php
│   │   ├── Client.php
│   │   ├── Negotiation.php
│   │   ├── OfferLetter.php
│   │   └── Role.php
│   │
│   └── Services/
│       ├── EventService.php
│       ├── NegotiationService.php
│       └── OfferLetterService.php
│
│
├── bootstrap/
│
├── config/
│
├── database/
│   │
│   ├── migrations/
│   │   ├── create_users_table.php
│   │   ├── create_events_table.php
│   │   ├── create_vendors_table.php
│   │   ├── create_clients_table.php
│   │   ├── create_negotiations_table.php
│   │   ├── create_offer_letters_table.php
│   │   └── add_role_to_users_table.php
│   │
│   ├── seeders/
│   │   ├── DatabaseSeeder.php
│   │   ├── UserSeeder.php
│   │   ├── AdminSeeder.php
│   │   └── RoleSeeder.php
│   │
│   └── factories/
│
│
├── public/
│   │
│   ├── images/
│   │   ├── landing/
│   │   ├── portfolio/
│   │   ├── vendor/
│   │   ├── team/
│   │   └── icons/
│   │
│   ├── css/
│   │   └── landing.css
│   │
│   ├── js/
│   │
│   └── uploads/
│       ├── profile/
│       ├── offer-letter/
│       └── event/
│
│
├── resources/
│   │
│   ├── views/
│   │   │
│   │   ├── layouts/
│   │   │   ├── app.blade.php
│   │   │   ├── guest.blade.php          # Breeze
│   │   │   ├── admin.blade.php
│   │   │   ├── client.blade.php
│   │   │   └── vendor.blade.php
│   │   │
│   │   ├── components/
│   │   │   ├── navbar.blade.php
│   │   │   ├── footer.blade.php
│   │   │   ├── hero.blade.php
│   │   │   ├── service-card.blade.php
│   │   │   ├── contact-form.blade.php
│   │   │   └── alert.blade.php
│   │   │
│   │   ├── landing/
│   │   │   ├── index.blade.php
│   │   │   ├── about.blade.php
│   │   │   ├── services.blade.php
│   │   │   ├── portfolio.blade.php
│   │   │   ├── team.blade.php
│   │   │   └── contact.blade.php
│   │   │
│   │   ├── auth/                        # Breeze
│   │   │   ├── login.blade.php
│   │   │   ├── register.blade.php
│   │   │   ├── forgot-password.blade.php
│   │   │   ├── reset-password.blade.php
│   │   │   ├── verify-email.blade.php
│   │   │   └── confirm-password.blade.php
│   │   │
│   │   ├── profile/                     # Breeze
│   │   │   ├── edit.blade.php
│   │   │   └── partials/
│   │   │
│   │   ├── admin/
│   │   │   ├── dashboard.blade.php
│   │   │   ├── event/
│   │   │   ├── vendor/
│   │   │   ├── client/
│   │   │   └── negotiation/
│   │   │
│   │   ├── client/
│   │   │   ├── dashboard.blade.php
│   │   │   ├── timeline.blade.php
│   │   │   └── offer-letter.blade.php
│   │   │
│   │   └── vendor/
│   │       ├── dashboard.blade.php
│   │       ├── portfolio.blade.php
│   │       ├── service.blade.php
│   │       └── negotiation.blade.php
│   │
│   ├── css/
│   └── js/
│
│
├── routes/
│   │
│   ├── web.php
│   ├── auth.php             # Breeze
│   ├── admin.php
│   ├── client.php
│   └── vendor.php
│
│
├── storage/
│
├── tests/
│   ├── Feature/
│   │   ├── Auth/
│   │   ├── Admin/
│   │   ├── Client/
│   │   └── Vendor/
│   │
│   └── Unit/
│
│
├── composer.json
├── package.json
├── vite.config.js
├── tailwind.config.js
└── .env

# 📌 Penutup

Dengan mengikuti aturan ini:

* Kolaborasi tim menjadi lebih rapi
* Konflik kode dapat diminimalisir
* Kontribusi tiap anggota dapat terlihat jelas
