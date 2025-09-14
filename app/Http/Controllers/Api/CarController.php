<?php
declare(strict_types=1);
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Domain\Car\Requests\CarRequest;
use App\Domain\Car\Services\CarService;
use App\Domain\Car\ValueObjects\CarMake;
use App\Domain\Car\ValueObjects\CarModel;
use App\Domain\Car\ValueObjects\CarPlate;
use Illuminate\Http\JsonResponse;

class CarController extends Controller
{
    public function __construct(private CarService $carService) {}

    public function store(CarRequest $request): JsonResponse
    {
        $car = $this->carService->create(
            new CarMake($request->make),
            new CarModel($request->model),
            new CarPlate($request->plate_number),
        );

        return response()->json(['data' => $car], 201);
    }

    public function index(): JsonResponse
    {
        $cars = $this->carService->list();
        return response()->json(['data' => $cars]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->carService->delete($id);
        return response()->json(['message' => 'Car deleted successfully']);
    }
}
