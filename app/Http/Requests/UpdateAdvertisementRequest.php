<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateAdvertisementRequest extends FormRequest
{
    public function authorize()
    {
        $advertisement = $this->route('advertisement');
        return Auth::check() && Auth::id() === $advertisement->user_id;
    }

    public function rules()
    {
        $rules = [
            'campaign_id' => 'nullable|exists:ad_campaigns,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:500',
            'target_url' => 'required|url|max:500',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'daily_budget' => 'required|numeric|min:1',
            'total_budget' => 'nullable|numeric|min:1',
            'placements' => 'required|array|min:1',
            'placements.*' => 'exists:ad_placements,id',
            'target_audience' => 'nullable|string|max:1000'
        ];

        // Banner specific rules
        if ($this->route('advertisement')->type === 'banner') {
            $rules = array_merge($rules, [
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'banner_size' => 'required|in:leaderboard,medium_rectangle,large_rectangle,wide_skyscraper,custom',
                'custom_width' => 'required_if:banner_size,custom|integer|min:100|max:1200',
                'custom_height' => 'required_if:banner_size,custom|integer|min:50|max:600',
                'animation_type' => 'nullable|in:none,fade,slide,zoom'
            ]);
        }

        // Video specific rules
        if ($this->route('advertisement')->type === 'video') {
            $rules = array_merge($rules, [
                'video_file' => 'nullable|mimes:mp4,avi,mov,wmv|max:51200',
                'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'video_duration' => 'required|integer|min:5|max:300',
                'skip_after' => 'nullable|integer|min:5|max:60',
                'autoplay' => 'nullable|boolean',
                'muted' => 'nullable|boolean',
                'controls' => 'nullable|boolean',
                'loop' => 'nullable|boolean'
            ]);
        }

        // Native ad specific rules
        if ($this->route('advertisement')->type === 'native') {
            $rules = array_merge($rules, [
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'call_to_action' => 'nullable|string|max:100'
            ]);
        }

        // Popup specific rules
        if ($this->route('advertisement')->type === 'popup') {
            $rules = array_merge($rules, [
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'popup_size' => 'required|in:small,medium,large'
            ]);
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'title.required' => 'حقل عنوان الإعلان مطلوب',
            'title.max' => 'عنوان الإعلان يجب ألا يزيد عن 255 حرف',
            'description.required' => 'حقل وصف الإعلان مطلوب',
            'description.max' => 'وصف الإعلان يجب ألا يزيد عن 500 حرف',
            'target_url.required' => 'حقل الرابط المستهدف مطلوب',
            'target_url.url' => 'الرابط المستهدف يجب أن يكون رابط صالح',
            'start_date.required' => 'حقل تاريخ البدء مطلوب',
            'start_date.after_or_equal' => 'تاريخ البدء يجب أن يكون اليوم أو تاريخ لاحق',
            'end_date.required' => 'حقل تاريخ الانتهاء مطلوب',
            'end_date.after' => 'تاريخ الانتهاء يجب أن يكون بعد تاريخ البدء',
            'daily_budget.required' => 'حقل الميزانية اليومية مطلوب',
            'daily_budget.min' => 'الميزانية اليومية يجب أن تكون على الأقل 1 ريال',
            'placements.required' => 'يجب اختيار موضع واحد على الأقل',
            'placements.min' => 'يجب اختيار موضع واحد على الأقل',
            'image.image' => 'الملف يجب أن يكون صورة',
            'image.mimes' => 'صيغ الصور المسموحة: JPEG, PNG, JPG, GIF',
            'image.max' => 'حجم الصورة يجب ألا يزيد عن 2 ميجابايت',
            'video_file.mimes' => 'صيغ الفيديو المسموحة: MP4, AVI, MOV, WMV',
            'video_file.max' => 'حجم الفيديو يجب ألا يزيد عن 50 ميجابايت',
            'video_duration.required' => 'حقل مدة الفيديو مطلوب',
            'video_duration.min' => 'مدة الفيديو يجب أن تكون على الأقل 5 ثواني',
            'video_duration.max' => 'مدة الفيديو يجب ألا تزيد عن 300 ثانية',
            'banner_size.required' => 'حقل حجم البانر مطلوب',
            'custom_width.required_if' => 'حقل العرض مطلوب عند اختيار حجم مخصص',
            'custom_height.required_if' => 'حقل الارتفاع مطلوب عند اختيار حجم مخصص'
        ];
    }
}
