# 🔧 Perbaikan AdminAnalyticsService

## Masalah
AdminAnalyticsService mencoba mengakses kolom `events.total_invoice` yang tidak ada di database. Kolom `total_invoice` sebenarnya ada di tabel `invoices`, bukan `events`.

## Struktur Database yang Benar

### Tabel Events
```sql
- id
- client_id (FK ke users)
- pic_admin_id (FK ke users)
- nama_event
- jenis_event
- tanggal_event
- lokasi_event
- jumlah_tamu
- detail_kebutuhan
- status_event
- timestamps
```

### Tabel Invoices
```sql
- id
- event_id (FK ke events)
- nomor_invoice
- total_invoice ✅ (ada di sini!)
- status_invoice
- tanggal_invoice
- timestamps
```

### Relasi
- Event hasMany Invoice
- Invoice belongsTo Event

## Perbaikan yang Dilakukan

### 1. Method `getTopClients()`
**Before:**
```php
->withSum(['events' => function($q) {...}], 'total_invoice') // ❌ Salah
```

**After:**
```php
->with(['events' => function($q) {...}])
->get()
->map(function($client) {
    $client->total_invoice_value = $client->events->sum(function($event) {
        return $event->invoices->sum('total_invoice'); // ✅ Benar
    });
    return $client;
})
```

### 2. Method `getTopEvents()`
**Before:**
```php
->orderBy('total_invoice', 'desc') // ❌ Kolom tidak ada di events
```

**After:**
```php
->with(['client', 'invoices'])
->get()
->map(function($event) {
    $event->total_invoice_value = $event->invoices->sum('total_invoice'); // ✅ Benar
    return $event;
})
->sortByDesc('total_invoice_value')
```

## File yang Diperbaiki

1. ✅ `app/Services/AdminAnalyticsService.php`
   - Method `getTopClients()` - Menggunakan relasi events->invoices
   - Method `getTopEvents()` - Menghitung sum dari invoices

2. ✅ `resources/views/admin/analytics/index.blade.php`
   - Mengganti `$client->events_sum_total_invoice` → `$client->total_invoice_value`
   - Mengganti `$event->total_invoice` → `$event->total_invoice_value`

3. ✅ `resources/views/admin/analytics/pdf.blade.php`
   - Mengganti `$client->events_sum_total_invoice` → `$client->total_invoice_value`
   - Mengganti `$event->total_invoice` → `$event->total_invoice_value`

4. ✅ `app/Exports/AnalyticsClientsSheet.php`
   - Mengganti `$client->events_sum_total_invoice` → `$client->total_invoice_value`

5. ✅ `app/Exports/AnalyticsEventsSheet.php`
   - Mengganti `$event->total_invoice` → `$event->total_invoice_value`

## Logika Perhitungan

### Total Invoice per Client
```php
$client->events->sum(function($event) {
    return $event->invoices->sum('total_invoice');
});
```
Ini menjumlahkan semua invoice dari semua event milik client tersebut.

### Total Invoice per Event
```php
$event->invoices->sum('total_invoice');
```
Ini menjumlahkan semua invoice yang terkait dengan event tersebut.

## Verifikasi

✅ Semua file PHP lolos syntax check
✅ Tidak ada perubahan pada database/migration
✅ Tidak ada perubahan pada struktur tabel
✅ Menggunakan relasi Eloquent yang sudah ada

## Cara Kerja Setelah Perbaikan

1. **Top Clients**: 
   - Load semua events per client (dengan filter)
   - Load semua invoices per event
   - Hitung total invoice dengan menjumlahkan invoices dari semua events
   - Sort berdasarkan total tersebut

2. **Top Events**:
   - Load semua events (dengan filter)
   - Load semua invoices per event
   - Hitung total invoice dengan menjumlahkan invoices
   - Sort berdasarkan total tersebut

## Testing

Untuk memverifikasi perbaikan:

```bash
# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Test akses
# Buka: /admin/analytics
# Pastikan Top Clients dan Top Events menampilkan data dengan benar
```

---

**Status**: ✅ Perbaikan Selesai
**Date**: 2026-07-05
