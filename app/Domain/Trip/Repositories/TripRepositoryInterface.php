<?php

namespace App\Domain\Trip\Repositories;

use App\Domain\Trip\Entities\Trip;
use App\Domain\User\Entities\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface TripRepositoryInterface
{
    public function save(Trip $trip): Trip;
    public function findById(int $id): ?Trip;
    public function getUserTrips(User $user, array $filters = []): LengthAwarePaginator;
    public function getAvailableTrips(array $filters = []): Collection;
}
