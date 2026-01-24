<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'min:3', 'max:30', 'unique:users,username', 'regex:/^[a-zA-Z0-9_]+$/'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'first_name' => ['required', 'string', 'min:2', 'max:50'],
            'last_name' => ['required', 'string', 'min:2', 'max:50'],
            'phone' => ['nullable', 'string', 'regex:/^[+]?[0-9\s\-\(\)]+$/'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'password_confirmation' => ['required'],
            'user_type' => ['required', 'in:user,agent,company,developer,investor,admin,super_admin'],
            'gender' => ['nullable', 'in:male,female,other'],
            'date_of_birth' => ['nullable', 'date', 'before:' . now()->subYears(18)->format('Y-m-d')],
            'country' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'language' => ['nullable', 'in:en,ar,fr'],
            'currency' => ['nullable', 'in:USD,EUR,EGP'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'username.required' => __('Username is required'),
            'username.unique' => __('Username is already taken'),
            'username.regex' => __('Username can only contain letters, numbers, and underscores'),
            'email.required' => __('Email address is required'),
            'email.email' => __('Please provide a valid email address'),
            'email.unique' => __('Email address is already registered'),
            'first_name.required' => __('First name is required'),
            'last_name.required' => __('Last name is required'),
            'password.required' => __('Password is required'),
            'password.confirmed' => __('Password confirmation does not match'),
            'user_type.required' => __('Account type is required'),
            'date_of_birth.before' => __('You must be at least 18 years old'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'username' => __('Username'),
            'email' => __('Email Address'),
            'first_name' => __('First Name'),
            'last_name' => __('Last Name'),
            'phone' => __('Phone Number'),
            'password' => __('Password'),
            'password_confirmation' => __('Password Confirmation'),
            'user_type' => __('Account Type'),
            'gender' => __('Gender'),
            'date_of_birth' => __('Date of Birth'),
            'country' => __('Country'),
            'city' => __('City'),
            'language' => __('Language'),
            'currency' => __('Currency'),
        ];
    }
}
