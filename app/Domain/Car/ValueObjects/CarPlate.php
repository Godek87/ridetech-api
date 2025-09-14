<?php
declare(strict_types=1);

namespace App\Domain\Car\ValueObjects;

class CarPlate
{
    public function __construct(
        public readonly string $value
    ) {
        if (!preg_match('/^[A-Z0-9-]+$/i', $value)) {
            throw new \InvalidArgumentException("Invalid plate number");
        }
    }
}
