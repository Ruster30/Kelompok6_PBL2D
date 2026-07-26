<?php

use App\Models\Document;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->director = User::factory()->create(['role' => 'director']);
    $this->client = User::factory()->create(['role' => 'client']);
});

it('lists documents', function () {
    $this->actingAs($this->admin);
    Document::factory()->count(3)->create();

    $response = $this->getJson('/api/ddms/documents');

    $response->assertStatus(200);
    $response->assertJsonStructure(['data' => []]);
});

it('shows single document', function () {
    $this->actingAs($this->admin);
    $document = Document::factory()->create();

    $response = $this->getJson("/api/ddms/documents/{$document->id}");

    $response->assertStatus(200);
    $response->assertJsonStructure(['data' => ['id', 'nama_file', 'tipe', 'status']]);
});

it('denies access for unauthorized user', function () {
    $this->actingAs($this->client);
    $document = Document::factory()->create();

    $response = $this->patchJson("/api/ddms/documents/{$document->id}/archive");

    $response->assertStatus(403);
});

it('approves document workflow end-to-end', function () {
    $this->actingAs($this->admin);

    // Create draft document
    $document = Document::factory()->create(['status' => 'draft']);

    // Submit for approval
    $response = $this->postJson("/api/ddms/documents/{$document->id}/submit", [
        'submitted_by' => $this->admin->id,
    ]);
    $response->assertStatus(200);
    $response->assertJsonStructure(['data' => ['id', 'status']]);
    $response->assertJsonPath('data.status', 'pending');

    // Approve (as director)
    $this->actingAs($this->director);
    $document->refresh();
    $approval = $document->latestApproval;

    $response = $this->postJson("/api/ddms/documents/{$document->id}/approve", [
        'approval_id' => $approval->id,
        'approver_id' => $this->director->id,
    ]);

    $response->assertStatus(200);
    $response->assertJsonStructure(['data' => ['id', 'status']]);
    $response->assertJsonPath('data.status', 'approved');

    // Verify document is published
    $document->refresh();
    expect($document->status->value)->toBe('approved');
});

it('rejects document', function () {
    $this->actingAs($this->admin);

    $document = Document::factory()->create(['status' => 'draft']);

    $this->postJson("/api/ddms/documents/{$document->id}/submit", [
        'submitted_by' => $this->admin->id,
    ]);

    $this->actingAs($this->director);
    $document->refresh();
    $approval = $document->latestApproval;

    $response = $this->postJson("/api/ddms/documents/{$approval->id}/reject", [
        'approval_id' => $approval->id,
        'approver_id' => $this->director->id,
        'reason' => 'Harga tidak sesuai budget.',
    ]);

    $response->assertStatus(200);
    $response->assertJsonPath('data.status', 'rejected');
});

it('rejects document without reason fails', function () {
    $this->actingAs($this->admin);
    $document = Document::factory()->create(['status' => 'draft']);

    $this->postJson("/api/ddms/documents/{$document->id}/submit", [
        'submitted_by' => $this->admin->id,
    ]);

    $this->actingAs($this->director);
    $document->refresh();
    $approval = $document->latestApproval;

    $response = $this->postJson("/api/ddms/documents/{$approval->id}/reject", [
        'approval_id' => $approval->id,
        'approver_id' => $this->director->id,
        'reason' => '',
    ]);

    $response->assertStatus(422);
});
