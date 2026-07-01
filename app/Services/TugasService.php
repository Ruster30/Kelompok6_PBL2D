<?php

namespace App\Services;

use App\Interfaces\TaskRepositoryInterface;

class TugasService
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository
    ) {}

    public function getTasks(int $vendorId, ?int $eventId = null): array
    {
        $tugas = $this->taskRepository->getByVendorId($vendorId, $eventId);

        return compact("tugas");
    }

    public function updateTaskStatus(int $vendorId, int $taskId, string $status): void
    {
        $task = $this->taskRepository->getByVendorIdAndId($vendorId, $taskId);

        $this->taskRepository->updateStatus($task->id, $status);
    }
}