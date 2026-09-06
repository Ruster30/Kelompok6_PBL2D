# Sequence Diagram
**Project:** Event Management System (Laravel)
**Module:** Digital Document Management System (DDMS)
**Version:** 1.0
**Status:** Draft
**Author:** Kelompok 6 PBL
**Last Update:** 24 Juli 2026

---

# 1. Pendahuluan

## 1.1 Tujuan

Dokumen ini menjelaskan interaksi antar aktor dan komponen sistem pada setiap proses utama DDMS.

Sequence Diagram menjadi acuan implementasi:

- Controller
- Service Layer
- Repository
- Notification
- PDF Generator
- QR Generator

---

# 2. Arsitektur Interaksi

Seluruh proses DDMS mengikuti arsitektur berikut.

```text
User
 │
 ▼
Controller
 │
 ▼
Service
 │
 ▼
Repository
 │
 ▼
Model
 │
 ▼
Database
```

Service dapat memanggil service lain apabila diperlukan.

---

# 3. Sequence - Create Draft Document

```text
Admin
 │
 ▼
DocumentController
 │
 ▼
DocumentService
 │
 ▼
DocumentRepository
 │
 ▼
Document Model
 │
 ▼
Database

<──────── Draft Created
```

---

## Deskripsi

1. Admin membuat dokumen.
2. Controller menerima request.
3. Service melakukan validasi bisnis.
4. Repository menyimpan data.
5. Draft berhasil dibuat.

---

# 4. Sequence - Submit Approval

```text
Admin
 │
 ▼
DocumentController
 │
 ▼
ApprovalService
 │
 ▼
ApprovalRepository
 │
 ▼
Database
 │
 ▼
NotificationService
 │
 ▼
Director
```

---

## Deskripsi

1. Admin menekan tombol Submit Approval.
2. Approval dibuat.
3. Status Document menjadi Waiting Approval.
4. Notification dikirim kepada Director.

---

# 5. Sequence - Approval Document

```text
Director
 │
 ▼
ApprovalController
 │
 ▼
ApprovalService
 │
 ▼
PIN Verification
 │
 ▼
ApprovalRepository
 │
 ▼
Database

 ┌───────────────────────────────┐
 │ Approved                      │
 └───────────────────────────────┘
               │
               ▼
      NumberingService
               │
               ▼
         QRService
               │
               ▼
        PDFService
               │
               ▼
     DocumentRepository
               │
               ▼
 NotificationService
               │
               ▼
            Admin
```

---

## Deskripsi

1. Director melakukan approval.
2. PIN diverifikasi.
3. Status approval diperbarui.
4. Sistem membuat nomor surat.
5. Sistem membuat QR.
6. Sistem membuat PDF Final.
7. Repository diperbarui.
8. Admin menerima notifikasi.

---

# 6. Sequence - Reject Document

```text
Director
 │
 ▼
ApprovalController
 │
 ▼
ApprovalService
 │
 ▼
ApprovalRepository
 │
 ▼
Database
 │
 ▼
NotificationService
 │
 ▼
Admin
```

---

## Deskripsi

1. Director menolak dokumen.
2. Alasan penolakan disimpan.
3. Status kembali menjadi Draft.
4. Admin menerima notifikasi.

---

# 7. Sequence - Send Document

```text
Admin
 │
 ▼
DocumentController
 │
 ▼
SendDocumentService
 │
 ▼
DocumentRepository
 │
 ▼
SendHistoryRepository
 │
 ▼
NotificationService
 │
 ▼
Client
```

---

## Deskripsi

1. Admin memilih dokumen.
2. Sistem membuat riwayat pengiriman.
3. Notification dikirim kepada Client.

---

# 8. Sequence - Public QR Verification

```text
Public
 │
 ▼
VerificationController
 │
 ▼
VerificationService
 │
 ▼
QR Repository
 │
 ▼
Database
 │
 ▼
Verification Log Repository
 │
 ▼
Database
 │
 ▼
Return Verification Result
```

---

## Deskripsi

1. QR dipindai.
2. Sistem memvalidasi hash QR.
3. Riwayat verifikasi disimpan.
4. Status dokumen ditampilkan.

---

# 9. Sequence - Upload General Document

```text
Admin
 │
 ▼
DocumentController
 │
 ▼
UploadService
 │
 ▼
Storage
 │
 ▼
DocumentRepository
 │
 ▼
Database
```

---

## Deskripsi

1. Admin memilih file.
2. File disimpan.
3. Metadata dokumen disimpan.
4. Repository diperbarui.

---

# 10. Dependency Diagram

```text
DocumentController
        │
        ▼
DocumentService
        │
        ├──────── ApprovalService
        ├──────── NumberingService
        ├──────── QRService
        ├──────── PDFService
        ├──────── SendDocumentService
        └──────── NotificationService
```

---

# 11. Service Interaction

| Service | Memanggil |
|----------|-----------|
| DocumentService | DocumentRepository |
| ApprovalService | ApprovalRepository |
| ApprovalService | NumberingService |
| ApprovalService | QRService |
| ApprovalService | PDFService |
| ApprovalService | NotificationService |
| SendDocumentService | SendHistoryRepository |
| VerificationService | VerificationRepository |

---

# 12. Design Principles

- Controller hanya menerima request.
- Business Logic berada pada Service Layer.
- Repository hanya mengakses database.
- Service dapat memanggil service lain.
- Notification dipisahkan dari business logic.
- PDF Generator dipisahkan dari Approval.
- QR Generator dipisahkan dari Document.

---

# 13. Output

Sequence Diagram menjadi dasar implementasi:

- Controller
- Service Layer
- Repository Pattern
- Notification
- PDF Generator
- QR Generator

---

# 14. Status

Draft.
