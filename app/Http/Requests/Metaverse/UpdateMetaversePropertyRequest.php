<?php

namespace App\Http\Requests\Metaverse;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
use Illuminate\Support\Facades\Storage;

class UpdateMetaversePropertyRequest extends FormRequest
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
        $propertyId = $this->route('property');

        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'virtual_world_id' => 'required|exists:virtual_worlds,id',
            'property_type' => 'required|in:residential,commercial,mixed,industrial,recreational,educational,healthcare,office,retail,hospitality',
            'location_coordinates' => 'required|string|max:255',
            'dimensions' => 'required|array',
            'dimensions.length' => 'required|numeric|min:1',
            'dimensions.width' => 'required|numeric|min:1',
            'dimensions.height' => 'required|numeric|min:1',
            'dimensions.depth' => 'nullable|numeric|min:1',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|max:10|in:USD,EUR,GBP,ETH,BTC,USDT',
            'is_for_sale' => 'boolean',
            'is_for_rent' => 'boolean',
            'rent_price' => 'nullable|numeric|min:0',
            'rent_currency' => 'nullable|string|max:10|in:USD,EUR,GBP,ETH,BTC,USDT',
            'rent_period' => 'nullable|string|max:20|in:hourly,daily,weekly,monthly,yearly',
            'status' => 'required|in:active,inactive,building,maintenance,suspended,deleted',
            'visibility' => 'required|in:public,private,restricted,unlisted',
            'access_level' => 'required|in:public,private,restricted,premium,invite_only',
            'features' => 'nullable|array',
            'amenities' => 'nullable|array',
            'utilities' => 'nullable|array',
            'zoning_info' => 'nullable|array',
            'building_restrictions' => 'nullable|array',
            'environmental_settings' => 'nullable|array',
            'security_settings' => 'nullable|array',
            'accessibility_features' => 'nullable|array',
            'multimedia_settings' => 'nullable|array',
            'interaction_settings' => 'nullable|array',
            'customization_options' => 'nullable|array',
            'is_nft' => 'boolean',
            'virtual_property_design_id' => 'nullable|exists:virtual_property_designs,id',
            'nft_id' => 'nullable|exists:metaverse_property_nfts,id',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,jpg,png,gif,webp|max:10240',
            'models' => 'nullable|array',
            'models.*' => 'file|mimes:obj,fbx,dae,3ds|max:10240',
            'textures' => 'nullable|array',
            'textures.*' => 'file|mimes:jpg,png,webp|max:5120',
            'remove_images' => 'nullable|array',
            'remove_images.*' => 'integer',
            'remove_models' => 'nullable|array',
            'remove_models.*' => 'integer',
            'remove_textures' => 'nullable|array',
            'remove_textures.*' => 'integer',
            'updated_by' => 'nullable|exists:users,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'title.required' => 'عنوان العقار مطلوب',
            'title.max' => 'يجب أن لا يتجاوز 255 حرفاً',
            'description.required' => 'وصف العقار مطلوب',
            'description.max' => 'يجب أن لا يتجاوز 2000 حرفاً',
            'virtual_world_id.required' => 'العالم الافتراضي مطلوب',
            'virtual_world_id.exists' => 'العالم الافتراضي غير موجود',
            'property_type.required' => 'نوع العقار مطلوب',
            'property_type.in' => 'نوع العقار غير صحيح',
            'location_coordinates.required' => 'الإحداثيات مطلوبة',
            'location_coordinates.max' => 'يجب أن لا يتجاوز 255 حرفاً',
            'dimensions.required' => 'أبعاد العقار مطلوبة',
            'dimensions.array' => 'أبعاد العقار يجب أن يكون مصفوفاً',
            'dimensions.length.required' => 'الطول مطلوب',
            'dimensions.length.min' => 'الطول يجب أن يكون على الأقل 1',
            'dimensions.width.required' => 'العرض مطلوب',
            'dimensions.width.min' => 'العرض يجب أن يكون على الأقل 1',
            'dimensions.height.required' => 'الارتفاع مطلوب',
            'dimensions.height.min' => 'الارتفاع يجب أن يكون على الأقل 1',
            'price.required' => 'السعر مطلوب',
            'price.min' => 'السعر يجب أن يكون رقم موجب',
            'currency.required' => 'العملة مطلوبة',
            'currency.in' => 'العملة غير مدعومة',
            'is_for_sale.boolean' => 'حالة البيع يجب أن تكون قيمة منطقية',
            'is_for_rent.boolean' => 'حالة الإيجار يجب أن تكون قيمة منطقية',
            'rent_price.required_if' => 'سعر الإيجار مطلوب عندما تكون العقار للإيجار',
            'rent_price.numeric' => 'سعر الإيجار يجب أن يكون رقم موجب',
            'rent_currency.in' => 'عملة الإيجار غير مدعومة',
            'rent_period.in' => 'فترة الإيجار غير مدعومة',
            'status.required' => 'الحالة مطلوبة',
            'status.in' => 'الحالة غير صحيحة',
            'visibility.required' => 'الرؤية مطلوبة',
            'visibility.in' => 'الرؤية غير صحيحة',
            'access_level.required' => 'مستوى الوصول مطلوب',
            'access_level.in' => 'مستوى الوصول غير صحيح',
            'virtual_property_design_id.exists' => 'تصميم العقار غير موجود',
            'nft_id.exists' => 'NFT غير موجود',
            'images.array' => 'الصور يجب أن يكون مصفوفاً',
            'images.*.0' => 'ملف الصورة يجب أن يكون صورة صالحة',
            'images.*.0.mimes' => 'تنسيق الملف غير مدعوم',
            'models.array' => 'النماذج يجب أن يكون ملفاً صالحاً',
            'models.*.0' => 'ملف النموذج يجب أن يكون صالحاً',
            'models.*.0.mimes' => 'تنسيق الملف غير مدعوم',
            'textures.array' => 'الملفات النسيج يجب أن تكون صورة أو webp',
            'textures.*.0' => 'ملف النسيج يجب أن يكون صورة أو webp',
            'updated_by.exists' => 'المستخدم غير موجود',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            'title' => 'عنوان العقار',
            'description' => 'وصف العقار',
            'virtual_world_id' => 'العالم الافتراضي',
            'property_type' => 'نوع العقار',
            'location_coordinates' => 'الإحداثيات',
            'dimensions' => 'أبعاد العقار',
            'price' => 'السعر',
            'currency' => 'العملة',
            'is_for_sale' => 'حالة البيع',
            'is_for_rent' => 'حالة الإيجار',
            'rent_price' => 'سعر الإيجار',
            'rent_currency' => 'عملة الإيجار',
            'rent_period' => 'فترة الإيجار',
            'status' => 'الحالة',
            'visibility' => 'الرؤية',
            'access_level' => 'مستوى الوصول',
            'features' => 'المميزات',
            'amenities' => 'المرافق',
            'utilities' => 'المرافق',
            'zoning_info' => 'معلومات التخطيط',
            'building_restrictions' => 'قيود البناء',
            'environmental_settings' => 'الإعدادات البيئية',
            'security_settings' => 'إعدادات الأمان',
            'accessibility_features' => 'ميزات الوصول',
            'multimedia_settings' => 'إعدادات الوسائط',
            'interaction_settings' => 'إعدادات التفاعل',
            'customization_options' => 'خيارات التخصيص',
            'is_nft' => 'هل هو NFT',
            'virtual_property_design_id' => 'تصميم العقار',
            'nft_id' => 'NFT',
            'images' => 'الصور',
            'models' => 'النماذج',
            'textures' => 'الملفات النسيج',
            'remove_images' => 'حذف الصور',
            'remove_models' => 'حذف النماذج',
            'remove_textures' => 'حذف الملفات النسيج',
            'updated_by' => 'المستخدم',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'dimensions' => $this->formatDimensions($this->input('dimensions')),
            'price' => $this->formatPrice($this->input('price')),
            'rent_price' => $this->formatPrice($this->input('rent_price')),
            'updated_by' => auth()->id(),
        ]);
    }

    /**
     * Format dimensions array.
     */
    private function formatDimensions($dimensions): array
    {
        if (is_string($dimensions)) {
            $dimensions = json_decode($dimensions, true) ?? [];
        }

        return [
            'length' => $dimensions['length'] ?? 0,
            'width' => $dimensions['width'] ?? 0,
            'height' => $dimensions['height'] ?? 0,
            'depth' => $dimensions['depth'] ?? 0,
        ];
    }

    /**
     * Format price to decimal.
     */
    private function formatPrice($price): float
    {
        return (float) str_replace(',', '', $price);
    }

    /**
     * Get the after validation hook.
     */
    public function after()
    {
        $property = $this->route('property');

        // Check if user owns the property or has permission
        if (!$this->user()->can('update', $property)) {
            $this->validator->errors()->add('authorization', 'ليس لديك صلاحية لتعديل هذا العقار');
        }

        // Check if property is being converted to NFT
        if ($this->input('is_nft') && !$property->is_nft && !$this->input('virtual_property_design_id')) {
            $this->validator->errors()->add('virtual_property_design_id', 'يجب تحديد تصميم العقار عند تحويله إلى NFT');
        }

        // Check if property is being converted from NFT
        if (!$this->input('is_nft') && $property->is_nft) {
            $this->validator->errors()->add('is_nft', 'لا يمكن تحويل NFT إلى عقار عادي');
        }

        // Validate dimensions
        $dimensions = $this->input('dimensions');
        if ($dimensions['length'] <= 0 || $dimensions['width'] <= 0 || $dimensions['height'] <= 0) {
            $this->validator->errors()->add('dimensions', 'جميع أبعاد العقار يجب أن تكون قيم موجبة');
        }

        // Validate price
        $price = $this->input('price');
        if ($price <= 0) {
            $this->validator->errors()->add('price', 'السعر يجب أن يكون أكبر من صفر');
        }

        // Validate rent price if property is for rent
        if ($this->input('is_for_rent') && $this->input('rent_price') <= 0) {
            $this->validator->errors()->add('rent_price', 'سعر الإيجار يجب أن يكون أكبر من صفر');
        }

        // Check if property has active transactions
        if ($property->transactions()->where('status', 'pending')->exists()) {
            $this->validator->errors()->add('status', 'لا يمكن تعديل العقار الذي لديه معاملات نشطة');
        }

        // Check if property is being sold and has active bids
        if ($this->input('is_for_sale') && $property->nft && $property->nft->bids()->where('status', 'active')->exists()) {
            $this->validator->errors()->add('is_for_sale', 'لا يمكن تغيير حالة البيع مع وجود مزايدات نشطة');
        }

        // Validate file removals
        $removeImages = $this->input('remove_images', []);
        if (!empty($removeImages)) {
            foreach ($removeImages as $imageId) {
                if (!$property->images()->where('id', $imageId)->exists()) {
                    $this->validator->errors()->add('remove_images', 'بعض الصور المحددة للحذف غير موجودة');
                    break;
                }
            }
        }

        $removeModels = $this->input('remove_models', []);
        if (!empty($removeModels)) {
            foreach ($removeModels as $modelId) {
                if (!$property->models()->where('id', $modelId)->exists()) {
                    $this->validator->errors()->add('remove_models', 'بعض النماذج المحددة للحذف غير موجودة');
                    break;
                }
            }
        }

        $removeTextures = $this->input('remove_textures', []);
        if (!empty($removeTextures)) {
            foreach ($removeTextures as $textureId) {
                if (!$property->textures()->where('id', $textureId)->exists()) {
                    $this->validator->errors()->add('remove_textures', 'بعض الملفات النسيج المحددة للحذف غير موجودة');
                    break;
                }
            }
        }

        // Check if price change is significant (more than 20%)
        $oldPrice = $property->price;
        $newPrice = $this->input('price');
        $priceChange = abs(($newPrice - $oldPrice) / $oldPrice) * 100;
        
        if ($priceChange > 20) {
            $this->validator->errors()->add('price', 'لا يمكن تغيير السعر بأكثر من 20% في مرة واحدة');
        }

        // Check if status change requires additional validation
        if ($this->input('status') === 'deleted' && $property->status !== 'inactive') {
            $this->validator->errors()->add('status', 'يجب أن يكون العقار غير نشط قبل حذفه');
        }

        if ($this->input('status') === 'suspended' && !$this->user()->hasRole('admin')) {
            $this->validator->errors()->add('status', 'فقط المشرف يمكنه تعليق العقار');
        }

        // Validate virtual world change
        if ($this->input('virtual_world_id') !== $property->virtual_world_id) {
            if ($property->transactions()->where('status', 'completed')->exists()) {
                $this->validator->errors()->add('virtual_world_id', 'لا يمكن تغيير العالم الافتراضي للعقار الذي لديه معاملات مكتملة');
            }
        }
    }
}
