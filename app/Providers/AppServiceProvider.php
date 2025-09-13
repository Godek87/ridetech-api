<?php
declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
// Опциональные зависимости для привязки интерфейсов к реализациям (если они есть в проекте)
use App\Domain\User\Repositories\UserRepositoryInterface;

use App\Domain\User\Repositories\UserRepository;
use Illuminate\Support\Facades\Route;


/**
 * AppServiceProvider — центральный сервис-провайдер приложения.
 *
 * Обязанности:
 * - регистрировать сервисы/биндинги в контейнере (register);
 * - конфигурировать глобальное поведение фреймворка (boot).
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Регистрируем сервисы и биндинги в контейнере.
     *
     * Здесь стоит регистрировать:
     * - привязки интерфейс -> реализация ($this->app->bind/->singleton);
     * - фабрики, адаптеры, сторонние клиенты и т.п.
     *
     * Нужно избегать выполнения тяжёлых операций в register (например, запросов в БД).
     *
     * @return void
     */
    public function register(): void
    {
        // Привязка репозитория пользователей, если интерфейс и реализация присутствуют в проекте.
        if (interface_exists(UserRepositoryInterface::class) && class_exists(UserRepository::class)) {
            // bind — каждый раз возвращается новый экземпляр; поменяйте на singleton при необходимости.
            $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
            $this->app->bind(\App\Domain\Trip\Repositories\TripRepositoryInterface::class, \App\Domain\Trip\Repositories\TripRepository::class);
            $this->app->bind(\App\Domain\Car\Repositories\CarRepositoryInterface::class, \App\Domain\Car\Repositories\CarRepository::class);
            $this->app->bind(\App\Domain\Review\Repositories\ReviewRepositoryInterface::class, \App\Domain\Review\Repositories\ReviewRepository::class);
        }

    }

    /**
     * Выполняем глобальную настройку после регистрации всех сервисов.
     *
     * Здесь выполняются операции типа:
     * - глобальная настройка пагинации и ресурсов;
     * - принудительная схема (https) в продакшене;
     * - регистрация rate limiters;
     * - регистрация observers для моделей (если нужно).
     *
     * @return void
     */
    public function boot(): void

    {

    // $this->routes(function () {
    //     Route::middleware('api')
    //         ->prefix('api')
    //         ->group(base_path('routes/api.php'));

    //     Route::middleware('web')
    //         ->group(base_path('routes/web.php'));
    // });
        // Отключаем обёртку JSON-ресурсов по умолчанию:
        // вместо { "data": { ... } } будет возвращаться непосредственно объект ресурса.
        // Это удобно, если вы всегда формируете ответы через Resource и не хотите лишнего уровня.
        JsonResource::withoutWrapping();

        // Настройка пагинации: используем Bootstrap-разметку для удобства фронтенда
        // (Paginator::useTailwind() в новых версиях Laravel — выберите нужную тему).
        Paginator::useBootstrap();

        // В продакшене принудительно форсим https (полезно за прокси/балансировщиками).
        // Используем config('app.env') и опциональный флаг config('app.force_https').
        if (config('app.env') === 'production' || config('app.force_https') === true) {
            URL::forceScheme('https');
        }

        // Пример определения named rate-limiter для логина:
        // Используйте этот limiter в маршрутах: ->middleware('throttle:login')
        RateLimiter::for('login', function (Request $request) {
            // Ограничение по IP и по email (если есть) — комбинированный ключ уменьшает шанс обхода.
            $email = (string) $request->input('email', $request->ip());

            return Limit::perMinute(10)->by($request->ip() . '|' . $email);
        });


    }
}
