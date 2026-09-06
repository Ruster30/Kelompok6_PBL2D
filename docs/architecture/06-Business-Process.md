# Business Process
**Project:** Event Management System (Laravel)
**Module:** Digital Document Management System (DDMS)
**Version:** 1.0
**Status:** Draft
**Author:** Kelompok 6 PBL
**Last Update:** 24 Juli 2026

---

# 1. Pendahuluan

## 1.1 Tujuan

Dokumen ini menjelaskan alur proses bisnis (Business Process) pada Digital Document Management System (DDMS).

Business Process menjadi acuan implementasi Service Layer, Controller, Notification, serta integrasi dengan modul Event Management System.

---

# 2. Ruang Lingkup

DDMS menangani lifecycle dokumen mulai dari pembuatan hingga pengarsipan.

Workflow bisnis Proposal, Event, Payment, Vendor, Timeline, dan Negotiation tetap berada pada modul masing-masing.

---

# 3. Aktor

| Aktor | Tanggung Jawab |
|--------|----------------|
| Admin | Membuat dan mengelola dokumen |
| Director | Melakukan approval |
| Client | Menerima dan mengunduh dokumen |
| Public | Melakukan verifikasi QR |

---

# 4. Workflow Dokumen Resmi

```text
Create Draft
      │
      ▼
Edit Draft
      │
      ▼
Submit Approval
      │
      ▼
Director Review
      │
 ┌────┴────┐
 │         │
 ▼         ▼
Reject   Approve
 │         │
 ▼         ▼
Back    Generate Number
Draft        │
             ▼
      Generate QR
             │
             ▼
      Generate PDF
             │
             ▼
      Repository
             │
             ▼
      Send Client
             │
             ▼
        Archive
```

---

# 5. Workflow Dokumen Umum

```text
Upload
   │
   ▼
Repository
   │
   ▼
Preview
   │
   ▼
Download
```

Dokumen umum tidak memiliki approval maupun nomor surat.

---

# 6. Workflow Invoice

```text
Generate Invoice
        │
        ▼
Repository
        │
        ▼
Client Download
```

Invoice mengikuti workflow Payment.

---

# 7. Workflow Kwitansi

```text
Generate Receipt
        │
        ▼
Repository
        │
        ▼
Client Download
```

---

# 8. Workflow Verifikasi QR

```text
Scan QR
    │
    ▼
Open Verification Page
    │
    ▼
Check Hash
    │
 ┌──┴───┐
 │      │
 ▼      ▼
Valid  Invalid
 │
 ▼
Show Document Information
```

---

# 9. Workflow Pengiriman

```text
Admin
 │
 ▼
Select Document
 │
 ▼
Send Client
 │
 ▼
Create Send History
 │
 ▼
Notification
```

---

# 10. Workflow Approval

```text
Admin
 │
 ▼
Submit Approval
 │
 ▼
Notification
 │
 ▼
Director Login
 │
 ▼
PIN Verification
 │
 ┌───────────────┐
 │               │
 ▼               ▼
Reject       Approve
 │               │
 ▼               ▼
Draft      Generate Number
                 │
                 ▼
           Generate QR
                 │
                 ▼
           Generate PDF
                 │
                 ▼
            Repository
```

---

# 11. Business Rules

- Approval hanya dilakukan oleh Director.
- PIN Approval wajib diverifikasi.
- QR dibuat setelah approval.
- Nomor surat dibuat setelah approval.
- Repository hanya menyimpan dokumen final.
- Semua aktivitas approval dicatat sebagai audit trail.

---

# 12. Integrasi Modul

| Modul | Integrasi |
|--------|-----------|
| Proposal | Generate Draft |
| Contract | Generate Contract |
| Payment | Generate Invoice |
| Event | Referensi Event |
| Notification | Approval & Send Notification |

---

# 13. Output

Business Process menjadi dasar implementasi:

- Service Layer
- Controller
- Notification
- State Diagram
- Sequence Diagram

---

# 14. Status

Draft.
