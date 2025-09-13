<?php
declare(strict_types=1);

namespace App\Domain\User\ValueObjects;

/**
 * Value Object для Email.
 */
final class Email
{
    private string $value;

    /**
     * Конструктор.
     *
     * @param string $value Входной email
     * @throws \InvalidArgumentException если email некорректен
     */
    public function __construct(string $value)
    {
        $normalized = mb_strtolower(trim($value));

        if ($normalized === '') {
            throw new \InvalidArgumentException('Email is empty.');
        }

        if (!filter_var($normalized, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email format.');
        }

        $this->value = $normalized;
    }

    /**
     * Получить нормализованное значение email
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Магический метод для безопасного приведения к строке
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Сравнение двух Email VO
     */
    public function equals(Email $other): bool
    {
        return $this->value === $other->value;
    }
}
