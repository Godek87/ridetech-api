<?php

declare(strict_types=1);

namespace App\Domain\Trip\ValueObjects;

class CreateTripVO
{
    public function __construct(
        public readonly int $passengerId,
        public readonly string $pickupAddress,
        public readonly string $dropoffAddress,
        public readonly ?string $preferences = null
    ) {}
}
