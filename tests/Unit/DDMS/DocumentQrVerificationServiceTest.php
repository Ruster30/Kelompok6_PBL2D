<?php

use App\Models\Document;
use App\Models\DocumentQrVerification;
use App\Models\User;
use App\Repositories\Contracts\DocumentQrVerificationRepositoryInterface;
use App\Repositories\Contracts\DocumentRepositoryInterface;
use App\Services\DdmsSettingService;
use App\Services\DocumentQrVerificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->qrRepo = mock(DocumentQrVerificationRepositoryInterface::class);
    $this->documentRepo = mock(DocumentRepositoryInterface::class);
    $this->settingService = mock(DdmsSettingService::class);
    $this->service = new DocumentQrVerificationService(
        $this->qrRepo,
        $this->documentRepo,
        $this->settingService,
    );
});

it('generates QR for published document', function () {
    $document = Document::factory()->create(['status' => 'published']);
    $user = User::factory()->create();

    $this->qrRepo->shouldReceive('findByDocument')->once()->andReturnNull();

    $this->qrRepo->shouldReceive('create')->once()->andReturn(
        new DocumentQrVerification(['verification_token' => str_repeat('a', 32)]),
    );
    $this->settingService->shouldReceive('getSettingValue')->andReturn(365);

    $result = $this->service->generate($document, $user);

    expect($result)->toBeInstanceOf(DocumentQrVerification::class);
    expect(strlen($result->verification_token))->toBe(32);
});

it('throws exception for non-published document', function () {
    $document = Document::factory()->create(['status' => 'draft']);
    $user = User::factory()->create();

    $this->service->generate($document, $user);
})->throws(\RuntimeException::class);

it('validates token correctly', function () {
    $qr = DocumentQrVerification::factory()->create([
        'verification_token' => str_repeat('b', 32),
        'expires_at' => now()->addDays(30),
    ]);

    $this->qrRepo->shouldReceive('findByToken')->andReturn($qr);

    $result = $this->service->validateToken($qr->verification_token);

    expect($result->verification_token)->toBe(str_repeat('b', 32));
});

it('throws exception for expired token', function () {
    $qr = DocumentQrVerification::factory()->create([
        'verification_token' => str_repeat('c', 32),
        'expires_at' => now()->subDays(1),
    ]);

    $this->qrRepo->shouldReceive('findByToken')->andReturn($qr);

    $this->service->validateToken($qr->verification_token);
})->throws(\App\Exceptions\DDMS\QrVerificationExpiredException::class);
