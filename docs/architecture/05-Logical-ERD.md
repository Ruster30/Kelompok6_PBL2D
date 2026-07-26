# Logical ERD
**Project:** Event Management System (Laravel)
**Module:** Digital Document Management System (DDMS)
**Version:** 1.0
**Status:** Draft
**Author:** Kelompok 6 PBL
**Last Update:** 24 Juli 2026

---

# 1. Pendahuluan

## 1.1 Tujuan

Dokumen ini menjelaskan Logical Entity Relationship Diagram (Logical ERD) untuk modul Digital Document Management System (DDMS).

Logical ERD menerjemahkan Conceptual ERD menjadi struktur data logis yang siap diimplementasikan ke dalam database.

Dokumen ini belum membahas implementasi migration maupun kode Laravel secara rinci.

---

# 2. Prinsip Perancangan

Logical ERD DDMS menggunakan prinsip berikut:

- Reuse Existing Table
- Extend Existing Table
- Add New Table Only When Necessary
- Separation of Responsibility
- Single Source of Truth

---

# 3. Existing Entity (Reuse)

Entity berikut sudah dimiliki oleh Event Management System dan akan digunakan kembali.

| Entity | Status | Keterangan |
|---------|--------|------------|
| users | Reuse | Pengguna sistem |
| events | Reuse | Data event |
| proposals | Reuse | Workflow proposal |
| contracts | Reuse | Workflow kontrak |
| payments | Reuse | Workflow pembayaran |
| notifications | Reuse | Notifikasi sistem |
| documents | Extend | Repository dokumen |

---

# 4. Existing Entity (Extend)

## Documents

Tabel documents tetap menjadi pusat repository dokumen.

Namun akan diperluas agar mendukung DDMS.

Contoh informasi tambahan:

- Jenis dokumen
- Sumber dokumen
- Lokasi file
- Status arsip
- Relasi ke event (opsional)

Workflow approval **tidak disimpan** pada tabel ini.

---

# 5. New Entity

Entity baru yang direkomendasikan.

| Entity | Tujuan |
|---------|---------|
| document_approvals | Approval dokumen |
| document_numberings | Nomor surat |
| document_qr_verifications | QR Verification |
| document_send_histories | Riwayat pengiriman |
| document_verification_logs | Riwayat scan QR |
| document_templates | Template dokumen |
| ddms_settings | Konfigurasi DDMS |

---

# 6. Logical Relationship

## documents

Berelasi dengan:

- users
- events
- document_templates
- document_approvals
- document_numberings
- document_qr_verifications
- document_send_histories
- document_verification_logs

---

## document_approvals

Berelasi dengan:

- documents
- users (Director)

---

## document_numberings

Berelasi dengan:

- documents

---

## document_qr_verifications

Berelasi dengan:

- documents

---

## document_send_histories

Berelasi dengan:

- documents
- users

---

## document_verification_logs

Berelasi dengan:

- document_qr_verifications

---

## document_templates

Berelasi dengan:

- documents

---

## ddms_settings

Digunakan oleh:

- Approval
- QR
- Numbering
- Template

---

# 7. Logical Diagram

```text
users
 │
 │
 ▼
documents
 ├────────────── document_approvals
 ├────────────── document_numberings
 ├────────────── document_qr_verifications
 ├────────────── document_send_histories
 ├────────────── document_templates
 └────────────── events

document_qr_verifications
        │
        ▼
document_verification_logs
```

---

# 8. Ownership

| Entity | Owner |
|---------|-------|
| documents | DDMS |
| document_approvals | DDMS |
| document_numberings | DDMS |
| document_qr_verifications | DDMS |
| document_send_histories | DDMS |
| document_verification_logs | DDMS |
| document_templates | DDMS |
| ddms_settings | DDMS |
| events | Event Module |
| proposals | Proposal Module |
| payments | Payment Module |

---

# 9. Normalization

Seluruh entity dirancang mengikuti Third Normal Form (3NF).

Prinsip yang digunakan:

- Tidak ada data approval di tabel documents.
- Tidak ada data QR di tabel documents.
- Tidak ada data nomor surat di tabel documents.
- Riwayat pengiriman dipisahkan.
- Riwayat verifikasi dipisahkan.

---

# 10. Integrasi dengan Modul Existing

DDMS tidak menggantikan modul Event Management System.

Integrasi dilakukan sebagai berikut:

| Modul | Integrasi |
|--------|-----------|
| Proposal | Generate Draft Document |
| Contract | Generate Contract |
| Payment | Generate Invoice & Receipt |
| Event | Referensi Event |
| Notification | Approval Notification |

---

# 11. Prinsip Database

- Reuse sebelum membuat tabel baru.
- Hindari duplikasi data.
- Satu tanggung jawab untuk setiap tabel.
- Approval bersifat independen.
- QR Verification bersifat independen.
- Repository menjadi pusat penyimpanan dokumen.

---

# 12. Output

Logical ERD menjadi dasar penyusunan:

- Migration Plan
- Model Laravel
- Repository Pattern
- Service Layer
- API Design

---

# 13. Catatan

Logical ERD mendefinisikan struktur logis data.

Implementasi fisik database akan dijelaskan pada dokumen Migration Plan.
