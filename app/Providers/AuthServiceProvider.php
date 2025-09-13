<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Связь моделей с политиками
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Регистрация политик
     */
    public function boot(): void
    {
        $this->registerPolicies();
        // Добавление Gate, если нужно
        // Gate::define('update-post', fn ($user, $post) => $user->id === $post->user_id);
    }
}
