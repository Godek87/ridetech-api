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
        $passenger = User::factory()->create(['role' => 'passenger']);
        return [
            'passenger_id' => $passenger->id,
            'from_address' => $this->faker->address,
            'to_address' => $this->faker->address,
            'preferences' => null,
            'status' => Trip::STATUS_PENDING,
            'price' => null,
        ];
    }
}
