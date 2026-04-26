# 📘 CONTRIBUTING GUIDELINES

Terima kasih telah berkontribusi pada project ini! 🎉

Dokumen ini berisi aturan dan panduan kerja tim agar kolaborasi berjalan rapi, jelas, dan mudah dievaluasi.

---

# 🧩 1. STRUKTUR BRANCH

Project ini menggunakan struktur branch berikut:

```
main
dev
dev/nama-anggota
feature/nama-fitur
```

### Penjelasan:

* `main` → versi final (production)
* `dev` → penggabungan semua fitur
* `dev/nama` → branch pribadi untuk tracking kontribusi
* `feature/...` → branch untuk pengerjaan fitur spesifik

---

# 👥 2. BRANCH PRIBADI

* Setiap anggota WAJIB memiliki branch pribadi
* Format penamaan:

  ```
  dev/nama
  ```
* Contoh:

  * `dev/andi`
  * `dev/budi`

Semua pekerjaan individu harus masuk ke branch ini terlebih dahulu.

---

# 🌿 3. FEATURE BRANCH

* Setiap fitur WAJIB dibuat dalam branch terpisah
* Format:

  ```
  feature/nama-fitur
  ```
* Contoh:

  * `feature/login`
  * `feature/register`

❌ Dilarang mengerjakan lebih dari satu fitur dalam satu branch

---

# 🔄 4. WORKFLOW

Alur kerja yang WAJIB diikuti:

```
dev → dev/nama → feature → dev/nama → dev → main
```

### Langkah detail:

1. Ambil update dari `dev`
2. Checkout ke `dev/nama`
3. Buat branch `feature/...`
4. Kerjakan fitur
5. Merge ke `dev/nama`
6. Buat Pull Request ke `dev`
7. Setelah stabil → merge `dev` ke `main`

---

# 🔀 5. PULL REQUEST (PR)

* Semua perubahan WAJIB melalui Pull Request
* PR harus berisi:

  * Deskripsi perubahan
  * Referensi issue (jika ada)
* Minimal 1 reviewer sebelum merge

❌ Dilarang merge tanpa review

---

# 📝 6. COMMIT MESSAGE

Gunakan format commit yang jelas:

```
add login feature - andi
fix bug register - budi
```

❌ Hindari commit seperti:

```
update
fix
coba
```

---

# 📌 7. ISSUE

* Semua task harus dibuat dalam Issue
* Wajib memiliki:

  * Judul yang jelas
  * Deskripsi
  * Assignee

---

# 🚫 8. LARANGAN

* ❌ Push langsung ke `main`
* ❌ Merge tanpa PR
* ❌ Bekerja tanpa branch `feature/...`
* ❌ Menggabungkan banyak fitur dalam satu branch
* ❌ Menghapus branch orang lain tanpa izin

---

# 🎯 9. TUJUAN

Aturan ini dibuat untuk:

* Memastikan kontribusi tiap anggota terlihat jelas
* Menjaga struktur project tetap rapi
* Menghindari konflik dalam pengembangan
* Mempermudah proses review dan penilaian

---

# 💡 CATATAN

* `dev/nama` = bukti kontribusi individu
* `feature/...` = bukti pengerjaan fitur

Gunakan kedua jenis branch ini dengan disiplin.

---

---

# 📄 10. TEMPLATE ISSUE

Gunakan template berikut saat membuat Issue:

```
Title: [Feature] Nama Fitur

Description:
- Jelaskan fitur secara singkat

Checklist:
- [ ] UI
- [ ] Logic
- [ ] Testing

Assignee: @nama
```

---

# 🔀 11. TEMPLATE PULL REQUEST

Gunakan template berikut saat membuat PR:

```
Title: Nama fitur

Description:
- Apa yang dikerjakan
- Perubahan apa saja

Related Issue: #nomor

Checklist:
- [ ] Sudah dites
- [ ] Tidak ada error
- [ ] Siap direview
```

---

# 🔒 12. BRANCH PROTECTION (WAJIB SETUP)

Atur di repository settings:

## Untuk branch `main`:

* Require pull request before merging
* Require approval (minimal 1 reviewer)
* Block direct push

## Untuk branch `dev`:

* Require pull request
* Optional: require status checks (CI/CD)

---

# 🎯 PENUTUP

Terima kasih dan selamat berkolaborasi! 🚀
