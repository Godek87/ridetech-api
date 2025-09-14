<?php

declare(strict_types=1);

namespace App\Domain\Trip\ValueObjects;

class UpdateTripVO
{
    public function __construct(
        public readonly string $pickupAddress,
        public readonly string $dropoffAddress,
        public readonly ?string $preferences,
        public readonly string $status
    ) {}
}

