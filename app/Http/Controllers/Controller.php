<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * Базовый контроллер приложения.
 *
 * Любой контроллер в приложении должен наследоваться от этого класса.
 * Предоставляет:
 * - Валидацию запросов
 * - Авторизацию
 * - Диспетчеризацию задач
 * - Middleware
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    // Здесь можно добавить общие методы для всех контроллеров, если нужно
}
