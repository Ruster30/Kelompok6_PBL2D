# Migration Design Final

**Project:** Event Management System (Laravel 12)
**Module:** Digital Document Management System (DDMS)
**Tanggal:** 25 Juli 2026
**Author:** Senior Laravel Database Architect
**Status:** Final Design (pre-implementation)
**Referensi: Data Dictionary v2.0, Migration Plan v2.0, Logical ERD v1.0, Domain Model v1.0, DDMS Blueprint v1.0, Audit Tabel documents, Analisis Database Existing

---

## 1. Daftar Migration Baru

| No | Nama File Migration | Tipe | Dependency |
|---|---|---|---|
| 1 | `alter_documents_table` | ALTER | documents (existing) |
| 2 | `create_document_templates_table` | CREATE | - |
| 3 | `create_ddms_settings_table` | CREATE | - |
| 4 | `create_document_approvals_table` | CREATE | documents, users |
| 5 | `create_document_numberings_table` | CREATE | documents |
| 6 | `create_document_qr_verifications_table` | CREATE | documents |
| 7 | `create_document_verification_logs_table` | CREATE | document_qr_verifications |
| 8 | `create_document_send_histories_table` | CREATE | documents, users |
| 9 | `create_activity_logs_table` | CREATE | documents, users |

**Total: 9 migration (1 ALTER + 8 CREATE)**

---

## 2. Daftar Migration ALTER

### 2.1 alter_documents_table

**Alasan:** documents sudah ada dan digunakan. Migration existing tidak boleh diubah. Kolom baru ditambah via ALTER.

**Kolom baru:** status, document_category, document_type, template_id, current_version, file_size, mime_type, is_archived, updated_by, archived_at.

| No | Kolom | Tipe | Nullable | Default | Kategori |
|---|---|---|---|---|---|
| 1 | status | enum(draft,waiting_approval,approved,rejected) | Tidak | draft | WAJIB |
| 2 | document_category | varchar(50) | Tidak | general | WAJIB |
| 3 | document_type | varchar(100) | Ya | NULL | OPSIONAL |
| 4 | template_id | bigint (FK) | Ya | NULL | WAJIB |
| 5 | current_version | integer | Tidak | 1 | WAJIB |
| 6 | file_size | bigint | Ya | NULL | OPSIONAL |
| 7 | mime_type | varchar(127) | Ya | NULL | OPSIONAL |
| 8 | is_archived | boolean | Tidak | false | WAJIB |
| 9 | updated_by | bigint (FK) | Ya | NULL | OPSIONAL |
| 10 | archived_at | timestamp | Ya | NULL | OPSIONAL |

---

## 3. Spesifikasi Tabel Baru

### 3.1 document_templates

**Tujuan:** Menyimpan template Blade untuk generate dokumen.

**Fungsi:** Mendefinisikan template per jenis dokumen, menyimpan lokasi Blade view, status aktif.

| Kolom | Tipe | Nullable | Unique | Keterangan |
|---|---|---|---|---|
| id | bigint | Tidak | PK | Auto increment |
| name | varchar(255) | Tidak | Tidak | Nama template |
| document_type | varchar(100) | Tidak | Tidak | Jenis dokumen |
| blade_view | varchar(255) | Tidak | Tidak | Path Blade view |
| is_active | boolean | Tidak | Tidak | Default true |
| created_at | timestamp | Ya | Tidak | Waktu dibuat |
| updated_at | timestamp | Ya | Tidak | Waktu diubah |

**Relasi:** Di-referensi oleh documents.template_id (One-to-Many).
**PK:** id
**FK:** Tidak ada
**Index:** document_type
**Cascade:** Tidak ada

---

### 3.2 ddms_settings

**Tujuan:** Konfigurasi global DDMS (PIN approval, format numbering, base URL QR, dll).

| Kolom | Tipe | Nullable | Unique | Keterangan |
|---|---|---|---|---|
| id | bigint | Tidak | PK | Auto increment |
| setting_key | varchar(100) | Tidak | Ya | Nama konfigurasi |
| setting_value | text | Ya | Tidak | Nilai konfigurasi |
| description | text | Ya | Tidak | Keterangan |
| created_at | timestamp | Ya | Tidak | Waktu dibuat |
| updated_at | timestamp | Ya | Tidak | Waktu diubah |

**Relasi:** Tidak ada (mandiri).
**PK:** id
**FK:** Tidak ada
**Index:** UNIQUE(setting_key)
**Cascade:** Tidak ada

