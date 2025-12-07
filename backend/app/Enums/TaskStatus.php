<?php

namespace App\Enums;

enum TaskStatus: string
{
    case PLANNED = 'planned';
    case IN_PROGRESS = 'in_progress';
    case DONE = 'done';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::PLANNED => 'Planned',
            self::IN_PROGRESS => 'In Progress',
            self::DONE => 'Done',
        };
    }
}

