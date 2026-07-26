# Audit Arsitektur Tabel documents

**Project:** Event Management System (Laravel 12)
**Modul:** Digital Document Management System (DDMS)
**Tanggal:** 25 Juli 2026
**Auditor:** Senior Laravel Architect
**Referensi:** Data Dictionary v2.0, Migration Plan v2.0, Logical ERD v1.0

---

## 1. Struktur Existing

### 1.1 Migration Asal

Tabel `documents` dibuat oleh migration `2026_06_19_000001_create_documents_table.php` dan diubah oleh migration `2026_06_26_000025_update_documents_tipe_column.php`.

### 1.2 Definisi Kolom Saat Ini

| # | Kolom | Tipe Data | Nullable | Default | Fungsi | Digunakan Oleh Modul |
|---|---|---|---|---|---|---|
| 1 | `id` | bigint (PK, AI) | Tidak | - | Primary key | Semua modul yang berelasi dengan documents |
| 2 | `event_id` | bigint (FK -> events) | Ya | NULL | Relasi ke event pemilik dokumen | Admin Proposal, Admin Payment, Client, Document Builder |
| 3 | `user_id` | bigint (FK -> users) | Ya | NULL | Relasi ke user pengunggah/pembuat dokumen | Admin Proposal, Document Builder |
| 4 | `nama_file` | varchar(255) | Tidak | - | Nama tampilan file (display name) | Admin Proposal (list, download), Client (preview, download) |
| 5 | `file_path` | varchar(255) | Tidak | - | Path file relatif terhadap storage/public | Admin Proposal (preview, download), Client (preview, download), Document Builder |
| 6 | `tipe` | varchar(50) | Tidak | lainnya | Jenis dokumen: proposal/kontrak/invoice/rab/laporan/kwitansi/lainnya | Admin Proposal (filter, upload), Document Builder (kwitansi) |
| 7 | `created_at` | timestamp | Ya | NULL | Waktu pembuatan | Semua modul |
| 8 | `updated_at` | timestamp | Ya | NULL | Waktu perubahan terakhir | Semua modul |

### 1.3 Catatan Penting tentang Kolom Existing

- **`event_id` nullable**: Dokumen tidak wajib terkait event. Ini mendukung dokumen umum (company profile, brosur, dll.).
- **`user_id` nullable**: Dokumen bisa diunggah oleh siapa saja. Untuk dokumen hasil generate (kwitansi), user_id diisi dengan auth()->id().
- **`tipe` awalnya enum**: Migration pertama mendefinisikan enum(proposal,kontrak,lainnya), lalu diubah menjadi varchar(50).
- **`nama_file` vs `file_path`**: Keduanya wajib diisi. `nama_file` adalah display name.
- **Tidak ada soft delete**: Dokumen yang dihapus akan hilang permanen (termasuk file di storage).

---

## 2. Dependency Analysis

### 2.1 Relasi dengan Tabel Lain

| Tabel | Relasi | Tipe | Keterangan |
|---|---|---|---|
| `events` | `documents.event_id` -> `events.id` | Many-to-One | Satu event bisa memiliki banyak dokumen. Dokumen boleh tidak terkait event. |
| `users` | `documents.user_id` -> `users.id` | Many-to-One | Satu user bisa mengunggah banyak dokumen. Dokumen bisa tidak memiliki pengunggah (untuk backfill). |
| `document_sends` | `document_sends.document_id` -> `documents.id` | One-to-Many | Satu dokumen bisa dikirim ke banyak client berkali-kali. |
| `proposals` | Tidak ada FK langsung | Tidak langsung | Proposal menyimpan file di `file_proposal`, bukan di `documents`. |
| `contracts` | Tidak ada FK langsung | Tidak langsung | Kontrak menyimpan file di `file_kontrak`, bukan di `documents`. |
| `invoices` | Tidak ada FK langsung | Tidak langsung | Invoice adalah tabel data (bukan file). |
| `payments` | Tidak ada FK langsung | Tidak langsung | Payments menyimpan `bukti_pembayaran` (file path) secara mandiri. |
| `reports` | Tidak ada FK langsung | Tidak langsung | Reports menyimpan `file_laporan` secara mandiri. |
| `notifications` | Tidak ada FK langsung | Tidak langsung | Notifikasi dibuat saat dokumen dikirim ke client. |