---

### 3.3 document_approvals

**Tujuan:** Menyimpan proses approval dokumen resmi oleh direktur.

| Kolom | Tipe | Nullable | Unique | Keterangan |
|---|---|---|---|---|---|
| id | bigint | Tidak | PK | Auto increment |
| document_id | bigint | Tidak | Tidak | FK documents |
| approver_id | bigint | Tidak | Tidak | FK users (direktur) |
| status | enum(pending,approved,rejected) | Tidak | Tidak | Status approval |
| approval_note | text | Ya | Tidak | Catatan direktur |
| approved_at | timestamp | Ya | Tidak | Waktu approval |
| created_at | timestamp | Ya | Tidak | Waktu dibuat |
| updated_at | timestamp | Ya | Tidak | Waktu diubah |

**Relasi:** document_id -> documents, approver_id -> users
**PK:** id
**FK:** document_id (CASCADE), approver_id (RESTRICT)
**Index:** document_id, approver_id, composite(document_id, status)

---

### 3.4 document_numberings

**Tujuan:** Nomor surat resmi untuk dokumen yang telah disetujui.

| Kolom | Tipe | Nullable | Unique | Keterangan |
|---|---|---|---|---|---|
| id | bigint | Tidak | PK | Auto increment |
| document_id | bigint | Tidak | Ya | FK documents |
| document_number | varchar(100) | Tidak | Ya | Nomor surat |
| generated_at | timestamp | Tidak | Tidak | Waktu generate |
| created_at | timestamp | Ya | Tidak | Waktu dibuat |
| updated_at | timestamp | Ya | Tidak | Waktu diubah |

**Relasi:** document_id -> documents (One-to-One)
**PK:** id
**FK:** document_id (CASCADE)
**Index:** UNIQUE(document_id), UNIQUE(document_number)

---

### 3.5 document_qr_verifications

**Tujuan:** Data QR Code untuk verifikasi keaslian dokumen.

| Kolom | Tipe | Nullable | Unique | Keterangan |
|---|---|---|---|---|---|
| id | bigint | Tidak | PK | Auto increment |
| document_id | bigint | Tidak | Ya | FK documents |
| verification_hash | varchar(64) | Tidak | Ya | Hash SHA-256 |
| qr_path | varchar(255) | Tidak | Tidak | Path file QR |
| generated_at | timestamp | Tidak | Tidak | Waktu generate |
| created_at | timestamp | Ya | Tidak | Waktu dibuat |
| updated_at | timestamp | Ya | Tidak | Waktu diubah |

**Relasi:** document_id -> documents (One-to-One), di-referensi oleh verification_logs
**PK:** id
**FK:** document_id (CASCADE)
**Index:** UNIQUE(document_id), UNIQUE(verification_hash)

---

### 3.6 document_verification_logs

**Tujuan:** Mencatat setiap scan QR Code.

| Kolom | Tipe | Nullable | Unique | Keterangan |
|---|---|---|---|---|---|
| id | bigint | Tidak | PK | Auto increment |
| qr_verification_id | bigint | Tidak | Tidak | FK document_qr_verifications |
| ip_address | varchar(45) | Ya | Tidak | IP client |
| browser | varchar(255) | Ya | Tidak | User-agent |
| device | varchar(255) | Ya | Tidak | Device |
| verification_status | enum(valid,invalid) | Tidak | Tidak | Hasil verifikasi |
| verified_at | timestamp | Tidak | Tidak | Waktu scan |
| created_at | timestamp | Ya | Tidak | Waktu dibuat |

**Relasi:** qr_verification_id -> document_qr_verifications
**PK:** id
**FK:** qr_verification_id (CASCADE)
**Index:** qr_verification_id, composite(qr_verification_id, verified_at)

---

### 3.7 document_send_histories

**Tujuan:** Riwayat pengiriman dokumen ke client. Melengkapi document_sends yang sudah ada.

| Kolom | Tipe | Nullable | Unique | Keterangan |
|---|---|---|---|---|---|
| id | bigint | Tidak | PK | Auto increment |
| document_id | bigint | Tidak | Tidak | FK documents |
| sender_id | bigint | Tidak | Tidak | FK users (admin) |
| receiver_id | bigint | Tidak | Tidak | FK users (client) |
| sent_at | timestamp | Tidak | Tidak | Waktu kirim |
| status | enum(sent,failed) | Tidak | Tidak | Status kirim |
| created_at | timestamp | Ya | Tidak | Waktu dibuat |

