# Conceptual ERD
**Project:** Event Management System (Laravel)
**Module:** Digital Document Management System (DDMS)
**Version:** 1.0
**Status:** Draft
**Author:** Kelompok 6 PBL
**Last Update:** 24 Juli 2026

---

# 1. Pendahuluan

## 1.1 Tujuan

Dokumen ini menjelaskan Entity Relationship Diagram (ERD) konseptual untuk modul Digital Document Management System (DDMS).

Conceptual ERD digunakan untuk mengidentifikasi hubungan antar entitas bisnis tanpa membahas implementasi database.

Dokumen ini menjadi dasar penyusunan Logical ERD.

---

# 2. Ruang Lingkup

Conceptual ERD hanya membahas modul DDMS.

Workflow bisnis seperti:

- Event
- Proposal
- Payment
- Vendor
- Timeline
- Negotiation

tetap berada pada Event Management System.

---

# 3. Tujuan Conceptual ERD

Conceptual ERD bertujuan untuk:

- Mengidentifikasi entity bisnis.
- Menentukan hubungan antar entity.
- Menentukan cardinality.
- Menentukan ownership setiap entity.
- Menjadi dasar Logical ERD.

---

# 4. Entity

## 4.1 Document

Entity utama DDMS.

Merepresentasikan seluruh dokumen perusahaan.

Jenis:

- Surat Penawaran
- Surat Revisi
- Kontrak
- RAB
- Invoice
- Kwitansi
- Proposal
- Company Profile
- Portofolio
- Dokumen Upload

---

## 4.2 Approval

Menyimpan proses persetujuan dokumen resmi.

---

## 4.3 Document Number

Menyimpan nomor dokumen resmi.

---

## 4.4 QR Verification

Menyimpan informasi QR Verification.

---

## 4.5 Repository

Merepresentasikan penyimpanan seluruh dokumen.

---

## 4.6 Send History

Menyimpan riwayat pengiriman dokumen.

---

## 4.7 Verification History

Menyimpan riwayat verifikasi QR.

---

## 4.8 Template

Template dokumen.

---

## 4.9 Settings

Konfigurasi DDMS.

---

# 5. External Entity

Entity berikut berasal dari Event Management System.

- Event
- Proposal
- Payment
- Contract
- Vendor
- User
- Notification

DDMS hanya berinteraksi dengan entity tersebut.

---

# 6. Relationship

## Document — Approval

Satu Document dapat memiliki satu atau lebih riwayat Approval.

Cardinality

```
Document (1)

↓

Approval (N)
```

---

## Document — Document Number

Satu Document memiliki maksimal satu nomor dokumen.

```
Document (1)

↓

Document Number (0..1)
```

---

## Document — QR Verification

Satu Document memiliki maksimal satu QR Verification.

```
Document (1)

↓

QR Verification (0..1)
```

---

## Document — Repository

Seluruh Document disimpan di Repository.

```
Document (1)

↓

Repository (1)
```

---

## Document — Send History

```
Document (1)

↓

Send History (N)
```

---

## Document — Verification History

```
Document (1)

↓

Verification History (N)
```

---

## Template — Document

```
Template (1)

↓

Document (N)
```

---

## Settings

Settings digunakan oleh:

- Approval
- Numbering
- QR
- Template

Settings tidak memiliki hubungan langsung terhadap Document.

---

# 7. External Relationship

Document dapat berasal dari:

- Event
- Proposal
- Contract
- Payment

Artinya DDMS tidak membuat Event.

DDMS hanya menerima hasil workflow bisnis.

---

# 8. Conceptual Diagram

```text
                    Event
                      │
                  Proposal
                      │
                  Contract
                      │
                  Payment
                      │
                      ▼

                  Document
     ┌─────────────┼──────────────┐
     │             │              │
     ▼             ▼              ▼
 Approval    Document Number   Template
     │             │
     │             ▼
     │      QR Verification
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

# 9. Cardinality Summary

| Relationship | Cardinality |
|-------------|-------------|
| Document → Approval | 1 : N |
| Document → Document Number | 1 : 0..1 |
| Document → QR Verification | 1 : 0..1 |
| Document → Repository | 1 : 1 |
| Document → Send History | 1 : N |
| Document → Verification History | 1 : N |
| Template → Document | 1 : N |

---

# 10. Design Principles

- Document merupakan Aggregate Root DDMS.
- Workflow bisnis tidak dipindahkan ke DDMS.
- DDMS hanya mengelola lifecycle dokumen.
- Repository menjadi Single Source of Truth.
- Approval dipisahkan dari Document.
- QR Verification dipisahkan dari Document.
- Numbering dipisahkan dari Document.

---

# 11. Catatan

Conceptual ERD tidak membahas:

- Primary Key
- Foreign Key
- Tipe Data
- Normalisasi
- Migration

Seluruh implementasi database akan dijelaskan pada dokumen **05-Logical-ERD.md**.
