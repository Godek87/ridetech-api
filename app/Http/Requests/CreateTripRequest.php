<?php
declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * CreateTripRequest — правила валидации создания поездки.
 *
 * Валидация минимальная: from_address, to_address required; preferences optional JSON.
 */
final class CreateTripRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Доступ только для аутентифицированных пользователей
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'from_address' => 'required|string|max:1024',
            'to_address' => 'required|string|max:1024',
            'preferences' => 'nullable|array',
            'price' => 'nullable|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'from_address.required' => 'Поле адрес отправления обязательно.',
            'to_address.required' => 'Поле адрес назначения обязательно.',
        ];
    }
}
