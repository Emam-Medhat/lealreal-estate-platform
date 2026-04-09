<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UpdateReportTemplateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Adjust based on your authorization logic
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $templateId = $this->route('template')->id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('report_templates', 'name')->ignore($templateId)
            ],
            'description' => 'nullable|string|max:1000',
            'category' => 'required|string|max:100',
            'template_type' => 'required|string|in:standard,custom,advanced',
            'configuration' => 'required|array',
            'configuration.data_sources' => 'required|array',
            'configuration.data_sources.*.type' => 'required|string|in:database,api,file,external',
            'configuration.data_sources.*.source' => 'required|string',
            'configuration.data_sources.*.query' => 'nullable|string',
            'configuration.fields' => 'required|array',
            'configuration.fields.*.name' => 'required|string|max:255',
            'configuration.fields.*.type' => 'required|string|in:text,number,date,boolean,image,table',
            'configuration.fields.*.label' => 'required|string|max:255',
            'configuration.fields.*.required' => 'boolean',
            'configuration.fields.*.default' => 'nullable',
            'parameters' => 'required|array',
            'parameters.*.name' => 'required|string|max:255',
            'parameters.*.type' => 'required|string|in:string,number,date,boolean,select,multiselect',
            'parameters.*.label' => 'required|string|max:255',
            'parameters.*.required' => 'boolean',
            'parameters.*.options' => 'required_if:parameters.*.type,select,multiselect|array',
            'parameters.*.validation' => 'nullable|array',
            'layout' => 'required|array',
            'layout.sections' => 'required|array',
            'layout.sections.*.name' => 'required|string|max:255',
            'layout.sections.*.type' => 'required|string|in:header,content,footer,sidebar',
            'layout.sections.*.order' => 'required|integer|min:0',
            'layout.sections.*.fields' => 'required|array',
            'layout.sections.*.fields.*.name' => 'required|string',
            'layout.sections.*.fields.*.order' => 'required|integer|min:0',
            'layout.sections.*.fields.*.width' => 'required|integer|min:1|max:12',
            'styles' => 'nullable|array',
            'styles.colors' => 'nullable|array',
            'styles.colors.primary' => 'nullable|string|max:7',
            'styles.colors.secondary' => 'nullable|string|max:7',
            'styles.colors.background' => 'nullable|string|max:7',
            'styles.colors.text' => 'nullable|string|max:7',
            'styles.fonts' => 'nullable|array',
            'styles.fonts.header_family' => 'nullable|string|max:100',
            'styles.fonts.body_family' => 'nullable|string|max:100',
            'styles.fonts.header_size' => 'nullable|integer|min:8|max:72',
            'styles.fonts.body_size' => 'nullable|integer|min:8|max:72',
            'styles.spacing' => 'nullable|array',
            'styles.spacing.margin' => 'nullable|integer|min:0',
            'styles.spacing.padding' => 'nullable|integer|min:0',
            'styles.borders' => 'nullable|array',
            'styles.borders.width' => 'nullable|integer|min:0',
            'styles.borders.color' => 'nullable|string|max:7',
            'styles.borders.radius' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'اسم القالب مطلوب',
            'name.unique' => 'اسم القالب مستخدم بالفعل',
            'name.max' => 'اسم القالب يجب ألا يتجاوز 255 حرفًا',
            'description.max' => 'الوصف يجب ألا يتجاوز 1000 حرف',
            'category.required' => 'فئة القالب مطلوبة',
            'template_type.required' => 'نوع القالب مطلوب',
            'template_type.in' => 'نوع القالب يجب أن يكون: standard, custom, أو advanced',
            'configuration.required' => 'تكوين القالب مطلوب',
            'configuration.data_sources.required' => 'مصادر البيانات مطلوبة',
            'configuration.fields.required' => 'حقول التقرير مطلوبة',
            'parameters.required' => 'معلمات القالب مطلوبة',
            'layout.required' => 'تخطيط القالب مطلوب',
            'layout.sections.required' => 'أقسام التخطيط مطلوبة',
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
            'name' => 'اسم القالب',
            'description' => 'الوصف',
            'category' => 'الفئة',
            'template_type' => 'نوع القالب',
            'configuration' => 'التكوين',
            'parameters' => 'المعلمات',
            'layout' => 'التخطيط',
            'styles' => 'الأنماط',
            'is_active' => 'الحالة',
        ];
    }
}
