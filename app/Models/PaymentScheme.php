<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\RabService;

class PaymentScheme extends Model
{
    use HasFactory;

    protected $table = "payment_schemes";

    protected $fillable = [
        "event_id",
        "jenis_pembayaran",
        "mode_dp",
        "nilai_dp",
        "persentase_dp",
    ];

    protected function casts(): array
    {
        return [
            "nilai_dp"       => "decimal:2",
            "persentase_dp"  => "decimal:2",
        ];
    }

    public function event()
    {
        return $this->belongsTo(Event::class, "event_id");
    }

    public function getDpNominalAttribute(): float
    {
        if ($this->jenis_pembayaran === "full_payment") {
            return 0;
        }

        $totalDibayarKlien = app(RabService::class)->getTotalDibayarKlien($this->event_id);

        if ($this->mode_dp === "nominal") {
            return (float) ($this->nilai_dp ?? 0);
        }

        return $totalDibayarKlien * ($this->persentase_dp / 100);
    }

    public function getSisaPelunasanAttribute(): float
    {
        if ($this->jenis_pembayaran === "full_payment") {
            return app(RabService::class)->getTotalDibayarKlien($this->event_id);
        }

        $total = app(RabService::class)->getTotalDibayarKlien($this->event_id);
        return max(0, $total - $this->dp_nominal);
    }
}
