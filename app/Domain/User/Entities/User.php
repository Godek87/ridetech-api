<?php
declare(strict_types=1);

namespace App\Domain\User\Entities;

use App\Domain\User\ValueObjects\Email;
use App\Domain\User\ValueObjects\Phone;
use App\Domain\User\ValueObjects\Password;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Database\Factories\UserFactory;

/**
 * Модель User (Eloquent) — доменная сущность пользователя.
 *
 * Отвечает за:
 * - структуру полей модели (mass assignment, скрытые поля, касты);
 * - поведение сущности (утилитарные методы вроде isPassenger/isDriver);
 * - фабричный метод createFromData для создания модели из value objects.
 */
class User extends Authenticatable

{
    use HasFactory;
    use HasApiTokens;

    protected static function newFactory()
{
    return \Database\Factories\UserFactory::new();
}

    protected  $fillable = [
        'name', 'email', 'phone', 'password', 'role',
    ];


    protected  $hidden = [
        'password', 'remember_token',
    ];

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    protected  $casts = [
        // пример: 'email_verified_at' => 'datetime',
    ];

    /**
     * Мутатор для пароля.
     *
     * Принимает:
     * - объект Password (value-object) — тогда используется getHashedValue();
     * - строку (plain-text или уже захешированную) — если строка выглядит как хеш,
     *   сохраняется как есть, иначе хешируется через Hash::make().
     *
     * Это защищает от двойного хеширования и обеспечивает, что пароль всегда в безопасном виде.
     *
     * @param Password|string $value
     * @return void
     */
    public function setPasswordAttribute($value): void
    {
        // Если передан VO Password — используем явно предоставленный хеш-метод VO.
        if ($value instanceof Password) {
            $this->attributes['password'] = $value->getHashedValue();
            return;
        }

        $value = (string) $value;

        // Простейшая проверка на строку-хеш (bcrypt / argon2 / 2y / 2a)
        // Если строка похожа на хеш — предполагаем, что уже захеширована.
        if (preg_match('/^\$(2y|2a|argon2i|argon2id)\$/', $value) === 1) {
            $this->attributes['password'] = $value;
            return;
        }

        // Иначе хешируем plain-text пароль.
        $this->attributes['password'] = Hash::make($value);
    }

    /**
     * Фабричный метод для создания и сохранения пользователя из value-objects.
     *
     * Объяснение работы:
     * - Принимает примитивное имя и value-objects для email, phone и password.
     * - Делает create(...) — при этом сработает мутатор setPasswordAttribute,
     *   если передать plain-text или Password-VO.
     * - Возвращает сохранённую модель User.
     * - В доменной модели фабрика удобна для инкапсуляции создания сущности.
     *   При желании можно перенести сохранение в репозиторий, чтобы модель не знала о персистенции.
     *
     * @param string   $name
     * @param Email    $email
     * @param Phone    $phone
     * @param Password $password
     * @param string   $role
     * @return self
     */
    public static function createFromData(string $name, Email $email, Phone $phone, Password $password, string $role): self
    {
        // Передаём Password-VO напрямую — мутатор модели обработает получение хеша.
        return self::create([
            'name' => $name,
            'email' => $email->getValue(),
            'phone' => $phone->getValue(),
            'password' => $password,
            'role' => $role,
        ]);
    }



    /**
     * Возвращает true, если пользователь имеет роль "passenger".
     *
     * Используется для проверки прав/поведения в домене.
     *
     * @return bool
     */
    public function isPassenger(): bool
    {
        return $this->role === 'passenger';
    }

    /**
     * Возвращает true, если пользователь имеет роль "driver".
     *
     * @return bool
     */
    public function isDriver(): bool
    {
        return $this->role === 'driver';
    }
}
