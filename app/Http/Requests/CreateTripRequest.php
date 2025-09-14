<?php
declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTripRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // TODO: добавить проверку роли (passenger)
    }

    public function rules(): array
    {
        return [
            'origin' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'preferences' => 'nullable|string|max:500',
        ];
    }
}
