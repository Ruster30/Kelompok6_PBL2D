# Architecture Audit

**Project:** Event Management System (Laravel 12)  
**Module:** Digital Document Management System (DDMS)  
**Version:** 1.0  
**Status:** Final  
**Author:** Kelompok 6 PBL  
**Reference:** Laporan Audit Arsitektur – 24 Juli 2026

---

# 1. Pendahuluan

## 1.1 Latar Belakang

Sebelum mengembangkan Digital Document Management System (DDMS), dilakukan audit terhadap arsitektur Event Management System yang telah berjalan.

Audit ini bertujuan untuk:

- Mengidentifikasi kondisi arsitektur saat ini.
- Menentukan komponen yang dapat digunakan kembali.
- Mengidentifikasi gap antara sistem existing dengan kebutuhan DDMS.
- Menentukan strategi integrasi yang aman tanpa mengganggu workflow bisnis yang telah berjalan.

Dokumen ini menjadi dasar seluruh keputusan desain pada dokumen arsitektur berikutnya.

---

# 2. Ringkasan Audit

Berdasarkan hasil audit, project saat ini merupakan Event Management System berbasis Laravel yang telah memiliki arsitektur yang cukup baik.

Karakteristik utama sistem:

- Framework Laravel 12
- Laravel Breeze Authentication
- Role Based Access Control
- Service Layer Pattern
- Repository Pattern
- PDF Generation
- Event sebagai central business entity

Audit juga menunjukkan bahwa sistem telah memiliki pemisahan business logic yang baik melalui Service Layer, meskipun implementasi Repository Pattern masih belum sepenuhnya konsisten. :contentReference[oaicite:0]{index=0}

---

# 3. Existing Architecture

## 3.1 Framework

- Laravel 12
- PHP 8.x
- Laravel Breeze Authentication

---

## 3.2 Authentication

Role yang tersedia:

- Admin
- Client
- Vendor

Autentikasi menggunakan middleware berbasis role sehingga setiap pengguna memperoleh akses sesuai tanggung jawabnya. :contentReference[oaicite:1]{index=1}

---

## 3.3 Service Layer

Audit menunjukkan:

- 28 Service Class
- Controller tipis (Thin Controller)
- Business Logic berada pada Service Layer
- Constructor Property Promotion telah diterapkan secara konsisten

Hal ini menunjukkan bahwa arsitektur Service Layer telah menjadi standar implementasi project. :contentReference[oaicite:2]{index=2}

---

## 3.4 Repository Pattern

Repository Pattern telah digunakan pada beberapa modul, antara lain:

- Document
- Proposal
- Vendor
- Task
- Timeline
- RAB
- Notification
- Event Vendor

Namun implementasinya belum sepenuhnya konsisten karena masih terdapat service yang mengakses model Eloquent secara langsung tanpa melalui repository. :contentReference[oaicite:3]{index=3}

---

## 3.5 PDF Generation

Sistem telah memiliki mekanisme pembuatan PDF menggunakan DomPDF.

Template yang tersedia meliputi:

- Proposal
- Kontrak
- Invoice
- RAB
- Kwitansi

Dengan demikian, DDMS tidak perlu membangun mekanisme PDF dari awal, tetapi cukup mengintegrasikan proses approval, QR Code, dan repository dokumen ke dalam mekanisme yang sudah ada. :contentReference[oaicite:4]{index=4}

---

# 4. Existing Database

Audit menunjukkan bahwa database terdiri dari sekitar 40 tabel, dengan entitas **events** sebagai pusat hubungan antar modul.

Tabel bisnis utama meliputi:

- events
- proposals
- contracts
- invoices
- payments
- payment_schemes
- rabs
- negotiations
- documents
- document_sends

Hubungan antar tabel menunjukkan bahwa sebagian besar dokumen telah terhubung dengan event sebagai entitas utama. :contentReference[oaicite:5]{index=5}

---

# 5. Existing Business Workflow

Workflow utama sistem saat ini:

1. Client membuat permintaan event.
2. Admin memproses permintaan.
3. Proposal dibuat dan dikirim.
4. Client menerima atau melakukan negosiasi.
5. Setelah proposal diterima:
   - RAB dibuat.
   - Kontrak dibuat.
   - Invoice dibuat.
6. Client melakukan pembayaran.
7. Admin melakukan verifikasi pembayaran.
8. Kwitansi dikirim.
9. Vendor menjalankan tugas dan dokumentasi event.

Workflow ini telah berjalan dengan baik dan menjadi dasar integrasi DDMS. :contentReference[oaicite:6]{index=6}

---

# 6. Temuan Audit

