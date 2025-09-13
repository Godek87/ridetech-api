<?php
declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * RegisterRequest — класс валидации входящего запроса на регистрацию пользователя.
 *
 * Ответственность:
 * - определяет правила валидации для полей регистрации (name, email, phone, password, role);
 * - выполняет предварительную нормализацию входных данных (prepareForValidation),
 *   чтобы правила (например unique / regex) проверялись на стандартизованных значениях;
 * - может содержать кастомные сообщения и атрибуты для более понятных ошибок.
 */
class RegisterRequest extends FormRequest
{
    /**
     * authorize — определяет, разрешён ли этот запрос для выполнения.
     *
     * Обычно возвращаем true, если доступ не ограничен (напр., регистрация открыта).
     * Для сложных кейсов можно внедрять проверки прав/фич-флагов.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * prepareForValidation — выполняет нормализацию/санитизацию входных данных
     * до применения правил валидации.
     *
     * Что делает здесь:
     * - обрезает пробелы и приводит email к нижнему регистру;
     * - удаляет форматирующие символы из телефона, преобразует ведущие "00" в "+";
     * - это важно, чтобы unique:users,email и regex для телефона были применены к нормализованным значениям.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        $input = $this->all();

        // Нормализация email: trim + lowercase
        if (isset($input['email'])) {
            $input['email'] = mb_strtolower(trim((string) $input['email']));
        }

        // Нормализация phone: убрать пробелы/дефисы/скобки/точки, преобразовать ведущие 00 в +
        if (isset($input['phone'])) {
            $phone = trim((string) $input['phone']);
            // Удаляем форматирующие символы
            $phone = (string) preg_replace('/[ \-\(\)\.]/', '', $phone);
            // Преобразуем "00..." в "+..."
            if (strpos($phone, '00') === 0) {
                $phone = '+' . substr($phone, 2);
            }
            $input['phone'] = $phone;
        }

        $this->merge($input);
    }

    /**
     * rules — возвращает массив правил валидации для запроса регистрации.
     *
     * Правила включают:
     * - name: обязательное строковое поле, ограничение длины;
     * - email: обязательный, корректный формат, уникальность в таблице users;
     * - phone: обязательный, уникальный и соответствует E.164-подобному regex;
     * - password: обязательный, минимальная длина (более строгие требования можно вынести в валидатор);
     * - role: обязательное значение из списка допустимых ролей.
     *
     * Примечания:
     * - Можно дополнить email правилом email:rfc,dns при желании дополнительной проверки DNS.
     * - Уникальность проверяется уже на нормализованном email/phone (prepareForValidation).
     *
     * @return array<string,mixed>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'required|string|max:255|unique:users,phone',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|string|in:passenger,driver',
        ];
    }

    /**
     * messages — (опционально) возвращает массив кастомных сообщений ошибок валидации.
     *
     * Здесь можно добавить локализованные и понятные сообщения для клиента.
     *
     * @return array<string,string>
     */
    public function messages(): array
    {
        return [
            'email.unique' => 'Пользователь с таким email уже зарегистрирован.',
            'phone.unique' => 'Пользователь с таким номером телефона уже зарегистрирован.',
            'phone.regex' => 'Номер телефона должен быть в международном формате (E.164).',
            'password.min' => 'Пароль должен содержать не менее 8 символов.',
            'role.in' => 'Роль должна быть либо "passenger", либо "driver".',
        ];
    }
}
