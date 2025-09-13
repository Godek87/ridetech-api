<?php

namespace App\Domain\Trip\Services;

use App\Domain\Trip\Entities\Trip;
use App\Domain\Trip\Repositories\TripRepositoryInterface;
use App\Domain\User\Entities\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class TripService
{
    private TripRepositoryInterface $repository;

    public function __construct(TripRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function createTrip(User $passenger, string $fromAddress, string $toAddress, array $preferences = []): Trip
    {
        if ($passenger->role !== 'passenger') {
            throw new \DomainException('Only passengers can create trips');
        }

        $trip = new Trip([
            'from_address' => $fromAddress,
            'to_address' => $toAddress,
            'preferences' => $preferences,
            'status' => 'pending',
            'passenger_id' => $passenger->id,
        ]);

        return $this->repository->save($trip);
    }

    public function getUserTrips(User $user, array $filters = []): LengthAwarePaginator
    {
        return $this->repository->getUserTrips($user, $filters);
    }

    public function getAvailableTrips(array $filters = []): Collection
    {
        return $this->repository->getAvailableTrips($filters);
    }

    public function acceptTrip(int $id, User $driver): Trip
    {
        $trip = $this->repository->findById($id);
        if (!$trip) {
            throw new \DomainException('Trip not found');
        }

        $trip->accept($driver);
        return $trip;
    }

    public function rejectTrip(int $id, User $driver): Trip
    {
        $trip = $this->repository->findById($id);
        if (!$trip) {
            throw new \DomainException('Trip not found');
        }

        $trip->reject();
        return $trip;
    }

    public function completeTrip(int $id, User $driver): Trip
    {
        $trip = $this->repository->findById($id);
        if (!$trip) {
            throw new \DomainException('Trip not found');
        }

        if ($trip->driver_id !== $driver->id) {
            throw new \DomainException('Unauthorized');
        }

        $trip->complete();
        return $trip;
    }

    public function updateTrip(int $id, User $user, array $data): Trip
    {
        $trip = $this->repository->findById($id);
        if (!$trip || $trip->passenger_id !== $user->id) {
            throw new \DomainException('Unauthorized or trip not found');
        }

        $trip->update($data);
        return $this->repository->save($trip);
    }

    public function cancelTrip(int $id, User $user): Trip
    {
        $trip = $this->repository->findById($id);
        if (!$trip || $trip->passenger_id !== $user->id) {
            throw new \DomainException('Unauthorized or trip not found');
        }

        $trip->cancel();
        return $trip;
    }
}
