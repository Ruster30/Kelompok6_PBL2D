# Backend Design
**Project:** Event Management System (Laravel)
**Module:** Digital Document Management System (DDMS)
**Framework:** Laravel 12
**Architecture:** Service Layer + Repository Pattern
**Version:** 1.0
**Status:** Draft
**Author:** Kelompok 6 PBL
**Last Update:** 24 Juli 2026

---

# 1. Pendahuluan

## 1.1 Tujuan

Dokumen ini menjelaskan desain backend untuk implementasi Digital Document Management System (DDMS).

Dokumen ini menjadi acuan implementasi Laravel.

---

# 2. Design Principles

Backend DDMS dibangun menggunakan prinsip:

- Thin Controller
- Service Layer
- Repository Pattern
- Dependency Injection
- Form Request Validation
- Policy Authorization
- Single Responsibility Principle

---

# 3. Arsitektur Backend

```text
Route
 │
 ▼
Controller
 │
 ▼
Form Request
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

Komponen lain dapat dipanggil dari Service:

- Notification
- PDF
- QR Generator
- Storage

---

# 4. Module Structure

```
DDMS

├── Document
├── Approval
├── Numbering
├── QR Verification
├── Repository
├── Template
├── Send History
├── Verification
├── Settings
```

---

# 5. Controller

## DocumentController

Tanggung jawab:

- Create Draft
- Edit Draft
- Delete Draft
- Upload Document
- Repository
- Preview
- Download

---

## ApprovalController

Tanggung jawab:

- Submit Approval
- Approve
- Reject

---

## VerificationController

Tanggung jawab:

- Public Verification

---

## TemplateController

Tanggung jawab:

- CRUD Template

---

## SettingsController

Tanggung jawab:

- DDMS Configuration

---

# 6. Form Request

## StoreDocumentRequest

Validasi pembuatan dokumen.

---

## UpdateDocumentRequest

Validasi perubahan dokumen.

---

## SubmitApprovalRequest

Validasi submit approval.

---

## ApprovalRequest

Validasi approval.

---

## RejectApprovalRequest

Validasi reject.

---

## UploadDocumentRequest

Validasi upload.

---

## TemplateRequest

Validasi template.

---

# 7. Services

## DocumentService

Tanggung jawab:

- Create Draft
- Update Draft
- Delete Draft
- Generate PDF
- Repository

---

## ApprovalService

Tanggung jawab:

- Submit
- Approve
- Reject
- PIN Validation

---

## NumberingService

Generate nomor surat.

---

## QRService

Generate QR.

---

## PDFService

Generate PDF.

---

## SendDocumentService

Mengirim dokumen.

---

## VerificationService

Validasi QR.

---

## TemplateService

Kelola template.

---

## SettingsService

Kelola konfigurasi.

---

# 8. Repository

## DocumentRepository

Mengelola Document.

---

## ApprovalRepository

Mengelola Approval.

---

## NumberingRepository

Mengelola nomor surat.

---

## QRRepository

Mengelola QR.

---

## SendHistoryRepository

Mengelola riwayat kirim.

---

## VerificationRepository

Mengelola log verifikasi.

---

## TemplateRepository

Mengelola template.

---

# 9. Models

- Document
- DocumentApproval
- DocumentNumbering
- DocumentQRVerification
- DocumentSendHistory
- DocumentVerificationLog
- DocumentTemplate

Model existing:

- User
- Event
- Proposal
- Payment

---

# 10. Notifications

## DirectorApprovalNotification

Dikirim saat dokumen diajukan.

---

## ApprovalResultNotification

Dikirim setelah approval.

---

## DocumentSentNotification

Dikirim ke Client.

---

# 11. Policies

## DocumentPolicy

Hak akses Document.

---

## ApprovalPolicy

Hak akses Approval.

---

## TemplatePolicy

Hak akses Template.

---

## SettingsPolicy

Hak akses konfigurasi.

---

# 12. Jobs (Opsional)

Job yang dapat dijalankan secara asynchronous.

- GeneratePDFJob
- GenerateQRJob
- SendDocumentJob

---

# 13. Storage

Penyimpanan:

```
storage/app/private/ddms

├── official
├── invoices
├── receipts
├── uploads
├── templates
└── qr
```

---

# 14. Dependency Injection

```text
Controller

↓

Service Interface

↓

Service

↓

Repository Interface

↓

Repository
```

---

# 15. Error Handling

Menggunakan:

- Form Request Validation
- Exception Handler
- Custom Exception
- Transaction Database

---

# 16. Logging

Log yang dicatat:

- Approval
- Reject
- Upload
- Download
- QR Verification
- Send Document

---

# 17. Security

- Authorization menggunakan Policy.
- Approval menggunakan PIN.
- File disimpan pada private storage.
- QR menggunakan hash unik.
- Semua approval dicatat sebagai audit trail.

---

# 18. Backend Checklist

## Controller

- [ ] DocumentController
- [ ] ApprovalController
- [ ] VerificationController
- [ ] TemplateController
- [ ] SettingsController

---

## Service

- [ ] DocumentService
- [ ] ApprovalService
- [ ] NumberingService
- [ ] QRService
- [ ] PDFService
- [ ] VerificationService
- [ ] SendDocumentService
- [ ] TemplateService
- [ ] SettingsService

---

## Repository

- [ ] DocumentRepository
- [ ] ApprovalRepository
- [ ] NumberingRepository
- [ ] QRRepository
- [ ] SendHistoryRepository
- [ ] VerificationRepository
- [ ] TemplateRepository

---

## Notification

- [ ] DirectorApprovalNotification
- [ ] ApprovalResultNotification
- [ ] DocumentSentNotification

---

## Policy

- [ ] DocumentPolicy
- [ ] ApprovalPolicy
- [ ] TemplatePolicy
- [ ] SettingsPolicy

---

# 19. Status

Draft.
