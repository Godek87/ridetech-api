<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Domain\Trip\Services\TripService;
use App\Domain\Trip\Entities\Trip;
use App\Events\TripStatusUpdated;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Http\Request;

class TripController extends Controller
{
    public function testBroadcast($id)
    {
        $trip = Trip::findOrFail($id);
        broadcast(new TripStatusUpdated($trip));
        return response()->json(['message' => 'Event broadcasted']);
    }
    private TripService $tripService;

    public function __construct(TripService $tripService)
    {
        $this->tripService = $tripService;
    }

    /**
     * @OA\Post(
     *     path="/api/trips",
     *     tags={"Trips"},
     *     summary="Create a new trip",
     *     description="Creates a new trip for the authenticated user",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"from_address", "to_address"},
     *             @OA\Property(property="from_address", type="string", example="Moscow, Red Square", maxLength=255),
     *             @OA\Property(property="to_address", type="string", example="St. Petersburg, Nevsky Prospect", maxLength=255),
     *             @OA\Property(property="preferences", type="array", nullable=true,
     *                 @OA\Items(type="string", example="non-smoking")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Trip created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="trip", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="from_address", type="string", example="Moscow, Red Square"),
     *                 @OA\Property(property="to_address", type="string", example="St. Petersburg, Nevsky Prospect"),
     *                 @OA\Property(property="status", type="string", example="pending"),
     *                 @OA\Property(property="passenger_id", type="integer", example=1),
     *                 @OA\Property(property="preferences", type="array", @OA\Items(type="string", example="non-smoking"))
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
     *             @OA\Property(property="message", type="string", example="The from_address field is required")
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
            'from_address' => 'required|string|max:255',
            'to_address' => 'required|string|max:255',
            'preferences' => 'nullable|array',
        ]);

        $trip = $this->tripService->createTrip(auth()->user(), $validated['from_address'], $validated['to_address'], $validated['preferences'] ?? []);

        return response()->json(['trip' => $trip], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/trips",
     *     tags={"Trips"},
     *     summary="Get user trips",
     *     description="Retrieves a list of trips for the authenticated user with optional filters",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", example="pending")
     *     ),
     *     @OA\Parameter(
     *         name="date",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", format="date", example="2025-09-13")
     *     ),
     *     @OA\Parameter(
     *         name="passenger_id",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="driver_id",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer", example=2)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of trips",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="from_address", type="string", example="Moscow, Red Square"),
     *                 @OA\Property(property="to_address", type="string", example="St. Petersburg, Nevsky Prospect"),
     *                 @OA\Property(property="status", type="string", example="pending"),
     *                 @OA\Property(property="passenger_id", type="integer", example=1),
     *                 @OA\Property(property="preferences", type="array", @OA\Items(type="string", example="non-smoking"))
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
    public function index(Request $request)
    {
        $filters = $request->only(['status', 'date', 'passenger_id', 'driver_id']);
        $trips = $this->tripService->getUserTrips(auth()->user(), $filters);

        return response()->json($trips);
    }

    /**
     * @OA\Get(
     *     path="/api/trips/{id}",
     *     tags={"Trips"},
     *     summary="Get a specific trip",
     *     description="Retrieves a specific trip by ID if the user is authorized (passenger or driver)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Trip details",
     *         @OA\JsonContent(
     *             @OA\Property(property="trip", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="from_address", type="string", example="Moscow, Red Square"),
     *                 @OA\Property(property="to_address", type="string", example="St. Petersburg, Nevsky Prospect"),
     *                 @OA\Property(property="status", type="string", example="pending"),
     *                 @OA\Property(property="passenger_id", type="integer", example=1),
     *                 @OA\Property(property="preferences", type="array", @OA\Items(type="string", example="non-smoking"))
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *     @OA\Response(response=403, description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Trip not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Trip not found")
     *         )
     *     ),
     *     @OA\Response(response=500, description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Server error")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $trip = $this->tripService->findById($id); // Добавь метод findById в service если нужно
        if (!$trip) {
            return response()->json(['message' => 'Trip not found'], 404);
        }

        // Проверка доступа
        if ($trip->passenger_id !== auth()->id() && $trip->driver_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json(['trip' => $trip]);
    }

    /**
     * @OA\Put(
     *     path="/api/trips/{id}",
     *     tags={"Trips"},
     *     summary="Update a trip",
     *     description="Updates a specific trip if the user is authorized",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="from_address", type="string", example="Moscow, Red Square", maxLength=255),
     *             @OA\Property(property="to_address", type="string", example="St. Petersburg, Nevsky Prospect", maxLength=255),
     *             @OA\Property(property="preferences", type="array", nullable=true,
     *                 @OA\Items(type="string", example="non-smoking")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Trip updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="trip", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="from_address", type="string", example="Moscow, Red Square"),
     *                 @OA\Property(property="to_address", type="string", example="St. Petersburg, Nevsky Prospect"),
     *                 @OA\Property(property="status", type="string", example="pending"),
     *                 @OA\Property(property="passenger_id", type="integer", example=1),
     *                 @OA\Property(property="preferences", type="array", @OA\Items(type="string", example="non-smoking"))
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *     @OA\Response(response=403, description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Trip not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Trip not found")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Invalid input data",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The from_address field is required")
     *         )
     *     ),
     *     @OA\Response(response=500, description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Server error")
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'from_address' => 'sometimes|required|string|max:255',
            'to_address' => 'sometimes|required|string|max:255',
            'preferences' => 'nullable|array',
        ]);

        $trip = $this->tripService->updateTrip($id, auth()->user(), $validated);

        return response()->json(['trip' => $trip]);
    }

    /**
     * @OA\Delete(
     *     path="/api/trips/{id}",
     *     tags={"Trips"},
     *     summary="Cancel a trip",
     *     description="Cancels a specific trip if the user is authorized",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Trip cancelled successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Trip cancelled")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *     @OA\Response(response=403, description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Trip not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Trip not found")
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
        $trip = $this->tripService->cancelTrip($id, auth()->user());

        return response()->json(['message' => 'Trip cancelled']);
    }

    /**
     * @OA\Get(
     *     path="/api/trips/available",
     *     tags={"Trips"},
     *     summary="Get available trips",
     *     description="Retrieves a list of available trips with optional filters",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", example="pending")
     *     ),
     *     @OA\Parameter(
     *         name="date",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", format="date", example="2025-09-13")
     *     ),
     *     @OA\Parameter(
     *         name="passenger_id",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of available trips",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="from_address", type="string", example="Moscow, Red Square"),
     *                 @OA\Property(property="to_address", type="string", example="St. Petersburg, Nevsky Prospect"),
     *                 @OA\Property(property="status", type="string", example="pending"),
     *                 @OA\Property(property="passenger_id", type="integer", example=1),
     *                 @OA\Property(property="preferences", type="array", @OA\Items(type="string", example="non-smoking"))
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
    public function available(Request $request)
    {
        $filters = $request->only(['status', 'date', 'passenger_id']);
        $trips = $this->tripService->getAvailableTrips($filters);

        return response()->json($trips);
    }

    /**
     * @OA\Post(
     *     path="/api/trips/{id}/accept",
     *     tags={"Trips"},
     *     summary="Accept a trip",
     *     description="Allows an authorized driver to accept a specific trip",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Trip accepted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Trip accepted"),
     *             @OA\Property(property="trip", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="from_address", type="string", example="Moscow, Red Square"),
     *                 @OA\Property(property="to_address", type="string", example="St. Petersburg, Nevsky Prospect"),
     *                 @OA\Property(property="status", type="string", example="accepted"),
     *                 @OA\Property(property="passenger_id", type="integer", example=1),
     *                 @OA\Property(property="driver_id", type="integer", example=2),
     *                 @OA\Property(property="preferences", type="array", @OA\Items(type="string", example="non-smoking"))
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *     @OA\Response(response=403, description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Trip not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Trip not found")
     *         )
     *     ),
     *     @OA\Response(response=500, description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Server error")
     *         )
     *     )
     * )
     */
    public function accept($id)
    {
        $trip = $this->tripService->acceptTrip($id, auth()->user());

        return response()->json(['message' => 'Trip accepted', 'trip' => $trip]);
    }

