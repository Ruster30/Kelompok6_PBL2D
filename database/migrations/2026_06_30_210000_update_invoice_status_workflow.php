<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'])) {
            DB::statement("ALTER TABLE invoices MODIFY status_invoice VARCHAR(50) NOT NULL DEFAULT 'belum_bayar'");
        } elseif ($driver === 'pgsql') {
            DB::statement("ALTER TABLE invoices ALTER COLUMN status_invoice TYPE VARCHAR(50)");
            DB::statement("ALTER TABLE invoices ALTER COLUMN status_invoice SET DEFAULT 'belum_bayar'");
        }

        DB::table('invoices')
            ->whereIn('status_invoice', ['draft', 'terkirim'])
            ->update(['status_invoice' => 'belum_bayar']);
    }

    public function down(): void
    {
        DB::table('invoices')
            ->where('status_invoice', 'belum_bayar')
            ->update(['status_invoice' => 'draft']);

        DB::table('invoices')
            ->where('status_invoice', 'menunggu_verifikasi')
            ->update(['status_invoice' => 'terkirim']);

        DB::table('invoices')
            ->where('status_invoice', 'ditolak')
            ->update(['status_invoice' => 'terkirim']);

        $driver = DB::connection()->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'])) {
            DB::statement("ALTER TABLE invoices MODIFY status_invoice ENUM('draft','terkirim','lunas') NOT NULL DEFAULT 'draft'");
        }
    }
};
