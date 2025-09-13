<?php
declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * UpdateTripRequest — правила для обновления поездки.
 *
 * Для пассажира: обновление адресов/preferences пока в status = pending.
 * Для водителя: действие action = accept|reject|complete.
 */
final class UpdateTripRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'from_address' => 'sometimes|string|max:1024',
            'to_address' => 'sometimes|string|max:1024',
            'preferences' => 'nullable|array',
            'action' => 'sometimes|string|in:accept,reject,complete',
        ];
    }

    public function messages(): array
    {
        return [
            'action.in' => 'Action должен быть одним из: accept, reject, complete.',
        ];
    }
}
