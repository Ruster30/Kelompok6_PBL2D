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
        // Cek jika sudah ada negosiasi — jangan isi sekarang, tunggu negosiasi selesai
        if (Negotiation::where('event_id', $event->id)->exists()) {
            return;
        }

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
        $tanggalEvent = $event->tanggal_event instanceof Carbon
            ? $event->tanggal_event
            : Carbon::parse($event->tanggal_event);

        $stages = self::buildStages($tanggalEvent, $sumber);

        foreach ($stages as $stage) {
            // Cegah duplikasi: cek nama_kegiatan yang sama untuk event ini
            $sudahAda = Timeline::where('event_id', $event->id)
                ->where('nama_kegiatan', $stage['nama_kegiatan'])
                ->exists();

            if (!$sudahAda) {
                Timeline::create(array_merge(['event_id' => $event->id], $stage));
            }
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
                'nama_kegiatan'    => 'Konfirmasi & Penandatanganan Kontrak',
                'deskripsi'        => "Penawaran diterima ({$sumber}). Lakukan konfirmasi akhir dan penandatanganan kontrak dengan client.",
                'penanggung_jawab' => 'Admin',
                'tanggal_kegiatan' => $now,
                'deadline'         => Carbon::parse($now)->addDays(3)->toDateString(),
                'status_kegiatan'  => 'belum_mulai',
            ],
            [
                'nama_kegiatan'    => 'Persiapan & Koordinasi Vendor',
                'deskripsi'        => 'Koordinasi dengan vendor terpilih, konfirmasi ketersediaan dan jadwal.',
                'penanggung_jawab' => 'Admin',
                'tanggal_kegiatan' => $tanggalEvent->copy()->subDays(30)->toDateString(),
                'deadline'         => $tanggalEvent->copy()->subDays(21)->toDateString(),
                'status_kegiatan'  => 'belum_mulai',
            ],
            [
                'nama_kegiatan'    => 'Setup Venue & Dekorasi',
                'deskripsi'        => 'Persiapan venue: pemasangan dekorasi, sound system, pencahayaan, dan perlengkapan event.',
                'penanggung_jawab' => 'Tim Lapangan',
                'tanggal_kegiatan' => $tanggalEvent->copy()->subDays(1)->toDateString(),
                'deadline'         => $tanggalEvent->copy()->subDays(1)->toDateString(),
                'status_kegiatan'  => 'belum_mulai',
            ],
            [
                'nama_kegiatan'    => 'Pelaksanaan Event',
                'deskripsi'        => 'Hari pelaksanaan event. Tim profesional Alpha Organizer hadir di lokasi.',
                'penanggung_jawab' => 'Tim Lapangan',
                'tanggal_kegiatan' => $tanggalEvent->toDateString(),
                'deadline'         => $tanggalEvent->toDateString(),
                'status_kegiatan'  => 'belum_mulai',
            ],
            [
                'nama_kegiatan'    => 'Evaluasi & Laporan Akhir',
                'deskripsi'        => 'Dokumentasi hasil event, pembersihan venue, dan pembuatan laporan akhir untuk client.',
                'penanggung_jawab' => 'Admin',
                'tanggal_kegiatan' => $tanggalEvent->copy()->addDays(1)->toDateString(),
                'deadline'         => $tanggalEvent->copy()->addDays(3)->toDateString(),
                'status_kegiatan'  => 'belum_mulai',
            ],
        ];
    }
}
