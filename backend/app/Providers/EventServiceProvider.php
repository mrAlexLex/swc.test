<?php

namespace App\Providers;

use App\Events\Task\TaskCreated;
use App\Listeners\Task\TaskCreated\SendEmailListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        TaskCreated::class => [
            SendEmailListener::class,
        ],
    ];

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