### 2.2 Analisis Ketergantungan

**Poin Kritis:**

1. **`documents` tidak menjadi storage utama untuk proposal, kontrak, invoice, payment, atau report.** Masing-masing modul menyimpan file-nya sendiri.
2. **Satu-satunya relasi ketergantungan kuat adalah `document_sends`.** Jika dokumen dihapus, data `document_sends` ikut terhapus (CASCADE).
3. **Tidak ada kendala referensial dari modul bisnis ke `documents`.** Mengubah struktur `documents` tidak akan merusak modul Proposal, Contract, Invoice, Payment, atau Report.
4. **`documents` digunakan oleh DocumentBuilderService** untuk menyimpan hasil generate kwitansi.

---

## 3. Workflow Analysis

### 3.1 Titik Integrasi

| Tahap | Modul | Method | Keterangan |
|---|---|---|---|
| Pembuatan | Admin Proposal | uploadDocument() / DocumentRepository::create() | Upload manual oleh admin |
| Generate | Document Builder | generateAndSaveKwitansi() | Auto-generate kwitansi |
| Penyimpanan | Storage (public) | Storage::disk(public)->put() | Semua file di public/documents/ |
| List/Filter | Admin Proposal | paginateWithFilters() | Pagination, search by nama_file, filter by tipe |
| Preview | Admin Proposal | preview() | Response file inline |
| Preview | Client | documentPreview() | Response file inline + verifikasi akses |
| Download | Admin Proposal | downloadDocument() | Response file attachment |
| Download | Client | documentDownload() | Response file attachment + verifikasi akses |
| Kirim | Admin Proposal | sendDocumentToClient() | Simpan riwayat kirim + notifikasi + email |
| Hapus | Admin Proposal | deleteDocument() | Hapus file + record DB |

### 3.2 Keterbatasan Workflow Saat Ini

1. **Tidak ada konsep status dokumen.** Dokumen langsung tersedia setelah diupload/digenerate.
2. **Tidak ada pemisahan kategori.** Semua dokumen diperlakukan sama.
3. **Tidak ada versioning.** Upload baru akan membuat record baru, bukan versi baru.
4. **Tidak ada mekanisme arsip.** Dokumen hanya bisa dihapus atau tetap aktif.
5. **Send menggunakan tabel `document_sends`**, yang mencatat pengiriman. Struktur ini kompatibel dengan DDMS.

---

## 4. Gap Analysis

| Kebutuhan DDMS | Status | Keterangan |
|---|---|---|
| Repository | Sebagian | List, filter, preview, download sudah ada. Arsip, kategori, tracking versi belum tersedia. |
| Approval | Belum | Tidak ada kolom status. Tidak ada relasi ke document_approvals. |
| Numbering | Belum | Tidak ada kolom atau relasi untuk nomor surat resmi. |
| QR Verification | Belum | Tidak ada kolom atau relasi untuk QR. |
| Activity Log | Belum | Tidak ada audit trail. Tidak ada kolom updated_by. |
| Template | Belum | Tidak ada relasi ke template (template_id). Template di-hardcode. |
| Security | Terbatas | Verifikasi akses client sudah ada. Belum kontrol akses berbasis status/role. |

### 4.1 Analisis Detail per Kebutuhan

**Repository:**
- Existing sudah mencakup upload, list, filter, preview, download, send, delete.
- Gap: Tidak ada metadata ukuran file (`file_size`), tipe MIME (`mime_type`), status arsip (`is_archived`), kategori dokumen (`document_category`).
- Gap: Tidak ada mekanisme backfill untuk dokumen lama.

