# Analisis Struktur Database — Project Event Management System

**Tanggal:** 25 Juli 2026
**Revisi:** Sinkronisasi dengan Arsitektur DDMS v2.0
**Tujuan:** Identifikasi struktur database untuk persiapan implementasi Digital Document Management System (DDMS)
**Metode:** Analisis menyeluruh terhadap migration, model Eloquent, dan relasi database, diselaraskan dengan dokumen arsitektur DDMS (Blueprint, Domain Model, Logical ERD, Data Dictionary, Migration Plan).

---

## 1. Daftar Migration (40 File)

| No | File Migration | Tipe | Tabel Dibuat/Diubah |
|---|---|---|---|
| 1 | `0001_01_01_000000_create_users_table.php` | Create | `users`, `password_reset_tokens`, `sessions` |
| 2 | `0001_01_01_000001_create_cache_table.php` | Create | `cache`, `cache_locks` |
| 3 | `0001_01_01_000002_create_jobs_table.php` | Create | `jobs`, `job_batches`, `failed_jobs` |
| 4 | `2024_01_01_000010_create_vendors_table.php` | Create | `vendors` |
| 5 | `2024_01_01_000020_create_events_table.php` | Create | `events` |
| 6 | `2024_01_01_000030_create_event_vendor_table.php` | Create | `event_vendor` (pivot) |
| 7 | `2024_01_01_000040_create_proposals_table.php` | Create | `proposals` |
| 8 | `2024_01_01_000050_create_contracts_table.php` | Create | `contracts` |
| 9 | `2024_01_01_000060_create_rabs_table.php` | Create | `rabs` |
| 10 | `2024_01_01_000070_create_invoices_table.php` | Create | `invoices` |
| 11 | `2024_01_01_000080_create_payments_table.php` | Create | `payments` |
| 12 | `2024_01_01_000090_create_timelines_table.php` | Create | `timelines` |
| 13 | `2024_01_01_000100_create_documentations_table.php` | Create | `documentations` |
| 14 | `2024_01_01_000110_create_reports_table.php` | Create | `reports` |
| 15 | `2024_01_01_000115_create_documentation_files_table.php` | Create | `documentation_files` |
| 16 | `2024_01_01_000120_create_landing_page_tables.php` | Create | `landing_sections`, `about_sections`, `about_statistics` |
| 17 | `2024_01_01_000130_create_cms_content_tables.php` | Create | `services`, `teams`, `portfolios`, `clients` (CMS) |
| 18 | `2026_06_09_110427_create_notifications_table.php` | Create | `notifications` |
| 19 | `2026_06_12_144239_create_tasks_table.php` | Create | `tasks` |
| 20 | `2026_06_16_145801_create_jadwals_table.php` | Create | `jadwals` |
| 21 | `2026_06_17_065205_create_feedback_table.php` | Create | `feedback` |
| 22 | `2026_06_19_000001_create_documents_table.php` | Create | `documents` |
| 23 | `2026_06_21_000001_make_vendor_user_optional.php` | Alter | `vendors.user_id` -> nullable |
| 24 | `2026_06_21_000002_add_timeline_details_and_event_budget.php` | Alter | `timelines` (+deskripsi, +penanggung_jawab, +deadline); `events` (+rentang_anggaran) |
| 25 | `2026_06_21_000003_add_email_to_vendors_table.php` | Alter | `vendors` (+email unique) |
| 26 | `2026_06_22_000000_create_negotiations_table.php` | Create | `negotiations` |
| 27 | `2026_06_22_122008_make_password_nullable_for_google_users.php` | Alter | `users.password` -> nullable |
| 28 | `2026_06_26_000025_update_documents_tipe_column.php` | Alter | `documents.tipe` enum -> string(50) |
| 29 | `2026_06_27_000001_create_client_notifications_sent_table.php` | Create+Alter | `admin_client_notifications`; `users` (+last_active_at) |
| 30 | `2026_06_28_101646_drop_status_from_clients_table.php` | Alter | `clients` (drop `status`) |
| 31 | `2026_06_29_000001_add_surat_penawaran_fields_to_events_table.php` | Alter | `events` (+nomor_surat_override, +tanggal_selesai, +luas_area, +terbilang) |
| 32 | `2026_06_29_000121_document_sends_table.php` | Create | `document_sends` |
| 33 | `2026_06_30_005451_add_is_active_to_proposals_table.php` | Alter | `proposals` (+is_active) |
| 34 | `2026_06_30_210000_update_invoice_status_workflow.php` | Alter | `invoices.status_invoice` enum -> varchar(50) |
| 35 | `2026_07_01_000111_add_perihal_to_events_table.php` | Alter | `events` (+perihal) |
| 36 | `2026_07_05_134028_add_satuan_to_rabs_table.php` | Alter | `rabs` (+satuan) |
| 37 | `2026_07_06_130254_add_deskripsi_to_event_vendor_table.php` | Alter | `event_vendor` (+deskripsi) |
| 38 | `2026_07_09_235852_create_rab_additional_details_table.php` | Create | `rab_additional_details` |
| 39 | `2026_07_10_080950_create_payment_schemes_table.php` | Create | `payment_schemes` |
| 40 | `2026_07_11_001819_add_status_pembayaran_to_events_table.php` | Alter | `events` (+status_pembayaran) |

