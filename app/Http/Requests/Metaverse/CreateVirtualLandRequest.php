<?php

namespace App\Http\Requests\Metaverse;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class CreateVirtualLandRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'virtual_world_id' => 'required|exists:virtual_worlds,id',
            'land_type' => 'required|in:residential,commercial,mixed,industrial,recreational,agricultural,institutional',
            'coordinates' => 'required|string|max:255',
            'area' => 'required|numeric|min:1',
            'area_unit' => 'required|string|max:20|in:sqm,sqft,acre,hectare',
            'dimensions' => 'nullable|array',
            'dimensions.length' => 'nullable|numeric|min:1',
            'dimensions.width' => 'nullable|numeric|min:1',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|max:10|in:USD,EUR,GBP,ETH,BTC,USDT',
            'ownership_status' => 'required|in:available,owned,leased,restricted,development',
            'zoning_types' => 'nullable|array',
            'zoning_types.*' => 'string|max:100',
            'max_building_height' => 'nullable|integer|min:1',
            'min_lot_size' => 'nullable|numeric|min:1',
            'setback_requirements' => 'nullable|array',
            'parking_requirements' => 'nullable|array',
            'development_status' => 'required|in:undeveloped,planned,under_construction,completed,partially_developed',
            'development_type' => 'nullable|string|max:100',
            'development_plan' => 'nullable|array',
            'estimated_development_cost' => 'nullable|numeric|min:0',
            'estimated_development_timeline' => 'nullable|string|max:100',
            'zoning_compliance' => 'boolean',
            'environmental_impact_assessment' => 'nullable|array',
            'infrastructure_requirements' => 'nullable|array',
            'is_prime_location' => 'boolean',
            'is_waterfront' => 'boolean',
            'terrain_type' => 'nullable|string|max:100',
            'soil_quality' => 'nullable|string|max:100',
            'elevation' => 'nullable|numeric',
            'distance_from_coast' => 'nullable|numeric',
            'water_body_proximity' => 'nullable|numeric',
            'flood_zone' => 'nullable|string|max:50',
            'utilities_available' => 'nullable|array',
            'access_roads' => 'nullable|array',
            'public_transport_access' => 'boolean',
            'nearby_amenities' => 'nullable|array',
            'market_value' => 'nullable|numeric|min:0',
            'assessment_value' => 'nullable|numeric|min:0',
            'tax_assessment' => 'nullable|numeric|min:0',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,jpg,png,gif,webp|max:10240',
            'survey_maps' => 'nullable|array',
            'survey_maps.*' => 'file|mimes:pdf,dwg,dxf|max:20480',
            'legal_documents' => 'nullable|array',
            'legal_documents.*' => 'file|mimes:pdf,doc,docx|max:10240',
            'created_by' => 'nullable|exists:users,id',
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
            'title.required' => 'عنوان الأرض مطلوب',
            'title.max' => 'يجب أن لا يتجاوز 255 حرفاً',
            'description.required' => 'وصف الأرض مطلوب',
            'description.max' => 'يجب أن لا يتجاوز 2000 حرفاً',
            'virtual_world_id.required' => 'العالم الافتراضي مطلوب',
            'virtual_world_id.exists' => 'العالم الافتراضي غير موجود',
            'land_type.required' => 'نوع الأرض مطلوب',
            'land_type.in' => 'نوع الأرض غير صحيح',
            'coordinates.required' => 'الإحداثيات مطلوبة',
            'coordinates.max' => 'يجب أن لا يتجاوز 255 حرفاً',
            'area.required' => 'مساحة الأرض مطلوبة',
            'area.min' => 'المساحة يجب أن تكون على الأقل 1',
            'area_unit.required' => 'وحدة المساحة مطلوبة',
            'area_unit.in' => 'وحدة المساحة غير صحيحة',
            'price.required' => 'السعر مطلوب',
            'price.min' => 'السعر يجب أن يكون رقم موجب',
            'currency.required' => 'العملة مطلوبة',
            'currency.in' => 'العملة غير مدعومة',
            'ownership_status.required' => 'حالة الملكية مطلوبة',
            'ownership_status.in' => 'حالة الملكية غير صحيحة',
            'zoning_types.array' => 'أنواع التخطيط يجب أن يكون مصفوفاً',
            'zoning_types.*.max' => 'نوع التخطيط يجب أن لا يتجاوز 100 حرفاً',
            'max_building_height.min' => 'الارتفاع الأقصى يجب أن يكون على الأقل 1',
            'min_lot_size.min' => 'الحد الأدنى لحجم القطعة يجب أن يكون رقم موجب',
            'development_status.required' => 'حالة التطوير مطلوبة',
            'development_status.in' => 'حالة التطوير غير صحيحة',
            'estimated_development_cost.min' => 'تكلفة التطوير يجب أن تكون رقم موجب',
            'zoning_compliance.boolean' => 'التوافق مع التخطيط يجب أن يكون قيمة منطقية',
            'is_prime_location.boolean' => 'الموقع المميز يجب أن يكون قيمة منطقية',
            'is_waterfront.boolean' => 'الموقع على الماء يجب أن يكون قيمة منطقية',
            'terrain_type.max' => 'نوع التضاريس يجب أن لا يتجاوز 100 حرفاً',
            'soil_quality.max' => 'جودة التربة يجب أن لا يتجاوز 100 حرفاً',
            'flood_zone.max' => 'منطقة الفيضان يجب أن لا يتجاوز 50 حرفاً',
            'public_transport_access.boolean' => 'الوصول للنقل العام يجب أن يكون قيمة منطقية',
            'market_value.min' => 'القيمة السوقية يجب أن تكون رقم موجب',
            'assessment_value.min' => 'قيمة التقييم يجب أن تكون رقم موجب',
            'tax_assessment.min' => 'التقييم الضريبي يجب أن يكون رقم موجب',
            'images.array' => 'الصور يجب أن يكون مصفوفاً',
            'images.*.0' => 'ملف الصورة يجب أن يكون صورة صالحة',
            'images.*.0.mimes' => 'تنسيق الملف غير مدعوم',
            'survey_maps.array' => 'خرائط المسح يجب أن يكون مصفوفاً',
            'survey_maps.*.0' => 'ملف الخريطة يجب أن يكون صالحاً',
            'survey_maps.*.0.mimes' => 'تنسيق الملف غير مدعوم',
            'legal_documents.array' => 'المستندات القانونية يجب أن يكون مصفوفاً',
            'legal_documents.*.0' => 'ملف المستند يجب أن يكون صالحاً',
            'legal_documents.*.0.mimes' => 'تنسيق الملف غير مدعوم',
            'created_by.exists' => 'المستخدم غير موجود',
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
            'title' => 'عنوان الأرض',
            'description' => 'وصف الأرض',
            'virtual_world_id' => 'العالم الافتراضي',
            'land_type' => 'نوع الأرض',
            'coordinates' => 'الإحداثيات',
            'area' => 'المساحة',
            'area_unit' => 'وحدة المساحة',
            'dimensions' => 'أبعاد الأرض',
            'price' => 'السعر',
            'currency' => 'العملة',
            'ownership_status' => 'حالة الملكية',
            'zoning_types' => 'أنواع التخطيط',
            'max_building_height' => 'الارتفاع الأقصى',
            'min_lot_size' => 'الحد الأدنى لحجم القطعة',
            'setback_requirements' => 'متطلبات التراجع',
            'parking_requirements' => 'متطلبات مواقف السيارات',
            'development_status' => 'حالة التطوير',
            'development_type' => 'نوع التطوير',
            'development_plan' => 'خطة التطوير',
            'estimated_development_cost' => 'تكلفة التطوير المقدرة',
            'estimated_development_timeline' => 'الجدول الزمني للتطوير',
            'zoning_compliance' => 'التوافق مع التخطيط',
            'environmental_impact_assessment' => 'تقييم الأثر البيئي',
            'infrastructure_requirements' => 'متطلبات البنية التحتية',
            'is_prime_location' => 'الموقع المميز',
            'is_waterfront' => 'الموقع على الماء',
            'terrain_type' => 'نوع التضاريس',
            'soil_quality' => 'جودة التربة',
            'elevation' => 'الارتفاع',
            'distance_from_coast' => 'المسافة من الساحل',
            'water_body_proximity' => 'القرب من المسطحات المائية',
            'flood_zone' => 'منطقة الفيضان',
            'utilities_available' => 'المرافق المتاحة',
            'access_roads' => 'طرق الوصول',
            'public_transport_access' => 'الوصول للنقل العام',
            'nearby_amenities' => 'المرافق القريبة',
            'market_value' => 'القيمة السوقية',
            'assessment_value' => 'قيمة التقييم',
            'tax_assessment' => 'التقييم الضريبي',
            'images' => 'الصور',
            'survey_maps' => 'خرائط المسح',
            'legal_documents' => 'المستندات القانونية',
            'created_by' => 'المستخدم',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'area' => $this->formatArea($this->input('area')),
            'price' => $this->formatPrice($this->input('price')),
            'estimated_development_cost' => $this->formatPrice($this->input('estimated_development_cost')),
            'market_value' => $this->formatPrice($this->input('market_value')),
            'assessment_value' => $this->formatPrice($this->input('assessment_value')),
            'tax_assessment' => $this->formatPrice($this->input('tax_assessment')),
            'created_by' => auth()->id(),
        ]);
    }

    /**
     * Format area to decimal.
     */
    private function formatArea($area): float
    {
        return (float) str_replace(',', '', $area);
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
        // Check if user owns the virtual world or has permission
        if (!$this->user()->can('create', 'metaverse.land.create')) {
            $this->validator->errors()->add('virtual_world_id', 'ليس لديك صلاحية لإنشاء أرض في هذا العالم الافتراضي');
        }

        // Validate area
        $area = $this->input('area');
        if ($area <= 0) {
            $this->validator->errors()->add('area', 'المساحة يجب أن تكون أكبر من صفر');
        }

        // Validate price
        $price = $this->input('price');
        if ($price <= 0) {
            $this->validator->errors()->add('price', 'السعر يجب أن يكون أكبر من صفر');
        }

        // Check if coordinates are valid
        $coordinates = $this->input('coordinates');
        if (!$this->isValidCoordinates($coordinates)) {
            $this->validator->errors()->add('coordinates', 'الإحداثيات غير صالحة');
        }

        // Check if land is waterfront but has no water body proximity
        if ($this->input('is_waterfront') && !$this->input('water_body_proximity')) {
            $this->validator->errors()->add('water_body_proximity', 'يجب تحديد القرب من المسطحات المائية للأرض على الماء');
        }

        // Check if development cost is provided for developed land
        if ($this->input('development_status') !== 'undeveloped' && !$this->input('estimated_development_cost')) {
            $this->validator->errors()->add('estimated_development_cost', 'يجب تحديد تكلفة التطوير للأرض المطورة');
        }

        // Check if zoning types are provided for residential/commercial land
        $landType = $this->input('land_type');
        if (in_array($landType, ['residential', 'commercial']) && empty($this->input('zoning_types'))) {
            $this->validator->errors()->add('zoning_types', 'يجب تحديد أنواع التخطيط للأرض السكنية أو التجارية');
        }

        // Check if max building height is reasonable
        $maxHeight = $this->input('max_building_height');
        if ($maxHeight && ($maxHeight < 1 || $maxHeight > 1000)) {
            $this->validator->errors()->add('max_building_height', 'الارتفاع الأقصى يجب أن يكون بين 1 و 1000 متر');
        }

        // Check if area is reasonable for land type
        if ($area && $landType) {
            $this->validateAreaForLandType($area, $landType);
        }

        // Check if price is reasonable for area and location
        if ($price && $area) {
            $pricePerUnit = $price / $area;
            if ($pricePerUnit > 10000) { // Very high price per unit
                $this->validator->errors()->add('price', 'السعر مرتفع جداً للمساحة المحددة');
            }
        }

        // Check if flood zone is provided for waterfront land
        if ($this->input('is_waterfront') && !$this->input('flood_zone')) {
            $this->validator->errors()->add('flood_zone', 'يجب تحديد منطقة الفيضان للأرض على الماء');
        }

        // Check if utilities are provided for developed land
        if ($this->input('development_status') === 'completed' && empty($this->input('utilities_available'))) {
            $this->validator->errors()->add('utilities_available', 'يجب تحديد المرافق المتاحة للأرض المكتملة');
        }

        // Check if access roads are provided for accessible land
        if ($this->input('public_transport_access') && empty($this->input('access_roads'))) {
            $this->validator->errors()->add('access_roads', 'يجب تحديد طرق الوصول للأرض التي لها وصول للنقل العام');
        }
    }

    /**
     * Validate if coordinates are valid.
     */
    private function isValidCoordinates($coordinates): bool
    {
        // Basic coordinate validation - can be enhanced based on coordinate format
        if (empty($coordinates)) {
            return false;
        }

        // Check if it's a valid coordinate format (x,y,z or similar)
        $parts = explode(',', $coordinates);
        if (count($parts) < 2) {
            return false;
        }

        foreach ($parts as $part) {
            if (!is_numeric(trim($part))) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validate area for land type.
     */
    private function validateAreaForLandType($area, $landType): void
    {
        $minAreas = [
            'residential' => 100,
            'commercial' => 200,
            'industrial' => 500,
            'recreational' => 1000,
            'agricultural' => 5000,
            'institutional' => 300,
        ];

        $minArea = $minAreas[$landType] ?? 100;

        if ($area < $minArea) {
            $this->validator->errors()->add('area', "المساحة يجب أن تكون على الأقل {$minArea} متر مربع لنوع الأرض {$landType}");
        }
    }
}
