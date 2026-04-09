<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['sometimes', 'required_without:login', 'string', 'email', 'max:255'],
            'login' => ['sometimes', 'required_without:email', 'string', 'max:255'],
            'password' => ['required', 'string', Password::defaults()],
            'remember' => ['sometimes', 'in:true,false,1,0'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required_without' => __('Email address is required.'),
            'login.required_without' => __('Email or username is required.'),
            'email.email' => __('Please provide a valid email address.'),
            'email.max' => __('Email address must not exceed 255 characters.'),
            'login.max' => __('Username must not exceed 255 characters.'),
            'password.required' => __('Password is required.'),
            'remember.in' => __('The remember field must be true or false.'),
        ];
    }

    public function authenticate(): void
    {
        $credentials = $this->only('email', 'password');
        $remember = $this->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            $this->throwValidationException(__('Invalid credentials. Please check your email and password.'));
        }

        $this->session()->regenerate();
    }

    protected function throwValidationException($message)
    {
        return back()
            ->withInput($this->only('email', 'remember'))
            ->withErrors([
                'email' => $message,
            ]);
    }
}