---

## 2. Daftar Tabel (37 Tabel)

### A. Sistem / Laravel Default (8 tabel)
| Tabel | Fungsi |
|---|---|
| users | Akun pengguna (admin, client, vendor) |
| password_reset_tokens | Token reset password |
| sessions | Session login |
| cache | Cache aplikasi |
| cache_locks | Lock cache |
| jobs | Antrian job |
| job_batches | Batch job |
| ailed_jobs | Job gagal |

### B. Core Event Management (5 tabel)
| Tabel | Fungsi |
|---|---|
| events | Data event/acara |
| endors | Data vendor |
| event_vendor | Pivot many-to-many event <-> vendor |
| 	asks | Tugas per event |
| jadwals | Jadwal kegiatan event |

### C. Dokumen & Proposal (5 tabel)
| Tabel | Fungsi |
|---|---|
| proposals | Proposal penawaran (dengan file upload) |
| contracts | Kontrak (dengan file upload) |
| invoices | Data invoice/tagihan |
| payments | Riwayat pembayaran (dengan upload bukti) |
| documents | Dokumen generik (file_path, tipe: proposal/kontrak/invoice/rab/laporan/kwitansi/lainnya) |

### D. Keuangan & RAB (4 tabel)
| Tabel | Fungsi |
|---|---|
| abs | Item Rincian Anggaran Biaya |
| ab_additional_details | Konfigurasi fee, PPN, PPh per event |
| payment_schemes | Skema pembayaran (full/dp+pelunasan) |
| 
egotiations | Riwayat negosiasi budget dari client |

### E. Timeline & Dokumentasi (4 tabel)
| Tabel | Fungsi |
|---|---|
| 	imelines | Timeline kegiatan event |
| documentations | Grup dokumentasi event |
| documentation_files | File dokumentasi (foto/video) |
| eports | File laporan akhir |

### F. Notifikasi & Pengiriman (3 tabel)
| Tabel | Fungsi |
|---|---|
| 
otifications | Notifikasi sistem untuk user |
| dmin_client_notifications | Riwayat notifikasi admin -> client |
| document_sends | Riwayat pengiriman dokumen admin -> client |

### G. Feedback (1 tabel)
| Tabel | Fungsi |
|---|---|
| eedback | Rating & ulasan client |

### H. Landing Page / CMS (7 tabel)
| Tabel | Fungsi |
|---|---|
| landing_sections | Section landing page (hero, contact, footer) |
| bout_sections | Konten halaman Tentang Kami |
| bout_statistics | Statistik (50+ Event, 96% Kepuasan) |
| services | Layanan (MICE, Production, Marketing, dll.) |
| 	eams | Anggota tim |
| portfolios | Portofolio project |
| clients | Klien/mitra (logo, website) |

