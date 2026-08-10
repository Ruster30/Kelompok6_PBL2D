<?php

use App\Enums\DocumentSource;
use App\Enums\DocumentStatus;
use App\Models\Document;
use App\Models\DocumentApproval;
use App\Models\User;
use App\Services\DirectorPinService;
use App\Services\DocumentBuilderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

/**
 * Buat dokumen Generated + Pending lengkap dengan approval pending.
 */
function makeDirectorPendingDocument(): Document
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
    // Isolasi verifikasi PIN flow: regenerasi PDF final diverifikasi terpisah
    // pada regression check, sehingga bukan blocker di sini.
    $this->mock(DocumentBuilderService::class, function ($mock) {
        $mock->shouldReceive('regenerateFinalPdf');
    });

    $this->director = User::factory()->create(['role' => 'director', 'email_verified_at' => now()]);
});

// === TEST 1: Set PIN => approval_pin tersimpan sebagai hash ===
it('TEST 1: stores approval_pin as hash when director sets pin', function () {
    $this->actingAs($this->director);

    $response = $this->post(route('director.settings.pin.store'), [
        'pin' => '123456',
        'pin_confirmation' => '123456',
    ]);

    $response->assertRedirect(route('director.settings.pin'));
    $response->assertSessionHas('success');

    $stored = $this->director->fresh()->approval_pin;

    expect($stored)->not->toBe('123456');
    expect(Hash::check('123456', $stored))->toBeTrue();
});

// === TEST 2: Approve tanpa PIN => gagal, status tetap Pending ===
it('TEST 2: approve without pin fails and document stays pending', function () {
    $this->actingAs($this->director);
    $document = makeDirectorPendingDocument();

    $response = $this->post(route('director.approval.approve', $document->id), []);

    $response->assertSessionHasErrors('pin');
    expect($document->fresh()->status)->toBe(DocumentStatus::Pending);
    expect($document->fresh()->latestApproval->status)->toBe('pending');
});

// === TEST 3: Approve dengan PIN salah => gagal, status tetap Pending ===
it('TEST 3: approve with wrong pin fails and document stays pending', function () {
    $this->actingAs($this->director);
    app(DirectorPinService::class)->setPin($this->director, '123456');
    $document = makeDirectorPendingDocument();

    $response = $this->post(route('director.approval.approve', $document->id), [
        'pin' => '999999',
    ]);

    $response->assertSessionHasErrors('pin');
    expect($document->fresh()->status)->toBe(DocumentStatus::Pending);
    expect($document->fresh()->latestApproval->status)->toBe('pending');
});

// === TEST 4: Approve dengan PIN benar => status menjadi Approved ===
it('TEST 4: approve with correct pin changes status to approved', function () {
    $this->actingAs($this->director);
    app(DirectorPinService::class)->setPin($this->director, '123456');
    $document = makeDirectorPendingDocument();

    $response = $this->post(route('director.approval.approve', $document->id), [
        'pin' => '123456',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');
    expect($document->fresh()->status)->toBe(DocumentStatus::Approved);
    expect($document->fresh()->latestApproval->status)->toBe('approved');
});

// === TEST 5: Reject dengan PIN salah => gagal, status tetap Pending ===
it('TEST 5: reject with wrong pin fails and document stays pending', function () {
    $this->actingAs($this->director);
    app(DirectorPinService::class)->setPin($this->director, '123456');
    $document = makeDirectorPendingDocument();

    $response = $this->post(route('director.approval.reject', $document->id), [
        'pin' => '999999',
        'reason' => 'Biaya tidak sesuai budget.',
    ]);

    $response->assertSessionHasErrors('pin');
    expect($document->fresh()->status)->toBe(DocumentStatus::Pending);
    expect($document->fresh()->latestApproval->status)->toBe('pending');
});

// === TEST 6: Reject dengan PIN benar => status menjadi Rejected ===
it('TEST 6: reject with correct pin changes status to rejected', function () {
    $this->actingAs($this->director);
    app(DirectorPinService::class)->setPin($this->director, '123456');
    $document = makeDirectorPendingDocument();

    $response = $this->post(route('director.approval.reject', $document->id), [
        'pin' => '123456',
        'reason' => 'Biaya tidak sesuai budget.',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');
    expect($document->fresh()->status)->toBe(DocumentStatus::Rejected);
    expect($document->fresh()->latestApproval->status)->toBe('rejected');
});