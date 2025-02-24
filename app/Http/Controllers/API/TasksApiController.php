<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskApiResource;
use App\Models\Task;
use App\Services\Tasks\CreateTaskService;
use App\Services\Tasks\UpdateTaskService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TasksApiController extends Controller
{
    public function index(Request $request)
    {
        $tasks = $request
            ->user()
            ->tasks()
            ->simplePaginate();

        return TaskApiResource::collection($tasks);
    }

    public function store(StoreTaskRequest $request, CreateTaskService $create)
    {
        $task = $create($request->validated(), $request->user());

        return new TaskApiResource($task);
    }

    public function update(Task $task, UpdateTaskRequest $request, UpdateTaskService $update)
    {
        $update($task, $request->validated());

        return new TaskApiResource($task);
    }

    public function destroy(Task $task)
    {
        Gate::authorize('delete', $task);

        $task->delete();

        return response()->noContent();
    }
}
