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
 * Предоставляет:
 * - Валидацию запросов
 * - Авторизацию
 * - Диспетчеризацию задач
 * - Middleware
 */

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="RideTech API",
 *     description="API для приложения совместных поездок"
 * )
 * @OA\Server(url="http://localhost:8000/api/v1", description="Development server")
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    // общие методы для всех контроллеров, если нужно
}
