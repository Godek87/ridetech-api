<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public const HOME = '/dashboard';

    public function boot(): void
    {

        $this->routes(function () {
            // API маршруты с префиксом /api
            Route::prefix('api')
                ->middleware('api')
                ->group(base_path('routes/api.php'));

            // Web маршруты
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });

        $this->configureRateLimiting();

    }

    protected function configureRateLimiting(): void
    {
         RateLimiter::for('api', function (Request $request) {
        if (app()->environment('local')) {
            // Локалка — без лимитов
            return Limit::none();
        }

        // Продакшен — обычные лимиты
        return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
    });
    }
}
