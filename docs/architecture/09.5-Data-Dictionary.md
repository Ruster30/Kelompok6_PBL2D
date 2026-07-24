# Migration Plan

**Project:** Event Management System (Laravel 12)
**Module:** Digital Document Management System (DDMS)
**Version:** 2.0
**Status:** Final
**Author:** Kelompok 6 PBL
**Reference:** Architecture Audit v1.0

---

# 1. Pendahuluan

## 1.1 Tujuan

Dokumen ini menjelaskan rencana implementasi perubahan struktur database untuk mengintegrasikan Digital Document Management System (DDMS) ke dalam Event Management System yang telah ada.

Migration Plan disusun berdasarkan prinsip:

- Extend Existing Architecture
- Backward Compatible
- Zero Business Workflow Break
- Incremental Migration

Dokumen ini menjadi acuan implementasi Laravel Migration.

---

# 2. Migration Principles

Seluruh perubahan database mengikuti prinsip berikut.

## Reuse Existing Table

Menggunakan tabel yang telah ada tanpa mengubah fungsi bisnis utamanya.

Contoh:

- users
- events
- proposals
- contracts
- invoices
- payments
- document_sends
- notifications

---

## Extend Existing Table

Menambahkan atribut baru apabila memang diperlukan tanpa mengubah workflow lama.

Tabel utama yang diperluas:

- documents

---

## New Supporting Table

Fitur DDMS yang memiliki lifecycle sendiri dibuat pada tabel terpisah.

Contoh:

- document_approvals
- document_numberings
- document_qr_verifications
- document_verification_logs
- activity_logs

---

# 3. Existing Tables

Tidak dilakukan perubahan struktur terhadap tabel berikut.

| Tabel | Status |
|--------|---------|
| users | Reuse |
| events | Reuse |
| proposals | Reuse |
| contracts | Reuse |
| invoices | Reuse |
| payments | Reuse |
| payment_schemes | Reuse |
| rabs | Reuse |
| negotiations | Reuse |
| document_sends | Reuse |
| notifications | Reuse |

---

# 4. Extended Table

## documents

DDMS menjadikan tabel ini sebagai pusat Repository Dokumen.

Kolom baru yang ditambahkan:

| Kolom | Tipe | Keterangan |
|--------|------|------------|
| status | enum | Draft, Waiting Approval, Approved, Rejected |
| current_version | integer | Versi aktif dokumen |
| is_archived | boolean | Status arsip |
| document_category | varchar | Official / General / Invoice |
| updated_by | bigint | User terakhir mengubah |

Catatan:

Informasi approval, nomor surat, QR Code, dan audit trail **tidak disimpan langsung** pada tabel ini, tetapi menggunakan tabel relasi agar memenuhi prinsip normalisasi.

---

# 5. New Tables

## document_approvals

Menyimpan proses approval dokumen.

---

## document_numberings

Menyimpan nomor surat resmi.

---

## document_qr_verifications

Menyimpan data QR Code.

---

## document_verification_logs

Mencatat seluruh proses scan QR.

---

## document_send_histories

Riwayat pengiriman dokumen.

---

## document_templates

Template Blade setiap dokumen.

---

## ddms_settings

Konfigurasi DDMS.

---

## activity_logs

Tabel baru untuk audit trail.

Mencatat:

- Create
- Update
- Delete
- Approval
- Reject
- Download
- Upload
- Send
- Verification

---

# 6. Migration Order

Migration dijalankan secara bertahap.

## Phase 1

Core Table

1. Alter Documents
2. Create Document Templates
3. Create DDMS Settings

---

## Phase 2

Workflow

4. Create Document Approvals
5. Create Document Numberings
6. Create Document QR Verifications

---

## Phase 3

History

7. Create Document Send Histories
8. Create Verification Logs
9. Create Activity Logs

---

# 7. Backfill Strategy

Data existing harus tetap dapat digunakan.

Backfill dilakukan terhadap seluruh data lama.

Contoh:

```
Semua document lama

↓

status = approved

↓

current_version = 1

↓

is_archived = false
```

Dengan pendekatan ini, seluruh dokumen lama tetap valid tanpa perlu approval ulang.

---

# 8. Event & Listener Preparation

Migration juga menyiapkan kebutuhan Event Driven Architecture.

Event:

- DocumentCreated
- DocumentSubmitted
- DocumentApproved
- DocumentRejected
- DocumentSent

Listener:

- GenerateDocumentNumber
- GenerateQRCode
- GeneratePDF
- SaveRepository
- SendNotification
- WriteActivityLog

---

# 9. Queue Preparation

Job yang akan dibuat.

- GeneratePDFJob
- GenerateQRCodeJob
- SendDocumentJob

Seluruh Job menggunakan Laravel Queue.

---

# 10. Middleware Preparation

Middleware baru.

CheckDocumentApproval

Fungsi:

Mencegah dokumen dikirim apabila belum berstatus Approved.

---

# 11. Existing Service Integration

Migration harus tetap kompatibel dengan service yang sudah ada.

Tidak mengganti:

- AdminProposalService
- AdminPaymentService
- ClientService
- TimelineService

DocumentBuilderService tetap digunakan sebagai generator dokumen.

DDMS hanya memperluas kemampuannya.

---

# 12. Risk Assessment

Risiko Rendah

- Tambah tabel baru

Risiko Sedang

- Alter documents

Risiko Tinggi

- Workflow Payment
- Proposal Versioning
- Timeline Auto Fill

Bagian tersebut tidak boleh mengalami perubahan perilaku.

---

# 13. Rollback Strategy

Apabila migration gagal:

- rollback migration terakhir
- tidak menghapus data lama
- mempertahankan workflow existing

---

# 14. Deployment Checklist

## Database

- [ ] Backup Production
- [ ] Jalankan Migration
- [ ] Jalankan Seeder
- [ ] Backfill Existing Document

---

## Application

- [ ] Register Event
- [ ] Register Listener
- [ ] Register Queue
- [ ] Register Middleware

---

## Verification

- [ ] Existing Proposal berjalan
- [ ] Existing Payment berjalan
- [ ] Existing Timeline berjalan
- [ ] Existing Document Builder berjalan
- [ ] DDMS berjalan

---

# 15. Deliverables

Migration Plan menghasilkan implementasi berikut.

- Laravel Migration
- Seeder
- Event
- Listener
- Queue
- Middleware
- Model
- Repository
- Service

---

# 16. Kesimpulan

Migration DDMS menerapkan pendekatan **Architectural Extension**, yaitu memperluas kemampuan Event Management System tanpa mengubah proses bisnis yang telah berjalan.

Dengan strategi bertahap, penggunaan tabel pendukung, dan mekanisme backfill, implementasi DDMS dapat dilakukan secara aman, kompatibel, dan mudah dipelihara dalam jangka panjang.
