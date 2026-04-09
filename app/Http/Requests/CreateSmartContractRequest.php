<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateSmartContractRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'source_code' => 'required|string|min:10',
            'abi' => 'required|array|min:1',
            'bytecode' => 'required|string|min:10',
            'compiler_version' => 'required|string|max:50',
            'optimization' => 'required|boolean',
            'type' => 'required|string|in:erc20,erc721,erc1155,custom',
            'constructor_args' => 'nullable|array',
            'gas_limit' => 'nullable|integer|min:21000|max:10000000',
            'gas_price' => 'nullable|integer|min:1|max:1000',
            'value' => 'nullable|integer|min:0',
            'metadata' => 'nullable|array',
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
            'name.required' => 'اسم العقد مطلوب',
            'name.max' => 'اسم العقد يجب ألا يزيد عن 255 حرف',
            'description.max' => 'الوصف يجب ألا يزيد عن 2000 حرف',
            'source_code.required' => 'الكود المصدري مطلوب',
            'source_code.min' => 'الكود المصدري يجب أن يكون على الأقل 10 أحرف',
            'abi.required' => 'ABI مطلوب',
            'abi.min' => 'يجب توفير ABI على الأقل عنصر واحد',
            'bytecode.required' => 'البايت كود مطلوب',
            'bytecode.min' => 'البايت كود يجب أن يكون على الأقل 10 أحرف',
            'compiler_version.required' => 'إصدار المترجم مطلوب',
            'compiler_version.max' => 'إصدار المترجم يجب ألا يزيد عن 50 حرف',
            'optimization.required' => 'إعدادات التحسين مطلوبة',
            'type.required' => 'نوع العقد مطلوب',
            'type.in' => 'نوع العقد غير صالح',
            'gas_limit.min' => 'حد الغاز يجب أن يكون على الأقل 21000',
            'gas_limit.max' => 'حد الغاز يجب ألا يزيد عن 10000000',
            'gas_price.min' => 'سعر الغاز يجب أن يكون على الأقل 1',
            'gas_price.max' => 'سعر الغاز يجب ألا يزيد عن 1000',
            'value.min' => 'القيمة يجب أن تكون رقم موجب',
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
            'name' => 'اسم العقد',
            'description' => 'الوصف',
            'source_code' => 'الكود المصدري',
            'abi' => 'ABI',
            'bytecode' => 'البايت كود',
            'compiler_version' => 'إصدار المترجم',
            'optimization' => 'التحسين',
            'type' => 'نوع العقد',
            'constructor_args' => 'وسائط الباني',
            'gas_limit' => 'حد الغاز',
            'gas_price' => 'سعر الغاز',
            'value' => 'القيمة',
            'metadata' => 'البيانات الوصفية',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($validator->failed()) {
                return;
            }

            // Validate source code syntax
            $sourceCode = $this->input('source_code');
            if (!$this->isValidSolidityCode($sourceCode)) {
                $validator->errors()->add('source_code', 'الكود المصدري يحتوي على أخطاء صياغية');
            }

            // Validate ABI format
            $abi = $this->input('abi');
            if (!$this->isValidAbi($abi)) {
                $validator->errors()->add('abi', 'تنسيق ABI غير صالح');
            }

            // Validate bytecode format
            $bytecode = $this->input('bytecode');
            if (!$this->isValidBytecode($bytecode)) {
                $validator->errors()->add('bytecode', 'تنسيق البايت كود غير صالح');
            }
        });
    }

    /**
     * Validate Solidity code syntax.
     */
    private function isValidSolidityCode($code): bool
    {
        // Basic Solidity syntax validation
        return preg_match('/^(pragma solidity|contract|interface|library|function|modifier|event|struct|enum|error)/m', $code) === 1;
    }

    /**
     * Validate ABI format.
     */
    private function isValidAbi($abi): bool
    {
        if (!is_array($abi)) {
            return false;
        }

        foreach ($abi as $item) {
            if (!isset($item['type'], $item['name'])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validate bytecode format.
     */
    private function isValidBytecode($bytecode): bool
    {
        // Bytecode should be a hex string starting with 0x
        return preg_match('/^0x[a-fA-F0-9]*$/', $bytecode) === 1 && strlen($bytecode) > 2;
    }
}
