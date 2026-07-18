<?php

use App\Models\User;
use App\Models\Task;
use App\Models\Event;
use App\Models\Vendor;
use App\Services\AdminTaskService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
});

// ─── Create Task ────────────────────────────────────────────

test('admin can create task via service', function () {
    $event  = Event::factory()->create();
    $vendor = Vendor::factory()->withAccount()->create();
    $service = app(AdminTaskService::class);

    $task = $service->createTask([
        'nama_tugas' => 'Siapkan Dekorasi',
        'event_id'   => $event->id,
        'vendor_id'  => $vendor->id,
        'prioritas'  => 'tinggi',
        'deadline'   => now()->addDays(7)->toDateString(),
        'status'     => 'ditugaskan',
        'deskripsi'  => 'Siapkan semua dekorasi untuk acara',
    ]);

    expect($task)->toBeInstanceOf(Task::class);
    $this->assertDatabaseHas('tasks', [
        'id'         => $task->id,
        'nama_tugas' => 'Siapkan Dekorasi',
    ]);
});

test('create task sends notification to vendor with account', function () {
    $event  = Event::factory()->create();
    $vendor = Vendor::factory()->withAccount()->create();
    $service = app(AdminTaskService::class);

    $service->createTask([
        'nama_tugas' => 'Siapkan Sound System',
        'event_id'   => $event->id,
        'vendor_id'  => $vendor->id,
        'prioritas'  => 'sedang',
        'deadline'   => now()->addDays(3)->toDateString(),
        'status'     => 'ditugaskan',
        'deskripsi'  => 'Siapkan sound system',
    ]);

    $this->assertDatabaseHas('notifications', [
        'user_id' => $vendor->user_id,
        'judul'   => 'Tugas Baru: Siapkan Sound System',
    ]);
});

// ─── Update Task ────────────────────────────────────────────

test('admin can update task via service', function () {
    $task    = Task::factory()->create(['nama_tugas' => 'Old Task']);
    $service = app(AdminTaskService::class);

    $updated = $service->updateTask($task, [
        'nama_tugas' => 'Updated Task',
        'event_id'   => $task->event_id,
        'vendor_id'  => $task->vendor_id,
        'prioritas'  => 'tinggi',
        'status'     => 'dikerjakan',
        'deskripsi'  => 'Updated description',
    ]);

    expect($updated->fresh()->nama_tugas)->toBe('Updated Task');
    expect($updated->fresh()->status)->toBe('dikerjakan');
});

// ─── Delete Task ────────────────────────────────────────────

test('admin can delete task via service', function () {
    $task    = Task::factory()->create();
    $service = app(AdminTaskService::class);

    $service->deleteTask($task);

    $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
});
