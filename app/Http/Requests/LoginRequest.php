<?php
declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * LoginRequest — класс валидации входящего запроса на аутентификацию (login).
 *
 * Ответственность:
 * - определяет правила валидации для полей входа (email, password);
 * - выполняет предварительную нормализацию входных данных (prepareForValidation),
 *   чтобы правила и дальнейшая логика работали с предсказуемым форматом (например, email в нижнем регистре);
 * - содержит кастомные сообщения ошибок валидации для удобного отображения клиенту.
 */
final class LoginRequest extends FormRequest
{
    /**
     * authorize — разрешён ли этот запрос.
     *
     * Для публичного входа обычно возвращаем true. Здесь можно реализовать
     * дополнительные проверки (feature flags, блокировки по IP и т.п.).
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * prepareForValidation — нормализация входа до применения правил.
     *
     * Нормализуем email: trim + lowercase. Это гарантирует, что сравнение/поиск
     * по email будет выполнено на стандартизированном значении.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        $input = $this->all();

        if (isset($input['email'])) {
            $input['email'] = mb_strtolower(trim((string) $input['email']));
        }

        $this->merge($input);
    }

    /**
     * rules — правила валидации запроса.
     *
     * Описание правил:
     * - email: обязателен, строка корректного формата почты;
     * - password: обязателен, строка, минимальная длина 8 символов (соответствует правилам регистрации).
     *
     * Возвращаем явный тип array для статической проверки.
     *
     * @return array<string,mixed>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|string|email|max:255',
            // Минимальная длина пароля соответствует RegisterRequest (для консистентности).
            'password' => 'required|string|min:8',
        ];
    }

    /**
     * messages — кастомные сообщения об ошибках валидации.
     *
     * Полезно для локализации и понятных ответов клиенту.
     *
     * @return array<string,string>
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Поле email обязательно.',
            'email.email' => 'Неверный формат email.',
            'email.max' => 'Email слишком длинный.',
            'password.required' => 'Поле пароль обязательно.',
            'password.min' => 'Пароль должен содержать не менее 8 символов.',
        ];
    }
}
