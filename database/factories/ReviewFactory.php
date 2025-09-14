<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Domain\Review\Entities\Review>
 */



use App\Domain\Review\Entities\Review;
use App\Domain\User\Entities\User;


class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'driver_id' => User::factory()->state(['role' => 'driver']),
            'rating' => $this->faker->numberBetween(1, 5),
            'comment' => $this->faker->optional()->sentence(),
        ];
    }
}