Audit menemukan beberapa kekurangan pada pengelolaan dokumen.

## 6.1 Document Status

Tabel `documents` belum memiliki status dokumen.

Seluruh dokumen langsung dianggap aktif setelah dibuat.

---

## 6.2 Approval Workflow

Belum terdapat mekanisme approval dokumen sebelum dikirim kepada client.

---

## 6.3 QR Verification

Belum terdapat QR Code untuk memverifikasi keaslian dokumen.

---

## 6.4 Digital Signature

Belum tersedia integrasi Tanda Tangan Elektronik (TTE).

---

## 6.5 Audit Trail

Belum terdapat pencatatan aktivitas dokumen secara menyeluruh.

---

## 6.6 Versioning

Versioning hanya diterapkan pada Proposal.

Dokumen lain belum memiliki mekanisme versioning. :contentReference[oaicite:7]{index=7}

---

# 7. Gap Analysis

Perbandingan antara kondisi saat ini dan kebutuhan DDMS.

| Fitur | Existing | Target DDMS |
|--------|----------|-------------|
| Document Status | Tidak tersedia | Draft → Waiting Approval → Approved/Rejected |
| Approval Workflow | Tidak tersedia | Approval Director |
| QR Verification | Tidak tersedia | QR Code pada PDF |
| Digital Signature | Belum tersedia | Integrasi TTE |
| Versioning | Proposal saja | Semua dokumen |
| Auto Numbering | Parsial | Terpusat |
| Audit Trail | Belum tersedia | Seluruh aktivitas dicatat |
| Document History | Riwayat kirim | Riwayat lengkap |
| Vendor Access | Belum tersedia | Vendor dapat melihat dokumen tertentu |

Audit juga merekomendasikan penggunaan kembali tabel yang sudah ada dan hanya menambahkan atribut atau tabel baru bila benar-benar diperlukan. :contentReference[oaicite:8]{index=8}

---

# 8. Risiko Implementasi

Audit mengidentifikasi beberapa bagian sistem yang memiliki risiko tinggi apabila diubah.

Komponen yang perlu dijaga:

- Workflow pembayaran
- DocumentBuilderService
- Document Send Logic
- Proposal Versioning
- Timeline Auto Fill

Perubahan pada bagian tersebut harus dilakukan secara hati-hati agar tidak mengganggu proses bisnis yang sudah berjalan. :contentReference[oaicite:9]{index=9}

---

# 9. Strategi Integrasi DDMS

Audit merekomendasikan pendekatan **Architectural Extension**, yaitu menambahkan kemampuan baru tanpa mengubah workflow lama.

Tahapan implementasi:

### Fase 1 – Persiapan Database

- Menambahkan atribut baru pada tabel `documents`.
- Melakukan backfill terhadap data lama.
- Menambahkan tabel verifikasi dokumen.

### Fase 2 – Approval Workflow

- Menambahkan `DocumentApprovalService`.
- Menambahkan middleware pemeriksaan approval.
- Menggunakan Event dan Listener.

### Fase 3 – QR Verification

- Menambahkan QR Code pada PDF.
- Menyediakan endpoint publik untuk verifikasi.

### Fase 4 – Audit Trail

- Menambahkan tabel activity log.
- Mencatat seluruh aktivitas dokumen.

Prinsip utama integrasi adalah:

> **Tambahkan, jangan hapus. Semua workflow lama harus tetap berjalan.** :contentReference[oaicite:10]{index=10}

---

# 10. Dampak terhadap Dokumentasi DDMS

Hasil audit menjadi dasar penyusunan dokumen arsitektur berikut:

- DDMS Blueprint
- Domain Model
- Functional Requirements
- Business Rules
- Conceptual ERD
- Logical ERD
- Business Process
- State Diagram
- Sequence Diagram
- Migration Plan
- Data Dictionary
- Backend Design

Seluruh dokumen tersebut harus mengikuti prinsip integrasi yang direkomendasikan oleh hasil audit.

---

# 11. Kesimpulan

Hasil audit menunjukkan bahwa Event Management System telah memiliki fondasi arsitektur yang baik melalui penerapan Service Layer, Repository Pattern, dan pemisahan tanggung jawab yang jelas.

Namun, sistem masih memiliki kekurangan pada aspek pengelolaan dokumen, seperti status dokumen, approval workflow, QR verification, audit trail, dan versioning.

Oleh karena itu, DDMS dikembangkan sebagai **modul tambahan (Architectural Extension)** yang memanfaatkan arsitektur dan workflow yang sudah ada, dengan tujuan meningkatkan pengelolaan dokumen tanpa mengubah proses bisnis utama.
