<?php

namespace App\Domain\Review\Repositories;

use App\Domain\Review\Entities\Review;
use App\Domain\User\Entities\User;
use Illuminate\Support\Collection;

interface ReviewRepositoryInterface
{
    public function save(Review $review): Review;
    public function getDriverReviews(User $driver): Collection;
}
