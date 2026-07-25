<?php

use App\Enums\ApprovalStatus;
use App\Enums\DocumentStatus;
use App\Exceptions\DDMS\ApprovalNotPendingException;
use App\Models\Document;
use App\Models\DocumentApproval;
use App\Models\User;
use App\Repositories\Contracts\DocumentApprovalRepositoryInterface;
use App\Repositories\Contracts\DocumentRepositoryInterface;
use App\Services\DocumentApprovalService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->approvalRepo = mock(DocumentApprovalRepositoryInterface::class);
    $this->documentRepo = mock(DocumentRepositoryInterface::class);
    $this->service = new DocumentApprovalService(
        $this->approvalRepo,
        $this->documentRepo,
    );
});

it('can submit document for approval', function () {
    $document = Document::factory()->create(['status' => DocumentStatus::Draft]);
    $user = User::factory()->create();

    $this->approvalRepo
        ->shouldReceive('create')
        ->once()
        ->andReturn(new DocumentApproval([
            'document_id' => $document->id,
            'submitted_by' => $user->id,
            'status' => ApprovalStatus::Pending,
        ]));

    $this->documentRepo
        ->shouldReceive('update')
        ->once()
        ->andReturn($document);

    $result = $this->service->submit($document, $user);

    expect($result)->toBeInstanceOf(DocumentApproval::class);
    expect($result->status->value)->toBe('pending');
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
        'status' => ApprovalStatus::Pending,
    ]);
    $user = User::factory()->create();

    $this->approvalRepo
        ->shouldReceive('update')
        ->once()
        ->andReturn(tap($approval)->update([
            'status' => ApprovalStatus::Approved,
            'approver_id' => $user->id,
        ]));

    $this->documentRepo
        ->shouldReceive('update')
        ->once()
        ->andReturn($document);

    $this->approvalRepo
        ->shouldReceive('findLatestByDocument')
        ->andReturn($approval);

    $result = $this->service->approve($approval, $user);

    expect($result->status->value)->toBe('approved');
});

it('throws exception when approving non-pending approval', function () {
    $document = Document::factory()->create();
    $approval = DocumentApproval::factory()->create([
        'document_id' => $document->id,
        'status' => ApprovalStatus::Approved,
    ]);
    $user = User::factory()->create();

    $this->service->approve($approval, $user);
})->throws(ApprovalNotPendingException::class);

it('throws exception when rejecting without reason', function () {
    $document = Document::factory()->create(['status' => DocumentStatus::Pending]);
    $approval = DocumentApproval::factory()->create([
        'document_id' => $document->id,
        'status' => ApprovalStatus::Pending,
    ]);
    $user = User::factory()->create();

    $this->service->reject($approval, $user, '');
})->throws(\RuntimeException::class, 'Alasan reject wajib diisi');
