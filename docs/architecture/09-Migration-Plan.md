# Migration Plan
**Project:** Event Management System (Laravel)
**Module:** Digital Document Management System (DDMS)
**Version:** 1.0
**Status:** Draft
**Author:** Kelompok 6 PBL
**Last Update:** 24 Juli 2026

---

# 1. Pendahuluan

## 1.1 Tujuan

Dokumen ini menjelaskan rencana perubahan struktur database yang diperlukan untuk mengimplementasikan Digital Document Management System (DDMS).

Dokumen ini belum berisi kode migration Laravel, namun menjadi acuan implementasi migration pada tahap development.

---

# 2. Prinsip Migration

Migration DDMS menggunakan prinsip berikut.

- Reuse Existing Table
- Extend Existing Table
- Create New Table Only When Necessary
- Tidak mengubah workflow modul lain
- Tidak menghapus tabel existing

---

# 3. Existing Table (Reuse)

Tabel berikut digunakan kembali tanpa perubahan struktur utama.

| Tabel | Modul |
|--------|-------|
| users | Authentication |
| events | Event |
| proposals | Proposal |
| contracts | Contract |
| payments | Payment |
| notifications | Notification |

---

# 4. Existing Table (Extend)

## documents

Tabel documents akan diperluas agar dapat menjadi Repository Dokumen.

Perubahan yang direncanakan:

- Menambahkan metadata dokumen
- Menambahkan informasi sumber dokumen
- Menambahkan relasi event (opsional)
- Menambahkan informasi arsip

Workflow approval tidak disimpan pada tabel ini.

---

# 5. New Tables

Berikut tabel baru yang akan dibuat.

| No | Nama Tabel | Fungsi |
|----|------------|--------|
| 1 | document_approvals | Approval dokumen |
| 2 | document_numberings | Nomor surat |
| 3 | document_qr_verifications | QR verification |
| 4 | document_send_histories | Riwayat pengiriman |
| 5 | document_verification_logs | Riwayat scan QR |
| 6 | document_templates | Template dokumen |
| 7 | ddms_settings | Konfigurasi DDMS |

---

# 6. Migration Order

Urutan migration yang direkomendasikan.

| Urutan | Migration |
|---------|-----------|
| 001 | Alter Documents Table |
| 002 | Create Document Templates |
| 003 | Create Document Approvals |
| 004 | Create Document Numberings |
| 005 | Create Document QR Verifications |
| 006 | Create Document Send Histories |
| 007 | Create Document Verification Logs |
| 008 | Create DDMS Settings |

---

# 7. Dependency

```text
documents
     │
     ├──────── document_templates
     ├──────── document_approvals
     ├──────── document_numberings
     ├──────── document_qr_verifications
     ├──────── document_send_histories
     └──────── events

document_qr_verifications
          │
          ▼
document_verification_logs
```

---

# 8. Impact Analysis

## Tidak Berubah

- Event
- Proposal
- Contract
- Payment
- Timeline
- Vendor

---

## Berubah

- Document
- Document Builder
- Repository
- Notification

---

# 9. Rollback Strategy

Jika migration gagal:

- rollback migration terakhir
- tidak mengubah data existing
- tidak menghapus dokumen lama

---

# 10. Checklist

## Existing

- [ ] users
- [ ] events
- [ ] proposals
- [ ] contracts
- [ ] payments
- [ ] notifications

---

## Extend

- [ ] documents

---

## Create

- [ ] document_templates
- [ ] document_approvals
- [ ] document_numberings
- [ ] document_qr_verifications
- [ ] document_send_histories
- [ ] document_verification_logs
- [ ] ddms_settings

---

# 11. Deliverables

Dokumen ini menjadi dasar pembuatan:

- Laravel Migration
- Model
- Repository
- Service
- Controller

---

# 12. Status

Draft.
