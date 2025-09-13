<?php

namespace App\Domain\Car\Entities;

use App\Domain\User\Entities\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Car extends Model
{
    protected $fillable = [
        'make',
        'model',
        'license_plate',
        'driver_id',
    ];

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    // Бизнес-логика DDD
    public function canBeUpdated(): bool
    {
        // Можно добавить логику, если нужно (например, если машина использовалась в поездках)
        return true;
    }
}
