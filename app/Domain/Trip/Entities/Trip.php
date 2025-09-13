<?php

declare(strict_types=1);

namespace App\Domain\Trip\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Database\Factories\TripFactory;

/**
 * Модель Trip — доменная сущность поездки.
 *
 * Отвечает за представление записи trips в БД, содержит константы статусов,
 * безопасные для массового заполнения поля, приведение типов и связи с другими сущностями.
 *
 * Best-practice:
 * - use HasFactory для поддержки фабрик в тестах;
 * - явно задаём $fillable и $casts;
 * - реализуем newFactory() для корректного разрешения фабрики в нестандартном namespace.
 */
final class Trip extends Model
{
    use HasFactory;

    // Название таблицы (опционально, соблюдено для явности)
    protected $table = 'trips';

    // Статусы поездки
    public const STATUS_PENDING = 'pending';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    // Поля, которые разрешено массово заполнять
    protected $fillable = [
        'passenger_id',
        'driver_id',
        'car_id',
        'from_address',
        'to_address',
        'preferences',
        'status',
        'price',
        'started_at',
        'finished_at',
        'cancelled_at',
    ];

    // Приведение типов полей
    protected $casts = [
        'preferences' => 'array',
        'price' => 'decimal:2',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /**
     * Для корректной работы фабрик, когда модель лежит в пользовательском namespace,
     * явно указываем фабрику.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return TripFactory::new();
    }

    /**
     * Связь: пассажир поездки (User).
     *
     * @return BelongsTo
     */
    public function passenger(): BelongsTo
    {
        return $this->belongsTo(\App\Domain\User\Entities\User::class, 'passenger_id');
    }

    /**
     * Связь: водитель поездки (User) — может быть null до назначения.
     *
     * @return BelongsTo
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(\App\Domain\User\Entities\User::class, 'driver_id');
    }

    /**
     * Связь: автомобиль, назначенный на поездку (Car) — может быть null.
     *
     * @return BelongsTo
     */
    public function car(): BelongsTo
    {
        return $this->belongsTo(\App\Domain\Car\Entities\Car::class, 'car_id');
    }

    /**
     * Связь: отзывы, связанные с этой поездкой.
     *
     * @return HasMany
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(\App\Domain\Review\Entities\Review::class, 'trip_id');
    }
}
