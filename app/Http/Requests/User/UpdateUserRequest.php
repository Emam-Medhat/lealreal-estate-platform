<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('user') ?? $this->route('id');

        return [
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $userId,
            'phone' => 'sometimes|string|max:20',
            'role' => 'sometimes|in:admin,agent,investor,company',
            'account_status' => 'sometimes|in:active,inactive,suspended',
            'profile' => 'sometimes|array',
            'preferences' => 'sometimes|array',
            'avatar' => 'sometimes|image|mimes:jpg,jpeg,png,gif|max:2048',
        ];
    }
}
