<?php
declare(strict_types=1);
namespace App\Domain\Car\ValueObjects;

class CarMake
{
    public function __construct(
        public readonly string $value
    ) {
        if (empty($value)) {
            throw new \InvalidArgumentException("Car make cannot be empty");
        }
    }
}
