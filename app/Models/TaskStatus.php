<?php

namespace App\Models;

enum TaskStatus: int
{
    case Pending = 0;
    case InProgress = 1;
    case Completed = 2;

    public static function fromName(mixed $input): ?self
    {
        return match ($input) {
            'pending' => self::Pending,
            'in_progress' => self::InProgress,
            'completed' => self::Completed,
            default => null,
        };
    }

    public function name(): string
    {
        return match($this) {
            self::Pending => 'pending',
            self::InProgress => 'in_progress',
            self::Completed => 'completed',
        };
    }
}