---

## 3. Relationship Antar Tabel

### Diagram Relasi

```
users (role: admin|client|vendor)
|-- hasMany -> events (client_id)
|-- hasMany -> events (pic_admin_id) [PIC admin]
|-- hasOne -> vendors (user_id)
|-- hasMany -> notifications
|-- hasMany -> admin_client_notifications (sender_id) [admin]
|-- hasMany -> admin_client_notifications (recipient_id) [client]
|-- hasMany -> negotiations (user_id)
|-- hasMany -> feedback (client_id)
L-- hasMany -> documents (user_id) [uploader]

events
|-- belongsTo -> users (client_id) [FK: users.id]
|-- belongsTo -> users (pic_admin_id) [FK: users.id, nullable]
|-- belongsToMany -> vendors (via event_vendor)
|-- hasMany -> proposals [FK: events.id]
|-- hasMany -> contracts [FK: events.id]
|-- hasMany -> rabs [FK: events.id]
|-- hasMany -> invoices [FK: events.id]
|-- hasMany -> timelines [FK: events.id]
|-- hasMany -> documentations [FK: events.id]
|-- hasMany -> reports [FK: events.id]
|-- hasMany -> tasks [FK: events.id]
|-- hasMany -> jadwals [FK: events.id]
|-- hasMany -> feedback [FK: events.id]
|-- hasMany -> negotiations [FK: events.id]
|-- hasMany -> documents [FK: events.id]
|-- hasOne -> rab_additional_details [FK: events.id]
|-- hasOne -> payment_schemes [FK: events.id]
L-- hasManyThrough -> payments (via invoices)

vendors
|-- belongsTo -> users (user_id) [nullable]
|-- belongsToMany -> events (via event_vendor)
L-- hasMany -> rabs (vendor_id) [nullable]

event_vendor (pivot) -> belongsTo -> events, vendors
proposals -> belongsTo -> events
contracts -> belongsTo -> events
invoices -> belongsTo -> events -> hasMany -> payments
payments -> belongsTo -> invoices
documents -> belongsTo -> events, users -> hasMany -> document_sends
document_sends -> belongsTo -> documents, users (sender), users (recipient)
rabs -> belongsTo -> events, vendors [nullable]
rab_additional_details -> belongsTo -> events
payment_schemes -> belongsTo -> events
negotiations -> belongsTo -> events, users
timelines -> belongsTo -> events
documentations -> belongsTo -> events
documentation_files -> belongsTo -> documentations
reports -> belongsTo -> events
tasks -> belongsTo -> events, vendors [nullable]
jadwals -> belongsTo -> events
feedback -> belongsTo -> events, users
```

### Foreign Key Matrix

| FK Column | Source Table | Target Table | On Delete |
|---|---|---|---|
| endors.user_id | vendors | users | CASCADE |
| events.client_id | events | users | CASCADE |
| events.pic_admin_id | events | users | SET NULL |
| event_vendor.event_id | event_vendor | events | CASCADE |
| event_vendor.vendor_id | event_vendor | vendors | CASCADE |
| proposals.event_id | proposals | events | CASCADE |
| contracts.event_id | contracts | events | CASCADE |
| abs.event_id | rabs | events | CASCADE |
| abs.vendor_id | rabs | vendors | SET NULL |
| invoices.event_id | invoices | events | CASCADE |
| payments.invoice_id | payments | invoices | CASCADE |
| 	imelines.event_id | timelines | events | CASCADE |
| documentations.event_id | documentations | events | CASCADE |
| documentation_files.documentation_id | documentation_files | documentations | CASCADE |
| eports.event_id | reports | events | CASCADE |
| 
otifications.user_id | notifications | users | CASCADE |
| 	asks.event_id | tasks | events | CASCADE |
| 	asks.vendor_id | tasks | vendors | SET NULL |
| jadwals.event_id | jadwals | events | CASCADE |
| eedback.event_id | feedback | events | CASCADE |
| eedback.client_id | feedback | users | CASCADE |
| documents.event_id | documents | events | SET NULL |
| documents.user_id | documents | users | SET NULL |
| 
egotiations.event_id | negotiations | events | CASCADE |
| 
egotiations.user_id | negotiations | users | CASCADE |
| dmin_client_notifications.sender_id | admin_client_notifications | users | CASCADE |
| dmin_client_notifications.recipient_id | admin_client_notifications | users | CASCADE |
| document_sends.document_id | document_sends | documents | CASCADE |
| document_sends.sender_id | document_sends | users | CASCADE |
| document_sends.recipient_id | document_sends | users | CASCADE |
| ab_additional_details.event_id | rab_additional_details | events | CASCADE |
| payment_schemes.event_id | payment_schemes | events | CASCADE |

