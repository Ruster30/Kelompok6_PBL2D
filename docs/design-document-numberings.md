# Desain Tabel document_numberings - Phase 2B DDMS (FINAL)

**Project:** Event Management System (Laravel 12)
**Module:** Digital Document Management System (DDMS)
**Tanggal:** 25 Juli 2026
**Author:** Senior Software Architect & Database Architect
**Status:** FINAL - APPROVED WITH REQUIRED REVISION

---

## 1. Daftar Perubahan

| No | Aspek | Sebelum | Sesudah | Alasan |
|---|---|---|---|---|
| 1 | Kolom period | varchar(20) | year: unsignedSmallInteger | Hanya menyimpan tahun. Numerik lebih efisien, jelas, dan sesuai domain. |
| 2 | Format nomor | SP/2026/001 (rekomendasi tunggal) | ALPHA/SP/2026/001 (rekomendasi final) | Mendukung multi-perusahaan. Lebih enterprise ready. |
| 3 | Kolom generated_at | timestamp, dipertahankan | **Dihapus** | Identik dengan created_at. Gunakan created_at sebagai timestamp generate. |
| 4 | Index generated_at | Ada | **Dihapus** | generated_at sudah dihapus. Index tidak relevan. |
| 5 | Total kolom | 10 | **9** | -1 kolom (generated_at) |
| 6 | Total index | 6 | **5** | -1 index (generated_at) |

---

## 2. Struktur Tabel Final (9 Kolom)

| No | Kolom | Tipe Data | Nullable | Default | Constraint | Alasan |
|---|---|---|---|---|---|---|
| 1 | id | bigint (PK, AI) | Tidak | - | PRIMARY | Standar Laravel |
| 2 | document_id | bigint (FK) | Tidak | - | UNIQUE + FK -> documents(id) CASCADE | Satu dokumen = satu nomor |
| 3 | document_number | varchar(100) | Tidak | - | UNIQUE | Nomor lengkap: ALPHA/SP/2026/001 |
| 4 | prefix | varchar(20) | Tidak | - | - | SP, KTR, INV, KW, RAB. Terpisah untuk query grouping. |
| 5 | year | unsignedSmallInteger | Tidak | - | - | Tahun: 2024-2099 (65535 max). Reset sequence tahunan. |
| 6 | sequence_number | integer | Tidak | - | UNIQUE (prefix, year, seq) | Urutan dalam prefix+tahun. Contoh: 1, 2, 3. |
| 7 | generated_by | bigint (FK) | Tidak | - | FK -> users(id) RESTRICT | User yang generate. Audit trail. |
| 8 | created_at | timestamp | Ya | NULL | - | Laravel. Juga berfungsi sebagai generated_at. |
| 9 | updated_at | timestamp | Ya | NULL | - | Laravel |

---

## 3. Analisis period vs year

### 3.1 Perbandingan

| Aspek | period (varchar(20)) | year (unsignedSmallInteger) |
|---|---|---|
| Storage | 20 bytes per baris | 2 bytes per baris |
| Performa query | Lebih lambat (string comparison) | Lebih cepat (integer comparison) |
| Kejelasan semantik | Tidak jelas apakah isinya tahun/bulan/periode fiskal | Jelas: ini tahun |
| Maintainability | Rentan diisi format tidak konsisten (2026, thn2026, 2026/2027) | Terjamin: hanya angka 0-65535 |
| Future extensibility | Bisa diisi bulan/semester/fiscal year | Hanya tahun. Jika perlu bulan, tambah kolom baru. |
| Validasi database | Tidak bisa (varchar bebas) | Otomatis: hanya integer valid |

### 3.2 Kesimpulan: Ganti period (varchar) -> year (unsignedSmallInteger)

Alasan:
1. **Domain sesuai** - hanya menyimpan tahun, bukan bulan/semester/fiscal year.
2. **Efisien** - 2 bytes vs 20 bytes. Untuk jutaan record, selisihnya signifikan.
3. **Performa** - integer comparison lebih cepat dari string comparison pada WHERE dan JOIN.
4. **Semantik jelas** - developer langsung paham kolom ini berisi tahun.
5. **Validasi otomatis** - database menolak input non-integer.

Jika di masa depan diperlukan numbering per bulan/semester, cukup tambah kolom baru `period_month` (tinyInteger) tanpa mengubah `year`. Ini sesuai prinsip Additive.

---

## 4. Analisis Format Nomor Dokumen

### 4.1 Perbandingan Format

