# Changelog

## v1.1.0

### Added

* Sistem autentikasi Laravel Breeze
* Dashboard Admin
* Dashboard Klien
* Dashboard Vendor
* CRUD Event
* CRUD Layanan Event
* CRUD Vendor
* Pengajuan Kebutuhan Event
* Proposal dan RAB
* Timeline Event
* Dokumentasi Event
* Upload Bukti Pembayaran
* Notifikasi Sistem
* Fitur Feedback Event
* Sistem Rating Event (1–5 Bintang)
* Ulasan Event oleh Client
* Halaman Feedback Event
* Tabel Feedback untuk penyimpanan evaluasi client

### Changed

* Perbaikan tampilan login Bootstrap
* Optimalisasi struktur database
* Halaman Event Klien ditambahkan tombol "Beri Feedback"
* Alur penyelesaian event diperbarui dengan proses evaluasi client
* Modul Event diperbarui untuk mendukung feedback setelah event selesai

### Fixed

* Bug redirect setelah login
* Bug validasi upload file

### Dependency

* Laravel Breeze
* Bootstrap 5

### Refactor

* Pemisahan controller berdasarkan modul
* Pemisahan blade layout
* Optimasi route group
* Penambahan relasi antara Event dan Feedback
* Penyesuaian struktur controller untuk pengelolaan feedback

### Impacted Modules

* Event Module
* Client Module
* Feedback Module
* Database Module
