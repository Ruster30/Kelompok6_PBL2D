# Domain Model DDMS
**Project:** Event Management System (Laravel)
**Module:** Digital Document Management System (DDMS)
**Version:** 1.0
**Status:** Draft
**Author:** Kelompok 6 PBL
**Last Update:** 24 Juli 2026

---

# 1. Pendahuluan

## 1.1 Tujuan

Dokumen ini menjelaskan Domain Model dari Digital Document Management System (DDMS).

Domain Model digunakan untuk mengidentifikasi objek bisnis yang dimiliki DDMS beserta hubungan antar objek sebelum dilakukan perancangan database (ERD).

Dokumen ini **bukan** merupakan desain database maupun implementasi Laravel.

---

# 2. Tujuan Domain Model

Domain Model bertujuan untuk:

- Mengidentifikasi objek bisnis DDMS.
- Menentukan batas tanggung jawab setiap domain.
- Menjadi dasar penyusunan ERD.
- Menjadi acuan implementasi Service Layer.
- Menghindari duplikasi tanggung jawab antar modul.

---

# 3. Ruang Lingkup

Domain Model hanya membahas modul Digital Document Management System.

Workflow bisnis seperti Proposal, Event, Contract, Payment, Timeline, Vendor, dan Negotiation tetap berada pada modul Event Management System.

---

# 4. Core Domain

Core Domain merupakan objek bisnis utama yang dimiliki DDMS.

---

## 4.1 Document

Document merupakan pusat dari seluruh proses DDMS.

Semua dokumen yang dihasilkan maupun diunggah akan direpresentasikan sebagai Document.

Jenis Document meliputi:

- Surat Penawaran
- Surat Revisi Penawaran
- Surat Kontrak
- RAB
- Invoice DP
- Invoice Pelunasan
- Kwitansi
- Proposal
- Company Profile
- Portofolio
- Dokumen Umum

### Tanggung Jawab

- Menyimpan metadata dokumen.
- Menjadi referensi utama seluruh proses DDMS.
- Menjadi pusat Repository Dokumen.

---

## 4.2 Approval

Approval merupakan proses persetujuan dokumen resmi.

Approval hanya berlaku untuk dokumen yang memerlukan pengesahan Direktur.

### Tanggung Jawab

- Submit Approval
- Approve
- Reject
- Approval Note
- Approval Time
- Approver

---

## 4.3 Document Number

Domain ini bertanggung jawab terhadap penomoran dokumen resmi.

### Tanggung Jawab

- Generate nomor surat.
- Menjaga urutan nomor.
- Menyesuaikan format nomor berdasarkan jenis dokumen.

Contoh:

001/SP/ALPHA/VIII/2026

---

## 4.4 QR Verification

Domain QR Verification bertanggung jawab terhadap proses validasi keaslian dokumen.

### Tanggung Jawab

- Generate Verification Hash
- Generate QR Code
- Public Verification
- Menampilkan status dokumen

---

## 4.5 Repository

Repository merupakan pusat penyimpanan seluruh dokumen.

### Tanggung Jawab

- Preview
- Download
- Search
- Filter
- Archive

Repository menjadi Single Source of Truth seluruh dokumen perusahaan.

---

# 5. Supporting Domain

Supporting Domain merupakan domain pendukung DDMS.

---

## 5.1 Send History

Menyimpan riwayat pengiriman dokumen.

### Tanggung Jawab

- Pengirim
- Penerima
- Waktu Pengiriman
- Status Pengiriman

---

## 5.2 Verification History

Menyimpan riwayat hasil scan QR.

### Tanggung Jawab

- Waktu Verifikasi
- Browser
- IP Address
- Device
- Status Verifikasi

---

## 5.3 Template

Mengelola template dokumen.

### Tanggung Jawab

- Blade Template
- Jenis Dokumen
- Template Aktif

---

## 5.4 Settings

Mengelola konfigurasi DDMS.

### Tanggung Jawab

- Approval
- QR Verification
- Nomor Surat
- Approval PIN
- Template

---

# 6. External Domain

