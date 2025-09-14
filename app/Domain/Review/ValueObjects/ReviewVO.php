<?php

declare(strict_types=1);
namespace App\Domain\Review\ValueObjects;

class ReviewVO
{
    public function __construct(
        public readonly int $passengerId,
        public readonly int $driverId,
        public readonly int $rating,
        public readonly ?string $comment = null
    ) {}
}
