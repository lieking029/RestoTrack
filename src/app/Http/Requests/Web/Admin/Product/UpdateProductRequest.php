<?php

namespace App\Http\Requests\Web\Admin\Product;

use App\Enums\UnitOfMeasurement;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'initial_stock' => ['sometimes', 'numeric', 'min:0'],
            'unit_of_measurement' => ['sometimes', new EnumValue(UnitOfMeasurement::class, false)],
            'expiration_date' => ['sometimes', 'date', 'after_or_equal:today'],
        ];
    }
}