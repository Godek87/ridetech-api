<?php

namespace App\Http\Controllers\Api;

use App\Domain\Review\Services\ReviewService;
use App\Domain\User\Entities\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    private ReviewService $reviewService;

    public function __construct(ReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }

    /**
     * @OA\Post(
     *     path="/api/drivers/{driverId}/reviews",
     *     tags={"Reviews"},
     *     summary="Create a review for a driver",
     *     description="Creates a new review for the specified driver by the authenticated user",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="driverId",
     *         in="path",
     *         required=true,
     *         description="ID of the driver being reviewed",
     *         @OA\Schema(type="integer", example=2)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"rating"},
     *             @OA\Property(property="rating", type="integer", minimum=1, maximum=5, example=5),
     *             @OA\Property(property="comment", type="string", nullable=true, example="Great driver!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Review created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="review", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="rating", type="integer", example=5),
     *                 @OA\Property(property="comment", type="string", nullable=true, example="Great driver!"),
     *                 @OA\Property(property="driver_id", type="integer", example=2),
     *                 @OA\Property(property="reviewer_id", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Driver not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Driver not found")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Invalid input data",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The rating field is required")
     *         )
     *     ),
     *     @OA\Response(response=500, description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Server error")
     *         )
     *     )
     * )
     */
    public function store(Request $request, $driverId)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $driver = User::findOrFail($driverId);

        $review = $this->reviewService->createReview(auth()->user(), $driver, $validated['rating'], $validated['comment'] ?? null);

        return response()->json(['review' => $review], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/drivers/{driverId}/reviews",
     *     tags={"Reviews"},
     *     summary="Get reviews for a driver",
     *     description="Retrieves a list of reviews for the specified driver",
     *     @OA\Parameter(
     *         name="driverId",
     *         in="path",
     *         required=true,
     *         description="ID of the driver whose reviews are being retrieved",
     *         @OA\Schema(type="integer", example=2)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of reviews",
     *         @OA\JsonContent(
     *             @OA\Property(property="reviews", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="rating", type="integer", example=5),
     *                     @OA\Property(property="comment", type="string", nullable=true, example="Great driver!"),
     *                     @OA\Property(property="driver_id", type="integer", example=2),
     *                     @OA\Property(property="reviewer_id", type="integer", example=1)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Driver not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Driver not found")
     *         )
     *     ),
     *     @OA\Response(response=500, description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Server error")
     *         )
     *     )
     * )
     */
    public function index($driverId)
    {
        $driver = User::findOrFail($driverId);

        $reviews = $this->reviewService->getDriverReviews($driver);

        return response()->json(['reviews' => $reviews]);
    }
}
