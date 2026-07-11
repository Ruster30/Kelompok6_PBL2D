<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\Document;
use App\Models\Event;
use App\Models\Invoice;
use App\Models\Notification;
use App\Models\Proposal;
use App\Models\Rab;
use App\Models\RabAdditionalDetail;
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
            'kwitansi'       => $this->generateKwitansi($event),
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
        $timelines = Timeline::where('event_id', $event->id)->orderBy('tanggal_kegiatan')->orderBy('id')->get();
        $vendors   = $event->vendors;
        $totalRab  = app(RabService::class)->getTotalDibayarKlien($event->id);

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
        $nilaiKontrak = $event->total_invoice ?: app(RabService::class)->getTotalDibayarKlien($event->id);

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

        // Ambil invoice pertama yang statusnya belum dibayar
        $invoice   = $event->invoices()
            ->whereIn("status_invoice", ["belum_bayar", "terkirim", "draft"])
            ->orderBy("id", "asc")
            ->first();
        $rabItems  = Rab::where("event_id", $event->id)->with("vendor")->get();
        $subtotalVendor = (float) $rabItems->sum("subtotal_biaya");
        $totalDibayarKlien = app(RabService::class)->getTotalDibayarKlien($event->id);

        // Client info
        $client = $event->client;

        // Additional details (Fee EO, PPN, PPh)
        $additional = \App\Models\RabAdditionalDetail::where('event_id', $event->id)->first();
        $feeEoAktif = (bool) ($additional?->fee_enabled ?? false);
        $feeEoNominal = $feeEoAktif ? ($subtotalVendor * ($additional->fee_percent / 100)) : 0;
        $dpp = $subtotalVendor + $feeEoNominal;
        $ppnAktif = (bool) ($additional?->ppn_enabled ?? false);
        $ppnNominal = $ppnAktif ? ($dpp * ($additional->ppn_percent / 100)) : 0;
        $pphAktif = (bool) ($additional?->pph_enabled ?? false);
        $pphNominal = $pphAktif ? ($dpp * ($additional->pph_percent / 100)) : 0;
        $grandTotal = (float) $totalDibayarKlien;

        // Payment scheme data
        $scheme = app(PaymentSchemeService::class)->getScheme($event->id);
        $paymentScheme = $scheme?->jenis_pembayaran ?? 'full_payment';
        $dpPersen = (float) ($scheme?->persentase_dp ?? 0);
        $dpNominal = (float) ($scheme?->dp_nominal ?? 0);
        $sisaPelunasan = (float) ($scheme?->sisa_pelunasan ?? $totalDibayarKlien);

        // Company info from LandingSection
        $companyAddress = \App\Models\LandingSection::getByKey('alamat')?->content ?? '';
        $companyPhone = \App\Models\LandingSection::getByKey('telepon')?->content ?? '';
        $companyEmail = \App\Models\LandingSection::getByKey('email')?->content ?? '';

        // Bank info (fallback object)
        $bankData = \App\Models\LandingSection::getByKey('bank')?->content;
        $bank = $bankData ? json_decode($bankData) : (object) [
            'nama_bank' => '-',
            'nomor_rekening' => '-',
            'atas_nama' => '-',
        ];

        $pdf = Pdf::loadView('admin.pdf_templates.invoice', compact(
            'event', 'invoice', 'client', 'rabItems', 'subtotalVendor',
            'feeEoAktif', 'feeEoNominal', 'ppnAktif', 'ppnNominal',
            'pphAktif', 'pphNominal', 'grandTotal',
            'paymentScheme', 'dpPersen', 'dpNominal', 'sisaPelunasan',
            'companyAddress', 'companyPhone', 'companyEmail', 'bank',
        ))->setPaper('a4', 'portrait');

        $filename = 'invoice-' . Str::slug($event->nama_event) . '-' . now()->format('YmdHis') . '.pdf';

        return ['pdf' => $pdf, 'filename' => $filename, 'jenis' => 'invoice'];
    }
