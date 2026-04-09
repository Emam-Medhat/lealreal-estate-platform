<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ForgotPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => __('Email address is required.'),
            'email.email' => __('Please provide a valid email address.'),
            'email.max' => __('Email address must not exceed 255 characters.'),
        ];
    }

    public function getEmail(): string
    {
        return $this->validated('email');
    }
}
