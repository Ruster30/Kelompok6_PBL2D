# Desain Tabel document_approvals - Phase 2 DDMS (FINAL)

**Project:** Event Management System (Laravel 12)
**Module:** Digital Document Management System (DDMS)
**Tanggal:** 25 Juli 2026
**Author:** Senior Laravel Software Architect & Database Architect
**Status:** FINAL - APPROVED WITH MINOR REVISION
**Referensi:** DDMS Blueprint v1.0, Data Dictionary v2.0, Migration Plan v2.0

---

## 1. Struktur Final Tabel

### 1.1 Daftar Kolom

| No | Kolom | Tipe Data | Nullable | Default | Keterangan |
|---|---|---|---|---|---|
| 1 | id | bigint (PK, AI) | Tidak | - | Primary key standar Laravel |
| 2 | document_id | bigint (FK) | Tidak | - | Relasi ke dokumen yang diajukan approval |
| 3 | submitted_by | bigint (FK) | Tidak | - | Admin pengaju. Untuk akuntabilitas. |
| 4 | approver_id | bigint (FK) | Ya | NULL | Direktur yang review. Nullable: belum diketahui saat submit. |
| 5 | status | string(50) | Tidak | pending | pending / approved / rejected |
| 6 | approval_note | text | Ya | NULL | Catatan direktur saat approve/reject. |
| 7 | submitted_at | timestamp | Ya | NULL | Waktu pengajuan approval oleh admin. |
| 8 | reviewed_at | timestamp | Ya | NULL | Waktu direktur selesai review (approve/reject). Nama netral. |
| 9 | created_at | timestamp | Ya | NULL | Standar Laravel |
| 10 | updated_at | timestamp | Ya | NULL | Standar Laravel |

### 1.2 Catatan Desain

- reviewed_at mencakup waktu approve maupun reject. Tidak perlu rejected_at terpisah.
- approval_note mencakup catatan approve maupun reject. Validasi: wajib jika reject.
- ip_address, approval_method tidak disimpan di tabel ini. Cukup di activity_logs / service layer.

---

## 2. Daftar Perubahan

| Sebelum | Sesudah | Alasan |
|---|---|---|
| approved_at (timestamp, nullable) | reviewed_at (timestamp, nullable) | approved_at hanya mencakup approve. reviewed_at netral: waktu review selesai, apa pun hasilnya (approved/rejected). |
| Composite index (status, created_at) | Composite index (status, submitted_at) | Dashboard direktur menampilkan pending berdasarkan waktu pengajuan (submitted_at), bukan waktu buat record (created_at). submitted_at = kapan admin benar-benar submit. |

**Tidak ada perubahan lain.** Jumlah kolom tetap 10. FK, cascade rules, status values, workflow tidak berubah.

---

## 3. Index Final

| Nama Index | Tipe | Kolom | Fungsi |
|---|---|---|---|
| PRIMARY | Primary | id | Primary key |
| appr_document_id_index | Biasa | document_id | Riwayat approval per dokumen |
| appr_submitted_by_index | Biasa | submitted_by | Submission per admin |
| appr_approver_id_index | Biasa | approver_id | Approval per direktur |
| appr_doc_status_index | Composite | (document_id, status) | Filter status per dokumen |
| appr_status_submitted_index | Composite | (status, submitted_at) | Dashboard pending diurutkan waktu submit |

**Alasan submitted_at untuk index:**
- created_at = waktu record dibuat (bisa berbeda jauh dari submit jika draft lama).
- submitted_at = waktu admin submit (akurat untuk antrian FIFO direktur).

---

## 4. Workflow Final

Draft -> [Admin Submit] -> Pending -> [Direktur Review + PIN] -> Approved -> Generate Number -> Generate QR -> PDF Final -> Repository
                                                                   -> Rejected -> Kembali ke Draft (revisi) -> Submit Ulang

**Tahapan:**
1. Submit: document_approvals.status=pending, submitted_at=now, documents.status=pending
2. Review: Direktur lihat daftar pending via index (status, submitted_at)
3. Approve: status=approved, reviewed_at=now, approver_id=direktur, trigger numbering+QR+PDF
4. Reject: status=rejected, reviewed_at=now, approval_note=wajib, notifikasi admin
5. Riwayat: Satu dokumen bisa punya banyak approval record (audit trail lengkap)

---

## 5. Validasi Arsitektur

| Prinsip | Status | Verifikasi |
|---|---|---|
| Additive & Non-Disruptive | Lolos | CREATE TABLE baru. Tidak ubah existing. |
| Backward Compatible | Lolos | Kode existing tidak perlu diubah. |
| 3NF | Lolos | Approval dipisah dari documents. Kolom atomik. |
| Laravel Best Practice | Lolos | foreignId, restrictOnDelete, named index, string status. |
| Repository Pattern Ready | Lolos | Query approval sederhana dan terprediksi. |
| Service Layer Ready | Lolos | Domain approval jelas: submit, approve, reject. |
| Single Responsibility | Lolos | Hanya data approval. Tidak campur numbering/QR/audit. |
| No workflow change | Lolos | Draft -> Submit -> Pending -> Approved/Rejected. |
| No column count change | Lolos | 10 kolom (sama, hanya rename). |

---

## 6. Kesimpulan

**Desain tabel document_approvals dinyatakan SIAP untuk tahap implementasi migration.**

| Aspek | Status |
|---|---|
| Revisi approved_at -> reviewed_at | Selesai |
| Revisi index (status, created_at) -> (status, submitted_at) | Selesai |
| Struktur final | 10 kolom, 6 index, 3 FK |
| Risiko | Rendah |
| Siap implementasi | Ya |
