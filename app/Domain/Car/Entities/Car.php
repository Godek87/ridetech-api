<?php
declare(strict_types=1);

namespace App\Domain\Car\Entities;
use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Database\Eloquent\Model;


class Car extends Model
{
      use HasFactory;

    protected static function newFactory()
    {
        return \Database\Factories\CarFactory::new();
    }
    protected $fillable = [
        'user_id',
        'make',
        'model',
        'plate_number',
    ];

    public function driver()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
