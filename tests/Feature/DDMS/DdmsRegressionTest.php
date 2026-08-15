<?php

use App\Enums\DocumentSource;
use App\Enums\DocumentStatus;
use App\Models\Document;
use App\Models\DocumentNumbering;
use App\Models\DocumentQrVerification;
use App\Models\Event;
use App\Models\User;
use App\Services\DocumentApprovalService;
use App\Services\DocumentBuilderService;
use App\Services\DocumentNumberService;
use App\Services\DocumentVerificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('public');
});

// === REGRESSION: Publish ===
it('regression publish: status Published, token + QR dibuat, nomor tetap', function () {
    $director = User::factory()->create(['role' => 'director']);
    $document = Document::factory()->create([
        'status' => DocumentStatus::Approved,
        'document_source' => DocumentSource::Generated,
    ]);
    DocumentNumbering::create([
        'document_id' => $document->id,
        'document_number' => 'ALK/SP/2026/0001',
        'prefix' => 'SP',
        'year' => 2026,
        'sequence_number' => 1,
        'generated_by' => $director->id,
    ]);

    // PDF final dicek terpisah (regression PDF); di sini fokus alur status/token/QR.
    $this->mock(DocumentBuilderService::class, function ($mock) {
        $mock->shouldReceive('regeneratePublishedPdf');
    });

    app(DocumentApprovalService::class)->publishDocument($document, $director);

    $fresh = $document->fresh();

    expect($fresh->status)->toBe(DocumentStatus::Published);

    $qr = DocumentQrVerification::where('document_id', $document->id)->first();

    expect($qr)->not->toBeNull();
    expect(strlen($qr->verification_token))->toBe(36);
    expect($qr->qr_path)->not->toBeNull();
    Storage::disk('public')->assertExists($qr->qr_path);
    expect($fresh->numbering->document_number)->toBe('ALK/SP/2026/0001');
});

// === REGRESSION: Verification Token ===
it('regression token: dibuat sekali dan immutable', function () {
    $director = User::factory()->create(['role' => 'director']);
    $document = Document::factory()->create([
        'status' => DocumentStatus::Published,
        'document_source' => DocumentSource::Generated,
    ]);
    DocumentNumbering::create([
        'document_id' => $document->id,
        'document_number' => 'ALK/SP/2026/0002',
        'prefix' => 'SP',
        'year' => 2026,
        'sequence_number' => 2,
        'generated_by' => $director->id,
    ]);

    $service = app(DocumentVerificationService::class);
    $token = $service->getOrCreateVerificationToken($document);
    $tokenAgain = $service->getOrCreateVerificationToken($document);

    expect(strlen($token))->toBe(36);
    expect($token)->toBe($tokenAgain);
    expect(DocumentQrVerification::count())->toBe(1);
});

it('regression token: ditolak jika dokumen belum Published', function () {
    $director = User::factory()->create(['role' => 'director']);
    $document = Document::factory()->create([
        'status' => DocumentStatus::Approved,
        'document_source' => DocumentSource::Generated,
    ]);
    DocumentNumbering::create([
        'document_id' => $document->id,
        'document_number' => 'ALK/SP/2026/0003',
        'prefix' => 'SP',
        'year' => 2026,
        'sequence_number' => 3,
        'generated_by' => $director->id,
    ]);

    try {
        app(DocumentVerificationService::class)->getOrCreateVerificationToken($document);
        $this->fail('Expected ValidationException to be thrown.');
    } catch (ValidationException $e) {
        expect($e->errors())->toHaveKey('verification');
    }

    expect(DocumentQrVerification::count())->toBe(0);
});

it('regression token: ditolak jika nomor surat belum ada', function () {
    $document = Document::factory()->create([
        'status' => DocumentStatus::Published,
        'document_source' => DocumentSource::Generated,
    ]);

    try {
        app(DocumentVerificationService::class)->getOrCreateVerificationToken($document);
        $this->fail('Expected ValidationException to be thrown.');
    } catch (ValidationException $e) {
        expect($e->errors())->toHaveKey('verification');
    }

    expect(DocumentQrVerification::count())->toBe(0);
});