| Format | Contoh | Keterbacaan | Multi Company | Multi Cabang | Multi Jenis | Parsing | SOP Indonesia |
|---|---|---|---|---|---|---|---|
| A: Seq/Prefix/Year | 001/SP/2026 | Sedang | Tidak | Tidak | Ya | Sulit (seq di depan) | Ya
| B: Year/Prefix/Seq | 2026/SP/001 | Baik | Tidak | Tidak | Ya | Mudah | Kurang umum
| C: Prefix/Year/Seq | SP/2026/001 | Baik | Tidak | Tidak | Ya | Mudah | Ya
| D: Company/Prefix/Year/Seq | ALPHA/SP/2026/001 | Sangat baik | **Ya** | **Ya** | Ya | Mudah | Ya (dengan kode perusahaan)
| E: Seq/Prefix/Company/Year | 001/SP/ALPHA/2026 | Sedang | Ya | Ya | Ya | Sulit | Sedang
| F: Prefix-Year-Seq (dash) | SP-2026-001 | Baik | Tidak | Tidak | Ya | Mudah | Tidak (slash lebih umum)

### 4.2 Rekomendasi Final: ALPHA/SP/2026/001

**Format:** `{company_code}/{prefix}/{year}/{sequence_3digit}`

Contoh:
- ALPHA/SP/2026/001 (Surat Penawaran pertama 2026)
- ALPHA/KTR/2026/015 (Kontrak ke-15 tahun 2026)
- ALPHA/KW/2026/050 (Kwitansi ke-50 tahun 2026)

### 4.3 Alasan Pemilihan

| Faktor | Penilaian | Penjelasan |
|---|---|---|
| Keterbacaan | Sangat baik | Segmen jelas dipisah slash. Urutan logis: siapa -> apa -> kapan -> keberapa. |
| Multi perusahaan | **Ya** | Kode perusahaan (ALPHA) sebagai segmen pertama. Jika nambah cabang/customer, ganti kode. |
| Multi cabang | **Ya** | Cukup ganti company_code dengan kode cabang (JKT/SP/2026/001, BDG/SP/2026/001). |
| Multi jenis dokumen | **Ya** | Prefix sebagai segmen kedua. Bisa ditambah tanpa batas. |
| Parsing | Mudah | explode(/ ) menghasilkan array [company, prefix, year, seq]. Setiap segmen jelas. |
| Maintainability | Tinggi | Format bisa diperpendek jadi Prefix/Year/Seq untuk internal. Service layer yang atur. |
| SOP Indonesia | Sesuai | Format nomor surat perusahaan umumnya menggunakan kode perusahaan + kode dokumen + tahun + urutan. |
| Enterprise ready | **Siap** | Mendukung multi-entitas tanpa perubahan struktur database. |

### 4.4 Contoh Skenario

```
1 Perusahaan, 1 Cabang:
  ALPHA/SP/2026/001, ALPHA/KTR/2026/001

1 Perusahaan, Multi Cabang:
  JKT/SP/2026/001, BDG/SP/2026/001
  (cabang sebagai company_code)

Multi Perusahaan (future):
  CLIENT-A/SP/2026/001, CLIENT-B/KTR/2026/001
  (company_code = kode klien)
```

### 4.5 Format Alternatif (Jika tidak butuh multi-company)

Jika di masa depan diputuskan tidak perlu multi-company, cukup gunakan 3 segmen:
SP/2026/001 (Prefix/Year/Sequence)

Cukup ubah format di service layer. Database tidak perlu perubahan.

---

## 5. Keputusan generated_at

### 5.1 Analisis

| Aspek | generated_at | created_at |
|---|---|---|
| Waktu diisi | Eksplisit oleh aplikasi | Otomatis oleh Laravel |
| Nilai bisnis | Jelas: waktu generate nomor | Sistem: waktu record dibuat |
| Perbedaan praktis | Selalu identik dengan created_at | Selalu identik dengan generated_at |
| API response | generated_at jelas sebagai waktu generate | created_at ambigu (bisa diartikan waktu input) |

### 5.2 Kesimpulan: generated_at TETAP DIPERTAHANKAN

Alasan:
1. **Semantik bisnis** - Dalam API dan laporan, field generated_at lebih jelas daripada created_at. Client dan direktur melihat "Waktu Generate", bukan "Waktu Dibuat".
2. **Reporting** - Filter laporan berdasarkan generated_at lebih natural. Contoh: "Tampilkan nomor yang digenerate antara 1-7 Juli 2026".
3. **Future queue** - Jika generate nomor dijalankan via queue/antrian, generated_at bisa berbeda dengan created_at (waktu record dibuat mungkin lebih awal dari waktu generate dieksekusi).
4. **Backward compatibility data** - Jika ada migrasi data dari sistem lama, generated_at dan created_at bisa diisi berbeda.
5. **Biaya rendah** - Penambahan 1 kolom timestamp (8 bytes) tidak signifikan.

