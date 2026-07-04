<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\Document;
use App\Models\Event;
use App\Models\Invoice;
use App\Models\Notification;
use App\Models\Proposal;
use App\Models\Rab;
use App\Models\Service;
use App\Models\Team;
use App\Models\Timeline;
use App\Models\Vendor;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentBuilderService
{
    /**
     * Generate PDF sesuai jenis dokumen.
     * Mengembalikan ['pdf' => PDF instance, 'filename' => string]
     */
    public function generate(Event $event, string $jenisDokumen): array
    {
        return match ($jenisDokumen) {
            'proposal'       => $this->generateProposal($event),
            'surat_kontrak'  => $this->generateSuratKontrak($event),
            'invoice'        => $this->generateInvoice($event),
            'rab'            => $this->generateRab($event),
            default          => throw new \InvalidArgumentException("Jenis dokumen tidak dikenal: {$jenisDokumen}"),
        };
    }

    // ─── PROPOSAL ───────────────────────────────────────────────────────────

    private function generateProposal(Event $event): array
    {
        $event->load([
            'client',
            'rabs.vendor',
            'timelines',
            'vendors',
        ]);

        $services  = Service::where('is_active', true)->orderBy('urutan')->get();
        $teams     = Team::where('is_active', true)->orderBy('urutan')->get();
        $rabItems  = Rab::where('event_id', $event->id)->with('vendor')->get();
        $timelines = Timeline::where('event_id', $event->id)->orderBy('tanggal_kegiatan')->get();
        $vendors   = $event->vendors;
        $totalRab  = $rabItems->sum('subtotal_biaya');

        $pdf = Pdf::loadView('admin.pdf_templates.proposal', compact(
            'event', 'services', 'teams', 'rabItems', 'timelines', 'vendors', 'totalRab'
        ))->setPaper('a4', 'portrait');

        $filename = 'proposal-' . Str::slug($event->nama_event) . '-' . now()->format('YmdHis') . '.pdf';

        return ['pdf' => $pdf, 'filename' => $filename, 'jenis' => 'proposal'];
    }

    // ─── SURAT KONTRAK ──────────────────────────────────────────────────────

    private function generateSuratKontrak(Event $event): array
    {
        $event->load(['client', 'contract', 'invoices']);

        $nomorKontrak = sprintf('KTR-%s-%03d', now()->format('Ymd'), Contract::whereDate('created_at', today())->count() + 1);
        // Gunakan total_invoice dari Event Model (invoice utama saja) atau total RAB
        $nilaiKontrak = $event->total_invoice ?: $event->rabs()->sum('subtotal_biaya');

        $pdf = Pdf::loadView('admin.pdf_templates.surat_kontrak', compact(
            'event', 'nomorKontrak', 'nilaiKontrak'
        ))->setPaper('a4', 'portrait');

        $filename = 'kontrak-' . Str::slug($event->nama_event) . '-' . now()->format('YmdHis') . '.pdf';

        return ['pdf' => $pdf, 'filename' => $filename, 'jenis' => 'surat_kontrak'];
    }

    // ─── INVOICE ────────────────────────────────────────────────────────────

    private function generateInvoice(Event $event): array
    {
        $event->load(['client', 'invoices.payments', 'rabs']);

        $invoice   = $event->invoices()->latest()->first();
        $rabItems  = Rab::where('event_id', $event->id)->with('vendor')->get();
        $totalItem = $rabItems->sum('subtotal_biaya');

        $nomorInvoice = $invoice?->nomor_invoice
            ?? sprintf('INV-%s-%03d', now()->format('Ymd'), Invoice::whereDate('created_at', today())->count() + 1);
        $totalInvoice = $invoice?->total_invoice ?? $totalItem;
        $statusInvoice = $invoice?->status_invoice ?? 'belum_bayar';
        $tanggalInvoice = $invoice?->tanggal_invoice?->format('d M Y') ?? now()->format('d M Y');

        $pdf = Pdf::loadView('admin.pdf_templates.invoice', compact(
            'event', 'invoice', 'rabItems', 'totalItem', 'totalInvoice',
            'nomorInvoice', 'statusInvoice', 'tanggalInvoice'
        ))->setPaper('a4', 'portrait');

        $filename = 'invoice-' . Str::slug($event->nama_event) . '-' . now()->format('YmdHis') . '.pdf';

        return ['pdf' => $pdf, 'filename' => $filename, 'jenis' => 'invoice'];
    }

    // ─── RAB ────────────────────────────────────────────────────────────────

    private function generateRab(Event $event): array
    {
        $event->load(['client']);

        $rabItems = Rab::where('event_id', $event->id)->with('vendor')->get();
        $total    = $rabItems->sum('subtotal_biaya');

        $pdf = Pdf::loadView('admin.pdf_templates.rab', compact(
            'event', 'rabItems', 'total'
        ))->setPaper('a4', 'portrait');

        $filename = 'rab-' . Str::slug($event->nama_event) . '-' . now()->format('YmdHis') . '.pdf';

        return ['pdf' => $pdf, 'filename' => $filename, 'jenis' => 'rab'];
    }

    // ─── KIRIM KE CLIENT ────────────────────────────────────────────────────

    /**
     * Simpan PDF ke storage, catat ke tabel documents, kirim notifikasi & email.
     */
    public function sendToClient(Event $event, string $jenisDokumen): Document
    {
        if ($jenisDokumen === 'invoice') {
            $this->ensureInvoice($event);
        }

        $generated = $this->generate($event, $jenisDokumen);

        /** @var \Barryvdh\DomPDF\PDF $pdf */
        $pdf      = $generated['pdf'];
        $filename = $generated['filename'];
        $jenis    = $generated['jenis'];

        // Simpan file ke storage/app/public/documents
        $path = 'documents/' . $filename;
        Storage::disk('public')->put($path, $pdf->output());

        // Map jenis dokumen ke nilai tipe di tabel documents
        $tipeEnum = match ($jenis) {
            'proposal'      => 'proposal',
            'surat_kontrak' => 'kontrak',
            'invoice'       => 'invoice',
            'rab'           => 'rab',
            default         => 'lainnya',
        };

        // Simpan ke tabel documents
        $document = Document::create([
            'event_id'  => $event->id,
            'user_id'   => auth()->id(),
            'nama_file' => $this->labelJenis($jenisDokumen) . ' - ' . $event->nama_event,
            'file_path' => $path,
            'tipe'      => $tipeEnum,
        ]);

        // Kirim notifikasi ke client
        Notification::create([
            'user_id' => $event->client_id,
            'judul'   => $this->labelJenis($jenisDokumen) . ' Tersedia',
            'pesan'   => $this->labelJenis($jenisDokumen) . ' untuk event "' . $event->nama_event . '" telah dikirim oleh admin.',
            'tipe'    => 'info',
        ]);

        // Kirim email jika MAIL_MAILER sudah dikonfigurasi (bukan log/array)
        $this->kirimEmail($event, $pdf, $filename, $jenisDokumen);

        return $document;
    }

    /**
     * Kirim email ke client dengan lampiran PDF.
     * Hanya dieksekusi jika mailer bukan 'log' atau 'array' (dev mode).
     */
    private function kirimEmail(Event $event, $pdf, string $filename, string $jenisDokumen): void
    {
        $mailer = config('mail.default');

        if (in_array($mailer, ['log', 'array'])) {
            return; // Skip pengiriman email di development
        }

        $clientEmail = $event->client?->email;
        if (! $clientEmail) {
            return;
        }

        $label = $this->labelJenis($jenisDokumen);

        try {
            Mail::raw(
                "Yth. {$event->client->name},\n\n"
                . "Bersama email ini kami lampirkan {$label} untuk event \"{$event->nama_event}\".\n"
                . "Silakan hubungi kami jika ada pertanyaan.\n\n"
                . "Hormat kami,\nCV. Alpha Multi Organizer",
                function ($message) use ($clientEmail, $event, $pdf, $filename, $label) {
                    $message->to($clientEmail, $event->client->name)
                        ->subject($label . ' - ' . $event->nama_event)
                        ->attachData($pdf->output(), $filename, ['mime' => 'application/pdf']);
                }
            );
        } catch (\Throwable) {
            // Gagal kirim email tidak menghentikan proses utama
        }
    }

    private function labelJenis(string $jenis): string
    {
        return match ($jenis) {
            'proposal'      => 'Proposal Event',
            'surat_kontrak' => 'Surat Kontrak',
            'invoice'       => 'Invoice',
            'rab'           => 'RAB (Rencana Anggaran Biaya)',
            default         => ucfirst($jenis),
        };
    }

    private function ensureInvoice(Event $event): Invoice
    {
        $existing = $event->invoices()
            ->where('status_invoice', '!=', 'lunas')
            ->latest()
            ->first();

        if ($existing) {
            return $existing;
        }

        $totalInvoice = Rab::where('event_id', $event->id)->sum('subtotal_biaya');

        return Invoice::create([
            'event_id' => $event->id,
            'nomor_invoice' => sprintf('INV-%s-%03d', now()->format('Ymd'), Invoice::whereDate('created_at', today())->count() + 1),
            'total_invoice' => $totalInvoice,
            'status_invoice' => 'belum_bayar',
            'tanggal_invoice' => now()->toDateString(),
        ]);
    }
}
