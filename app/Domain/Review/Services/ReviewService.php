<?php

namespace App\Domain\Review\Services;

use App\Domain\Review\Entities\Review;
use App\Domain\Review\Repositories\ReviewRepositoryInterface;
use App\Domain\User\Entities\User;
use Illuminate\Support\Collection;

class ReviewService
{
    private ReviewRepositoryInterface $repository;

    public function __construct(ReviewRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function createReview(User $passenger, User $driver, int $rating, ?string $comment): Review
    {
        if ($passenger->role !== 'passenger') {
            throw new \DomainException('Only passengers can leave reviews');
        }

        $review = new Review([
            'rating' => $rating,
            'comment' => $comment,
            'passenger_id' => $passenger->id,
            'driver_id' => $driver->id,
        ]);

        return $this->repository->save($review);
    }

    public function getDriverReviews(User $driver): Collection
    {
        return $this->repository->getDriverReviews($driver);
    }
}
