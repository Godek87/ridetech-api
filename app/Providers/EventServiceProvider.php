<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * События и их слушатели
     */
    protected $listen = [
        // 'App\Events\EventName' => [
        //     'App\Listeners\EventListener',
        // ],
    ];

    /**
     * Регистрация слушателей событий
     */
    public function boot(): void
    {
        parent::boot();
    }
}
