<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class RespondToReviewRequest extends FormRequest
{
    public function authorize()
    {
        $review = $this->route('review');
        
        // Check if user is authorized to respond
        return Auth::check() && (
            Auth::id() === $review->reviewable->user_id || // Property owner
            Auth::user()->role === 'admin' || // Admin
            Auth::user()->role === 'moderator' // Moderator
        );
    }

    public function rules()
    {
        return [
            'response' => 'required|string|max:1000',
            'response_type' => 'required|in:public,private',
            'is_official' => 'nullable|boolean',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048'
        ];
    }

    public function messages()
    {
        return [
            'response.required' => 'حقل الرد مطلوب',
            'response.max' => 'الرد يجب ألا يزيد عن 1000 حرف',
            'response_type.required' => 'حقل نوع الرد مطلوب',
            'response_type.in' => 'نوع الرد غير صالح',
            'is_official.boolean' => 'حقل الرد الرسمي يجب أن يكون قيمة منطقية',
            'attachments.array' => 'المرفقات يجب أن تكون مصفوفة',
            'attachments.*.file' => 'كل مرفق يجب أن يكون ملف',
            'attachments.*.mimes' => 'صيغ الملفات المسموحة: PDF, DOC, DOCX, JPG, JPEG, PNG',
            'attachments.*.max' => 'حجم كل ملف يجب ألا يزيد عن 2 ميجابايت'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $review = $this->route('review');
            
            // Check if review allows responses
            if ($review && $review->responses->count() >= 3) {
                $validator->errors()->add('response', 'لا يمكن إضافة أكثر من 3 ردود على التقييم الواحد');
            }
            
            // Check if response time window is still valid (24 hours)
            if ($review && $review->created_at->diffInHours(now()) > 24) {
                $validator->errors()->add('response', 'انتهت فترة الرد على هذا التقييم (24 ساعة)');
            }
        });
    }
}
