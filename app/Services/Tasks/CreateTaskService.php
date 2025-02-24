<?php

namespace App\Services\Tasks;

use App\Models\Task;
use App\Models\User;
use Illuminate\Contracts\Support\Responsable;

class CreateTaskService implements Responsable
{
    public function __invoke(array $data, User $user): Task
    {
        $task = new Task();
        $task->forceFill($data);
        $task->user_id = $user->id;
        $task->save();

        return $task;
    }

    public function toResponse($request)
    {
        return redirect()->route('tasks.index');
    }
}
