<?php

namespace App\Services\User;

use App\Mail\TaskMail;
use App\Models\Task\Task;
use Illuminate\Support\Facades\Mail;

class UserService
{
    public static function sendTaskMail(Task $task): void
    {
        $task->loadMissing('user');

        $mail = new TaskMail($task);

        Mail::to($task->user)->queue($mail);
    }
}
