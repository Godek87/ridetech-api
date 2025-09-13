<?php

namespace App\Domain\Car\Repositories;

use App\Domain\Car\Entities\Car;
use App\Domain\User\Entities\User;
use Illuminate\Support\Collection;

interface CarRepositoryInterface
{
    public function save(Car $car): Car;
    public function findById(int $id): ?Car;
    public function getByDriver(User $driver): Collection;
    public function delete(Car $car): void;
}