**Approval:**
- Dokumen existing langsung aktif setelah diupload. Tidak ada konsep menunggu approval.
- Kolom `status` perlu ditambahkan untuk mendukung workflow: draft -> waiting_approval -> approved -> rejected.
- Dokumen existing harus di-backfill dengan status `approved` (dianggap sudah disetujui).

**Numbering:**
- Nomor surat dikelola secara independen di Proposal (`nomor_proposal`), Invoice (`nomor_invoice`), dan Kwitansi (generate di DocumentBuilderService).
- DDMS membutuhkan `document_numberings` sebagai tabel baru untuk sentralisasi nomor dokumen resmi.
- Tabel `documents` tidak perlu menyimpan nomor surat - cukup relasi ke `document_numberings`.

**QR Verification:**
- QR Code belum ada sama sekali di sistem.
- Data QR disimpan di `document_qr_verifications` (tabel baru), bukan di `documents`.

**Activity Log:**
- Tidak ada pencatatan aktivitas untuk dokumen saat ini.
- `activity_logs` sebagai tabel baru lebih sesuai daripada membebani `documents` dengan kolom audit.
- Kolom `updated_by` di `documents` diperlukan untuk mengetahui pengubah terakhir tanpa join ke `activity_logs`.

**Template:**
- Template saat ini berupa Blade view yang di-load langsung oleh DocumentBuilderService.
- DDMS membutuhkan `document_templates` sebagai tabel baru.
- Kolom `template_id` (FK) di `documents` diperlukan untuk mencatat template yang digunakan.

---

## 5. Candidate Columns

### 5.1 Kolom yang Direkomendasikan untuk Ditambahkan

| No | Kolom | Tipe | Nullable | Default | Alasan Penambahan | Modul Pengguna |
|---|---|---|---|---|---|---|
| 1 | `status` | enum (draft,waiting,approved,rejected) | Tidak | draft | Mendukung workflow approval DDMS. Dokumen existing di-backfill approved. | Approval, Repository |
| 2 | `document_category` | varchar(50) | Tidak | general | Kategori: official (perlu approval), general (tanpa approval), invoice, receipt. | Approval, Repository |
| 3 | `document_type` | varchar(100) | Ya | NULL | Jenis spesifik (surat_penawaran, kontrak, rab, invoice_dp, kwitansi, dll.) | Document Builder, Repository |
| 4 | `template_id` | bigint (FK -> doc_templates) | Ya | NULL | Relasi ke template untuk generate dokumen. | Document Builder, Repository |
| 5 | `current_version` | integer | Tidak | 1 | Counter versi untuk tracking perubahan. | Repository, Document Builder |
| 6 | `file_size` | bigint | Ya | NULL | Ukuran file dalam bytes untuk UI dan quota. | Repository (UI), Storage |
| 7 | `mime_type` | varchar(127) | Ya | NULL | MIME type untuk rendering yang tepat. | Repository (Preview) |
| 8 | `is_archived` | boolean | Tidak | false | Status arsip. Dokumen diarsipkan tidak muncul di list aktif. | Repository |
| 9 | `updated_by` | bigint (FK -> users) | Ya | NULL | Mencatat user terakhir yang mengubah dokumen. | Semua modul |
| 10 | `archived_at` | timestamp | Ya | NULL | Waktu pengarsipan. Melengkapi is_archived. | Repository |

### 5.2 Kolom yang Tidak Perlu Ditambahkan

