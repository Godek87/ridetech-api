<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Trip\Entities\Trip;
use App\Domain\User\Entities\User;
use Illuminate\Database\Eloquent\Factories\Factory;

final class TripFactory extends Factory
{
    protected $model = Trip::class;

    public function definition(): array
    {


        return [
            //'passenger_id' => User::factory()->state(['role' => 'passenger']),
            'driver_id' => null,
            'from_address' => $this->faker->address,
            'to_address' => $this->faker->address,
            'status' => 'pending',
            'price' => $this->faker->randomFloat(2, 10, 100),
        ];
    }
}
