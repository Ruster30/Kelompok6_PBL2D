# State Diagram
**Project:** Event Management System (Laravel)
**Module:** Digital Document Management System (DDMS)
**Version:** 1.0
**Status:** Draft
**Author:** Kelompok 6 PBL
**Last Update:** 24 Juli 2026

---

# 1. Pendahuluan

## 1.1 Tujuan

Dokumen ini menjelaskan perubahan status (state transition) setiap entity utama pada Digital Document Management System (DDMS).

State Diagram digunakan sebagai dasar implementasi:

- Service Layer
- Validation
- Authorization
- Notification
- Workflow

---

# 2. Ruang Lingkup

State Diagram hanya membahas entity yang dimiliki DDMS.

Workflow Proposal, Payment, Event, Timeline, Vendor, dan Contract tetap berada pada modul masing-masing.

---

# 3. State Diagram Document

Status yang dimiliki Document.

```text
Create
  │
  ▼
Draft
  │
  ▼
Waiting Approval
  │
 ┌┴──────────────┐
 │               │
 ▼               ▼
Rejected     Approved
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
                │
                ▼
            Sent
                │
                ▼
           Archived
```

---

## Status

| Status | Keterangan |
|----------|------------|
| Draft | Dokumen sedang dibuat |
| Waiting Approval | Menunggu persetujuan Director |
| Rejected | Ditolak Director |
| Approved | Disetujui Director |
| Repository | Dokumen telah diarsipkan |
| Sent | Dokumen telah dikirim |
| Archived | Dokumen selesai |

---

# 4. State Diagram Approval

```text
Pending
   │
 ┌─┴────────┐
 │          │
 ▼          ▼
Approved  Rejected
```

---

## Status Approval

| Status | Keterangan |
|---------|------------|
| Pending | Menunggu approval |
| Approved | Disetujui |
| Rejected | Ditolak |

---

# 5. State Diagram Numbering

```text
Waiting
   │
   ▼
Generated
```

---

# 6. State Diagram QR Verification

```text
Inactive
    │
    ▼
Generated
    │
    ▼
Verified
```

---

# 7. State Diagram Send History

```text
Pending
   │
 ┌─┴────────────┐
 │              │
 ▼              ▼
Sent         Failed
```

---

# 8. State Diagram Verification History

```text
Scan
 │
 ▼
Checking
 │
 ┌────┴─────────┐
 │              │
 ▼              ▼
Valid       Invalid
```

---

# 9. Allowed Transition

## Document

| From | To |
|------|----|
| Draft | Waiting Approval |
| Waiting Approval | Approved |
| Waiting Approval | Rejected |
| Rejected | Draft |
| Approved | Repository |
| Repository | Sent |
| Sent | Archived |

---

## Approval

| From | To |
|------|----|
| Pending | Approved |
| Pending | Rejected |

---

## QR Verification

| From | To |
|------|----|
| Inactive | Generated |
| Generated | Verified |

---

## Send History

| From | To |
|------|----|
| Pending | Sent |
| Pending | Failed |

---

# 10. Invalid Transition

Transisi berikut tidak diperbolehkan.

| Tidak Diizinkan |
|-----------------|
| Draft → Approved |
| Draft → Repository |
| Waiting Approval → Archived |
| Rejected → Repository |
| Pending Approval → Sent |
| Repository → Draft |

---

# 11. Business Rules

- Dokumen hanya dapat masuk Repository setelah Approved.
- QR hanya dapat dibuat setelah nomor surat tersedia.
- Nomor surat hanya dibuat setelah Approval.
- Dokumen yang ditolak kembali ke status Draft.
- Dokumen Archived tidak dapat diedit.

---

# 12. Mapping Status Laravel

Rekomendasi enum/status pada implementasi Laravel.

## Document

```
draft
waiting_approval
approved
rejected
repository
sent
archived
```

---

## Approval

```
pending
approved
rejected
```

---

## QR Verification

```
inactive
generated
verified
```

---

## Send History

```
pending
sent
failed
```

---

# 13. Output

State Diagram menjadi dasar implementasi:

- Enum
- Validation
- Service Layer
- Controller
- Notification

---

# 14. Status

Draft.
