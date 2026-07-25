# Desain Tabel document_qr_verifications - Phase 2C DDMS (FINAL)

**Project:** Event Management System (Laravel 12)
**Module:** Digital Document Management System (DDMS)
**Tanggal:** 25 Juli 2026
**Author:** Senior Software Architect & Database Architect
**Status:** FINAL - APPROVED WITH MINOR REVISION

---

## 1. Daftar Perubahan

| No | Aspek | Sebelum | Sesudah | Alasan |
|---|---|---|---|---|
| 1 | generated_by ON DELETE | SET NULL | RESTRICT | Konsisten dengan document_numberings. Preservasi audit trail. Kompatibel SoftDeletes. |
| 2 | verification_token tipe | varchar(64) | char(32) | Token selalu 32 hex chars. Fixed-length lebih efisien untuk index UNIQUE. |

**Tidak ada perubahan lain.** Struktur tetap 9 kolom. FK, unique, index lain tidak berubah.

---

## 2. Keputusan generated_by: SET NULL -> RESTRICT

| Aspek | SET NULL | RESTRICT |
|---|---|---|
| Audit trail | generated_by hilang jika user dihapus | generated_by tetap ada |
| Konsistensi numbering | Berbeda (numberings: RESTRICT) | Sama (numberings: RESTRICT) |
| Soft Delete | Tidak relevan | Kompatibel (soft delete tidak hapus record) |
| QR functionality | QR tetap berfungsi | QR tetap berfungsi |
| Enterprise standard | Audit trail bisa hilang | Audit trail terjamin |

**Rekomendasi: RESTRICT.** Konsisten dengan document_numberings, preservasi audit trail, kompatibel dengan soft delete.

---

## 3. Keputusan verification_token: varchar(64) -> char(32)

| Aspek | varchar(64) | char(32) |
|---|---|---|
| Storage per baris | 33-65 bytes | 32 bytes fixed |
| Index performa | Baik | Lebih baik (fixed-width) |
| Fleksibilitas | Bisa tampung token 0-64 char | Hanya 32 char (fixed) |
| Perubahan algoritma | Siap jika token 64 char | Perlu ALTER TABLE |
| Semantik | Variable-length (tidak sesuai) | Fixed-length (sesuai) |

**Analisis:** Token selalu 32 hex chars dari bin2hex(random_bytes(16)). Tidak ada variasi panjang. char(32) lebih tepat untuk data fixed-length, memberikan performa index UNIQUE lebih baik, dan storage lebih efisien. Risiko perubahan algoritma rendah; ALTER TABLE mudah jika diperlukan.

**Rekomendasi: char(32)**

---

## 4. Struktur Tabel Final

| No | Kolom | Tipe Data | Nullable | Default | Constraint |
|---|---|---|---|---|---|
| 1 | id | bigint PK AI | Tidak | - | PRIMARY |
| 2 | document_id | bigint FK | Tidak | - | UNIQUE, FK -> documents CASCADE |
| 3 | verification_token | char(32) | Tidak | - | UNIQUE |
| 4 | qr_path | varchar(255) | Tidak | - | - |
| 5 | generated_by | bigint FK | Tidak | - | FK -> users RESTRICT |
| 6 | generated_at | timestamp | Tidak | CURRENT_TIMESTAMP | - |
| 7 | expires_at | timestamp | Ya | NULL | - |
| 8 | created_at | timestamp | Ya | NULL | - |
| 9 | updated_at | timestamp | Ya | NULL | - |

---

## 5. Foreign Key

| FK | Reference | ON DELETE | ON UPDATE |
|---|---|---|---|
| document_id | documents(id) | CASCADE | CASCADE |
| generated_by | users(id) | RESTRICT | CASCADE |

---

## 6. Unique Constraint

| Kolom | Fungsi |
|---|---|
| document_id | Satu dokumen = satu QR |
| verification_token | Tidak ada duplikasi token |

---

## 7. Index

| Nama Index | Tipe | Kolom | Auto? |
|---|---|---|---|
| (auto) | UNIQUE | document_id | Ya (foreignId()->unique()) |
| (auto) | UNIQUE | verification_token | Ya (char()->unique()) |
| (auto) | Biasa | document_id | Ya (foreignId()->unique()) |
| (auto) | Biasa | generated_by | Ya (foreignId()->constrained()) |
| (opsional) | Biasa | expires_at | Tidak (untuk future) |

**Catatan:** Tidak ada index manual. Semua auto dari Laravel.

---

## 8. Validasi Arsitektur

| Prinsip | Status |
|---|---|
| Additive & Non-Disruptive | Lolos |
| Backward Compatible | Lolos |
| 3NF | Lolos |
| Repository Pattern Ready | Lolos |
| Service Layer Ready | Lolos |
| Enterprise Ready | Lolos |
| Maintainable | Lolos |

---

## 9. Kesimpulan

| Aspek | Status |
|---|---|
| generated_by | RESTRICT (konsisten dengan numberings) |
| verification_token | char(32) (fixed-length, optimal) |
| Struktur final | 9 kolom, 2 FK, 2 UNIQUE |
| Risiko | Rendah |
| Siap implementasi migration | Ya |