| Kolom | Alasan Tidak Ditambahkan | Alternatif |
|---|---|---|
| approval_status | Status approval sudah diakomodasi kolom `status`. Detail ada di document_approvals. | Kolom `status` + tabel document_approvals |
| document_number | Nomor surat entitas terpisah, melanggar 3NF. | Tabel baru document_numberings |
| verification_hash / qr_path | Data QR bersifat independen. | Tabel baru document_qr_verifications |
| approver_id | Approval bisa multi-level ke depan. | Tabel document_approvals |
| approved_at | Waktu approval spesifik per riwayat. | document_approvals.approved_at |
| source (generated/uploaded) | Bisa diinfer dari template_id. | Infer dari template_id |
| file_hash | Untuk deduplikasi, tidak kritikal v1.0. | Ditunda |

### 5.3 Kolom Existing yang Dipertahankan

| Kolom | Alasan |
|---|---|
| id | Primary key |
| event_id | Relasi ke central entity. Kritis untuk semua modul. |
| user_id | Mengetahui pembuat/uploader. Backward compatibility. |
| nama_file | Display name di UI. |
| file_path | Lokasi file di storage. |
| tipe | Sudah digunakan luas di filter dan kode. |
| created_at, updated_at | Standar Laravel. |

---

## 6. Impact Analysis

### 6.1 Dampak terhadap Modul Existing

| Modul | Dampak | Risiko | Penjelasan |
|---|---|---|---|
| Admin Proposal | Rendah | Rendah | Hanya upload, list, filter, preview, download. Kolom baru tidak mengubah logika. |
| Client | Rendah | Rendah | Hanya preview dan download. Tidak terpengaruh. |
| Document Builder | Rendah | Rendah | generateAndSaveKwitansi() perlu isi kolom baru. |
| Admin Payment | Rendah | Rendah | Memanggil Document Builder. Dampak tidak langsung. |
| Proposal, Contract, Invoice, Payment, Timeline, Vendor, Reports | Tidak ada | Rendah | Tidak ada relasi FK langsung ke documents. |
| Event | Tidak ada | Rendah | Relasi hasMany tetap sama. |
| Document Sends | Tidak ada | Rendah | FK document_id tetap. |

### 6.2 Kesimpulan Dampak

- **Tidak ada modul bisnis yang memiliki ketergantungan wajib ke kolom baru.**
- Modul perlu penyesuaian minimal: DocumentBuilderService.
- **Risiko keseluruhan: Rendah.**

---

## 7. Final Recommendation

### 7.1 Kolom Existing (Dipertahankan)

1. id (PK, AI)
2. event_id (FK -> events, nullable)
3. user_id (FK -> users, nullable)
4. nama_file (varchar)
5. file_path (varchar)
6. tipe (varchar 50, default lainnya)
7. created_at (timestamp)
8. updated_at (timestamp)

### 7.2 Kolom Baru yang Direkomendasikan (10 Kolom)

1. **status** (enum, not null, default draft)
   - Mendukung workflow approval DDMS
   - Backfill: approved untuk semua dokumen existing

2. **document_category** (varchar 50, not null, default general)
   - Membedakan official / general / invoice / receipt
   - Backfill: general untuk dokumen existing

3. **document_type** (varchar 100, nullable)
   - Jenis spesifik dokumen
   - Backfill: NULL

4. **template_id** (bigint, FK -> document_templates, nullable)
   - Relasi ke template generate
   - Backfill: NULL

5. **current_version** (integer, not null, default 1)
   - Counter versi
   - Backfill: 1

6. **file_size** (bigint, nullable)
   - Ukuran file untuk UI
   - Backfill: NULL

7. **mime_type** (varchar 127, nullable)
   - MIME type untuk preview
   - Backfill: NULL

8. **is_archived** (boolean, not null, default false)
   - Status arsip
   - Backfill: false

9. **updated_by** (bigint, FK -> users, nullable)
   - Pengubah terakhir
   - Backfill: NULL

10. **archived_at** (timestamp, nullable)
    - Waktu pengarsipan
    - Backfill: NULL

### 7.3 Kolom yang Tidak Perlu Ditambahkan ke documents

