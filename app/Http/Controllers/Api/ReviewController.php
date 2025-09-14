<?php
declare(strict_types=1);
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Domain\Review\Entities\Review;
use App\Domain\Review\Services\ReviewService;
use App\Domain\User\Entities\User;
use Illuminate\Http\Request;


class ReviewController extends Controller
{
    protected ReviewService $reviewService;

    public function __construct(ReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }
    public function store(Request $request, $driverId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        try {
            $review = $this->reviewService->createReview(
                auth()->id(),
                (int) $driverId,
                $request->rating,
                $request->comment
            );

            return response()->json($review, 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function index($driverId)
    {
        try {
            $reviews = $this->reviewService->getDriverReviews((int) $driverId);
            $average = $this->reviewService->getDriverAverageRating((int) $driverId);

            return response()->json([
                'average_rating' => $average,
                'reviews' => $reviews,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