**Relasi:** document_id -> documents, sender_id -> users, receiver_id -> users
**PK:** id
**FK:** document_id (CASCADE), sender_id (SET NULL), receiver_id (SET NULL)
**Index:** document_id, sender_id, receiver_id, composite(document_id, sent_at)

---

### 3.8 activity_logs

**Tujuan:** Audit trail seluruh aktivitas DDMS.

| Kolom | Tipe | Nullable | Unique | Keterangan |
|---|---|---|---|---|---|
| id | bigint | Tidak | PK | Auto increment |
| user_id | bigint | Ya | Tidak | FK users (nullable) |
| document_id | bigint | Ya | Tidak | FK documents (nullable) |
| event | varchar(100) | Tidak | Tidak | Nama event |
| description | text | Ya | Tidak | Deskripsi |
| ip_address | varchar(45) | Ya | Tidak | IP pelaku |
| created_at | timestamp | Tidak | Tidak | Waktu aktivitas |

**Relasi:** user_id -> users, document_id -> documents
**PK:** id
**FK:** user_id (SET NULL), document_id (SET NULL)
**Index:** user_id, document_id, composite(document_id, event), created_at

---

## 4. Analisis Kolom documents (Evaluasi Akhir)

### 4.1 Kolom Existing (8 kolom)

| Kolom | Keputusan | Alasan |
|---|---|---|
| id | Dipertahankan | Primary key |
| event_id | Dipertahankan | Relasi ke events. Nullable untuk dokumen umum. |
| user_id | Dipertahankan | Pembuat/uploader. Backward compatibility. |
| nama_file | Dipertahankan | Display name. Digunakan Admin Proposal, Client, Document Builder. |
| file_path | Dipertahankan | Lokasi file storage. |
| tipe | Dipertahankan | Filter di upload form, model constants, views. |
| created_at | Dipertahankan | Standar |
| updated_at | Dipertahankan | Standar |

### 4.2 Kolom Baru (10 kolom)

| Kolom | Keputusan | Alasan |
|---|---|---|
| status | WAJIB | Workflow approval DDMS. Default draft. |
| document_category | WAJIB | Kategori official/general/invoice/receipt. |
| document_type | OPSIONAL (nullable) | Lihat 4.3 untuk analisis vs tipe. |
| template_id | WAJIB (nullable) | Relasi ke document_templates. |
| current_version | WAJIB | Counter versi. Default 1. |
| file_size | OPSIONAL (nullable) | Metadata UI. |
| mime_type | OPSIONAL (nullable) | Metadata preview. |
| is_archived | WAJIB | Fitur arsip. Default false. |
| updated_by | OPSIONAL (nullable) | Audit trail dasar. |
| archived_at | OPSIONAL (nullable) | Waktu arsip. |

### 4.3 Analisis document_type vs tipe

**Keputusan:** document_type TETAP ditambahkan sebagai kolom OPSIONAL (nullable).

**Alasan:**
- tipe = kategori storage/origin (proposal, kontrak, invoice, rab, laporan, kwitansi, lainnya).
- document_type = klasifikasi bisnis (surat_penawaran, surat_kontrak, rab, invoice_dp, invoice_pelunasan, kwitansi, proposal, company_profile, portfolio, umum).
- tipe sudah terintegrasi di seluruh kode existing. Mengubah fungsinya berisiko tinggi.
- document_type nullable sehingga kode existing tidak perlu diubah.
- Contoh: proposal dengan tipe bisa memiliki document_type = surat_penawaran.

### 4.4 Kolom Data Dictionary yang Tidak Ditambahkan

| Kolom (DD) | Alasan Tidak Ditambah | Alternatif |
|---|---|---|
| title | nama_file berfungsi sebagai title. | Gunakan nama_file |
| file_name | Bisa didapat dari nama_file atau di-parse dari file_path. | Gunakan nama_file |
| source | Bisa diinfer dari template_id. | Infer dari template_id |
| created_by | user_id (existing) sudah berfungsi. | Gunakan user_id |

---

## 5. Analisis Foreign Key

### 5.1 Tabel documents (ALTER)

| FK Column | Reference | ON DELETE | ON UPDATE |
|---|---|---|---|
| event_id (existing) | events(id) | SET NULL | CASCADE |
| user_id (existing) | users(id) | SET NULL | CASCADE |
| template_id (baru) | document_templates(id) | SET NULL | CASCADE |
| updated_by (baru) | users(id) | SET NULL | CASCADE |

