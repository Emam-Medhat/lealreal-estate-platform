<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JoinChallengeRequest extends FormRequest
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
            'challenge_id' => 'required|exists:property_challenges,id',
            'accept_terms' => 'required|accepted',
            'team_name' => 'nullable|string|max:100',
            'team_members' => 'nullable|array|max:5',
            'team_members.*' => 'exists:users,id',
            'notes' => 'nullable|string|max:500',
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
            'challenge_id.required' => 'حقل التحدي مطلوب',
            'challenge_id.exists' => 'التحدي المحدد غير موجود',
            'accept_terms.required' => 'يجب الموافقة على الشروط والأحكام',
            'accept_terms.accepted' => 'يجب قبول الشروط والأحكام',
            'team_name.max' => 'الحد الأقصى لاسم الفريق هو 100 حرف',
            'team_members.max' => 'الحد الأقصى لأعضاء الفريق هو 5',
            'team_members.*.exists' => 'أحد أعضاء الفريق غير موجود',
            'notes.max' => 'الحد الأقصى للملاحظات هو 500 حرف',
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
            'challenge_id' => 'التحدي',
            'accept_terms' => 'الموافقة على الشروط',
            'team_name' => 'اسم الفريق',
            'team_members' => 'أعضاء الفريق',
            'notes' => 'الملاحظات',
        ];
    }
}