// === REGRESSION: PDF (render nyata via DomPDF) ===
it('regression pdf: regeneratePublishedPdf menulis file PDF valid', function () {
    $director = User::factory()->create(['role' => 'director']);
    $event = Event::factory()->create();
    $document = Document::factory()->create([
        'event_id' => $event->id,
        'status' => DocumentStatus::Published,
        'document_source' => DocumentSource::Generated,
        'tipe' => 'kwitansi',
        'file_path' => 'documents/regression-final.pdf',
    ]);
    DocumentNumbering::create([
        'document_id' => $document->id,
        'document_number' => 'ALK/KW/2026/0001',
        'prefix' => 'KW',
        'year' => 2026,
        'sequence_number' => 1,
        'generated_by' => $director->id,
    ]);
    DocumentQrVerification::create([
        'document_id' => $document->id,
        'verification_token' => (string) \Illuminate\Support\Str::uuid(),
        'qr_path' => 'document-qr/regression.png',
        'generated_by' => $director->id,
        'generated_at' => now(),
    ]);

    app(DocumentBuilderService::class)->regeneratePublishedPdf($document);

    $content = Storage::disk('public')->get('documents/regression-final.pdf');

    expect($content)->not->toBeNull();
    expect(substr($content, 0, 5))->toBe('%PDF-');
    expect(strlen($content))->toBeGreaterThan(1000);
});

// === REGRESSION: Numbering (manual) ===
it('regression numbering: admin dapat set nomor manual pada dokumen Draft', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $document = Document::factory()->create(['status' => DocumentStatus::Draft]);

    app(DocumentNumberService::class)->setManualNumber($document, 'ALK/SP/2026/0100', $admin);

    expect($document->fresh()->numbering->document_number)->toBe('ALK/SP/2026/0100');
});

it('regression numbering: nomor manual ditolak untuk dokumen non-Draft', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $document = Document::factory()->create(['status' => DocumentStatus::Approved]);

    try {
        app(DocumentNumberService::class)->setManualNumber($document, 'ALK/SP/2026/0101', $admin);
        $this->fail('Expected ValidationException to be thrown.');
    } catch (ValidationException $e) {
        expect($e->errors())->toHaveKey('nomor_surat');
    }
});

// === REGRESSION: Approval / Reject error handling via web route ===
it('regression approval: approve dokumen non-valid gagal dengan pesan error, status tidak berubah', function () {
    $director = User::factory()->create(['role' => 'director']);
    app(\App\Services\DirectorPinService::class)->setPin($director, '123456');

    $document = Document::factory()->create([
        'status' => DocumentStatus::Draft,
        'document_source' => DocumentSource::Generated,
    ]);

    $this->actingAs($director)
        ->post(route('director.approval.approve', $document->id), ['pin' => '123456'])
        ->assertRedirect()
        ->assertSessionHas('error');

    expect($document->fresh()->status)->toBe(DocumentStatus::Draft);
});

it('regression reject: reject tanpa alasan gagal dengan pesan error, status tetap Pending', function () {
    $director = User::factory()->create(['role' => 'director']);
    app(\App\Services\DirectorPinService::class)->setPin($director, '123456');
    $admin = User::factory()->create(['role' => 'admin']);
    $document = Document::factory()->create([
        'status' => DocumentStatus::Pending,
        'document_source' => DocumentSource::Generated,
    ]);
    \App\Models\DocumentApproval::factory()->create([
        'document_id' => $document->id,
        'submitted_by' => $admin->id,
        'status' => 'pending',
    ]);

    $this->actingAs($director)
        ->post(route('director.approval.reject', $document->id), ['pin' => '123456', 'reason' => ''])
        ->assertRedirect()
        ->assertSessionHasErrors('reason');

    expect($document->fresh()->status)->toBe(DocumentStatus::Pending);
});