    /**
     * @OA\Post(
     *     path="/api/trips/{id}/reject",
     *     tags={"Trips"},
     *     summary="Reject a trip",
     *     description="Allows an authorized driver to reject a specific trip",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Trip rejected successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Trip rejected"),
     *             @OA\Property(property="trip", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="from_address", type="string", example="Moscow, Red Square"),
     *                 @OA\Property(property="to_address", type="string", example="St. Petersburg, Nevsky Prospect"),
     *                 @OA\Property(property="status", type="string", example="rejected"),
     *                 @OA\Property(property="passenger_id", type="integer", example=1),
     *                 @OA\Property(property="preferences", type="array", @OA\Items(type="string", example="non-smoking"))
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *     @OA\Response(response=403, description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Trip not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Trip not found")
     *         )
     *     ),
     *     @OA\Response(response=500, description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Server error")
     *         )
     *     )
     * )
     */
    public function reject($id)
    {
        $trip = $this->tripService->rejectTrip($id, auth()->user());

        return response()->json(['message' => 'Trip rejected', 'trip' => $trip]);
    }

    /**
     * @OA\Post(
     *     path="/api/trips/{id}/complete",
     *     tags={"Trips"},
     *     summary="Complete a trip",
     *     description="Marks a specific trip as completed if the user is authorized",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Trip completed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Trip completed"),
     *             @OA\Property(property="trip", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="from_address", type="string", example="Moscow, Red Square"),
     *                 @OA\Property(property="to_address", type="string", example="St. Petersburg, Nevsky Prospect"),
     *                 @OA\Property(property="status", type="string", example="completed"),
     *                 @OA\Property(property="passenger_id", type="integer", example=1),
     *                 @OA\Property(property="driver_id", type="integer", example=2),
     *                 @OA\Property(property="preferences", type="array", @OA\Items(type="string", example="non-smoking"))
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *     @OA\Response(response=403, description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Trip not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Trip not found")
     *         )
     *     ),
     *     @OA\Response(response=500, description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Server error")
     *         )
     *     )
     * )
     */
    public function complete($id)
    {
        $trip = $this->tripService->completeTrip($id, auth()->user());

        return response()->json(['message' => 'Trip completed', 'trip' => $trip]);
    }


}
