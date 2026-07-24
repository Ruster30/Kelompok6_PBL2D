# Frontend Design

**Project:** Event Management System (Laravel 12)
**Module:** Digital Document Management System (DDMS)
**Version:** 1.0
**Status:** Final
**Author:** Kelompok 6 PBL
**Frontend Stack:** Blade + Bootstrap 5 + JavaScript

---

# 1. Pendahuluan

## 1.1 Tujuan

Dokumen ini menjelaskan rancangan antarmuka (Frontend) untuk modul Digital Document Management System (DDMS).

Frontend dirancang dengan prinsip:

- User Friendly
- Responsive
- Consistent UI
- Easy Navigation
- Bootstrap Component
- Reuse Existing Layout

Dokumen ini menjadi acuan implementasi Blade View pada Laravel.

---

# 2. Design Principles

Seluruh halaman mengikuti prinsip berikut.

- Responsive Design
- Bootstrap 5 Component
- Reuse Existing Layout
- Minimal Click Navigation
- Consistent Color
- Consistent Typography
- Mobile Friendly

---

# 3. User Roles

Empat role utama yang menggunakan DDMS.

| Role | Hak Akses |
|--------|-----------|
| Admin | Mengelola seluruh dokumen |
| Director | Approval Dokumen |
| Client | Melihat & Mengunduh Dokumen |
| Vendor | Upload Dokumen Pendukung |

---

# 4. Sitemap

```
Dashboard

│

├── Repository

│   ├── Official Documents

│   ├── General Documents

│   ├── Invoice

│   └── Receipt

│

├── Draft Documents

│

├── Approval

│

├── Templates

│

├── Verification

│

├── Activity Log

│

└── Settings
```

---

# 5. Menu Sidebar

## Admin

Dashboard

Repository

Draft

Approval

Templates

Activity Log

Settings

---

## Director

Dashboard

Approval

Repository

---

## Client

Dashboard

My Documents

Verification

---

## Vendor

Dashboard

Upload Documents

Repository

---

# 6. Halaman Dashboard DDMS

## Widget

Jumlah Draft

Jumlah Menunggu Approval

Jumlah Disetujui

Jumlah Ditolak

Jumlah Dokumen

Jumlah Verifikasi QR

Aktivitas Hari Ini

---

## Recent Activity

Menampilkan:

- Dokumen terbaru
- Approval terbaru
- Upload terbaru
- QR Verification terbaru

---

# 7. Repository Page

Tabel utama.

Kolom:

- Nomor
- Judul
- Jenis
- Event
- Versi
- Status
- Tanggal
- Aksi

Aksi:

Preview

Download

History

Edit

Delete

---

# 8. Draft Page

Menampilkan dokumen Draft.

Action:

Edit

Delete

Submit Approval

Preview

---

# 9. Approval Page

Director melihat seluruh dokumen.

Card Approval:

Judul

Jenis

Event

Versi

Tanggal

Button:

Preview

Approve

Reject

---

# 10. Detail Document

Informasi:

Nomor Surat

Status

QR Code

Approval

Version

Riwayat

Button:

Download

Preview

History

---

# 11. Template Management

CRUD Template.

Kolom:

Nama

Jenis

Blade View

Status

Action

---

# 12. Activity Log

Menampilkan:

Tanggal

User

Aktivitas

Dokumen

IP Address

Filter:

Tanggal

User

Jenis Aktivitas

---

# 13. Verification Page

Halaman publik.

User scan QR.

Sistem menampilkan:

Valid

atau

Invalid

Informasi:

Nomor Surat

Judul

Tanggal

Status

---

# 14. Settings Page

Konfigurasi:

Nomor Surat

Approval PIN

QR

Template

Storage

Notification

---

# 15. Modal Components

Digunakan untuk:

Preview PDF

Reject Reason

Delete Confirmation

Approval PIN

Upload File

---

# 16. Alert Components

Bootstrap Alert.

Jenis:

Success

Danger

Warning

Info

---

# 17. Badge Components

Draft

Waiting Approval

Approved

Rejected

Archived

---

# 18. Button Standard

Primary

Save

Secondary

Back

Success

Approve

Danger

Reject

Warning

Submit Approval

Info

Preview

Dark

History

---

# 19. Table Standard

Menggunakan Bootstrap Table.

Fitur:

Pagination

Search

Sorting

Filter

Responsive

---

# 20. Form Standard

Seluruh form menggunakan:

Bootstrap Validation

Laravel Error Message

Old Input

CSRF Protection

---

# 21. Responsive Design

Desktop

Sidebar

Table

Modal Besar

---

Tablet

Sidebar Collapse

Responsive Table

---

Mobile

Hamburger Menu

Card Layout

Responsive Form

---

# 22. UI Components

Komponen yang digunakan kembali.

- Navbar
- Sidebar
- Breadcrumb
- Page Header
- Card Widget
- Data Table
- Modal
- Alert
- Badge
- Pagination

---

# 23. Navigation Flow

## Admin

Dashboard

↓

Repository

↓

Preview

↓

Submit Approval

↓

Approval

↓

Repository

↓

Download

---

## Director

Dashboard

↓

Approval

↓

Preview

↓

Approve

↓

Repository

---

## Client

Dashboard

↓

My Documents

↓

Preview

↓

Download

---

## Vendor

Dashboard

↓

Upload

↓

Repository

---

# 24. Permission Matrix

| Menu | Admin | Director | Client | Vendor |
|-------|:----:|:--------:|:------:|:------:|
| Dashboard | ✔ | ✔ | ✔ | ✔ |
| Repository | ✔ | ✔ | ✔ | ✔ |
| Draft | ✔ | ✖ | ✖ | ✖ |
| Approval | ✔ | ✔ | ✖ | ✖ |
| Templates | ✔ | ✖ | ✖ | ✖ |
| Activity Log | ✔ | ✖ | ✖ | ✖ |
| Settings | ✔ | ✖ | ✖ | ✖ |
| Upload | ✖ | ✖ | ✖ | ✔ |
| Verification | ✔ | ✔ | ✔ | ✔ |

---

# 25. Integrasi Backend

| Halaman | Controller | Service |
|----------|------------|----------|
| Dashboard | DashboardController | DashboardService |
| Repository | DocumentController | DocumentBuilderService |
| Approval | ApprovalController | DocumentApprovalService |
| Verification | VerificationController | DocumentVerificationService |
| Templates | TemplateController | TemplateService |
| Settings | SettingsController | SettingsService |
| Activity Log | ActivityController | ActivityLogService |

---

# 26. Testing Frontend

Pengujian meliputi:

- Responsive Test
- Cross Browser Test
- Form Validation
- Navigation Test
- Permission Test
- Download PDF Test
- QR Verification Test

---

# 27. Kesimpulan

Frontend DDMS dirancang sebagai perluasan dari Event Management System dengan tetap mempertahankan konsistensi tampilan aplikasi yang sudah ada.

Seluruh halaman menggunakan Blade, Bootstrap 5, dan komponen yang dapat digunakan kembali (Reusable Components). Dengan pendekatan ini, implementasi antarmuka menjadi lebih mudah dipelihara, responsif, dan selaras dengan arsitektur backend yang telah dirancang.