private function generateRab(Event $event): array
    {
        $event->load(['client']);

        $rabItems = Rab::where('event_id', $event->id)->with('vendor')->get();
        $total    = $rabItems->sum('subtotal_biaya');
        $additionalDetail = RabAdditionalDetail::where('event_id', $event->id)->first();

        $pdf = Pdf::loadView('admin.pdf_templates.rab', compact(
            'event', 'rabItems', 'total', 'additionalDetail'
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
            'kwitansi'      => 'kwitansi',
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
            'kwitansi'      => 'Kwitansi',
            default         => ucfirst($jenis),
        };
    }

    private function ensureInvoice(Event $event): ?Invoice
    {
        // Cari invoice yang belum dibayar
        $existing = $event->invoices()
            ->whereIn('status_invoice', ['belum_bayar', 'terkirim', 'draft'])
            ->orderBy('id', 'asc')
            ->first();

        if ($existing) {
            return $existing;
        }

        // Jika tidak ada, cek skema pembayaran
        $scheme = app(PaymentSchemeService::class)->getScheme($event->id);
        if (!$scheme) {
            return null;
        }

        if ($scheme->jenis_pembayaran === 'full_payment') {
            return Invoice::create([
                'event_id' => $event->id,
                'nomor_invoice' => sprintf('INV-%s-%03d', now()->format('Ymd'), Invoice::whereDate('created_at', today())->count() + 1),
                'total_invoice' => $scheme->sisa_pelunasan,
                'status_invoice' => 'belum_bayar',
                'tanggal_invoice' => now()->toDateString(),
            ]);
        }

        // DP + Pelunasan: buat invoice DP saja (invoice pelunasan dibuat saat DP diverifikasi)
        return Invoice::create([
            'event_id' => $event->id,
            'nomor_invoice' => sprintf('INV-%s-%03d', now()->format('Ymd'), Invoice::whereDate('created_at', today())->count() + 1),
            'total_invoice' => $scheme->dp_nominal,
            'status_invoice' => 'belum_bayar',
            'tanggal_invoice' => now()->toDateString(),
        ]);
    }
    /**
     * Generate Kwitansi via generate() dispatch.
     */
    private function generateKwitansiWithLabel(Event $event, ?string $labelOverride = null): array
    {
        $generated = $this->generate($event, 'kwitansi');
        return $generated;
    }

    private function generateKwitansi(Event $event, ?string $labelOverride = null): array
    {
        $event->load(['client', 'invoices.payments']);

        $invoice = $event->invoices()
            ->whereIn("status_invoice", ["dibayar", "lunas", "dp_lunas"])
            ->orderBy("id", "desc")
            ->first();

        $nomorKwitansi = sprintf('KW-%s-%03d',
            now()->format('Ymd'),
            Document::where('tipe', 'kwitansi')->whereDate('created_at', today())->count() + 1
        );

        $companyLogo = public_path('images/logo.png');
        $companyName = 'CV. Alpha Multi Organizer';

        $payment = $event->payments()
            ->where('status_pembayaran', 'diverifikasi')
            ->orderBy('id', 'desc')
            ->first();

        $jenisPembayaran = $payment?->jenis_pembayaran ?? 'full_payment';
        $jenisPembayaranLabel = $labelOverride ?? match ($jenisPembayaran) {
            'dp'        => 'DP (Down Payment)',
            'pelunasan' => 'Pelunasan',
            default     => 'Full Payment',
        };

        $nominal = $payment?->nominal ?? $invoice?->total_invoice ?? 0;
        $tanggalKwitansi = $payment?->tanggal_pembayaran ?? now();

        $companyAddress = \App\Models\LandingSection::getByKey('alamat')?->content ?? '';
        $companyPhone = \App\Models\LandingSection::getByKey('telepon')?->content ?? '';
        $companyEmail = \App\Models\LandingSection::getByKey('email')?->content ?? '';

        $pdf = Pdf::loadView('admin.pdf_templates.kwitansi', compact(
            'event', 'invoice', 'nomorKwitansi', 'companyLogo', 'companyName',
            'companyAddress', 'companyPhone', 'companyEmail',
            'jenisPembayaranLabel', 'nominal', 'tanggalKwitansi',
        ))->setPaper('a4', 'portrait');

        $filename = 'kwitansi-' . Str::slug($event->nama_event) . '-' . now()->format('YmdHis') . '.pdf';

        return ['pdf' => $pdf, 'filename' => $filename, 'jenis' => 'kwitansi'];
    }

    /**
     * Generate Kwitansi dan simpan ke storage.
     * Dipanggil dari AdminPaymentService saat verifikasi pembayaran.
     */
    public function generateAndSaveKwitansi(Event $event, ?string $labelOverride = null): Document
    {
        $generated = $this->generateKwitansiWithLabel($event, $labelOverride);
        $pdf = $generated['pdf'];
        $filename = $generated['filename'];

        $path = 'documents/' . $filename;
        Storage::disk('public')->put($path, $pdf->output());

        $document = Document::create([
            'event_id'  => $event->id,
            'user_id'   => auth()->id() ?? 1,
            'nama_file' => 'Kwitansi - ' . $event->nama_event,
            'file_path' => $path,
            'tipe'      => 'kwitansi',
        ]);

        return $document;
    }

}
