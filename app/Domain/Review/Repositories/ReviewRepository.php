<?php

namespace App\Domain\Review\Repositories;

use App\Domain\Review\Entities\Review;
use App\Domain\User\Entities\User;
use Illuminate\Support\Collection;

class ReviewRepository implements ReviewRepositoryInterface
{
    public function save(Review $review): Review
    {
        $review->save();
        return $review;
    }

    public function getDriverReviews(User $driver): Collection
    {
        return Review::where('driver_id', $driver->id)->get();
    }
}
