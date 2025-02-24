<?php

namespace App\Services\Tasks;

use App\Models\Task;

class UpdateTaskService
{
    public function __invoke(Task $task, array $data): Task
    {
        $task->forceFill($data);
        $task->save();

        return $task;
    }
}
