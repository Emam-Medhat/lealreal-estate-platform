<?php

namespace App\Http\Requests\Neighborhood;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateCommunityEventRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'community_id' => ['required', 'exists:communities,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'event_type' => ['required', 'string', 'in:social,educational,sports,cultural,religious,charity,business,entertainment,health,other'],
            'status' => ['required', 'string', 'in:draft,published,cancelled,completed'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'location' => ['required', 'string', 'max:500'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'organizer_name' => ['required', 'string', 'max:255'],
            'organizer_email' => ['nullable', 'email', 'max:255'],
            'organizer_phone' => ['nullable', 'string', 'max:50'],
            'max_participants' => ['nullable', 'integer', 'min:1'],
            'current_participants' => ['nullable', 'integer', 'min:0'],
            'age_restriction' => ['nullable', 'string', 'in:all,18+,21+,family,kids'],
            'price_info' => ['nullable', 'array'],
            'price_info.is_free' => ['nullable', 'boolean'],
            'price_info.price' => ['nullable', 'numeric', 'min:0'],
            'price_info.currency' => ['nullable', 'string', 'max:10'],
            'price_info.payment_methods' => ['nullable', 'array'],
            'schedule' => ['nullable', 'array'],
            'schedule.sessions' => ['nullable', 'array'],
            'requirements' => ['nullable', 'array'],
            'facilities' => ['nullable', 'array'],
            'contact_info' => ['nullable', 'array'],
            'social_sharing' => ['nullable', 'array'],
            'images' => ['nullable', 'array'],
            'images.*' => ['nullable', 'image', 'max:5120'],
            'cover_image' => ['nullable', 'image', 'max:5120'],
            'gallery' => ['nullable', 'array'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['nullable', 'string', 'max:50'],
            'metadata' => ['nullable', 'array'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'community_id.required' => 'المجتمع مطلوب',
            'community_id.exists' => 'المجتمع المحدد غير موجود',
            'title.required' => 'عنوان الفعالية مطلوب',
            'title.max' => 'يجب ألا يتجاوز عنوان الفعالية 255 حرفًا',
            'description.required' => 'وصف الفعالية مطلوب',
            'description.max' => 'يجب ألا يتجاوز وصف الفعالية 5000 حرف',
            'event_type.required' => 'نوع الفعالية مطلوب',
            'event_type.in' => 'نوع الفعالية غير صالح',
            'status.required' => 'حالة الفعالية مطلوبة',
            'status.in' => 'حالة الفعالية غير صالحة',
            'start_date.required' => 'تاريخ البدء مطلوب',
            'start_date.date' => 'تاريخ البدء يجب أن يكون تاريخًا صالحًا',
            'start_date.after_or_equal' => 'تاريخ البدء يجب أن يكون اليوم أو في المستقبل',
            'end_date.required' => 'تاريخ الانتهاء مطلوب',
            'end_date.date' => 'تاريخ الانتهاء يجب أن يكون تاريخًا صالحًا',
            'end_date.after' => 'تاريخ الانتهاء يجب أن يكون بعد تاريخ البدء',
            'location.required' => 'موقع الفعالية مطلوب',
            'location.max' => 'يجب ألا يتجاوز موقع الفعالية 500 حرف',
            'latitude.numeric' => 'خط العرض يجب أن يكون رقمًا',
            'latitude.between' => 'خط العرض يجب أن يكون بين -90 و 90',
            'longitude.numeric' => 'خط الطول يجب أن يكون رقمًا',
            'longitude.between' => 'خط الطول يجب أن يكون بين -180 و 180',
            'organizer_name.required' => 'اسم المنظم مطلوب',
            'organizer_name.max' => 'يجب ألا يتجاوز اسم المنظم 255 حرفًا',
            'organizer_email.email' => 'البريد الإلكتروني غير صالح',
            'organizer_email.max' => 'يجب ألا يتجاوز البريد الإلكتروني 255 حرفًا',
            'organizer_phone.max' => 'يجب ألا يتجاوز رقم الهاتف 50 حرفًا',
            'max_participants.integer' => 'الحد الأقصى للمشاركين يجب أن يكون رقمًا صحيحًا',
            'max_participants.min' => 'الحد الأقصى للمشاركين يجب أن يكون 1 أو أكثر',
            'current_participants.integer' => 'عدد المشاركين الحالي يجب أن يكون رقمًا صحيحًا',
            'current_participants.min' => 'عدد المشاركين الحالي يجب أن يكون 0 أو أكثر',
            'age_restriction.in' => 'تقييد العمر غير صالح',
            'price_info.is_free.boolean' => 'حالة المجانية يجب أن تكون منطقية',
            'price_info.price.numeric' => 'السعر يجب أن يكون رقمًا',
            'price_info.price.min' => 'السعر يجب أن يكون 0 أو أكثر',
            'price_info.currency.max' => 'يجب ألا يتجاوز العملة 10 أحرف',
            'images.*.image' => 'يجب أن يكون الملف صورة',
            'images.*.max' => 'يجب ألا يتجاوز حجم الصورة 5 ميجابايت',
            'cover_image.image' => 'يجب أن يكون الملف صورة',
            'cover_image.max' => 'يجب ألا يتجاوز حجم الصورة 5 ميجابايت',
            'tags.*.max' => 'يجب ألا يتجاوز العلامة 50 حرفًا',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'community_id' => 'المجتمع',
            'title' => 'عنوان الفعالية',
            'description' => 'وصف الفعالية',
            'event_type' => 'نوع الفعالية',
            'status' => 'حالة الفعالية',
            'start_date' => 'تاريخ البدء',
            'end_date' => 'تاريخ الانتهاء',
            'location' => 'موقع الفعالية',
            'latitude' => 'خط العرض',
            'longitude' => 'خط الطول',
            'organizer_name' => 'اسم المنظم',
            'organizer_email' => 'بريد المنظم الإلكتروني',
            'organizer_phone' => 'هاتف المنظم',
            'max_participants' => 'الحد الأقصى للمشاركين',
            'current_participants' => 'عدد المشاركين الحالي',
            'age_restriction' => 'تقييد العمر',
            'price_info' => 'معلومات السعر',
            'price_info.is_free' => 'مجاني',
            'price_info.price' => 'السعر',
            'price_info.currency' => 'العملة',
            'price_info.payment_methods' => 'طرق الدفع',
            'schedule' => 'الجدول',
            'schedule.sessions' => 'الجلسات',
            'requirements' => 'المتطلبات',
            'facilities' => 'المرافق',
            'contact_info' => 'معلومات الاتصال',
            'social_sharing' => 'المشاركة الاجتماعية',
            'images' => 'الصور',
            'cover_image' => 'الصورة الرئيسية',
            'gallery' => 'المعرض',
            'tags' => 'العلامات',
            'metadata' => 'البيانات الوصفية',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validate participant counts
            if ($this->has('max_participants') && $this->has('current_participants')) {
                if ($this->current_participants > $this->max_participants) {
                    $validator->errors()->add('current_participants', 'عدد المشاركين الحالي لا يمكن أن يتجاوز الحد الأقصى للمشاركين');
                }
            }
            
            // Validate coordinates
            if ($this->has('latitude') && $this->has('longitude')) {
                if ($this->latitude && !$this->longitude) {
                    $validator->errors()->add('longitude', 'يجب إدخال خط الطول عند إدخال خط العرض');
                }
                if ($this->longitude && !$this->latitude) {
                    $validator->errors()->add('latitude', 'يجب إدخال خط العرض عند إدخال خط الطول');
                }
            }
            
            // Validate price info
            if ($this->has('price_info') && is_array($this->price_info)) {
                $isFree = $this->price_info['is_free'] ?? false;
                $price = $this->price_info['price'] ?? null;
                
                if ($isFree && $price !== null && $price > 0) {
                    $validator->errors()->add('price_info.price', 'السعر يجب أن يكون 0 أو فارغ للفعاليات المجانية');
                }
                
                if (!$isFree && ($price === null || $price <= 0)) {
                    $validator->errors()->add('price_info.price', 'السعر مطلوب للفعاليات غير المجانية');
                }
            }
            
            // Validate schedule sessions
            if ($this->has('schedule') && isset($this->schedule['sessions']) && is_array($this->schedule['sessions'])) {
                foreach ($this->schedule['sessions'] as $index => $session) {
                    if (!isset($session['start_time']) || !isset($session['end_time'])) {
                        $validator->errors()->add("schedule.sessions.{$index}", 'كل جلسة يجب أن تحتوي على وقت البدء والانتهاء');
                    }
                    
                    if (isset($session['start_time']) && isset($session['end_time'])) {
                        if ($session['start_time'] >= $session['end_time']) {
                            $validator->errors()->add("schedule.sessions.{$index}", 'وقت البدء يجب أن يكون قبل وقت الانتهاء');
                        }
                    }
                }
            }
            
            // Validate date logic
            if ($this->has('start_date') && $this->has('end_date')) {
                $startDate = $this->start_date;
                $endDate = $this->end_date;
                
                if (is_string($startDate) && is_string($endDate)) {
                    $start = \Carbon\Carbon::parse($startDate);
                    $end = \Carbon\Carbon::parse($endDate);
                    
                    if ($start->diffInDays($end) > 365) {
                        $validator->errors()->add('end_date', 'لا يمكن أن تتجاوز مدة الفعالية 365 يومًا');
                    }
                }
            }
            
            // Validate tags
            if ($this->has('tags') && is_array($this->tags)) {
                if (count($this->tags) > 10) {
                    $validator->errors()->add('tags', 'يجب ألا يتجاوز عدد العلامات 10');
                }
                
                foreach ($this->tags as $index => $tag) {
                    if (strlen($tag) > 50) {
                        $validator->errors()->add("tags.{$index}", 'يجب ألا يتجاوز طول العلامة 50 حرفًا');
                    }
                }
            }
            
            // Validate images
            if ($this->has('images') && is_array($this->images)) {
                if (count($this->images) > 10) {
                    $validator->errors()->add('images', 'يجب ألا يتجاوز عدد الصور 10');
                }
            }
            
            // Validate gallery
            if ($this->has('gallery') && is_array($this->gallery)) {
                if (count($this->gallery) > 20) {
                    $validator->errors()->add('gallery', 'يجب ألا يتجاوز عدد صور المعرض 20');
                }
            }
        });
    }
}