---

## 4. Kandidat Tabel Reuse untuk DDMS

Berdasarkan arsitektur DDMS (Migration Plan & Data Dictionary v2.0), tabel-tabel berikut dapat digunakan kembali tanpa modifikasi:

| Tabel | Dokumen Arsitektur | Alasan Reuse |
|---|---|---|
| **`documents`** | Data Dictionary (3.3), Migration Plan (4) | Backbone Repository DDMS. Akan di-extend dengan kolom baru. |
| **`document_sends`** | Migration Plan (3) | Riwayat pengiriman dokumen. DDMS menambahkan `document_send_histories` sebagai tabel baru, tetapi `document_sends` tetap dipertahankan untuk kompatibilitas. |
| **`users`** | Data Dictionary (3.1), Migration Plan (3) | Basis otentikasi dan otorisasi seluruh pengguna DDMS (admin, direktur, client). |
| **`events`** | Data Dictionary (3.2), Migration Plan (3) | Central entity. Setiap dokumen DDMS terhubung ke event_id. |
| **`notifications`** | Migration Plan (3) | Notifikasi approval dikirim melalui modul ini. |

### Reuse Tanpa Perubahan (Tabel Non-DDMS)

Tabel berikut tetap digunakan oleh workflow bisnis masing-masing dan **tidak perlu diubah** untuk DDMS:

| Tabel | Modul |
|---|---|
| proposals | Proposal Module |
| contracts | Contract Module |
| invoices | Payment Module |
| payments | Payment Module |
| abs | RAB Module |
| payment_schemes | Payment Module |
| 
egotiations | Proposal Module |

---

## 5. Kandidat Tabel Extend untuk DDMS

Berdasarkan Migration Plan (4), hanya **satu tabel** yang perlu di-extend:

### documents

Kolom baru yang ditambahkan (via migration baru, bukan alter migration existing):

| Kolom | Tipe | Keterangan |
|---|---|---|
| 	emplate_id | bigint (FK) | Relasi ke document_templates |
| 	itle | varchar | Judul dokumen (menggantikan 
ama_file untuk dokumen resmi) |
| document_type | varchar | Jenis dokumen (surat_penawaran, kontrak, rab, invoice, kwitansi, proposal, company_profile, portfolio, umum) |
| document_category | enum | Kategori: official, general, invoice, eceipt |
| source | enum | Sumber: generated, uploaded |
| status | enum | Status: draft, waiting_approval, pproved, ejected |
| current_version | integer | Versi aktif dokumen (default 1) |
| ile_size | bigint (nullable) | Ukuran file dalam bytes |
| mime_type | varchar (nullable) | MIME type file |
| is_archived | boolean | Status arsip (default false) |
| updated_by | bigint (FK, nullable) | User terakhir yang mengubah |

**Penting:** Informasi approval, nomor surat, QR Code, dan audit trail **tidak disimpan** di tabel documents, melainkan di tabel relasi terpisah (prinsip normalisasi 3NF).

---

## 6. Tabel Baru untuk DDMS — Tahap 1 (Implementasi Awal)

