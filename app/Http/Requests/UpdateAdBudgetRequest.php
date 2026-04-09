<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateAdBudgetRequest extends FormRequest
{
    public function authorize()
    {
        $budget = $this->route('budget');
        return Auth::check() && (
            Auth::id() === $budget->campaign->user_id || // Campaign owner
            Auth::user()->role === 'admin' // Admin
        );
    }

    public function rules()
    {
        return [
            'total_budget' => 'nullable|numeric|min:10',
            'daily_budget' => 'nullable|numeric|min:1',
            'alert_threshold' => 'nullable|numeric|min:1|max:100',
            'spending_limit' => 'nullable|numeric|min:1',
            'limit_type' => 'nullable|in:daily,weekly,monthly,total',
            'auto_renew' => 'nullable|boolean',
            'renewal_amount' => 'nullable|numeric|min:1',
            'renewal_trigger' => 'nullable|in:exhausted,below_threshold',
            'delivery_method' => 'nullable|in:standard,accelerated',
            'budget_type' => 'nullable|in:standard,accelerated,limited',
            'action' => 'required|in:update,add_funds,adjust_daily,set_limit,pause,resume'
        ];
    }

    public function messages()
    {
        return [
            'total_budget.min' => 'الميزانية الإجمالية يجب أن تكون على الأقل 10 ريال',
            'daily_budget.min' => 'الميزانية اليومية يجب أن تكون على الأقل 1 ريال',
            'alert_threshold.min' => 'عتبة التنبيه يجب أن تكون بين 1 و 100',
            'alert_threshold.max' => 'عتبة التنبيه يجب أن تكون بين 1 و 100',
            'spending_limit.min' => 'حد الإنفاق يجب أن يكون على الأقل 1 ريال',
            'limit_type.in' => 'نوع الحد غير صالح',
            'renewal_amount.min' => 'مبلغ التجديد يجب أن يكون على الأقل 1 ريال',
            'renewal_trigger.in' => 'محفز التجديد غير صالح',
            'delivery_method.in' => 'طريقة التسليم غير صالحة',
            'budget_type.in' => 'نوع الميزانية غير صالح',
            'action.required' => 'حقل الإجراء مطلوب',
            'action.in' => 'الإجراء غير صالح'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $budget = $this->route('budget');
            $action = $this->input('action');
            
            // Action-specific validations
            switch ($action) {
                case 'add_funds':
                    $amount = $this->input('amount');
                    if (!$amount || $amount < 1) {
                        $validator->errors()->add('amount', 'المبلغ يجب أن يكون على الأقل 1 ريال');
                    }
                    if ($amount && $amount > 10000) {
                        $validator->errors()->add('amount', 'الحد الأقصى للإضافة هو 10,000 ريال');
                    }
                    break;
                    
                case 'adjust_daily':
                    $dailyBudget = $this->input('daily_budget');
                    if (!$dailyBudget) {
                        $validator->errors()->add('daily_budget', 'الميزانية اليومية مطلوبة لهذا الإجراء');
                    }
                    if ($dailyBudget && $budget->remaining_budget < $dailyBudget) {
                        $validator->errors()->add('daily_budget', 'الميزانية اليومية تتجاوز الميزانية المتبقية');
                    }
                    break;
                    
                case 'set_limit':
                    $spendingLimit = $this->input('spending_limit');
                    $limitType = $this->input('limit_type');
                    
                    if (!$spendingLimit) {
                        $validator->errors()->add('spending_limit', 'حد الإنفاق مطلوب لهذا الإجراء');
                    }
                    if (!$limitType) {
                        $validator->errors()->add('limit_type', 'نوع الحد مطلوب لهذا الإجراء');
                    }
                    
                    if ($spendingLimit && $limitType === 'total' && $spendingLimit > $budget->total_budget) {
                        $validator->errors()->add('spending_limit', 'حد الإنفاق الإجمالي يجب ألا يتجاوز الميزانية الإجمالية');
                    }
                    break;
            }
            
            // Validate budget status for certain actions
            if (in_array($action, ['pause', 'resume']) && $budget->status === 'exhausted') {
                $validator->errors()->add('action', 'لا يمكن إيقاف أو استئناف ميزانية منتهية');
            }
            
            // Validate renewal settings
            $autoRenew = $this->input('auto_renew');
            $renewalAmount = $this->input('renewal_amount');
            
            if ($autoRenew && !$renewalAmount && $action === 'update') {
                $validator->errors()->add('renewal_amount', 'مبلغ التجديد مطلوب عند تفعيل التجديد التلقائي');
            }
        });
    }
}
