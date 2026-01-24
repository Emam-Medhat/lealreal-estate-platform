<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateAuctionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'property_id' => 'required|exists:properties,id',
            'starting_price' => 'required|numeric|min:0',
            'reserve_price' => 'nullable|numeric|min:starting_price',
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
            'auction_type' => ['required', Rule::in(['english', 'dutch', 'sealed_bid'])],
            'auction_settings' => 'nullable|array',
            'terms_conditions' => 'nullable|string|max:10000',
            'auto_extend' => 'boolean',
            'extend_duration' => 'nullable|integer|min:1|max:60',
            'bid_increment' => 'required|numeric|min:1',
            'minimum_bid' => 'nullable|numeric|min:starting_price',
            'requires_verification' => 'boolean',
            'buyer_fee_percentage' => 'required|numeric|min:0|max:100',
            'seller_fee_percentage' => 'required|numeric|min:0|max:100',
            'featured_image' => 'nullable|url',
            'images' => 'nullable|array',
            'images.*' => 'url',
            'documents' => 'nullable|array',
            'documents.*' => 'url',
            'metadata' => 'nullable|array',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'عنوان المزاد مطلوب',
            'description.required' => 'وصف المزاد مطلوب',
            'property_id.required' => 'العقار مطلوب',
            'property_id.exists' => 'العقار المحدد غير موجود',
            'starting_price.required' => 'سعر البداية مطلوب',
            'starting_price.min' => 'سعر البداية يجب أن يكون 0 أو أكثر',
            'reserve_price.min' => 'سعر الاحتياط يجب أن يكون أكبر من أو يساوي سعر البداية',
            'start_time.after' => 'وقت البدء يجب أن يكون في المستقبل',
            'end_time.after' => 'وقت الانتهاء يجب أن يكون بعد وقت البدء',
            'auction_type.required' => 'نوع المزاد مطلوب',
            'auction_type.in' => 'نوع المزاد غير صالح',
            'bid_increment.required' => 'زيادة المزايدة مطلوبة',
            'bid_increment.min' => 'زيادة المزايدة يجب أن تكون 1 أو أكثر',
            'minimum_bid.min' => 'الحد الأدنى للمزايدة يجب أن يكون أكبر من أو يساوي سعر البداية',
            'buyer_fee_percentage.max' => 'نسبة رسوم المشتري يجب أن تكون 100% أو أقل',
            'seller_fee_percentage.max' => 'نسبة رسوم البائع يجب أن تكون 100% أو أقل',
        ];
    }
}