Berdasarkan Data Dictionary v2.0, Logical ERD, dan Migration Plan DDMS, implementasi tahap pertama mencakup **7 tabel baru**:

### 6.1 document_templates

Template Blade untuk generate dokumen.

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint | PK |
| 
ame | varchar | Nama template |
| document_type | varchar | Jenis dokumen |
| lade_view | varchar | Lokasi file Blade |
| is_active | boolean | Status aktif |
| created_at | timestamp | Waktu dibuat |
| updated_at | timestamp | Waktu diubah |

### 6.2 ddms_settings

Konfigurasi global DDMS.

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint | PK |
| setting_key | varchar | Nama konfigurasi (unique) |
| setting_value | text (nullable) | Nilai konfigurasi |
| description | text (nullable) | Keterangan |
| created_at | timestamp | Waktu dibuat |
| updated_at | timestamp | Waktu diubah |

### 6.3 document_approvals

Menyimpan proses approval dokumen resmi (Surat Penawaran, Kontrak, RAB).

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint | PK |
| document_id | bigint (FK) | Relasi ke documents |
| pprover_id | bigint (FK) | Relasi ke users (role direktur) |
| status | enum | pending, pproved, ejected |
| pproval_note | text (nullable) | Catatan direktur |
| pproved_at | timestamp (nullable) | Waktu approval |
| created_at | timestamp | Waktu dibuat |
| updated_at | timestamp | Waktu diubah |

### 6.4 document_numberings

Menyimpan nomor surat resmi yang telah di-generate setelah disetujui.

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint | PK |
| document_id | bigint (FK) | Relasi ke documents |
| document_number | varchar | Nomor surat (unique) |
| generated_at | timestamp | Waktu generate |
| created_at | timestamp | Waktu dibuat |
| updated_at | timestamp | Waktu diubah |

### 6.5 document_qr_verifications

Menyimpan data QR Code yang di-generate setelah approval.

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint | PK |
| document_id | bigint (FK) | Relasi ke documents |
| erification_hash | varchar | Hash unik untuk QR (unique) |
| qr_path | varchar | Lokasi file QR |
| generated_at | timestamp | Waktu generate |
| created_at | timestamp | Waktu dibuat |
| updated_at | timestamp | Waktu diubah |

### 6.6 document_verification_logs

Mencatat setiap aktivitas scan QR Code untuk verifikasi keaslian dokumen.

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint | PK |
| qr_verification_id | bigint (FK) | Relasi ke document_qr_verifications |
| ip_address | varchar (nullable) | IP client |
| rowser | varchar (nullable) | Browser client |
| device | varchar (nullable) | Device client |
| erification_status | enum | alid, invalid |
| erified_at | timestamp | Waktu scan |
| created_at | timestamp | Waktu dibuat |

### 6.7 activity_logs

Audit trail seluruh aktivitas di DDMS. Menggantikan document_audit_logs pada rekomendasi awal.

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint | PK |
| user_id | bigint (FK, nullable) | Pelaku |
| document_id | bigint (FK, nullable) | Dokumen terkait |
| event | varchar | Jenis aktivitas (document.created, document.approved, dll.) |
| description | text (nullable) | Deskripsi aktivitas |
| ip_address | varchar (nullable) | IP client |
| created_at | timestamp | Waktu aktivitas |

---

## 7. Fitur yang Ditunda ke Pengembangan Masa Depan

Berdasarkan Domain Model (10) dan Blueprint (12) DDMS, fitur berikut **tidak termasuk dalam implementasi v1.0** dan direncanakan untuk versi selanjutnya:

### 7.1 document_versions

Manajemen versi dokumen (riwayat perubahan file).

**Status:** Ditunda — tidak termasuk Tahap 1.

**Rencana:** Akan diimplementasikan setelah core DDMS berjalan stabil.

### 7.2 digital_signatures

Tanda tangan elektronik / TTE BSrE.

**Status:** Ditunda — tidak termasuk Tahap 1.

