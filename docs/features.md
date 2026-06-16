# Feature Documentation

## Login

### Tujuan

Memastikan hanya pengguna yang terdaftar yang dapat mengakses sistem.

### Aktor

* Admin
* Klien
* Vendor

### Alur

User memasukkan email dan password → sistem melakukan validasi → dashboard ditampilkan.

### Route

POST /login

### Controller

AuthenticatedSessionController

---

## Pengajuan Kebutuhan Event

### Tujuan

Klien dapat mengirim kebutuhan event kepada admin.

### Aktor

Klien

### Alur

Klien mengisi form kebutuhan event → data tersimpan → admin menerima notifikasi.

### Route

POST /event-requirements

### Controller

EventRequirementController

---

## Penyusunan Proposal dan RAB

### Tujuan

Admin membuat proposal dan estimasi biaya event.

### Aktor

Admin

### Alur

Admin membuat proposal → membuat RAB → mengirim ke klien.

### Route

POST /proposals

### Controller

ProposalController

---

## Pembayaran

### Tujuan

Klien melakukan pembayaran event.

### Aktor

Klien

### Alur

Upload bukti pembayaran → admin memverifikasi → status pembayaran diperbarui.

### Route

POST /payments

### Controller

PaymentController

---

## Timeline Event

### Tujuan

Memantau progres pelaksanaan event.

### Aktor

Admin dan Vendor

### Alur

Admin membuat timeline → vendor melihat timeline → tugas dilaksanakan.

### Controller

TimelineController
