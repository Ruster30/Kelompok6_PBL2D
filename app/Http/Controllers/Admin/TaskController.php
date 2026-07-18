<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Services\AdminTaskService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct(
        private AdminTaskService $taskService
    ) {}

    public function index(Request $request)
    {
        $data = $this->taskService->getIndexData(
            $request->search,
            $request->status
        );

        return view("admin.tasks.index", $data);
    }

    public function store(StoreTaskRequest $request)
    {
        $this->taskService->createTask($request->validated());

        return redirect()
            ->route("admin.tasks.index")
            ->with("success", "Tugas berhasil dibuat.");
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        $this->taskService->updateTask($task, $request->validated());

        return redirect()
            ->route("admin.tasks.index")
            ->with("success", "Tugas berhasil diperbarui.");
    }

    public function destroy(Task $task)
    {
        $this->taskService->deleteTask($task);

        return redirect()
            ->route("admin.tasks.index")
            ->with("success", "Tugas berhasil dihapus.");
    }
}