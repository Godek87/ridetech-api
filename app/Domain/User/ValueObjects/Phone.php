<?php
declare(strict_types=1);

namespace App\Domain\User\ValueObjects;

/**
 * Value Object для номера телефона.
 *
 * Отвечает за:
 * - валидацию формата номера при создании;
 * - нормализацию (например, удаление пробелов и дефисов);
 * - предоставление значения для сохранения (getValue / __toString);
 * - сравнение двух Phone VO (equals).
 */
final class Phone
{
    private string $value;

    /**
     * Конструктор.
     *
     * @param string $value Входной номер телефона
     * @throws \InvalidArgumentException если формат некорректен
     */
    public function __construct(string $value)
    {
        // Нормализация: удаляем пробелы, дефисы, скобки
        $normalized = preg_replace('/[\s\-\(\)]/', '', trim($value));

        if ($normalized === '') {
            throw new \InvalidArgumentException('Phone number is empty.');
        }

        // Простая проверка: должен содержать только цифры и + в начале
        if (!preg_match('/^\+?\d+$/', $normalized)) {
            throw new \InvalidArgumentException('Invalid phone number format.');
        }

        $this->value = $normalized;
    }

    /**
     * Получить нормализованное значение телефона
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
     * Сравнение двух Phone VO
     */
    public function equals(Phone $other): bool
    {
        return $this->value === $other->value;
    }
}
