<?php

use App\Enums\DocumentSource;
use App\Enums\DocumentStatus;
use App\Models\Document;
use App\Models\DocumentApproval;
use App\Models\User;
use App\Services\DirectorPinService;
use App\Services\DocumentBuilderService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Buat dokumen Generated + Pending + approval pending untuk halaman review.
 */
function createPendingDirectorDocument(): Document
{
    $admin = User::factory()->create(['role' => 'admin']);

    $document = Document::factory()->create([
        'status' => DocumentStatus::Pending,
        'document_source' => DocumentSource::Generated,
    ]);

    DocumentApproval::factory()->create([
        'document_id' => $document->id,
        'submitted_by' => $admin->id,
        'status' => 'pending',
    ]);

    return $document->fresh();
}

beforeEach(function () {
    $this->mock(DocumentBuilderService::class, function ($mock) {
        $mock->shouldReceive('regenerateFinalPdf');
    });

    $this->director = User::factory()->create(['role' => 'director', 'email_verified_at' => now()]);
});

it('halaman pending: tidak ada PIN permanen, hanya tombol Approve dan Reject', function () {
    $document = createPendingDirectorDocument();

    $response = $this->actingAs($this->director)->get(route('director.approval.show', $document->id));

    $response->assertOk();
    $html = $response->getContent();

    // Tombol Approve/Reject membuka modal (bukan form inline/collapse).
    expect($html)->toContain('data-bs-toggle="modal" data-bs-target="#approveModal"');
    expect($html)->toContain('data-bs-toggle="modal" data-bs-target="#rejectModal"');
    expect($html)->not->toContain('data-bs-toggle="collapse"');

    // Input PIN hanya ada di dalam modal: tepat 2 (approve + reject).
    expect(substr_count($html, 'name="pin"'))->toBe(2);
    expect(substr_count($html, 'type="password"'))->toBe(2);
    expect(substr_count($html, 'modal fade'))->toBe(2);

    // Judul modal dan tombol Batal.
    expect($html)->toContain('>Konfirmasi Approve</h5>');
    expect($html)->toContain('>Konfirmasi Reject</h5>');
    expect(substr_count($html, '>Batal</button>'))->toBe(2);

    // PIN tetap dikirim via POST form biasa ke route approve/reject.
    expect($html)->toContain('action="' . route('director.approval.approve', $document->id) . '"');
    expect($html)->toContain('action="' . route('director.approval.reject', $document->id) . '"');
});

it('halaman approved: tidak ada modal/PIN, workflow Publish tetap ada', function () {
    $document = Document::factory()->create([
        'status' => DocumentStatus::Approved,
        'document_source' => DocumentSource::Generated,
    ]);

    $response = $this->actingAs($this->director)->get(route('director.approval.show', $document->id));

    $response->assertOk();
    $html = $response->getContent();

    expect($html)->not->toContain('name="pin"');
    expect($html)->not->toContain('modal fade');
    expect($html)->toContain('action="' . route('director.approval.publish', $document->id) . '"');
});

it('validation error pin pada approve: modal approve terbuka kembali', function () {
    $document = createPendingDirectorDocument();
    app(DirectorPinService::class)->setPin($this->director, '123456');

    $this->actingAs($this->director)
        ->post(route('director.approval.approve', $document->id), ['pin' => '999999'])
        ->assertRedirect()
        ->assertSessionHasErrors('pin');
    expect($document->fresh()->status)->toBe(DocumentStatus::Pending);

    $html = $this->get(route('director.approval.show', $document->id))->getContent();

    expect($html)->toContain('bootstrap.Modal.getOrCreateInstance(approveModalEl).show()');
    expect($html)->not->toContain('bootstrap.Modal.getOrCreateInstance(rejectModalEl).show()');
});

it('validation error pin pada reject: modal reject terbuka kembali', function () {
    $document = createPendingDirectorDocument();
    app(DirectorPinService::class)->setPin($this->director, '123456');

    $this->actingAs($this->director)
        ->post(route('director.approval.reject', $document->id), ['pin' => '999999', 'reason' => 'Tidak sesuai'])
        ->assertRedirect()
        ->assertSessionHasErrors('pin');
    expect($document->fresh()->status)->toBe(DocumentStatus::Pending);

    $html = $this->get(route('director.approval.show', $document->id))->getContent();

    expect($html)->toContain('bootstrap.Modal.getOrCreateInstance(rejectModalEl).show()');
    expect($html)->not->toContain('bootstrap.Modal.getOrCreateInstance(approveModalEl).show()');
});

it('validation error reason kosong pada reject: modal reject terbuka kembali', function () {
    $document = createPendingDirectorDocument();
    app(DirectorPinService::class)->setPin($this->director, '123456');

    $this->actingAs($this->director)
        ->post(route('director.approval.reject', $document->id), ['pin' => '123456', 'reason' => ''])
        ->assertRedirect()
        ->assertSessionHasErrors('reason');
    expect($document->fresh()->status)->toBe(DocumentStatus::Pending);

    $html = $this->get(route('director.approval.show', $document->id))->getContent();

    expect($html)->toContain('bootstrap.Modal.getOrCreateInstance(rejectModalEl).show()');
    expect($html)->not->toContain('bootstrap.Modal.getOrCreateInstance(approveModalEl).show()');
});