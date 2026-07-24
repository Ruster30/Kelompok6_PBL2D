# Backend Design

**Project:** Event Management System (Laravel 12)
**Module:** Digital Document Management System (DDMS)
**Version:** 2.0
**Status:** Final
**Author:** Kelompok 6 PBL
**Architecture:** Service Layer + Repository Pattern
**Reference:** Architecture Audit v1.0

---

# 1. Pendahuluan

## 1.1 Tujuan

Dokumen ini menjelaskan rancangan implementasi backend DDMS pada Event Management System.

Backend DDMS dikembangkan dengan pendekatan **Architectural Extension**, yaitu memperluas kemampuan sistem yang telah ada tanpa mengubah proses bisnis utama.

Dokumen ini menjadi acuan implementasi:

- Controller
- Form Request
- Service
- Repository
- Model
- Event
- Listener
- Queue
- Policy
- Middleware
- Notification

---

# 2. Design Principles

Backend mengikuti prinsip berikut.

- Thin Controller
- Service Layer
- Repository Pattern
- Dependency Injection
- SOLID Principle
- Event Driven Architecture
- Queue Processing
- Policy Authorization
- Separation of Responsibility

---

# 3. Backend Architecture

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

Service dapat memanggil:

- Notification
- Event
- Queue
- Storage
- PDF Generator
- QR Generator

---

# 4. Existing Components (Dipertahankan)

Komponen berikut **tidak diganti**, hanya diperluas.

## Existing Services

- AdminProposalService
- AdminPaymentService
- ClientService
- TimelineService
- RabService
- VendorDashboardService
- AdminAnalyticsService
- DocumentBuilderService

## Existing Repository

- DocumentRepository
- ProposalRepository
- TimelineRepository
- VendorRepository
- RabRepository
- NotificationRepository

---

# 5. New Components

## Services

### DocumentApprovalService

Tanggung jawab:

- Submit Approval
- Approve Document
- Reject Document
- Validasi PIN Director
- Mengubah status dokumen

---

### DocumentVerificationService

Tanggung jawab:

- Verifikasi QR Code
- Validasi hash
- Menyimpan Verification Log

---

### DocumentNumberingService

Tanggung jawab:

- Generate nomor surat
- Menjamin nomor unik
- Menangani format penomoran

---

### ActivityLogService

Tanggung jawab:

- Menyimpan Audit Trail
- Logging seluruh aktivitas DDMS

---

# 6. Existing Service Extension

## DocumentBuilderService

Service ini **tetap dipertahankan**.

DDMS hanya menambahkan kemampuan baru.

Fitur tambahan:

- Generate Draft
- Generate PDF Final
- Generate QR Code
- Generate Nomor Surat
- Integrasi Approval
- Integrasi Repository

---

# 7. Controllers

## DocumentController

Fungsi:

- Create Draft
- Update Draft
- Delete Draft
- Preview
- Download
- Repository

---

## ApprovalController

Fungsi:

- Submit Approval
- Approve
- Reject

---

## VerificationController

Fungsi:

- Public QR Verification

---

## TemplateController

CRUD Template.

---

## SettingsController

Konfigurasi DDMS.

---

# 8. Form Requests

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

Validasi upload file.

---

## VerifyDocumentRequest

Validasi QR Verification.

---

# 9. Repository Layer

Repository baru.

## DocumentApprovalRepository

Approval data.

---

## DocumentNumberingRepository

Nomor surat.

---

## DocumentVerificationRepository

QR Verification.

---

## ActivityLogRepository

Audit Trail.

---

Repository existing tetap digunakan.

---

# 10. Event Driven Architecture

## Event

- DocumentCreated
- DocumentUpdated
- DocumentSubmitted
- DocumentApproved
- DocumentRejected
- DocumentSent
- DocumentVerified

---

## Listener

- GenerateDocumentNumber
- GenerateQRCode
- GeneratePDF
- SaveRepository
- SendNotification
- WriteActivityLog

---

# 11. Queue

Queue digunakan untuk proses berat.

Job:

- GeneratePDFJob
- GenerateQRCodeJob
- SendDocumentJob

Keuntungan:

- Respon aplikasi lebih cepat.
- Proses berat berjalan di background.

---

# 12. Middleware

Middleware baru.

## CheckDocumentApproval

Mencegah:

- Download Final
- Send To Client

apabila status dokumen belum Approved.

---

# 13. Notification

Notification baru.

## DirectorApprovalNotification

Memberi tahu Director.

---

## ApprovalResultNotification

Memberi tahu Admin.

---

## DocumentSentNotification

Memberi tahu Client.

---

## VerificationNotification (Opsional)

Memberi tahu apabila terjadi verifikasi tertentu sesuai kebutuhan bisnis.

---

# 14. Policy

## DocumentPolicy

Hak akses dokumen.

---

## ApprovalPolicy

Hak akses approval.

---

## VerificationPolicy

Hak akses verifikasi.

---

## TemplatePolicy

Hak akses template.

---

# 15. Storage Structure

```text
storage/

└── app/

    └── private/

        └── ddms/

            ├── official/
            ├── invoice/
            ├── receipt/
            ├── uploaded/
            ├── template/
            ├── qr/
            └── archive/
```

---

# 16. Security

Keamanan sistem meliputi:

- Laravel Policy
- Middleware
- Approval PIN
- Private Storage
- Signed URL (jika diperlukan)
- QR Hash
- Audit Trail

---

# 17. Audit Trail

Seluruh aktivitas dicatat.

Aktivitas:

- Create
- Update
- Delete
- Submit Approval
- Approve
- Reject
- Generate Number
- Generate QR
- Generate PDF
- Download
- Upload
- Send
- Verify

---

# 18. Dependency Flow

```text
Route

↓

Controller

↓

Form Request

↓

Service

↓

Repository

↓

Model

↓

Database

↓

Event

↓

Listener

↓

Queue

↓

Notification

↓

Activity Log
```

---

# 19. Deployment Checklist

## Backend

- [ ] Controller
- [ ] Request
- [ ] Service
- [ ] Repository
- [ ] Model
- [ ] Policy

---

## Infrastructure

- [ ] Queue
- [ ] Event
- [ ] Listener
- [ ] Notification
- [ ] Middleware

---

## Database

- [ ] Migration
- [ ] Seeder
- [ ] Backfill

---

## Testing

- [ ] Unit Test
- [ ] Feature Test
- [ ] Integration Test

---

# 20. Roadmap Implementasi

## Tahap 1

- Database
- Migration
- Model

---

## Tahap 2

- Repository
- Service

---

## Tahap 3

- Controller
- Request
- Policy

---

## Tahap 4

- Event
- Listener
- Queue

---

## Tahap 5

- Notification
- Activity Log

---

## Tahap 6

- Testing
- Deployment

---

# 21. Kesimpulan

Backend DDMS dikembangkan sebagai perluasan dari Event Management System yang telah ada.

Pendekatan ini mempertahankan Service Layer, Repository Pattern, dan workflow bisnis yang sudah stabil, sekaligus menambahkan kemampuan baru seperti Approval Workflow, QR Verification, Audit Trail, serta Event Driven Architecture.

Dengan pendekatan **Architectural Extension**, implementasi DDMS dapat dilakukan secara bertahap tanpa mengganggu modul yang sudah berjalan.
