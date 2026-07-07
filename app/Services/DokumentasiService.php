<?php

namespace App\Services;

use App\Models\Documentation;
use App\Models\DocumentationFile;
use App\Models\Task;
use App\Models\User;
use App\Models\Notification;
use App\Models\Vendor;
use Illuminate\Support\Facades\DB;

class DokumentasiService
{
    public function storeDokumentasi(array $validated, array $files, int $vendorId): void
    {
        $tugas = Task::where("id", $validated["tugas_id"])
            ->where("vendor_id", $vendorId)
            ->firstOrFail();

        // Gunakan DB Transaction untuk memastikan semua file terupload atau tidak sama sekali
        \DB::transaction(function() use ($validated, $files, $tugas, $vendorId) {
            $documentation = Documentation::firstOrCreate(
                [
                    "event_id" => $tugas->event_id,
                    "judul"    => $validated["judul"] ?: "Dokumentasi " . $tugas->nama_tugas,
                ],
                [
                    "deskripsi" => $validated["catatan"] ?? null,
                ]
            );

            // Upload setiap file
            foreach ($files as $file) {
                $path      = $file->store("documentation-files", "public");
                $extension = strtolower($file->getClientOriginalExtension());
                $tipeFile  = in_array($extension, ["mp4", "mov"]) ? "video" : "foto";

                DocumentationFile::create([
                    "documentation_id" => $documentation->id,
                    "file_path"        => $path,
                    "tipe_file"        => $tipeFile,
                    "status"           => "menunggu",
                ]);
            }

            // Kirim notifikasi ke Admin (sekali saja untuk semua file)
            $vendor = Vendor::find($vendorId);
            $vendorName = $vendor?->nama_vendor ?? "Vendor";
            $tugas->load('event');
            $eventName = $tugas->event?->nama_event ?? "Event";
            $fileCount = count($files);
            $fileText = $fileCount === 1 ? "1 file dokumentasi" : "$fileCount file dokumentasi";

            User::where('role', 'admin')->each(function (User $admin) use ($vendorName, $tugas, $eventName, $fileText) {
                Notification::create([
                    'user_id' => $admin->id,
                    'judul'   => 'Dokumentasi Vendor Diunggah',
                    'pesan'   => sprintf(
                        'Vendor "%s" telah mengunggah %s untuk tugas "%s" pada event "%s".',
                        $vendorName,
                        $fileText,
                        $tugas->nama_tugas,
                        $eventName
                    ),
                    'tipe'    => 'info',
                    'dibaca'  => false,
                ]);
            });
        });
    }
}