### 5.2 Tabel Baru

| Tabel | FK Column | Reference | ON DELETE | ON UPDATE |
|---|---|---|---|---|
| document_approvals | document_id | documents(id) | CASCADE | CASCADE |
| document_approvals | approver_id | users(id) | RESTRICT | CASCADE |
| document_numberings | document_id | documents(id) | CASCADE | CASCADE |
| document_qr_verifications | document_id | documents(id) | CASCADE | CASCADE |
| document_verification_logs | qr_verification_id | document_qr_verifications(id) | CASCADE | CASCADE |
| document_send_histories | document_id | documents(id) | CASCADE | CASCADE |
| document_send_histories | sender_id | users(id) | SET NULL | CASCADE |
| document_send_histories | receiver_id | users(id) | SET NULL | CASCADE |
| activity_logs | user_id | users(id) | SET NULL | CASCADE |
| activity_logs | document_id | documents(id) | SET NULL | CASCADE |

### 5.3 Alasan Pemilihan

| Rule | Kapan | Contoh |
|---|---|---|
| CASCADE | Child tidak berguna tanpa parent | approvals, numberings, QR, logs |
| SET NULL | Child tetap berguna tanpa parent | event_id, user_id, template_id, sender, receiver, activity_logs |
| RESTRICT | Mencegah hapus data beregulasi | approver_id (riwayat approval) |

---

## 6. Analisis Index

### 6.1 Tabel documents (Index Baru)

| Index | Tipe | Kolom | Alasan |
|---|---|---|---|
| doc_status_idx | Biasa | status | Filter status approval |
| doc_category_idx | Biasa | document_category | Filter kategori |
| doc_archived_idx | Biasa | is_archived | Filter aktif/arsip |
| doc_template_id_idx | Biasa | template_id | Join templates |
| doc_updated_by_idx | Biasa | updated_by | Join users |

### 6.2 Tabel Baru

| Tabel | Index | Tipe | Kolom |
|---|---|---|---|
| document_templates | tmpl_doc_type_idx | Biasa | document_type |
| ddms_settings | settings_key_unique | UNIQUE | setting_key |
| document_approvals | appr_doc_id_idx | Biasa | document_id |
| document_approvals | appr_approver_idx | Biasa | approver_id |
| document_approvals | appr_doc_status_idx | COMPOSITE | (document_id, status) |
| document_numberings | num_doc_id_unique | UNIQUE | document_id |
| document_numberings | num_number_unique | UNIQUE | document_number |
| document_qr_verifications | qr_doc_id_unique | UNIQUE | document_id |
| document_qr_verifications | qr_hash_unique | UNIQUE | verification_hash |
| document_verification_logs | vlog_qr_id_idx | Biasa | qr_verification_id |
| document_verification_logs | vlog_qr_time_idx | COMPOSITE | (qr_verification_id, verified_at) |
| document_send_histories | send_doc_id_idx | Biasa | document_id |
| document_send_histories | send_sender_idx | Biasa | sender_id |
| document_send_histories | send_receiver_idx | Biasa | receiver_id |
| document_send_histories | send_doc_time_idx | COMPOSITE | (document_id, sent_at) |
| activity_logs | log_user_id_idx | Biasa | user_id |
| activity_logs | log_doc_id_idx | Biasa | document_id |
| activity_logs | log_doc_event_idx | COMPOSITE | (document_id, event) |
| activity_logs | log_created_at_idx | Biasa | created_at |

---

## 7. Backfill Strategy

### 7.1 Data Existing

Tabel documents mungkin berisi data dari seeder/aktivitas sebelumnya. Data ini harus di-backfill.

### 7.2 Nilai Backfill

| Kolom | Nilai | Alasan |
|---|---|---|
| status | approved | Dokumen existing sudah aktif/terkirim. Tidak perlu workflow. |
| document_category | general | Tidak diklasifikasikan. General aman. |
| document_type | NULL | Tidak diketahui. Biarkan NULL. |
| template_id | NULL | Tidak terikat template. |
| current_version | 1 | Versi awal. |
| file_size | NULL | Bisa diisi job terpisah. |
| mime_type | NULL | Bisa diisi job terpisah. |
| is_archived | false | Dianggap aktif. |
| updated_by | NULL | Tidak diketahui. |
| archived_at | NULL | Belum ada arsip. |