| Kolom | Data Disimpan Di |
|---|---|
| document_number | document_numberings (tabel baru) |
| verification_hash | document_qr_verifications (tabel baru) |
| qr_path | document_qr_verifications (tabel baru) |
| approval_status | Diwakili kolom status di documents |
| approver_id | document_approvals (tabel baru) |
| approval_note | document_approvals (tabel baru) |
| approved_at | document_approvals (tabel baru) |
| source | Bisa diinfer dari template_id |
| file_hash | Ditunda |

---

## 8. Architecture Validation

### 8.1 Additive and Non-Disruptive

| Prinsip | Status | Validasi |
|---|---|---|
| Tidak mengubah migration existing | Lolos | Semua perubahan via ALTER TABLE migration baru. |
| Tidak mengubah workflow lama | Lolos | Dokumen existing tetap bisa diakses. |
| Tambahkan, jangan hapus | Lolos | Tidak ada kolom yang dihapus. |
| Kompatibel dengan kode lama | Lolos | Semua kolom baru nullable atau punya default. |

### 8.2 Service Layer

| Prinsip | Status | Validasi |
|---|---|---|
| Business logic di Service | Lolos | DocumentBuilderService dan AdminProposalService. |
| Thin Controller | Lolos | Controller hanya memanggil Service. |
| Tidak ada query di Controller | Lolos | Semua query di Repository/Service. |

### 8.3 Repository Pattern

| Prinsip | Status | Validasi |
|---|---|---|
| Interface tersedia | Lolos | DocumentRepositoryInterface sudah ada. |
| Implementation terpisah | Lolos | DocumentRepository ada. |
| DI digunakan | Lolos | Constructor injection. |

### 8.4 Separation of Responsibility

| Prinsip | Status | Validasi |
|---|---|---|
| Approval terpisah | Lolos | document_approvals (tabel baru). |
| Numbering terpisah | Lolos | document_numberings (tabel baru). |
| QR terpisah | Lolos | document_qr_verifications (tabel baru). |
| Audit trail terpisah | Lolos | activity_logs (tabel baru). |
| Workflow bisnis tetap di modul masing-masing | Lolos | Proposal, Contract, Payment, RAB tidak diubah. |

### 8.5 Database Normalization (3NF)

| Aturan 3NF | Status | Validasi |
|---|---|---|
| No repeating groups | Lolos | Setiap kolom berisi nilai atomik. |
| No partial dependencies | Lolos | Semua kolom bergantung penuh pada PK. |
| No transitive dependencies | Lolos | Approval, numbering, QR, audit dipisah. |

### 8.6 Hal yang Perlu Disesuaikan

1. **DocumentBuilderService perlu di-refactor** untuk mengisi kolom baru saat create.
2. **DocumentRepository perlu diperluas** dengan method update status, archive, filter.
3. **tipe vs document_type**: `tipe` adalah kategori penyimpanan (proposal, kontrak, dll). `document_type` adalah jenis spesifik (surat_penawaran, surat_kontrak, dll). Perlu dokumentasi jelas.

---

## 9. Kesimpulan

### Status Kesiapan documents untuk DDMS

- **Core functionality sudah siap**: Upload, list, filter, preview, download, send, delete.
- **10 kolom baru diperlukan**: status, document_category, document_type, template_id, current_version, file_size, mime_type, is_archived, updated_by, archived_at.
- **6 domain dipisahkan ke tabel baru**: approval, numbering, QR, verification log, template, activity log.
- **Risiko rendah**: Seluruh perubahan additive dengan default value aman.
- **Backward compatible**: Kode existing tetap berjalan tanpa modifikasi.

### Prioritas Implementasi

1. **Phase 1**: ALTER TABLE documents + Create document_templates + Create ddms_settings
2. **Phase 2**: Create document_approvals + Create document_numberings + Create document_qr_verifications
3. **Phase 3**: Create document_verification_logs + Create activity_logs
4. **Backfill**: status=approved, current_version=1, is_archived=false, document_category=general
5. **Deferred**: document_versions, digital_signatures, file_hash

