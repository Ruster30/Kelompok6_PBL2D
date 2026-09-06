<?php

use App\Enums\DocumentSource;
use App\Enums\DocumentStatus;
use App\Exceptions\DDMS\ApprovalNotPendingException;
use App\Models\Document;
use App\Models\DocumentApproval;
use App\Models\DocumentNumbering;
use App\Models\User;
use App\Repositories\Contracts\DocumentApprovalRepositoryInterface;
use App\Repositories\Contracts\DocumentNumberingRepositoryInterface;
use App\Repositories\Contracts\DocumentRepositoryInterface;
use App\Services\DirectorPinService;
use App\Services\DocumentApprovalService;
use App\Services\DocumentBuilderService;
use App\Services\DocumentNumberService;
use App\Services\DocumentQrCodeService;
use App\Services\DocumentVerificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->approvalRepo = mock(DocumentApprovalRepositoryInterface::class);
    $this->documentRepo = mock(DocumentRepositoryInterface::class);
    $this->numberingRepo = mock(DocumentNumberingRepositoryInterface::class);
    $this->pinService = mock(DirectorPinService::class);
    $this->numberService = mock(DocumentNumberService::class);
    $this->qrCodeService = mock(DocumentQrCodeService::class);
    $this->verificationService = mock(DocumentVerificationService::class);

    $this->service = new DocumentApprovalService(
        $this->approvalRepo,
        $this->documentRepo,
        $this->numberingRepo,
        $this->pinService,
        $this->numberService,
        $this->qrCodeService,
        $this->verificationService,
    );
});

it('can submit document for approval', function () {
    $document = Document::factory()->create(['status' => DocumentStatus::Draft]);
    $document->setRelation('numbering', new DocumentNumbering([
        'document_number' => 'ALK/SP/2026/0001',
    ]));
    $user = User::factory()->create();

    $this->approvalRepo
        ->shouldReceive('findLatestByDocument')
        ->with($document->id)
        ->once()
        ->andReturnNull();

    $this->approvalRepo
        ->shouldReceive('create')
        ->once()
        ->andReturn(new DocumentApproval([
            'document_id' => $document->id,
            'submitted_by' => $user->id,
            'status' => 'pending',
        ]));

    $this->documentRepo
        ->shouldReceive('update')
        ->once()
        ->andReturn($document);

    $result = $this->service->submit($document, $user);

    expect($result)->toBeInstanceOf(DocumentApproval::class);
    expect($result->status)->toBe('pending');
});

it('throws exception when submitting non-draft document', function () {
    $document = Document::factory()->create(['status' => DocumentStatus::Approved]);
    $user = User::factory()->create();

    $this->service->submit($document, $user);
})->throws(\RuntimeException::class, 'Hanya dokumen dengan status Draft');

it('can approve a pending approval', function () {
    $document = Document::factory()->create(['status' => DocumentStatus::Pending]);
    $approval = DocumentApproval::factory()->create([
        'document_id' => $document->id,
        'status' => 'pending',
    ]);
    $user = User::factory()->create();

    $this->approvalRepo
        ->shouldReceive('update')
        ->once()
        ->andReturnUsing(function (DocumentApproval $approval, array $data) {
            $approval->forceFill($data)->save();

            return $approval;
        });

    $this->documentRepo
        ->shouldReceive('update')
        ->once()
        ->andReturn($document);

    $result = $this->service->approve($approval, $user);

    expect($result->status)->toBe('approved');
});

it('throws exception when approving non-pending approval', function () {
    $document = Document::factory()->create();
    $approval = DocumentApproval::factory()->create([
        'document_id' => $document->id,
        'status' => 'approved',
    ]);
    $user = User::factory()->create();

    $this->service->approve($approval, $user);
})->throws(ApprovalNotPendingException::class);

it('throws exception when rejecting without reason', function () {
    $document = Document::factory()->create(['status' => DocumentStatus::Pending]);
    $approval = DocumentApproval::factory()->create([
        'document_id' => $document->id,
        'status' => 'pending',
    ]);
    $user = User::factory()->create();

    $this->service->reject($approval, $user, '');
})->throws(\RuntimeException::class, 'Alasan reject wajib diisi');

