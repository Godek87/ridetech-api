<?php
declare(strict_types=1);

namespace App\Domain\User\ValueObjects;

use Illuminate\Support\Facades\Hash;

/**
 * Value Object для пароля.
 */
final class Password
{
    private string $plainValue;
    private ?string $hashedValue = null;

    /**
     * Конструктор.
     *
     * @param string $plainValue Plain-текст пароля
     * @throws \InvalidArgumentException если пароль слишком короткий
     */
    public function __construct(string $plainValue)
    {
        if (mb_strlen($plainValue) < 8) {
            throw new \InvalidArgumentException('Password must be at least 8 characters long.');
        }

        $this->plainValue = $plainValue;
    }

    /**
     * Возвращает plain-текст пароля (для сервисов проверки, никогда не для БД!)
     */
    public function getPlainValue(): string
    {
        return $this->plainValue;
    }

    /**
     * Возвращает захешированный пароль для сохранения в БД.
     */
    public function getHashedValue(): string
    {
        if ($this->hashedValue === null) {
            $this->hashedValue = Hash::make($this->plainValue);
        }

        return $this->hashedValue;
    }

    /**
     * Проверяет соответствие plain-пароля хешу из БД.
     */
    public function verifyAgainstHash(string $hash): bool
    {
        return Hash::check($this->plainValue, $hash);
    }

    /**
     * Не позволяем сериализовать plain-пароль.
     */
    public function __serialize(): array
    {
        return [];
    }

    public function __unserialize(array $data): void
    {
        // intentionally empty
    }

    /**
     * Для удобства безопасного преобразования в строку — возвращаем захешированное значение.
     */
    public function __toString(): string
    {
        return $this->getHashedValue();
    }
}
