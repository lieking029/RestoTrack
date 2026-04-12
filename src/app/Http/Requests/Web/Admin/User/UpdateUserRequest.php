<?php

namespace App\Http\Requests\Web\Admin\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules for updating a user.
     */
    public function rules(): array
    {
        return [
            'first_name' => ['sometimes', 'string', 'max:255', 'regex:/^[a-zA-Z\s\-\.]+$/'],
            'middle_name' => ['sometimes', 'nullable', 'string', 'max:255', 'regex:/^[a-zA-Z\s\-\.]+$/'],
            'last_name' => ['sometimes', 'string', 'max:255', 'regex:/^[a-zA-Z\s\-\.]+$/'],
            'email' => ['sometimes', 'string', 'email:rfc,dns', 'max:255', Rule::unique('users', 'email')->ignore($this->user)],
            'password' => ['sometimes', 'nullable', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/'],
            'user_type' => ['sometimes', 'integer', 'in:0,1,2'],
            'employee_role' => ['required_if:user_type,2', 'nullable', 'string', 'in:cashier,cook,chef,server,barista'],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.regex' => 'First name must only contain letters, spaces, hyphens, and dots.',
            'middle_name.regex' => 'Middle name must only contain letters, spaces, hyphens, and dots.',
            'last_name.regex' => 'Last name must only contain letters, spaces, hyphens, and dots.',
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one symbol.',
        ];
    }
}