**Rencana:** Menunggu regulasi dan integrasi dengan penyedia TTE resmi (BSrE).

### Fitur Lain yang Ditunda (Blueprint DDMS 12)

- Multi-Level Approval
- OCR (Optical Character Recognition)
- AI Document Assistant
- Template Editor (GUI)
- Integrasi SRIKANDI

---

## 8. Tabel yang Dihapus dari Rekomendasi Awal

Berdasarkan sinkronisasi dengan arsitektur DDMS, tabel-tabel berikut **dihapus** dari rekomendasi karena tidak sesuai dengan desain yang telah disepakati:

| Tabel | Alasan Penghapusan |
|---|---|
| document_categories | Tidak ada dalam Logical ERD/Data Dictionary. Kategori dokumen diwakili oleh kolom document_category di tabel documents. |
| document_types | Tidak ada dalam Logical ERD/Data Dictionary. Jenis dokumen diwakili oleh kolom document_type di tabel documents. |
| document_statuses | Tidak ada dalam Data Dictionary. Status dokumen diwakili oleh kolom status di tabel documents dan riwayat approval di document_approvals. |
| document_generations | Tidak ada dalam arsitektur DDMS. Template langsung di-referensi oleh documents.template_id. |
| document_audit_logs | Nama diubah menjadi ctivity_logs sesuai Data Dictionary (4.8). |

---

## 9. Risiko Implementasi

### Risiko Tinggi

| Risiko | Dampak | Mitigasi |
|---|---|---|
| **Mengubah migration existing** | Migration sudah digunakan di production. Mengubahnya bisa merusak data dan relasi. | JANGAN ubah migration existing. Buat migration baru untuk DDMS. |
| **Modifikasi langsung tabel `documents`** | Tabel sudah digunakan oleh fitur upload di berbagai controller. | Tambah kolom via migration baru (ALTER TABLE), jangan mengubah migration `2026_06_19_000001`. |
| **Dualisme data dokumen** | Data proposal bisa ada di `proposals` dan `documents`. Potensi inkonsistensi jika workflow tidak diselaraskan. | Dokumen dari modul bisnis (proposal, kontrak, invoice) tetap dikelola oleh modul masing-masing. DDMS hanya mengelola hasil akhir dokumen. |

### Risiko Sedang

| Risiko | Dampak | Mitigasi |
|---|---|---|
| **Redundansi penyimpanan file** | `proposals`, `contracts`, `reports`, `documents`, `payments`, `documentation_files` semuanya menyimpan file path. | DDMS tidak mengambil alih storage modul lain. Cukup catat referensi dokumen di `documents` untuk keperluan repository terpusat. |
| **Perubahan workflow existing** | Menambah status/halaman DDMS bisa mengganggu workflow event yang sudah berjalan. | DDMS adalah layer di atas workflow bisnis. Workflow modul lain tetap dipertahankan (Blueprint 4.2). |
| **Alter tabel `documents`** | Kolom baru bisa bertabrakan dengan kode existing yang menggunakan `documents`. | Pastikan semua kolom baru bersifat nullable atau memiliki default value. |

### Risiko Rendah

| Risiko | Dampak | Mitigasi |
|---|---|---|
| **Performa query** | `activity_logs` akan bertambah cepat. | Indexing pada user_id, document_id, event. Gunakan pagination. |
| **Hak akses** | Role existing (admin/client/vendor) belum mengakomodasi role direktur. | Tambah role `direktur` atau gunakan permission-based access. Jangan ubah role enum yang sudah ada. |
| **Nomor surat duplikat** | Potensi duplikasi nomor jika tidak di-handle dengan atomic transaction. | Gunakan database transaction dan unique constraint pada `document_number`. |

---

## 10. Ringkasan Strategi Implementasi DDMS

### Pendekatan: **Additive & Non-Disruptive**

Berdasarkan arsitektur DDMS yang telah disepakati (Blueprint v1.0, Domain Model v1.0, Logical ERD v1.0, Data Dictionary v2.0, Migration Plan v2.0), strategi implementasi adalah:

