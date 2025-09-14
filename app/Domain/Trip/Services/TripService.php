<?php
declare(strict_types=1);



namespace App\Domain\Trip\Services;

use App\Domain\Trip\Entities\Trip;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TripService
{
    /**
     * Создать поездку (Passenger).
     */
    public function createTrip(int $userId, array $data): Trip
    {
        return Trip::create([
            'user_id' => $userId,
            'origin' => $data['origin'],
            'destination' => $data['destination'],
            'preferences' => $data['preferences'] ?? null,
            'status' => 'pending',
        ]);
    }

    /**
     * Получить список поездок пользователя.
     */
    public function getUserTrips(int $userId): Collection
    {
        return Trip::where('user_id', $userId)->get();
    }

    /**
     * Получить детали поездки.
     */
    public function getTripById(int $userId, int $id): Trip
    {
        $trip = Trip::where('user_id', $userId)->find($id);

        if (!$trip) {
            throw new ModelNotFoundException("Trip not found");
        }

        return $trip;
    }

    public function getAvailableTrips(array $filters = [])
    {
        $query = Trip::where('status', 'pending');

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (isset($filters['date'])) {
            $query->whereDate('created_at', $filters['date']);
        }
        if (isset($filters['passenger_id'])) {
            $query->where('passenger_id', $filters['passenger_id']);
        }

        return $query->get();
    }

    /**
     * Обновить поездку.
     */
    public function updateTrip(int $userId, int $id, array $data): Trip
    {
        $trip = $this->getTripById($userId, $id);
        $trip->update($data);

        return $trip;
    }

    /**
     * Отменить поездку.
     */
    public function cancelTrip(int $userId, int $id): void
    {
        $trip = $this->getTripById($userId, $id);
        $trip->update(['status' => 'cancelled']);
    }
}
