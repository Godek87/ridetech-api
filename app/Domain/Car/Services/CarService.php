<?php
declare(strict_types=1);

namespace App\Domain\Car\Services;

use App\Domain\Car\Entities\Car;
use App\Domain\Car\ValueObjects\CarMake;
use App\Domain\Car\ValueObjects\CarModel;
use App\Domain\Car\ValueObjects\CarPlate;
use Illuminate\Support\Facades\Auth;

class CarService
{
    public function create(CarMake $make, CarModel $model, CarPlate $plate): Car
    {
        return Car::create([
            'user_id' => Auth::id(),
            'make' => $make->value,
            'model' => $model->value,
            'plate_number' => $plate->value,
        ]);
    }

    public function list(): \Illuminate\Database\Eloquent\Collection
    {
        return Car::where('user_id', Auth::id())->get();
    }

    public function delete(int $id): bool
    {
        $car = Car::where('user_id', Auth::id())->findOrFail($id);
        return $car->delete();
    }
}
