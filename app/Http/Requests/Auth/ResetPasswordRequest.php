<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules\Password as PasswordRule;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'token' => ['required'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'confirmed', PasswordRule::defaults()],
            'password_confirmation' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'token.required' => __('Reset token is required.'),
            'email.required' => __('Email address is required.'),
            'email.email' => __('Please provide a valid email address.'),
            'password.required' => __('Password is required.'),
            'password.confirmed' => __('Password confirmation does not match.'),
            'password_confirmation.required' => __('Password confirmation is required.'),
        ];
    }

    public function getCredentials(): array
    {
        return $this->only('email', 'token', 'password', 'password_confirmation');
    }

    public function getBroker(): string
    {
        return Password::getDefaultBroker();
    }

    public function getHashedPassword(): string
    {
        return Hash::make($this->validated('password'));
    }

    public function getEmail(): string
    {
        return $this->validated('email');
    }

    public function getToken(): string
    {
        return $this->validated('token');
    }
}
