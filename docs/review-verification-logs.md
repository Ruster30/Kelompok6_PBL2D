# Final Architecture Review — document_verification_logs

**Project:** Event Management System (Laravel 12)
**Module:** Digital Document Management System (DDMS)
**Reviewer:** Senior Software Architect & Laravel Enterprise Architect
**Tanggal:** 25 Juli 2026

---

## 1. Ringkasan Struktur Saat Ini

| Kolom | Tipe | Nullable | Fungsi |
|---|---|---|---|
| id | bigint PK | Tidak | Primary Key |
| verification_id | bigint FK | Tidak | Relasi ke QR |
| verified_at | timestamp | Tidak | Waktu scan |
| status | string(20) | Tidak | Hasil verifikasi |
| ip_address | string(45) | Ya | IP pengakses |
| user_agent | text | Ya | Browser/perangkat |
| verified_by | bigint FK | Ya | User (jika login) |
| created_at | timestamp | Ya | Laravel |
| updated_at | timestamp | Ya | Laravel |

---

## 2. Review Poin 1 — Status Values

### Status Saat Ini

| Status | Makna |
|---|---|
| `valid` | Token ditemukan, dokumen valid, QR berlaku |
| `expired` | Token ditemukan, QR melewati batas waktu |
| `invalid` | Token ditemukan tetapi data tidak valid |
| `tampered` | Indikasi manipulasi QR/dokumen |

### Analisis

| Status tambahan | Rekomendasi | Alasan |
|---|---|---|
| `not_found` | **Tidak ditambahkan** | Token tidak ditemukan di database = 404 response, bukan log entry. Log hanya dibuat jika token valid dan diverifikasi. |
| `revoked` | **Belum diperlukan** | Mekanisme revoke dokumen belum ada di arsitektur DDMS v1.0. Jika ditambahkan nanti, string status bisa diperluas tanpa migration. |
| `blocked` | **Belum diperlukan** | Sama dengan revoked — belum ada mekanisme block di v1.0. |

**Kesimpulan: 4 status saat ini SUDAH CUKUP untuk v1.0.**

Karena status menggunakan `string(20)` (bukan enum), penambahan status baru di masa depan tidak memerlukan migration — cukup tambah validasi di FormRequest/Service Layer.

---

## 3. Review Poin 2 — verification_source

### Analisis

Saat ini tabel tidak menyimpan informasi **dari mana** verifikasi berasal. Ini adalah gap untuk enterprise audit trail.

| Sumber | Kapan terjadi | Nilai audit |
|---|---|---|
| `public` | Scan publik via halaman `/verify/{token}` | Mengetahui rasio scan publik |
| `admin` | Verifikasi dari panel admin | Mendeteksi admin yang sering verifikasi |
| `api` | Integrasi third-party via API | Monitoring penggunaan API |
| `mobile` | Verifikasi dari mobile app | Tracking adopsi mobile |
| `system` | Batch job / cron | Deteksi anomali sistem |

### Keuntungan vs Kerugian

| Keuntungan | Kerugian |
|---|---|
| Audit trail lengkap — tahu sumber verifikasi | 1 kolom tambahan (minimal overhead) |
| Security monitoring — deteksi pola mencurigakan | Harus diisi oleh setiap channel (disiplin engineering) |
| Business intelligence — channel paling aktif | - |
| Troubleshooting — tahu sumber masalah | - |

### Rekomendasi: TAMBAHKAN kolom `source`

```php
$table->string('source', 20)->default('public');
```

- Default `public` — backward compatible (scan publik adalah channel utama)
- `string(20)` — konsisten dengan prinsip non-enum DDMS
- Nullable tidak diperlukan karena setiap verifikasi pasti punya sumber

---

## 4. Review Poin 3 — Metadata Lain

| Metadata | Rekomendasi | Alasan |
|---|---|---|
| `country` / `city` | **Tidak** | Data geolocation lebih baik di-handle aplikasi (GeoIP service), bukan database. Data bisa berubah dan tidak reliabel. |
| `duration_ms` | **Tidak** | Terlalu teknis untuk tabel audit. Cukup di log aplikasi (monitoring). |
| `device_fingerprint` | **Tidak** | Kompleksitas tinggi, manfaat rendah untuk v1.0. Privasi juga perlu dipertimbangkan. |
| `notes` / `reason` | **Tidak** | Tidak ada use case bisnis yang membutuhkan catatan per verifikasi. Status sudah cukup menjelaskan hasil. |
| `document_snapshot` | **Tidak** | Melanggar 3NF — data dokumen ada di tabel documents. Snapshot bisa diambil via join. |

**Kesimpulan: Tidak ada metadata tambahan yang diperlukan selain `source`.**

---

## 5. Evaluasi Arsitektur

| Prinsip | Status | Catatan |
|---|---|---|
| **Audit Trail** | ✅ Baik | Setelah ditambah `source`: siapa, kapan, dari mana, hasil, perangkat — semua tercatat. |
| **3NF** | ✅ Lolos | Kolom atomik. Log terpisah dari QR. Tidak ada data berulang. |
| **Enterprise Architecture** | ⚠️ Perlu `source` | Tanpa `source`, audit trail tidak lengkap. Tidak tahu apakah verifikasi dari publik, admin, atau API. |
| **Future Proof** | ✅ Baik | status string (mudah ditambah). user_agent text. ip_address 45 support IPv6. |
| **Separation of Responsibility** | ✅ Lolos | Log (data dinamis) terpisah dari QR (data statis). |
| **Laravel Best Practice** | ✅ Lolos | foreignId, cascadeOnDelete, nullOnDelete, named index, useCurrent. |

---

## 6. Dampak Perubahan

### Jika `source` ditambahkan:

| Aspek | Dampak |
|---|---|
| Migration | Update file existing (belum dijalankan) — aman |
| Migration dependency | Tidak ada — hanya 1 kolom baru |
| Data existing | Default `public` — semua log existing otomatis terisi |
| Backward compatibility | 100% — kolom baru dengan default value |
| Rollback | `dropColumn('source')` di down() |

---

## 7. Keputusan Akhir

### Perlu revisi kecil sebelum Phase 4.

**Tambahkan 1 kolom:**

```php
$table->string('source', 20)->default('public');
```

Setelah `verified_by`, sebelum `timestamps`.

**Struktur final (9 kolom + timestamps = 11 kolom):**

```
document_verification_logs
├── id
├── verification_id (FK, CASCADE)
├── verified_at
├── status              (string: valid, expired, invalid, tampered)
├── ip_address          (nullable)
├── user_agent          (nullable)
├── verified_by         (FK, nullable, SET NULL)
├── source              (string: public, admin, api, mobile, system)  ← BARU
├── created_at
└── updated_at
```

### Tidak perlu diubah:
- Status values — 4 sudah cukup. Perluasan via string tanpa migration.
- Metadata lain — tidak ada yang cukup urgent untuk v1.0.
- Index — sudah optimal.

### Siap lanjut ke Phase 4 (Model & Relationship) setelah revisi migration.