1. **Jangan ubah migration existing** — semua tabel core event management tetap utuh.
2. **Extend documents sebagai backbone DDMS** — tambah kolom baru via migration terpisah. Informasi approval, numbering, QR dipisah ke tabel relasi (3NF).
3. **Migration bertahap (Migration Plan 6):**
   - Phase 1 (Core): Alter Documents, Create Document Templates, Create DDMS Settings
   - Phase 2 (Workflow): Create Document Approvals, Create Document Numberings, Create Document QR Verifications
   - Phase 3 (History): Create Document Send Histories, Create Verification Logs, Create Activity Logs
4. **Reuse tabel existing** — users, events, document_sends, 
otifications tanpa perubahan.
5. **DDMS sebagai layer** — tidak mengambil alih workflow bisnis Proposal, Contract, Payment, RAB (Separation of Responsibility).
6. **Backfill strategy** — data dokumen lama diisi status pproved, current_version = 1, is_archived = false.

### Model Data Kunci

```
+---------------------------+
|      EVENT MANAGEMENT    |  (tidak diubah)
|  events, proposals,      |
|  contracts, invoices,    |
|  payments, rabs, dll.   |
+-----------+-----------+
            |
            v (hasil workflow)
+-----------+-----------+
|        DOCUMENTS      |  EXTEND + kolom baru
|  (Backbone Repository) |
+---+---+---+---+---+--+
    |   |   |   |   |
    v   v   v   v   v
 +--+ +--+ +--+ +--+ +--+
 |Ap-| |Nu-| |QR | |Se-| |Ve-|
 |pro-| |mbe-| |Ver| |nd | |ri-|
 |vals| |ring| |if.| |Hi.| |fi.|
 +----+ +----+ +---+ +--+ +--+

 +-------------------+
 |  activity_logs    |  (Audit Trail)
 +-------------------+

 +-------------------+
 |  ddms_settings    |  (Konfigurasi)
 +-------------------+

 +-------------------+
 | doc_templates    |  (Template Blade)
 +-------------------+
```

### Status Implementasi Berdasarkan Arsitektur DDMS

| Komponen | Status | Dokumen Referensi |
|---|---|---|
| documents (extend) | Tahap 1 | Data Dictionary 3.3, Migration Plan 4 |
| document_templates | Tahap 1 | Data Dictionary 4.6, Migration Plan 5 |
| ddms_settings | Tahap 1 | Data Dictionary 4.7, Migration Plan 5 |
| document_approvals | Tahap 1 | Data Dictionary 4.1, Migration Plan 5 |
| document_numberings | Tahap 1 | Data Dictionary 4.2, Migration Plan 5 |
| document_qr_verifications | Tahap 1 | Data Dictionary 4.3, Migration Plan 5 |
| document_verification_logs | Tahap 1 | Data Dictionary 4.4, Migration Plan 5 |
| ctivity_logs | Tahap 1 | Data Dictionary 4.8, Migration Plan 5 |
| document_versions | **Ditunda** | Domain Model 10, Blueprint 12 |
| digital_signatures | **Ditunda** | Domain Model 10, Blueprint 12 |

---

## 11. Referensi Dokumen Arsitektur DDMS

Laporan ini disinkronkan dengan dokumen arsitektur DDMS berikut (sebagai single source of truth):

| Dokumen | File | Status |
|---|---|---|
| DDMS Blueprint | docs/architecture/02-DDMS-Blueprint.md | Draft |
| Domain Model | docs/architecture/03-Domain-Model.md | Draft |
| Conceptual ERD | docs/architecture/04-Conceptual-ERD.md | Draft |
| Logical ERD | docs/architecture/05-Logical-ERD.md | Draft |
| Migration Plan | docs/architecture/09-Migration-Plan.md | Final |
| Data Dictionary | docs/architecture/09.5-Data-Dictionary.md | Final |
| Implementation Guide | docs/architecture/12-Implementation-Guide.md | Final |

