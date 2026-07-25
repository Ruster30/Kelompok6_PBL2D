<?php

use App\Models\Document;
use App\Models\DocumentNumbering;
use App\Models\User;
use App\Repositories\Contracts\DocumentNumberingRepositoryInterface;
use App\Repositories\Contracts\DocumentRepositoryInterface;
use App\Services\DdmsSettingService;
use App\Services\DocumentNumberingService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->numberingRepo = mock(DocumentNumberingRepositoryInterface::class);
    $this->documentRepo = mock(DocumentRepositoryInterface::class);
    $this->settingService = mock(DdmsSettingService::class);
    $this->service = new DocumentNumberingService(
        $this->numberingRepo,
        $this->documentRepo,
        $this->settingService,
    );
});

it('generates number for approved document', function () {
    $document = Document::factory()->create(['status' => 'approved', 'tipe' => 'proposal']);
    $user = User::factory()->create();

    $this->numberingRepo->shouldReceive('nextSequence')->once()->andReturn(1);
    $this->numberingRepo->shouldReceive('create')->once()->andReturn(
        new DocumentNumbering(['document_number' => 'SP/2026/0001']),
    );
    $this->documentRepo->shouldReceive('update')->once()->andReturn($document);
    $this->settingService->shouldReceive('getSettingValue')->andReturn(null);

    $result = $this->service->generate($document, $user);

    expect($result)->toBeInstanceOf(DocumentNumbering::class);
});

it('throws exception for non-approved document', function () {
    $document = Document::factory()->create(['status' => 'draft']);
    $user = User::factory()->create();

    $this->service->generate($document, $user);
})->throws(\App\Exceptions\DDMS\DocumentNotApprovedException::class);
