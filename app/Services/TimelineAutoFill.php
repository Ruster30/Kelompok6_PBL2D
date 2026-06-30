<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Timeline;
use App\Models\Negotiation;
use Carbon\Carbon;

/**
 * TimelineAutoFill
 * ─────────────────────────────────────────────────────────────
 * Dipanggil dari dua jalur:
 *
 *  A) Proposal diterima LANGSUNG (tanpa negosiasi)
 *     → proposalDiterima($event)
 *
 *  B) Negosiasi SELESAI — client submit negosiasi terakhir
 *     dan admin menerima / kirim revisi terakhir diterima client
 *     → negosiasiSelesai($event, $negotiation)
 *
 * Keduanya menghasilkan timeline standar 5 tahap yang diisi otomatis
 * berdasarkan tanggal_event milik event.
 * ─────────────────────────────────────────────────────────────
 */
class TimelineAutoFill
{
    /**
     * Panggil ini ketika proposal diterima LANGSUNG (tanpa negosiasi).
     */
    public static function proposalDiterima(Event $event): void
    {
        self::isiTimeline($event, 'Proposal Diterima (Langsung)');
    }

    /**
     * Panggil ini ketika negosiasi selesai dan penawaran revisi diterima client.
     */
    public static function negosiasiSelesai(Event $event, ?Negotiation $negotiation = null): void
    {
        self::isiTimeline($event, 'Proposal Diterima (Setelah Negosiasi)');
    }

    /**
     * Core: isi timeline standar 5 tahap untuk event.
     * Jika timeline sudah ada (tahap yang sama), lewati agar tidak duplikat.
     */
    private static function isiTimeline(Event $event, string $sumber): void
    {
        if (Timeline::where('event_id', $event->id)->exists()) {
            return;
        }

        $tanggalEvent = $event->tanggal_event instanceof Carbon
            ? $event->tanggal_event
            : Carbon::parse($event->tanggal_event);

        $stages = self::buildStages($tanggalEvent, $sumber);

        foreach ($stages as $stage) {
            Timeline::create(array_merge(['event_id' => $event->id], $stage));
        }

        // Update status event → diproses
        if ($event->status_event === 'menunggu') {
            $event->update(['status_event' => 'diproses']);
        }
    }

    /**
     * Definisi 5 tahap standar timeline event.
     * Tanggal dihitung mundur dari tanggal_event.
     */
    private static function buildStages(Carbon $tanggalEvent, string $sumber): array
    {
        $now = now()->toDateString();

        return [
            [
                'nama_kegiatan'    => 'Kick Off Meeting',
                'deskripsi'        => "Penawaran diterima ({$sumber}). Lakukan pertemuan awal untuk menyamakan kebutuhan, PIC, dan jadwal kerja event.",
                'penanggung_jawab' => 'Admin',
                'tanggal_kegiatan' => $now,
                'deadline'         => Carbon::parse($now)->addDays(3)->toDateString(),
                'status_kegiatan'  => 'belum_mulai',
            ],
            [
                'nama_kegiatan'    => 'Persiapan Event',
                'deskripsi'        => 'Koordinasi vendor, kebutuhan perlengkapan, rundown, dan persiapan teknis sebelum hari event.',
                'penanggung_jawab' => 'Admin',
                'tanggal_kegiatan' => $tanggalEvent->copy()->subDays(30)->toDateString(),
                'deadline'         => $tanggalEvent->copy()->subDays(21)->toDateString(),
                'status_kegiatan'  => 'belum_mulai',
            ],
            [
                'nama_kegiatan'    => 'Hari Pelaksanaan',
                'deskripsi'        => 'Hari pelaksanaan event. Tim profesional Alpha Organizer hadir di lokasi.',
                'penanggung_jawab' => 'Tim Lapangan',
                'tanggal_kegiatan' => $tanggalEvent->toDateString(),
                'deadline'         => $tanggalEvent->toDateString(),
                'status_kegiatan'  => 'belum_mulai',
            ],
            [
                'nama_kegiatan'    => 'Evaluasi Event',
                'deskripsi'        => 'Evaluasi pelaksanaan, rangkuman dokumentasi, dan penyusunan laporan akhir untuk client.',
                'penanggung_jawab' => 'Admin',
                'tanggal_kegiatan' => $tanggalEvent->copy()->addDays(1)->toDateString(),
                'deadline'         => $tanggalEvent->copy()->addDays(3)->toDateString(),
                'status_kegiatan'  => 'belum_mulai',
            ],
        ];
    }
}

