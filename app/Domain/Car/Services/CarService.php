<?php

namespace App\Domain\Car\Services;

use App\Domain\Car\Entities\Car;
use App\Domain\Car\Repositories\CarRepositoryInterface;
use App\Domain\User\Entities\User;
use Illuminate\Support\Collection;

class CarService
{
    private CarRepositoryInterface $repository;

    public function __construct(CarRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function createCar(User $driver, string $make, string $model, string $licensePlate): Car
    {
        if ($driver->role !== 'driver') {
            throw new \DomainException('Only drivers can add cars');
        }

        $car = new Car([
            'make' => $make,
            'model' => $model,
            'license_plate' => $licensePlate,
            'driver_id' => $driver->id,
        ]);

        return $this->repository->save($car);
    }

    public function getDriverCars(User $driver): Collection
    {
        return $this->repository->getDriverCars($driver);
    }

    public function updateCar(Car $car, User $driver, array $data): Car
    {
        if ($car->driver_id !== $driver->id || !$car->canBeUpdated()) {
            throw new \DomainException('Cannot update this car');
        }

        $car->update($data);
        return $this->repository->save($car);
    }

    public function deleteCar(Car $car, User $driver): void
    {
        if ($car->driver_id !== $driver->id) {
            throw new \DomainException('Unauthorized');
        }

        $this->repository->delete($car);
    }
}
