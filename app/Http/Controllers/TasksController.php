<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Services\Tasks\CreateTaskService;
use App\Services\Tasks\UpdateTaskService;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TasksController extends Controller
{
    public function index(Request $request)
    {
        $tasks = $request
            ->user()
            ->tasks()
            ->get();

        return view('tasks.index', ['tasks' => $tasks]);
    }

    public function create()
    {
        return view('tasks.create');
    }

    public function store(StoreTaskRequest $request, CreateTaskService $create)
    {
        $create($request->validated(), $request->user());

        return $create;
    }

    public function edit(Task $task)
    {
        return view('tasks.edit', ['task' => $task]);
    }

    public function update(Task $task, UpdateTaskRequest $request, UpdateTaskService $update)
    {
        $update($task, $request->validated());

        return redirect()->route('tasks.index');
    }

    public function destroy(Task $task)
    {
        Gate::authorize('delete', $task);

        $task->delete();

        return redirect()->route('tasks.index');
    }
}

