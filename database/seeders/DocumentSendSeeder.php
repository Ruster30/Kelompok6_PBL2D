<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Document;
use App\Models\DocumentSend;
use App\Models\User;

class DocumentSendSeeder extends Seeder
{
    public function run(): void
    {
        $admin     = User::where("role", "admin")->first();
        $documents = Document::all();

        if (!$admin || $documents->isEmpty()) return;

        foreach ($documents->random(min(3, $documents->count())) as $doc) {
            DocumentSend::create([
                "document_id"  => $doc->id,
                "sender_id"    => $admin->id,
                "recipient_id" => $doc->event->client_id,
                "pesan"        => "Dokumen terkait event Anda, silakan dicek.",
                "email_sent"   => (bool) rand(0, 1),
                "sent_at"      => now()->subHours(rand(1, 48)),
            ]);
        }
    }
}