<?php

namespace App\Domain\Car\Repositories;

use App\Domain\Car\Entities\Car;
use App\Domain\User\Entities\User;
use Illuminate\Support\Collection;

class CarRepository implements CarRepositoryInterface
{
    public function save(Car $car): Car
    {
        $car->save();
        return $car;
    }

    public function findById(int $id): ?Car
    {
        return Car::find($id);
    }

    public function getByDriver(User $driver): Collection
    {
        return Car::where('driver_id', $driver->id)->get();
    }

    public function delete(Car $car): void
    {
        $car->delete();
    }
}
