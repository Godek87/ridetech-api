<?php

namespace App\Domain\Trip\Repositories;

use App\Domain\Trip\Entities\Trip;
use App\Domain\User\Entities\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class TripRepository implements TripRepositoryInterface
{
    public function save(Trip $trip): Trip
    {
        $trip->save();
        return $trip;
    }

    public function findById(int $id): ?Trip
    {
        return Trip::find($id);
    }

    public function getUserTrips(User $user, array $filters = []): LengthAwarePaginator
    {
        $cacheKey = 'user_trips_' . $user->id . '_' . md5(json_encode($filters));
        return Cache::tags(['trips', 'user_' . $user->id])->remember($cacheKey, 3600, function () use ($user, $filters) {
            $query = Trip::where('passenger_id', $user->id)
                ->orWhere('driver_id', $user->id);

            $query->filter($filters);

            return $query->paginate(10);
        });
    }
    public function getAvailableTrips(array $filters = []): Collection
    {
        $cacheKey = 'available_trips_' . md5(json_encode($filters));
        return Cache::tags(['trips'])->remember($cacheKey, 3600, function () use ($filters) {
            $query = Trip::where('status', 'pending')
                ->whereNull('driver_id');

            $query->filter($filters);

            return $query->get();
        });
    }
}
