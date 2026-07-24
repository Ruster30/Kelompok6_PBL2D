# Digital Document Management System (DDMS) Blueprint
**Project:** Event Management System (Laravel)
**Version:** 1.0
**Status:** Draft
**Author:** Kelompok 6 PBL
**Last Update:** 24 Juli 2026

---

# 1. Pendahuluan

## 1.1 Latar Belakang

Saat ini sistem Event Management telah memiliki beberapa modul pengelolaan dokumen seperti Proposal, Kontrak, Invoice, RAB, dan Document Builder. Namun, setiap modul masih berjalan sendiri-sendiri dan belum memiliki mekanisme pengelolaan dokumen secara terpusat.

Untuk itu dikembangkan **Digital Document Management System (DDMS)** sebagai lapisan (layer) yang mengelola seluruh siklus hidup dokumen resmi perusahaan.

DDMS tidak menggantikan workflow bisnis yang telah ada, melainkan mengintegrasikan seluruh dokumen ke dalam satu sistem manajemen dokumen yang terpusat.

---

# 2. Tujuan

DDMS bertujuan untuk:

- Mengelola seluruh dokumen perusahaan secara terpusat.
- Menambahkan Approval Workflow.
- Menambahkan QR Verification.
- Menambahkan Nomor Surat Otomatis.
- Menambahkan Audit Trail.
- Menjadi pusat arsip seluruh dokumen.

---

# 3. Ruang Lingkup

DDMS hanya mengelola dokumen.

Workflow bisnis tetap berada pada modul masing-masing.

Contoh:

Proposal → Modul Proposal

Invoice → Modul Payment

Kontrak → Modul Contract

RAB → Modul RAB

DDMS hanya mengelola hasil dokumen dari modul-modul tersebut.

---

# 4. Prinsip Arsitektur

DDMS dibangun berdasarkan prinsip berikut.

## 4.1 Single Source of Truth

Semua dokumen berada pada satu Repository Dokumen.

Baik dokumen hasil upload maupun hasil generate.

---

## 4.2 Layer Architecture

DDMS merupakan layer di atas workflow bisnis.

Workflow Proposal, Kontrak, Invoice, dan Payment tidak diubah.

---

## 4.3 Separation of Responsibility

Workflow tetap berada pada modul bisnis.

DDMS hanya mengelola:

- Approval
- Arsip
- QR
- Nomor Surat
- Riwayat Dokumen

---

## 4.4 Incremental Development

Semua perubahan dilakukan dengan prinsip:

> Tambahkan, jangan hapus.

Workflow lama harus tetap berjalan.

---

# 5. Jenis Dokumen

## 5.1 Surat Resmi

Memerlukan Approval Direktur.

Contoh:

- Surat Penawaran
- Surat Revisi Penawaran
- Surat Kontrak
- RAB

Karakteristik:

- Draft
- Approval
- Nomor Surat
- QR Verification
- PDF Final

---

## 5.2 Dokumen Umum

Upload manual.

Contoh:

- Proposal
- Company Profile
- Portofolio
- Brosur
- Lampiran

Karakteristik:

- Upload
- Arsip
- Preview
- Download
- Send

---

## 5.3 Dokumen Pembayaran

Contoh:

- Invoice DP
- Invoice Pelunasan
- Kwitansi

Mengikuti workflow pembayaran.

Tidak menggunakan Approval.

---

# 6. Modul DDMS

## Document Builder

Membuat Draft.

Output:

Draft Dokumen.

---

## Approval Workflow

Approver:

Direktur

Fitur:

- Submit Approval
- Approve
- Reject
- Approval Note

---

## PDF Generator

Setelah Approval:

- Generate Nomor Surat
- Generate QR
- Tempel TTD Direktur
- Generate PDF Final

---

## Repository Dokumen

Pusat seluruh dokumen.

Fitur:

- Preview
- Download
- Send
- History
- Search

---

## QR Verification

QR hanya dibuat setelah Approval.

QR mengarah ke halaman verifikasi publik.

---

## History

Menyimpan:

- Approval History
- Send History
- Verification History

---

# 7. Role

## Super Administrator

- Kelola User
- Kelola Setting
- Audit
- Template

---

## Admin

- Generate Draft
- Upload Dokumen
- Submit Approval
- Kirim Dokumen

---

## Direktur

- Preview
- Approve
- Reject

Approval memerlukan PIN Approval.

---

## Client

- Preview
- Download

---

## Vendor

Tidak memiliki akses DDMS.

---

# 8. Workflow

## Surat Resmi

Draft

↓

Submit Approval

↓

Direktur Review

↓

PIN Approval

↓

Approve

↓

Generate Nomor Surat

↓

Generate QR

↓

Generate PDF Final

↓

Repository

↓

Send

---

Jika Ditolak

Draft

↓

Submit

↓

Reject

↓

Catatan Revisi

↓

Kembali Draft

---

## Dokumen Umum

Upload

↓

Repository

↓

Preview

↓

Send

---

## Dokumen Pembayaran

Generate Invoice

↓

Pembayaran

↓

Verifikasi

↓

Generate Kwitansi

↓

Repository

---

# 9. Integrasi

DDMS terintegrasi dengan:

- Events
- Proposal
- Contract
- Invoice
- Payment
- Notification
- Document Builder

Workflow masing-masing modul tetap dipertahankan.

---

# 10. Keamanan

Approval dilakukan oleh Direktur.

Approval memerlukan PIN Approval.

QR hanya dibuat setelah Approval.

PDF Final tidak dapat diubah.

---

# 11. Target Versi 1.0

Fitur yang termasuk dalam DDMS v1.0

- Approval Workflow
- Approval PIN
- QR Verification
- Nomor Surat
- Repository Dokumen
- Approval History
- Send History
- Verification History

---

# 12. Tidak Termasuk v1.0

Fitur berikut direncanakan untuk versi selanjutnya.

- TTE BSrE
- Multi-Level Approval
- OCR
- AI Document Assistant
- Template Editor
- Integrasi SRIKANDI

---

# 13. Prinsip Implementasi

1. Tidak mengubah workflow bisnis yang telah berjalan.
2. Semua perubahan dilakukan secara bertahap.
3. Menambah fitur tanpa menghapus fitur lama.
4. Mengutamakan kompatibilitas dengan sistem yang sudah ada.
5. Menjadikan DDMS sebagai pusat pengelolaan dokumen perusahaan.

---

# 14. Roadmap

Tahap 1

- Architecture Audit

Status:

✅ Selesai

---

Tahap 2

- Blueprint DDMS

Status:

🔄 Draft

---

Tahap 3

- Domain Model

Status:

⏳ Belum Dimulai

---

Tahap 4

- ERD

Status:

⏳ Belum Dimulai

---

Tahap 5

- Migration Plan

Status:

⏳ Belum Dimulai

---

Tahap 6

- Backend Development

Status:

⏳ Belum Dimulai

---

Tahap 7

- Frontend Development

Status:

⏳ Belum Dimulai

---

Tahap 8

- Integration Testing

Status:

⏳ Belum Dimulai
