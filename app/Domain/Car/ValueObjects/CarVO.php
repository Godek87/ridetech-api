<?php

declare(strict_types=1);
namespace App\Domain\Car\ValueObjects;

class CarVO
{
    public function __construct(
        public readonly int $userId,
        public readonly string $make,
        public readonly string $model,
        public readonly string $licensePlate
    ) {}
}

