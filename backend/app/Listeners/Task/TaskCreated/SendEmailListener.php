<?php

namespace App\Listeners\Task\TaskCreated;

use App\Events\Task\TaskCreated;
use App\Services\User\UserService;

class SendEmailListener
{
    public function __construct(private UserService $userService)
    {
    }

    public function handle(TaskCreated $event): void
    {
        $this->userService->sendTaskMail($event->task);
    }
}
