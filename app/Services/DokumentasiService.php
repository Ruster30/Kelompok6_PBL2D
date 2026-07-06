<?php

namespace App\Services;

use App\Models\Documentation;
use App\Models\DocumentationFile;
use App\Models\Task;
use App\Models\User;
use App\Models\Notification;
use App\Models\Vendor;
use Illuminate\Http\UploadedFile;

class DokumentasiService
{
    public function storeDokumentasi(array $validated, UploadedFile $file, int $vendorId): void
    {
        $tugas = Task::where("id", $validated["tugas_id"])
            ->where("vendor_id", $vendorId)
            ->firstOrFail();

        $documentation = Documentation::firstOrCreate(
            [
                "event_id" => $tugas->event_id,
                "judul"    => $validated["judul"] ?: "Dokumentasi " . $tugas->nama_tugas,
            ],
            [
                "deskripsi" => $validated["catatan"] ?? null,
            ]
        );

        $path      = $file->store("documentation-files", "public");
        $extension = strtolower($file->getClientOriginalExtension());
        $tipeFile  = in_array($extension, ["mp4", "mov"]) ? "video" : "foto";

        DocumentationFile::create([
            "documentation_id" => $documentation->id,
            "file_path"        => $path,
            "tipe_file"        => $tipeFile,
            "status"           => "menunggu",
        ]);

        // Kirim notifikasi ke Admin
        $vendor = Vendor::find($vendorId);
        $vendorName = $vendor?->nama_vendor ?? "Vendor";
        $tugas->load('event');
        $eventName = $tugas->event?->nama_event ?? "Event";

        User::where('role', 'admin')->each(function (User $admin) use ($vendorName, $tugas, $eventName) {
            Notification::create([
                'user_id' => $admin->id,
                'judul'   => 'Dokumentasi Vendor Diunggah',
                'pesan'   => 'Vendor "' . $vendorName . '" telah mengunggah dokumentasi untuk tugas "' . $tugas->nama_tugas . '" pada event "' . $eventName . '".',
                'tipe'    => 'info',
                'dibaca'  => false,
            ]);
        });
    }
}