Berikut merupakan domain yang berada di luar DDMS namun berinteraksi dengannya.

- Event
- Proposal
- Contract
- Invoice
- Payment
- Timeline
- Vendor
- Negotiation
- Notification
- User

DDMS tidak mengambil alih tanggung jawab domain tersebut.

---

# 7. Domain Relationship

Hubungan antar domain DDMS.

```text
                        Document
                            │
      ┌─────────────────────┼─────────────────────┐
      │                     │                     │
      ▼                     ▼                     ▼
 Approval            Document Number          Template
      │                     │
      │                     ▼
      │              QR Verification
      │
      ▼
 Repository
      │
      ├──────── Send History
      │
      └──────── Verification History

                    ▲
                    │
                Settings
```

---

# 8. Domain Boundary

```text
+------------------------------------------------------+
|             EVENT MANAGEMENT SYSTEM                  |
|------------------------------------------------------|
| Event                                                |
| Proposal                                             |
| Contract                                             |
| Invoice                                              |
| Payment                                              |
| Timeline                                             |
| Vendor                                               |
| Negotiation                                          |
| Notification                                         |
+-------------------------┬----------------------------+
                          │
                          ▼
+------------------------------------------------------+
|                      DDMS                            |
|------------------------------------------------------|
| Document                                             |
| Approval                                             |
| Repository                                           |
| Document Number                                      |
| QR Verification                                      |
| Template                                             |
| Settings                                             |
| Send History                                         |
| Verification History                                 |
+------------------------------------------------------+
```

---

# 9. Domain Rules

| Rule ID | Aturan |
|----------|--------|
| DR-001 | Hanya Surat Resmi yang memerlukan Approval Direktur. |
| DR-002 | QR Verification hanya dibuat setelah Approval. |
| DR-003 | Nomor Surat hanya diberikan kepada dokumen resmi yang telah disetujui. |
| DR-004 | Dokumen Upload tidak memerlukan Approval maupun Nomor Surat. |
| DR-005 | Invoice dan Kwitansi mengikuti workflow pembayaran. |
| DR-006 | Semua dokumen harus masuk Repository Dokumen. |
| DR-007 | Approval wajib memiliki Audit Trail. |
| DR-008 | Direktur wajib melakukan Approval menggunakan PIN Approval. |
| DR-009 | PDF Final hanya dihasilkan setelah proses Approval selesai. |
| DR-010 | Repository tidak mengubah workflow bisnis modul lain. |

---

# 10. Design Principles

DDMS dikembangkan berdasarkan prinsip berikut.

## Single Source of Truth

Seluruh dokumen berada dalam satu Repository.

---

## Separation of Responsibility

Workflow bisnis tetap berada pada modul bisnis.

DDMS hanya mengelola dokumen.

---

## Modular Architecture

Setiap domain memiliki tanggung jawab yang jelas.

---

## Incremental Development

Perubahan dilakukan dengan prinsip:

> Tambahkan, jangan hapus.

---

## Extensibility

DDMS harus mudah dikembangkan untuk mendukung:

- Multi-Level Approval
- TTE BSrE
- OCR
- AI Document Assistant
- Template Editor

---

# 11. Deliverables

Dokumen ini menjadi dasar penyusunan:

- ERD
- Migration Plan
- Service Layer
- Repository
- Controller
- Business Process
- State Diagram
- Sequence Diagram

---

# 12. Status

| Tahapan | Status |
|----------|--------|
| Architecture Audit | ✅ Selesai |
| DDMS Blueprint | ✅ Draft |
| Domain Model | ✅ Draft |
| ERD | ⏳ Belum Dimulai |
| Migration Plan | ⏳ Belum Dimulai |
| Backend Design | ⏳ Belum Dimulai |
| Frontend Design | ⏳ Belum Dimulai |
| Implementation | ⏳ Belum Dimulai |

---

# 13. Catatan

Domain Model merupakan representasi konsep bisnis DDMS.

Dokumen ini tidak menggambarkan struktur database.

Seluruh desain database akan dijelaskan pada dokumen **04-ERD.md**.
