<?php

namespace Database\Factories;

use App\Domain\Car\Entities\Car;
use App\Domain\User\Entities\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CarFactory extends Factory
{
    protected $model = Car::class;

    public function definition(): array
    {
        return [
            'driver_id' => User::factory(),
            'brand' => $this->faker->randomElement(['Toyota', 'Honda', 'BMW', 'Mercedes', 'Audi']),
            'model' => $this->faker->word,
            'year' => $this->faker->numberBetween(2010, 2024),
            'license_plate' => $this->faker->regexify('[A-Z]{2}[0-9]{3}[A-Z]{2}'),
            'color' => $this->faker->colorName,
        ];
    }
}
