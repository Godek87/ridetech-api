<?php

namespace App\Http\Controllers\Api;

use App\Domain\Car\Services\CarService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CarController extends Controller
{
    private CarService $carService;

    public function __construct(CarService $carService)
    {
        $this->carService = $carService;
    }

    /**
     * @OA\Post(
     *     path="/api/cars",
     *     tags={"Cars"},
     *     summary="Create a new car",
     *     description="Creates a new car for the authenticated driver",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"make", "model", "license_plate"},
     *             @OA\Property(property="make", type="string", example="Toyota", maxLength=255),
     *             @OA\Property(property="model", type="string", example="Camry", maxLength=255),
     *             @OA\Property(property="license_plate", type="string", example="ABC123", description="Must be unique")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Car created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="car", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="make", type="string", example="Toyota"),
     *                 @OA\Property(property="model", type="string", example="Camry"),
     *                 @OA\Property(property="license_plate", type="string", example="ABC123"),
     *                 @OA\Property(property="driver_id", type="integer", example=2)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Invalid input data",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The license_plate has already been taken")
     *         )
     *     ),
     *     @OA\Response(response=500, description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Server error")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'make' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'license_plate' => 'required|string|unique:cars,license_plate',
        ]);

        $car = $this->carService->createCar(auth()->user(), $validated['make'], $validated['model'], $validated['license_plate']);

        return response()->json(['car' => $car], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/cars",
     *     tags={"Cars"},
     *     summary="Get driver cars",
     *     description="Retrieves a list of cars belonging to the authenticated driver",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of cars",
     *         @OA\JsonContent(
     *             @OA\Property(property="cars", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="make", type="string", example="Toyota"),
     *                     @OA\Property(property="model", type="string", example="Camry"),
     *                     @OA\Property(property="license_plate", type="string", example="ABC123"),
     *                     @OA\Property(property="driver_id", type="integer", example=2)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *     @OA\Response(response=500, description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Server error")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $cars = $this->carService->getDriverCars(auth()->user());

        return response()->json(['cars' => $cars]);
    }

    /**
     * @OA\Delete(
     *     path="/api/cars/{id}",
     *     tags={"Cars"},
     *     summary="Delete a car",
     *     description="Deletes a specific car if it belongs to the authenticated driver",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Car deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Car deleted")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Car not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Car not found")
     *         )
     *     ),
     *     @OA\Response(response=500, description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Server error")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $car = $this->carService->getDriverCars(auth()->user())->where('id', $id)->first();
        if (!$car) {
            return response()->json(['message' => 'Car not found'], 404);
        }

        $this->carService->deleteCar($car, auth()->user());

        return response()->json(['message' => 'Car deleted']);
    }
}