### 7.3 Metode
1. ALTER TABLE dengan default value.
2. UPDATE batch setelah migration: SET status=approved, document_category=general, current_version=1, is_archived=false WHERE status IS NULL.
3. Job opsional untuk file_size dan mime_type.

---

## 8. Analisis Risiko

### 8.1 Dampak per Modul

| Modul | Risiko | Keterangan |
|---|---|---|
| Proposal | Tidak ada | Tidak ada FK ke documents |
| Contract | Tidak ada | Tidak ada FK ke documents |
| Invoice | Tidak ada | Tabel data, bukan file |
| Payment | Tidak ada | Bukti bayar mandiri |
| Timeline | Tidak ada | Tidak ada relasi |
| Vendor | Tidak ada | Tidak ada relasi |
| Client | Rendah | Preview/download tetap sama |
| Document Builder | Rendah | Perlu isi kolom baru saat create |
| Admin Proposal | Rendah | Upload/filter tetap pakai kolom lama |
| Admin Payment | Rendah | Dampak tidak langsung via Builder |
| Notifications | Tidak ada | Tidak ada relasi |
| Document Sends | Tidak ada | FK document_id tetap |

### 8.2 Matriks Risiko

| Aspek | Tingkat | Keterangan |
|---|---|---|
| Struktur DB | Rendah | Semua additive, tidak ada DROP |
| Kode existing | Rendah | Tidak perlu perubahan |
| Data loss | Tidak ada | Hanya tambah kolom |
| Performa | Rendah | Index sesuai kebutuhan |
| Backward compat | Terjamin | Kolom baru nullable/default |
| Deployment | Rendah | Rollback aman |

---

## 9. Roadmap Migration

### Urutan 4 Fase

**Phase 1 (Core):**
1. alter_documents_table
2. create_document_templates_table
3. create_ddms_settings_table

**Phase 2 (Workflow):**
4. create_document_approvals_table
5. create_document_numberings_table
6. create_document_qr_verifications_table

**Phase 3 (History):**
7. create_document_verification_logs_table
8. create_document_send_histories_table
9. create_activity_logs_table

**Phase 4 (Backfill):**
10. Update data existing
11. Job backfill (opsional)

### Alasan Urutan
- Phase 1: Core tables harus siap sebelum workflow. templates & settings tidak bergantung tabel lain.
- Phase 2: Workflow membutuhkan documents yang sudah di-extend. approval -> numbering -> QR adalah proses berurutan.
- Phase 3: History/logs membutuhkan tabel workflow yang sudah ada. verification_logs bergantung pada QR.
- Phase 4: Backfill dilakukan setelah semua struktur siap.

---

## 10. Checklist Validasi Arsitektur

| Prinsip | Status | Bukti |
|---|---|---|
| Additive | Lolos | Semua CREATE TABLE + ALTER TABLE ADD. Tidak ada destructive change. |
| Non-Disruptive | Lolos | Migration existing tidak disentuh. Semua kolom baru nullable/default. |
| Service Layer | Lolos | Logika bisnis akan di service baru, bukan di migration. |
| Repository Pattern | Lolos | DocumentRepositoryInterface sudah ada. Akan diperluas. |
| 3NF | Lolos | Approval, numbering, QR, audit, send history dipisah ke tabel sendiri. |
| Backward Compatible | Lolos | Kode existing berfungsi tanpa perubahan. |
| Low Risk | Lolos | Semua migration bisa rollback. Risiko tertinggi: Document Builder (minor). |
| Separation of Resp. | Lolos | Setiap domain punya tabel sendiri dengan tanggung jawab jelas. |
| FK Integrity | Lolos | CASCADE/SET NULL/RESTRICT sesuai kebutuhan bisnis. |
| Index Optimization | Lolos | Index pada kolom filter, join, sorting. Unique untuk integritas. |

---

## 11. Kesimpulan

- **9 migration** (1 ALTER + 8 CREATE) dalam 4 fase.
- **10 kolom baru** di documents (6 wajib, 4 opsional).
- **8 tabel baru**: document_templates, ddms_settings, document_approvals, document_numberings, document_qr_verifications, document_verification_logs, document_send_histories, activity_logs.
- **document_type** = OPSIONAL, melengkapi tipe existing.
- **Risiko: Rendah.** Backward compatibility terjamin.
- **Siap implementasi** di Task 2B.

