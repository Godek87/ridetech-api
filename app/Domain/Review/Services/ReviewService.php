<?php
declare(strict_types=1);

namespace App\Domain\Review\Services;

use App\Domain\Review\Entities\Review;
use App\Domain\User\Entities\User;
use Illuminate\Support\Collection;

class ReviewService
{
    /**
     * Оставить отзыв водителю
     */
    public function createReview(int $userId, int $driverId, int $rating, ?string $comment): Review
    {
        $driver = User::findOrFail($driverId);

        if (!$driver->hasRole('driver')) {
            throw new \Exception('Этот пользователь не является водителем');
        }

        return Review::create([
            'user_id'   => $userId,
            'driver_id' => $driverId,
            'rating'    => $rating,
            'comment'   => $comment,
        ]);
    }

    /**
     * Получить список отзывов о водителе
     */
    public function getDriverReviews(int $driverId)
    {
        $driver = User::findOrFail($driverId);

        if (!$driver->hasRole('driver')) {
            throw new \Exception('Этот пользователь не является водителем');
        }

        return Review::with('user:id,name')
            ->where('driver_id', $driverId)
            ->latest()
            ->get();
    }

    /**
     * Посчитать средний рейтинг водителя
     */
    public function getDriverAverageRating(int $driverId): float
    {
        return (float) Review::where('driver_id', $driverId)->avg('rating');
    }
}
