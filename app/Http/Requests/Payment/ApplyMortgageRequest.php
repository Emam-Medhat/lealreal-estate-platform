<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class ApplyMortgageRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'property_id' => 'nullable|exists:properties,id',
            'loan_type' => 'required|in:fixed,variable,adjustable,fha,va,conventional,jumbo',
            'loan_amount' => 'required|numeric|min:10000|max:10000000',
            'down_payment' => 'required|numeric|min:0|max:loan_amount',
            'down_payment_percentage' => 'required|numeric|min:0|max:100',
            'loan_term_years' => 'required|integer|between:5,40',
            'interest_rate' => 'required|numeric|min:0|max:30',
            'fixed_rate_period' => 'nullable|integer|between:1,30',
            'amortization_type' => 'required|in:standard,interest_only,balloon',
            'property_value' => 'required|numeric|min:10000|max:50000000',
            'property_address' => 'required|array',
            'property_address.street' => 'required|string|max:255',
            'property_address.city' => 'required|string|max:100',
            'property_address.state' => 'required|string|max:100',
            'property_address.postal_code' => 'required|string|max:20',
            'property_address.country' => 'required|string|size:2',
            'property_type' => 'required|in:single_family,multi_family,condo,townhouse,commercial',
            'property_use' => 'required|in:primary_residence,secondary_residence,investment_property',
            'borrower_income' => 'required|numeric|min:0|max:10000000',
            'borrower_employment' => 'required|array',
            'borrower_employment.employer' => 'required|string|max:255',
            'borrower_employment.position' => 'required|string|max:255',
            'borrower_employment.employment_type' => 'required|in:full_time,part_time,self_employed,retired',
            'borrower_employment.years_at_job' => 'required|integer|min:0|max:50',
            'borrower_employment.years_in_field' => 'required|integer|min:0|max:50',
            'borrower_employment.annual_income' => 'required|numeric|min:0|max:10000000',
            'borrower_credit_score' => 'required|integer|between:300,850',
            'borrower_debts' => 'required|numeric|min:0|max:10000000',
            'borrower_assets' => 'required|numeric|min:0|max:50000000',
            'co_borrower' => 'boolean',
            'co_borrower.first_name' => 'required_if:co_borrower,true|string|max:255',
            'co_borrower.last_name' => 'required_if:co_borrower,true|string|max:255',
            'co_borrower.email' => 'required_if:co_borrower,true|email|max:255',
            'co_borrower.phone' => 'required_if:co_borrower,true|string|max:20',
            'co_borrower.income' => 'required_if:co_borrower,true|numeric|min:0|max:10000000',
            'co_borrower.credit_score' => 'required_if:co_borrower,true|integer|between:300,850',
            'documents' => 'nullable|array',
            'documents.*.type' => 'required_with:documents|string|in:id_proof,income_proof,employment_proof,asset_proof,property_proof,credit_report',
            'documents.*.name' => 'required_with:documents|string|max:255',
            'documents.*.file' => 'required_with:documents|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'terms_accepted' => 'required|accepted',
            'privacy_accepted' => 'required|accepted',
            'consent_to_credit_check' => 'required|accepted',
            'contact_preferences' => 'nullable|array',
            'contact_preferences.email' => 'nullable|boolean',
            'contact_preferences.phone' => 'nullable|boolean',
            'contact_preferences.sms' => 'nullable|boolean',
            'contact_preferences.mail' => 'nullable|boolean',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages()
    {
        return [
            'loan_type.in' => 'Loan type must be one of: fixed, variable, adjustable, FHA, VA, conventional, jumbo',
            'loan_amount.min' => 'Loan amount must be at least $10,000',
            'loan_amount.max' => 'Loan amount cannot exceed $10,000,000',
            'down_payment.max' => 'Down payment cannot exceed loan amount',
            'loan_term_years.between' => 'Loan term must be between 5 and 40 years',
            'interest_rate.max' => 'Interest rate cannot exceed 30%',
            'amortization_type.in' => 'Amortization type must be one of: standard, interest only, balloon',
            'property_type.in' => 'Property type must be one of: single family, multi family, condo, townhouse, commercial',
            'property_use.in' => 'Property use must be one of: primary residence, secondary residence, investment property',
            'borrower_credit_score.between' => 'Credit score must be between 300 and 850',
            'co_borrower.first_name.required_if' => 'Co-borrower first name is required when co-borrower is enabled',
            'co_borrower.last_name.required_if' => 'Co-borrower last name is required when co-borrower is enabled',
            'co_borrower.email.required_if' => 'Co-borrower email is required when co-borrower is enabled',
            'co_borrower.phone.required_if' => 'Co-borrower phone is required when co-borrower is enabled',
            'co_borrower.income.required_if' => 'Co-borrower income is required when co-borrower is enabled',
            'co_borrower.credit_score.required_if' => 'Co-borrower credit score is required when co-borrower is enabled',
            'terms_accepted.required' => 'You must accept the terms and conditions',
            'privacy_accepted.required' => 'You must accept the privacy policy',
            'consent_to_credit_check.required' => 'You must consent to a credit check',
            'documents.*.file.mimes' => 'Documents must be PDF, Word, or image files',
            'documents.*.file.max' => 'Document files cannot exceed 10MB',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'loan_amount' => number_format($this->loan_amount, 2, '.', ''),
            'down_payment' => number_format($this->down_payment, 2, '.', ''),
            'property_value' => number_format($this->property_value, 2, '.', ''),
            'borrower_income' => number_format($this->borrower_income, 2, '.', ''),
            'borrower_debts' => number_format($this->borrower_debts, 2, '.', ''),
            'borrower_assets' => number_format($this->borrower_assets, 2, '.', ''),
            'co_borrower.income' => $this->co_borrower ? number_format($this->co_borrower['income'], 2, '.', '') : null,
        ]);
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validate LTV ratio
            if ($this->loan_amount && $this->property_value) {
                $ltvRatio = ($this->loan_amount / $this->property_value) * 100;
                
                if ($ltvRatio > 97) {
                    $validator->errors()->add('loan_amount', 'Loan-to-value ratio cannot exceed 97%');
                }
            }

            // Validate down payment percentage
            if ($this->down_payment && $this->property_value) {
                $downPaymentPercentage = ($this->down_payment / $this->property_value) * 100;
                
                if (abs($downPaymentPercentage - $this->down_payment_percentage) > 0.1) {
                    $validator->errors()->add('down_payment_percentage', 'Down payment percentage does not match calculated value');
                }
            }

            // Validate minimum down payment based on loan type
            if ($this->loan_type && $this->property_value) {
                $minDownPaymentPercentages = [
                    'conventional' => 5,
                    'fha' => 3.5,
                    'va' => 0,
                    'jumbo' => 10,
                ];

                $minPercentage = $minDownPaymentPercentages[$this->loan_type] ?? 5;
                $requiredDownPayment = ($this->property_value * $minPercentage) / 100;

                if ($this->down_payment < $requiredDownPayment) {
                    $validator->errors()->add('down_payment', "Minimum down payment for {$this->loan_type} loans is {$minPercentage}%");
                }
            }

            // Validate DTI ratio
            if ($this->borrower_income && $this->borrower_debts) {
                $monthlyIncome = $this->borrower_income / 12;
                $monthlyDebts = $this->borrower_debts / 12;
                
                // Estimate monthly payment (simplified)
                $monthlyRate = ($this->interest_rate / 100) / 12;
                $numPayments = $this->loan_term_years * 12;
                $loanPrincipal = $this->loan_amount - $this->down_payment;
                
                if ($monthlyRate > 0) {
                    $estimatedMonthlyPayment = $loanPrincipal * ($monthlyRate * pow(1 + $monthlyRate, $numPayments)) / 
                                            (pow(1 + $monthlyRate, $numPayments) - 1);
                } else {
                    $estimatedMonthlyPayment = $loanPrincipal / $numPayments;
                }
                
                $totalMonthlyDebts = $monthlyDebts + $estimatedMonthlyPayment;
                $dtiRatio = ($totalMonthlyDebts / $monthlyIncome) * 100;
                
                if ($dtiRatio > 43) {
                    $validator->errors()->add('borrower_debts', 'Debt-to-income ratio cannot exceed 43%');
                }
            }

            // Validate co-borrower credit score
            if ($this->co_borrower && $this->co_borrower['credit_score']) {
                if ($this->co_borrower['credit_score'] < 620) {
                    $validator->errors()->add('co_borrower.credit_score', 'Co-borrower credit score must be at least 620');
                }
            }

            // Validate property value vs loan amount
            if ($this->property_value && $this->loan_amount) {
                if ($this->loan_amount > $this->property_value) {
                    $validator->errors()->add('loan_amount', 'Loan amount cannot exceed property value');
                }
            }

            // Validate employment history
            if ($this->borrower_employment && $this->borrower_employment['years_at_job']) {
                if ($this->borrower_employment['years_at_job'] < 2) {
                    $validator->errors()->add('borrower_employment.years_at_job', 'Minimum 2 years at current job required');
                }
            }
        });
    }
}
