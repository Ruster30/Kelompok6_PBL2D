<?php

namespace App\Services;

use App\Interfaces\TaskRepositoryInterface;
use App\Models\Event;
use App\Models\Notification;
use App\Models\Task;
use App\Models\Vendor;

class AdminTaskService
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository
    ) {}

    public function getIndexData(?string $search, ?string $status): array
    {
        $tasks  = $this->taskRepository->paginateWithFilters($search, $status);
        $events = $this->taskRepository->getAllEvents();
        $vendors = $this->taskRepository->getAllVendors();

        return compact("tasks", "events", "vendors");
    }

    public function createTask(array $data): Task
    {
        $task = $this->taskRepository->create($data);
        $this->sendTaskNotification($data, "Tugas Baru: " . $data["nama_tugas"], function ($vendor, $event, $data) {
            $deadlineInfo = $data["deadline"]
                ? " Deadline: " . \Carbon\Carbon::parse($data["deadline"])->format("d M Y") . "."
                : "";
            return "Anda mendapat tugas baru \"" . $data["nama_tugas"] . "\" untuk event \"" . $event->nama_event . "\"." . $deadlineInfo;
        });

        return $task;
    }

    public function updateTask(Task $task, array $data): Task
    {
        $task = $this->taskRepository->adminUpdate($task, $data);
        $this->sendTaskNotification($data, "Tugas Diperbarui: " . $data["nama_tugas"], function ($vendor, $event, $data) {
            $deadlineInfo = $data["deadline"]
                ? " Deadline: " . \Carbon\Carbon::parse($data["deadline"])->format("d M Y") . "."
                : "";
            return "Tugas \"" . $data["nama_tugas"] . "\" untuk event \"" . $event->nama_event . "\" telah diperbarui." . $deadlineInfo;
        });

        return $task;
    }

    public function deleteTask(Task $task): void
    {
        $this->taskRepository->adminDelete($task);
    }

    private function sendTaskNotification(array $data, string $judul, callable $messageCallback): void
    {
        if (empty($data["vendor_id"])) {
            return;
        }

        $vendor = Vendor::with("user")->find($data["vendor_id"]);
        $event  = Event::find($data["event_id"]);

        if ($vendor && $vendor->user_id && $event) {
            Notification::create([
                "user_id" => $vendor->user_id,
                "judul"   => $judul,
                "pesan"   => $messageCallback($vendor, $event, $data),
                "tipe"    => "event",
            ]);
        }
    }
}