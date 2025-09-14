<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Domain\Trip\Services\TripService;
use App\Domain\Trip\Entities\Trip;
use App\Events\TripStatusUpdated;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Http\Request;

namespace App\Http\Controllers\Api;

use App\Domain\Trip\Services\TripService;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTripRequest;
use App\Http\Requests\UpdateTripRequest;
use App\Http\Resources\TripResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class TripController extends Controller
{
    private TripService $tripService;

    public function __construct(TripService $tripService)
    {
        $this->tripService = $tripService;
    }

    /**
     * Создать поездку (только пассажир).
     */
    public function store(CreateTripRequest $request): JsonResponse
    {
        try {
            $trip = $this->tripService->createTrip(Auth::id(), $request->validated());

            return (new TripResource($trip))
                ->response()
                ->setStatusCode(Response::HTTP_CREATED);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Server error', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Список поездок пользователя.
     */
    public function index(): JsonResponse
    {
        try {
            $trips = $this->tripService->getUserTrips(Auth::id());

            return TripResource::collection($trips)
                ->response()
                ->setStatusCode(Response::HTTP_OK);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Server error'], 500);
        }
    }

    /**
     * Детали поездки.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $trip = $this->tripService->getTripById(Auth::id(), $id);

            return (new TripResource($trip))
                ->response()
                ->setStatusCode(Response::HTTP_OK);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Server error'], 500);
        }
    }
    /**
     * available.
     */
      public function available(Request $request)
    {
    $filters = $request->only(['status', 'date', 'passenger_id']);
    $trips = $this->tripService->getAvailableTrips($filters);
     return response()->json($trips);
    }
    /**
     * test brodcast.
     */
    public function testBroadcast($id)
    {
        $trip = Trip::findOrFail($id);
        broadcast(new TripStatusUpdated($trip));
        return response()->json(['message' => 'Event broadcasted']);
    }
    /**
     * Обновить поездку.
     */
    public function update(UpdateTripRequest $request, int $id): JsonResponse
    {
        try {
            $trip = $this->tripService->updateTrip(Auth::id(), $id, $request->validated());

            return (new TripResource($trip))
                ->response()
                ->setStatusCode(Response::HTTP_OK);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Server error'], 500);
        }
    }

    /**
     * Отменить поездку.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->tripService->cancelTrip(Auth::id(), $id);

            return response()->json(['message' => 'Trip cancelled'], Response::HTTP_NO_CONTENT);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Server error'], 500);
        }
    }


}
