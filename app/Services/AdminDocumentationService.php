<?php

namespace App\Services;

use App\Models\Documentation;
use App\Models\DocumentationFile;
use App\Models\Notification;
use App\Models\Task;
use App\Models\Vendor;

class AdminDocumentationService
{
    public function getDocumentations(?string $search, ?string $status): array
    {
        $query = Documentation::with(["event", "files"])->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where("judul", "like", "%{$search}%")
                  ->orWhereHas("event", fn($q2) => $q2->where("nama_event", "like", "%{$search}%"));
            });
        }

        // FIX: Filter status sekarang berfungsi dengan benar
        // Hanya tampilkan dokumentasi yang memiliki file dengan status tertentu
        if ($status) {
            $query->whereHas("files", function($q) use ($status) {
                $q->where("status", $status);
            });
        }

        $documentations = $query->paginate(10)->withQueryString();

        // Hitung statistik berdasarkan filter yang aktif
        $totalDocsQuery = Documentation::query();
        if ($search) {
            $totalDocsQuery->where(function ($q) use ($search) {
                $q->where("judul", "like", "%{$search}%")
                  ->orWhereHas("event", fn($q2) => $q2->where("nama_event", "like", "%{$search}%"));
            });
        }
        $totalDocs = $totalDocsQuery->count();

        // Statistik file berdasarkan status
        $pendingDocs  = DocumentationFile::where("status", "menunggu")->count();
        $approvedDocs = DocumentationFile::where("status", "disetujui")->count();

        return compact("documentations", "totalDocs", "pendingDocs", "approvedDocs");
    }

    public function approveFile(DocumentationFile $file): void
    {
        $file->update(["status" => "disetujui"]);
        
        // Kirim notifikasi ke Vendor
        $this->sendNotificationToVendor($file, 'disetujui');
    }

    public function rejectFile(DocumentationFile $file): void
    {
        $file->update(["status" => "ditolak"]);
        
        // Kirim notifikasi ke Vendor
        $this->sendNotificationToVendor($file, 'ditolak');
    }

    /**
     * Kirim notifikasi ke vendor terkait approval/rejection dokumentasi
     */
    private function sendNotificationToVendor(DocumentationFile $file, string $action): void
    {
        // Load relasi yang diperlukan
        $file->load(['documentation.event']);
        
        $documentation = $file->documentation;
        $event = $documentation->event;
        
        if (!$event) {
            return;
        }

        // Cari vendor yang terkait dengan event ini
        // Ambil semua vendor yang memiliki tugas di event ini
        $vendorIds = Task::where('event_id', $event->id)
            ->whereNotNull('vendor_id')
            ->distinct()
            ->pluck('vendor_id');

        $vendors = Vendor::whereIn('id', $vendorIds)->get();

        foreach ($vendors as $vendor) {
            if (!$vendor->user) {
                continue;
            }

            // Buat pesan notifikasi
            if ($action === 'disetujui') {
                $judul = 'Dokumentasi Disetujui';
                $pesan = sprintf(
                    'Dokumentasi untuk event "%s" telah disetujui oleh Admin. File dokumentasi Anda telah diverifikasi dan diterima.',
                    $event->nama_event
                );
                $tipe = 'sukses';
            } else {
                $judul = 'Dokumentasi Ditolak';
                $pesan = sprintf(
                    'Dokumentasi untuk event "%s" ditolak oleh Admin. Silakan lakukan perbaikan dan unggah kembali dokumentasi yang sesuai.',
                    $event->nama_event
                );
                $tipe = 'peringatan';
            }

            // Simpan notifikasi ke database
            Notification::create([
                'user_id' => $vendor->user_id,
                'judul' => $judul,
                'pesan' => $pesan,
                'tipe' => $tipe,
                'dibaca' => false,
            ]);
        }
    }
}