---

## 6. Keputusan Index generated_at

### 6.1 Analisis

| Aspek | Analisis |
|---|---|---|
| Query yang mungkin | WHERE generated_at BETWEEN ? AND ?, ORDER BY generated_at DESC |
| Frekuensi penggunaan | Rendah - sedang (reporting, dashboard) |
| Manfaat index | Mempercepat filter tanggal generate |
| Overhead index | Memperlambat INSERT (1 index tambahan per baris) |
| Alternatif | index created_at (jika dianggap cukup) |

### 6.2 Kesimpulan: Index generated_at DIHAPUS

Alasan:
1. **Volume data rendah** - Jumlah dokumen per tahun terbatas (ratusan, bukan jutaan). Full table scan masih cepat.
2. **INSERT overhead** - Setiap INSERT perlu maintain index. Untuk tabel dengan INSERT dominan (write-heavy), index tambahan memberatkan.
3. **Alternatif sudah cukup** - Index pada (prefix, year, sequence_number) sudah mencakup kebutuhan query utama. Untuk sorting kronologis, ORDER BY id DESC sudah cukup karena id auto-increment.
4. **Jika nanti diperlukan** - Index bisa ditambahkan via ALTER TABLE kapan saja tanpa mengubah aplikasi.

---

## 7. Index Final (5 Index)

| Nama Index | Tipe | Kolom | Fungsi | Auto dari FK? |
|---|---|---|---|---|
| num_document_id_unique | UNIQUE | document_id | Satu dokumen satu nomor | Tidak |
| num_number_unique | UNIQUE | document_number | Nomor unik | Tidak |
| num_seq_unique | UNIQUE | (prefix, year, sequence_number) | Sequence unik per prefix+tahun | Tidak |
| num_prefix_year_index | Biasa | (prefix, year) | Hitung MAX(sequence) WHERE prefix=X AND year=X | Tidak |
| num_generated_by_index | Biasa | generated_by | Join dengan users | Ya (dari foreignId()->constrained()) |

**Catatan:** index pada document_id dan generated_by sudah otomatis dibuat oleh foreignId()->constrained(). Tidak perlu index manual.

---

## 8. Struktur Final Lengkap

```
document_numberings (9 kolom, 5 index, 2 FK)

Kolom:                                Index:
  id (PK, AI)                           PRIMARY (id)
  document_id (FK, UNIQUE)              UNIQUE (document_id) [manual]
  document_number (UNIQUE)              UNIQUE (document_number) [manual]
  prefix                                UNIQUE (prefix, year, sequence_number) [manual]
  year (unsignedSmallInteger)           INDEX (prefix, year) [manual]
  sequence_number                       INDEX (generated_by) [auto dari FK]
  generated_by (FK, RESTRICT)
  created_at
  updated_at
```

### Foreign Key

| FK | ON DELETE | ON UPDATE |
|---|---|---|
| document_id -> documents(id) | CASCADE | CASCADE |
| generated_by -> users(id) | RESTRICT | CASCADE |

### Format Nomor Final

```
ALPHA/SP/2026/001
^     ^   ^    ^
|     |   |    +-- sequence (3 digit, padding)
|     |   +------- year
|     +----------- prefix (SP, KTR, INV, KW, RAB)
+----------------- company code (ALPHA)
```

---

## 9. Validasi Arsitektur

| Prinsip | Status | Verifikasi |
|---|---|---|
| Additive & Non-Disruptive | Lolos | CREATE TABLE baru. Tidak mengubah tabel existing. |
| Backward Compatible | Lolos | Kode existing tidak perlu diubah. |
| 3NF | Lolos | Nomor terpisah dari dokumen. Kolom atomik. |
| Repository Pattern Ready | Lolos | Query: findByDocumentId, getNextSequence, findByPrefix. |
| Service Layer Ready | Lolos | NumberingService.generate() - domain jelas. |
| Enterprise Ready | Lolos | Format ALPHA/SP/2026/001 mendukung multi-entitas. 3 UNIQUE constraint. Locking mechanism. |
| Maintainable | Lolos | 9 kolom, 5 index. Format fleksibel. Reset tahunan. |
