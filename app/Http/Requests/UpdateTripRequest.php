<?php
declare(strict_types=1);
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTripRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'origin' => 'sometimes|string|max:255',
            'destination' => 'sometimes|string|max:255',
            'preferences' => 'nullable|string|max:500',
            'status' => 'sometimes|in:pending,accepted,completed,cancelled',
        ];
    }
}
