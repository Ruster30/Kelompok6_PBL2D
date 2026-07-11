<?php

use App\Models\Proposal;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// ─── Status Helpers ─────────────────────────────────────────

test('isWaiting returns true when status is menunggu_konfirmasi', function () {
    $proposal = Proposal::factory()->create(['status' => 'menunggu_konfirmasi']);

    expect($proposal->isWaiting())->toBeTrue();
    expect($proposal->isNegotiation())->toBeFalse();
    expect($proposal->isRevision())->toBeFalse();
    expect($proposal->isAccepted())->toBeFalse();
    expect($proposal->isRejected())->toBeFalse();
});

test('isNegotiation returns true when status is negosiasi', function () {
    $proposal = Proposal::factory()->negotiation()->create();

    expect($proposal->isNegotiation())->toBeTrue();
    expect($proposal->isWaiting())->toBeFalse();
});

test('isRevision returns true when status is direvisi', function () {
    $proposal = Proposal::factory()->revision()->create();

    expect($proposal->isRevision())->toBeTrue();
});

test('isAccepted returns true when status is diterima', function () {
    $proposal = Proposal::factory()->accepted()->create();

    expect($proposal->isAccepted())->toBeTrue();
});

test('isRejected returns true when status is ditolak', function () {
    $proposal = Proposal::factory()->rejected()->create();

    expect($proposal->isRejected())->toBeTrue();
});

// ─── Status Label ───────────────────────────────────────────

test('status_label returns Indonesian labels for all proposal statuses', function () {
    $p = Proposal::factory()->create(['status' => 'menunggu_konfirmasi']);
    expect($p->status_label)->toBe('Menunggu Konfirmasi');

    $p->update(['status' => 'negosiasi']);
    expect($p->fresh()->status_label)->toBe('Negosiasi Diajukan');

    $p->update(['status' => 'direvisi']);
    expect($p->fresh()->status_label)->toBe('Penawaran Direvisi');

    $p->update(['status' => 'diterima']);
    expect($p->fresh()->status_label)->toBe('Diterima');

    $p->update(['status' => 'ditolak']);
    expect($p->fresh()->status_label)->toBe('Ditolak');
});

// ─── Badge Class ────────────────────────────────────────────

test('badge_class returns correct CSS class for each proposal status', function () {
    $p = Proposal::factory()->create(['status' => 'menunggu_konfirmasi']);
    expect($p->badge_class)->toBe('badge-info');

    $p->update(['status' => 'negosiasi']);
    expect($p->fresh()->badge_class)->toBe('badge-warning');

    $p->update(['status' => 'direvisi']);
    expect($p->fresh()->badge_class)->toBe('badge-secondary');

    $p->update(['status' => 'diterima']);
    expect($p->fresh()->badge_class)->toBe('badge-success');

    $p->update(['status' => 'ditolak']);
    expect($p->fresh()->badge_class)->toBe('badge-danger');
});

// ─── File URL ───────────────────────────────────────────────

test('file_url returns storage url when file exists', function () {
    $proposal = Proposal::factory()->create(['file_proposal' => 'proposals/test.pdf']);

    expect($proposal->file_url)->toContain('storage/proposals/test.pdf');
});