it('director can approve generated pending document with correct pin', function () {
    $document = Document::factory()->create([
        'status' => DocumentStatus::Pending,
        'document_source' => DocumentSource::Generated,
    ]);
    $approval = DocumentApproval::factory()->create([
        'document_id' => $document->id,
        'status' => 'pending',
    ]);
    $director = User::factory()->create(['role' => 'director']);

    $this->pinService->shouldReceive('verifyPin')->once()->with($director, '123456');

    $this->approvalRepo
        ->shouldReceive('findLatestByDocument')
        ->with($document->id)
        ->once()
        ->andReturn($approval);

    $this->approvalRepo
        ->shouldReceive('update')
        ->once()
        ->andReturnUsing(function (DocumentApproval $approval, array $data) {
            $approval->forceFill($data)->save();

            return $approval;
        });

    $this->documentRepo
        ->shouldReceive('update')
        ->once()
        ->andReturnUsing(function (Document $document, array $data) {
            $document->forceFill($data)->save();

            return $document;
        });

    $this->app->instance(
        DocumentBuilderService::class,
        \Mockery::mock(DocumentBuilderService::class)->shouldIgnoreMissing(),
    );

    $result = $this->service->directorApprove($document, $director, '123456');

    expect($result->status)->toBe(DocumentStatus::Approved);
    expect($approval->fresh()->status)->toBe('approved');
});

it('director approve with wrong pin fails and status stays pending', function () {
    $document = Document::factory()->create([
        'status' => DocumentStatus::Pending,
        'document_source' => DocumentSource::Generated,
    ]);
    DocumentApproval::factory()->create([
        'document_id' => $document->id,
        'status' => 'pending',
    ]);
    $director = User::factory()->create(['role' => 'director']);

    $this->pinService->shouldReceive('verifyPin')->once()->andThrow(
        ValidationException::withMessages(['pin' => 'PIN yang dimasukkan salah.']),
    );

    try {
        $this->service->directorApprove($document, $director, '000000');
        $this->fail('Expected ValidationException to be thrown.');
    } catch (ValidationException) {
        // expected
    }

    expect($document->fresh()->status)->toBe(DocumentStatus::Pending);
});

it('director can reject generated pending document with correct pin', function () {
    $document = Document::factory()->create([
        'status' => DocumentStatus::Pending,
        'document_source' => DocumentSource::Generated,
    ]);
    $approval = DocumentApproval::factory()->create([
        'document_id' => $document->id,
        'status' => 'pending',
    ]);
    $director = User::factory()->create(['role' => 'director']);

    $this->pinService->shouldReceive('verifyPin')->once()->with($director, '123456');

    $this->approvalRepo
        ->shouldReceive('findLatestByDocument')
        ->with($document->id)
        ->once()
        ->andReturn($approval);

    $this->approvalRepo
        ->shouldReceive('update')
        ->once()
        ->andReturnUsing(function (DocumentApproval $approval, array $data) {
            $approval->forceFill($data)->save();

            return $approval;
        });

    $this->documentRepo
        ->shouldReceive('update')
        ->once()
        ->andReturnUsing(function (Document $document, array $data) {
            $document->forceFill($data)->save();

            return $document;
        });

    $result = $this->service->directorReject($document, $director, 'Biaya tidak sesuai budget.', '123456');

    expect($result->status)->toBe(DocumentStatus::Rejected);
    expect($approval->fresh()->status)->toBe('rejected');
});

it('director reject with wrong pin fails and status stays pending', function () {
    $document = Document::factory()->create([
        'status' => DocumentStatus::Pending,
        'document_source' => DocumentSource::Generated,
    ]);
    DocumentApproval::factory()->create([
        'document_id' => $document->id,
        'status' => 'pending',
    ]);
    $director = User::factory()->create(['role' => 'director']);

    $this->pinService->shouldReceive('verifyPin')->once()->andThrow(
        ValidationException::withMessages(['pin' => 'PIN yang dimasukkan salah.']),
    );

    try {
        $this->service->directorReject($document, $director, 'Biaya tidak sesuai budget.', '000000');
        $this->fail('Expected ValidationException to be thrown.');
    } catch (ValidationException) {
        // expected
    }

    expect($document->fresh()->status)->toBe(DocumentStatus::Pending);
});