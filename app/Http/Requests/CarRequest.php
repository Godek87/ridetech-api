<?php
declare(strict_types=1);
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // роль проверяется в middleware
    }

    public function rules(): array
    {
        return [
            'make' => 'required|string|max:100',
            'model' => 'required|string|max:100',
            'plate_number' => 'required|string|max:20|unique:cars,plate_number',
        ];
    }
}
