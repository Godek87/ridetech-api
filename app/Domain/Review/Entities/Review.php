<?php

namespace App\Domain\Review\Entities;

use App\Domain\User\Entities\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Review extends Model
{
      use HasFactory;

    protected static function newFactory()
    {
        return \Database\Factories\ReviewFactory::new();
    }
   protected $fillable = [
    'user_id',
    'driver_id',
    'rating',
    'comment'
];

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function passenger(): BelongsTo
    {
        return $this->belongsTo(User::class, 'passenger_id');
    }
}
