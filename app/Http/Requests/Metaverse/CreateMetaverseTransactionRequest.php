<?php

namespace App\Http\Requests\Metaverse;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class CreateMetaverseTransactionRequest extends FormRequest
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
            'type' => 'required|in:property_purchase,property_sale,property_offer,land_purchase,land_sale,land_offer,nft_purchase,nft_sale,nft_bid,nft_transfer,tour_booking,event_ticket,showroom_rental,service_fee,subscription,royalty_payment,refund,donation',
            'metaverse_property_id' => 'nullable|exists:metaverse_properties,id',
            'virtual_land_id' => 'nullable|exists:virtual_lands,id',
            'metaverse_property_nft_id' => 'nullable|exists:metaverse_property_nfts,id',
            'metaverse_showroom_id' => 'nullable|exists:metaverse_showrooms,id',
            'virtual_property_tour_id' => 'nullable|exists:virtual_property_tours,id',
            'buyer_id' => 'nullable|exists:users,id',
            'seller_id' => 'nullable|exists:users,id',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|max:10|in:USD,EUR,GBP,ETH,BTC,USDT',
            'status' => 'required|in:pending,confirmed,completed,failed,cancelled,refunded,disputed,investigating,suspended,deleted',
            'payment_method' => 'nullable|string|max:50|in:crypto,credit_card,debit_card,bank_transfer,paypal,stripe,wallet,cash,check',
            'transaction_hash' => 'nullable|string|max:255',
            'blockchain' => 'nullable|string|max:50|in:ethereum,polygon,binance_smart_chain,solana,avalanche,bitcoin,cardano,polkadot',
            'gas_fee' => 'nullable|numeric|min:0',
            'confirmation_count' => 'nullable|integer|min:0',
            'confirmed_at' => 'nullable|date',
            'completed_at' => 'nullable|date',
            'failed_at' => 'nullable|date',
            'cancelled_at' => 'nullable|date',
            'refund_amount' => 'nullable|numeric|min:0',
            'refund_reason' => 'nullable|string|max:1000',
            'refund_processed_at' => 'nullable|date',
            'notes' => 'nullable|string|max:2000',
            'message' => 'nullable|string|max:1000',
            'metadata' => 'nullable|array',
            'additional_data' => 'nullable|array',
            'verification_status' => 'required|in:pending,verified,failed,flagged,suspended,rejected',
            'verified_at' => 'nullable|date',
            'fraud_score' => 'nullable|numeric|min:0|max:100',
            'risk_level' => 'required|in:low,medium,high,critical',
            'compliance_status' => 'required|in:compliant,non_compliant,under_review,flagged,suspended',
            'tax_amount' => 'nullable|numeric|min:0',
            'tax_currency' => 'nullable|string|max:10|in:USD,EUR,GBP,ETH,BTC,USDT',
            'fee_amount' => 'nullable|numeric|min:0',
            'fee_currency' => 'nullable|string|max:10|in:USD,EUR,GBP,ETH,BTC,USDT',
            'net_amount' => 'nullable|numeric|min:0',
            'net_currency' => 'nullable|string|max:10|in:USD,EUR,GBP,ETH,BTC,USDT',
            'documents' => 'nullable|array',
            'documents.*' => 'file|mimes:pdf,doc,docx,jpg,png|max:10240',
            'receipts' => 'nullable|array',
            'receipts.*' => 'file|mimes:pdf,jpg,png|max:10240',
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
            'type.required' => 'نوع المعاملة مطلوب',
            'type.in' => 'نوع المعاملة غير صحيح',
            'metaverse_property_id.exists' => 'العقار غير موجود',
            'virtual_land_id.exists' => 'الأرض غير موجودة',
            'metaverse_property_nft_id.exists' => 'NFT غير موجود',
            'metaverse_showroom_id.exists' => 'صالة العرض غير موجودة',
            'virtual_property_tour_id.exists' => 'الجولة غير موجودة',
            'buyer_id.exists' => 'المشتري غير موجود',
            'seller_id.exists' => 'البائع غير موجود',
            'amount.required' => 'المبلغ مطلوب',
            'amount.min' => 'المبلغ يجب أن يكون رقم موجب',
            'currency.required' => 'العملة مطلوبة',
            'currency.in' => 'العملة غير مدعومة',
            'status.required' => 'الحالة مطلوبة',
            'status.in' => 'الحالة غير صحيحة',
            'payment_method.in' => 'طريقة الدفع غير مدعومة',
            'blockchain.in' => 'البلوك تشين غير مدعوم',
            'gas_fee.min' => 'رسوم الغاز يجب أن تكون رقم موجب',
            'confirmation_count.min' => 'عدد التأكيدات يجب أن يكون رقم موجب',
            'refund_amount.min' => 'مبلغ الاسترداد يجب أن يكون رقم موجب',
            'refund_reason.max' => 'سبب الاسترداد يجب أن لا يتجاوز 1000 حرفاً',
            'notes.max' => 'الملاحظات يجب أن لا تتجاوز 2000 حرفاً',
            'message.max' => 'الرسالة يجب أن لا تتجاوز 1000 حرفاً',
            'verification_status.required' => 'حالة التحقق مطلوبة',
            'verification_status.in' => 'حالة التحقق غير صحيحة',
            'fraud_score.min' => 'درجة الاحتيال يجب أن تكون بين 0 و 100',
            'fraud_score.max' => 'درجة الاحتيال يجب أن تكون بين 0 و 100',
            'risk_level.required' => 'مستوى المخاطرة مطلوب',
            'risk_level.in' => 'مستوى المخاطرة غير صحيح',
            'compliance_status.required' => 'حالة الامتثال مطلوبة',
            'compliance_status.in' => 'حالة الامتثال غير صحيحة',
            'tax_amount.min' => 'مبلغ الضريبة يجب أن يكون رقم موجب',
            'fee_amount.min' => 'مبلغ الرسوم يجب أن يكون رقم موجب',
            'net_amount.min' => 'المبلغ الصافي يجب أن يكون رقم موجب',
            'documents.array' => 'المستندات يجب أن يكون مصفوفاً',
            'documents.*.0' => 'ملف المستند يجب أن يكون صالحاً',
            'documents.*.0.mimes' => 'تنسيق الملف غير مدعوم',
            'receipts.array' => 'الإيصالات يجب أن يكون مصفوفاً',
            'receipts.*.0' => 'ملف الإيصال يجب أن يكون صالحاً',
            'receipts.*.0.mimes' => 'تنسيق الملف غير مدعوم',
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
            'type' => 'نوع المعاملة',
            'metaverse_property_id' => 'العقار',
            'virtual_land_id' => 'الأرض',
            'metaverse_property_nft_id' => 'NFT',
            'metaverse_showroom_id' => 'صالة العرض',
            'virtual_property_tour_id' => 'الجولة',
            'buyer_id' => 'المشتري',
            'seller_id' => 'البائع',
            'amount' => 'المبلغ',
            'currency' => 'العملة',
            'status' => 'الحالة',
            'payment_method' => 'طريقة الدفع',
            'transaction_hash' => 'هاش المعاملة',
            'blockchain' => 'البلوك تشين',
            'gas_fee' => 'رسوم الغاز',
            'confirmation_count' => 'عدد التأكيدات',
            'confirmed_at' => 'تاريخ التأكيد',
            'completed_at' => 'تاريخ الإكمال',
            'failed_at' => 'تاريخ الفشل',
            'cancelled_at' => 'تاريخ الإلغاء',
            'refund_amount' => 'مبلغ الاسترداد',
            'refund_reason' => 'سبب الاسترداد',
            'refund_processed_at' => 'تاريخ معالجة الاسترداد',
            'notes' => 'الملاحظات',
            'message' => 'الرسالة',
            'metadata' => 'البيانات الوصفية',
            'additional_data' => 'البيانات الإضافية',
            'verification_status' => 'حالة التحقق',
            'verified_at' => 'تاريخ التحقق',
            'fraud_score' => 'درجة الاحتيال',
            'risk_level' => 'مستوى المخاطرة',
            'compliance_status' => 'حالة الامتثال',
            'tax_amount' => 'مبلغ الضريبة',
            'tax_currency' => 'عملة الضريبة',
            'fee_amount' => 'مبلغ الرسوم',
            'fee_currency' => 'عملة الرسوم',
            'net_amount' => 'المبلغ الصافي',
            'net_currency' => 'عملة المبلغ الصافي',
            'documents' => 'المستندات',
            'receipts' => 'الإيصالات',
            'created_by' => 'المستخدم',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'amount' => $this->formatAmount($this->input('amount')),
            'gas_fee' => $this->formatAmount($this->input('gas_fee')),
            'refund_amount' => $this->formatAmount($this->input('refund_amount')),
            'tax_amount' => $this->formatAmount($this->input('tax_amount')),
            'fee_amount' => $this->formatAmount($this->input('fee_amount')),
            'net_amount' => $this->formatAmount($this->input('net_amount')),
            'created_by' => auth()->id(),
        ]);
    }

    /**
     * Format amount to decimal.
     */
    private function formatAmount($amount): float
    {
        return (float) str_replace(',', '', $amount);
    }

    /**
     * Get the after validation hook.
     */
    public function after()
    {
        $transactionType = $this->input('type');
        $amount = $this->input('amount');
        $currency = $this->input('currency');

        // Validate transaction type specific requirements
        $this->validateTransactionTypeRequirements($transactionType);

        // Validate amount
        if ($amount <= 0) {
            $this->validator->errors()->add('amount', 'المبلغ يجب أن يكون أكبر من صفر');
        }

        // Validate buyer and seller
        $this->validateBuyerSeller();

        // Validate asset ownership
        $this->validateAssetOwnership();

        // Validate blockchain transactions
        $this->validateBlockchainTransaction();

        // Validate payment method
        $this->validatePaymentMethod();

        // Validate fees and taxes
        $this->validateFeesAndTaxes();

        // Validate risk assessment
        $this->validateRiskAssessment();

        // Validate compliance
        $this->validateCompliance();

        // Validate transaction hash for blockchain transactions
        $this->validateTransactionHash();

        // Validate dates
        $this->validateDates();

        // Validate documents
        $this->validateDocuments();
    }

    /**
     * Validate transaction type specific requirements.
     */
    private function validateTransactionTypeRequirements($type): void
    {
        switch ($type) {
            case 'property_purchase':
            case 'property_sale':
                if (!$this->input('metaverse_property_id')) {
                    $this->validator->errors()->add('metaverse_property_id', 'العقار مطلوب لهذه المعاملة');
                }
                break;

            case 'land_purchase':
            case 'land_sale':
                if (!$this->input('virtual_land_id')) {
                    $this->validator->errors()->add('virtual_land_id', 'الأرض مطلوبة لهذه المعاملة');
                }
                break;

            case 'nft_purchase':
            case 'nft_sale':
            case 'nft_bid':
                if (!$this->input('metaverse_property_nft_id')) {
                    $this->validator->errors()->add('metaverse_property_nft_id', 'NFT مطلوب لهذه المعاملة');
                }
                break;

            case 'tour_booking':
                if (!$this->input('virtual_property_tour_id')) {
                    $this->validator->errors()->add('virtual_property_tour_id', 'الجولة مطلوبة لهذه المعاملة');
                }
                break;

            case 'refund':
                if (!$this->input('refund_reason')) {
                    $this->validator->errors()->add('refund_reason', 'سبب الاسترداد مطلوب للمعاملات المستردة');
                }
                break;
        }
    }

    /**
     * Validate buyer and seller.
     */
    private function validateBuyerSeller(): void
    {
        $buyerId = $this->input('buyer_id');
        $sellerId = $this->input('seller_id');
        $type = $this->input('type');

        // Check if buyer and seller are different
        if ($buyerId && $sellerId && $buyerId === $sellerId) {
            $this->validator->errors()->add('buyer_id', 'المشتري والبائع يجب أن يكونا مختلفين');
        }

        // Check if buyer is required for purchase transactions
        if (in_array($type, ['property_purchase', 'land_purchase', 'nft_purchase', 'tour_booking']) && !$buyerId) {
            $this->validator->errors()->add('buyer_id', 'المشتري مطلوب لهذه المعاملة');
        }

        // Check if seller is required for sale transactions
        if (in_array($type, ['property_sale', 'land_sale', 'nft_sale']) && !$sellerId) {
            $this->validator->errors()->add('seller_id', 'البائع مطلوب لهذه المعاملة');
        }
    }

    /**
     * Validate asset ownership.
     */
    private function validateAssetOwnership(): void
    {
        $type = $this->input('type');
        $sellerId = $this->input('seller_id');

        if (in_array($type, ['property_sale', 'land_sale', 'nft_sale']) && $sellerId) {
            // Check if seller owns the asset
            switch ($type) {
                case 'property_sale':
                    $property = \App\Models\Metaverse\MetaverseProperty::find($this->input('metaverse_property_id'));
                    if ($property && $property->owner_id !== $sellerId) {
                        $this->validator->errors()->add('seller_id', 'البائع لا يملك هذا العقار');
                    }
                    break;

                case 'land_sale':
                    $land = \App\Models\Metaverse\VirtualLand::find($this->input('virtual_land_id'));
                    if ($land && $land->owner_id !== $sellerId) {
                        $this->validator->errors()->add('seller_id', 'البائع لا يملك هذه الأرض');
                    }
                    break;

                case 'nft_sale':
                    $nft = \App\Models\Metaverse\MetaversePropertyNft::find($this->input('metaverse_property_nft_id'));
                    if ($nft && $nft->owner_id !== $sellerId) {
                        $this->validator->errors()->add('seller_id', 'البائع لا يملك هذا NFT');
                    }
                    break;
            }
        }
    }

    /**
     * Validate blockchain transaction.
     */
    private function validateBlockchainTransaction(): void
    {
        $blockchain = $this->input('blockchain');
        $transactionHash = $this->input('transaction_hash');
        $paymentMethod = $this->input('payment_method');

        if ($blockchain && !$transactionHash) {
            $this->validator->errors()->add('transaction_hash', 'هاش المعاملة مطلوب للمعاملات على البلوك تشين');
        }

        if ($paymentMethod === 'crypto' && !$blockchain) {
            $this->validator->errors()->add('blockchain', 'البلوك تشين مطلوب للمعاملات بالعملات المشفرة');
        }

        if ($transactionHash && !$this->isValidTransactionHash($transactionHash)) {
            $this->validator->errors()->add('transaction_hash', 'هاش المعاملة غير صالح');
        }
    }

    /**
     * Validate payment method.
     */
    private function validatePaymentMethod(): void
    {
        $paymentMethod = $this->input('payment_method');
        $amount = $this->input('amount');
        $currency = $this->input('currency');

        if ($paymentMethod === 'crypto' && !in_array($currency, ['ETH', 'BTC', 'USDT'])) {
            $this->validator->errors()->add('currency', 'يجب استخدام عملة مشفرة للدفع بالعملات المشفرة');
        }

        if ($paymentMethod === 'credit_card' && $amount > 10000) {
            $this->validator->errors()->add('amount', 'المبلغ مرتفع جداً للدفع ببطاقة الائتمان');
        }
    }

    /**
     * Validate fees and taxes.
     */
    private function validateFeesAndTaxes(): void
    {
        $amount = $this->input('amount');
        $feeAmount = $this->input('fee_amount');
        $taxAmount = $this->input('tax_amount');

        if ($feeAmount && $feeAmount > $amount) {
            $this->validator->errors()->add('fee_amount', 'مبلغ الرسوم لا يمكن أن يتجاوز المبلغ الإجمالي');
        }

        if ($taxAmount && $taxAmount > $amount) {
            $this->validator->errors()->add('tax_amount', 'مبلغ الضريبة لا يمكن أن يتجاوز المبلغ الإجمالي');
        }

        if ($feeAmount && $taxAmount && ($feeAmount + $taxAmount) > $amount) {
            $this->validator->errors()->add('fee_amount', 'مجموع الرسوم والضرائب لا يمكن أن يتجاوز المبلغ الإجمالي');
        }
    }

    /**
     * Validate risk assessment.
     */
    private function validateRiskAssessment(): void
    {
        $fraudScore = $this->input('fraud_score');
        $riskLevel = $this->input('risk_level');
        $amount = $this->input('amount');

        if ($fraudScore !== null) {
            if ($fraudScore < 0 || $fraudScore > 100) {
                $this->validator->errors()->add('fraud_score', 'درجة الاحتيال يجب أن تكون بين 0 و 100');
            }

            // Check if risk level matches fraud score
            if ($fraudScore >= 80 && $riskLevel !== 'critical') {
                $this->validator->errors()->add('risk_level', 'مستوى المخاطرة يجب أن يكون حرجاً مع درجة احتيال عالية');
            } elseif ($fraudScore >= 60 && $riskLevel === 'low') {
                $this->validator->errors()->add('risk_level', 'مستوى المخاطرة يجب أن يكون مرتفعاً مع درجة احتيال متوسطة');
            }
        }

        // High amount transactions should have risk assessment
        if ($amount > 50000 && !$fraudScore) {
            $this->validator->errors()->add('fraud_score', 'تقييم المخاطرة مطلوب للمعاملات الكبيرة');
        }
    }

    /**
     * Validate compliance.
     */
    private function validateCompliance(): void
    {
        $amount = $this->input('amount');
        $complianceStatus = $this->input('compliance_status');

        // High value transactions should be compliant
        if ($amount > 100000 && $complianceStatus !== 'compliant') {
            $this->validator->errors()->add('compliance_status', 'المعاملات الكبيرة يجب أن تكون متوافقة');
        }

        // International transactions should be under review
        $buyerId = $this->input('buyer_id');
        $sellerId = $this->input('seller_id');

        if ($buyerId && $sellerId) {
            $buyer = \App\Models\User::find($buyerId);
            $seller = \App\Models\User::find($sellerId);

            if ($buyer && $seller && $buyer->country !== $seller->country) {
                if ($complianceStatus === 'compliant') {
                    $this->validator->errors()->add('compliance_status', 'المعاملات الدولية يجب أن تكون تحت المراجعة');
                }
            }
        }
    }

    /**
     * Validate transaction hash.
     */
    private function validateTransactionHash(): void
    {
        $transactionHash = $this->input('transaction_hash');
        $blockchain = $this->input('blockchain');

        if ($transactionHash && $blockchain) {
            // Check if transaction hash format matches blockchain
            if (!$this->isValidTransactionHashForBlockchain($transactionHash, $blockchain)) {
                $this->validator->errors()->add('transaction_hash', 'هاش المعاملة لا يتوافق مع البلوك تشين المحدد');
            }
        }
    }

    /**
     * Validate dates.
     */
    private function validateDates(): void
    {
        $confirmedAt = $this->input('confirmed_at');
        $completedAt = $this->input('completed_at');
        $failedAt = $this->input('failed_at');
        $cancelledAt = $this->input('cancelled_at');

        // Check if completion date is after confirmation date
        if ($confirmedAt && $completedAt && $completedAt < $confirmedAt) {
            $this->validator->errors()->add('completed_at', 'تاريخ الإكمال يجب أن يكون بعد تاريخ التأكيد');
        }

        // Check if dates are in the future
        $now = now();
        if ($confirmedAt && $confirmedAt > $now) {
            $this->validator->errors()->add('confirmed_at', 'تاريخ التأكيد لا يمكن أن يكون في المستقبل');
        }
    }

    /**
     * Validate documents.
     */
    private function validateDocuments(): void
    {
        $documents = $this->input('documents', []);
        $receipts = $this->input('receipts', []);

        // Check total file size
        $totalSize = 0;
        foreach ($documents as $document) {
            if ($document && $document->getSize()) {
                $totalSize += $document->getSize();
            }
        }

        foreach ($receipts as $receipt) {
            if ($receipt && $receipt->getSize()) {
                $totalSize += $receipt->getSize();
            }
        }

        if ($totalSize > 50 * 1024 * 1024) { // 50MB
            $this->validator->errors()->add('documents', 'حجم الملفات الإجمالي يجب أن لا يتجاوز 50 ميجابايت');
        }
    }

    /**
     * Check if transaction hash is valid.
     */
    private function isValidTransactionHash($hash): bool
    {
        return preg_match('/^0x[a-fA-F0-9]{64}$/', $hash);
    }

    /**
     * Check if transaction hash is valid for blockchain.
     */
    private function isValidTransactionHashForBlockchain($hash, $blockchain): bool
    {
        switch ($blockchain) {
            case 'ethereum':
            case 'polygon':
            case 'binance_smart_chain':
                return preg_match('/^0x[a-fA-F0-9]{64}$/', $hash);
            case 'bitcoin':
                return preg_match('/^[a-fA-F0-9]{64}$/', $hash);
            case 'solana':
                return preg_match('/^[a-fA-F0-9]{88}$/', $hash);
            default:
                return true;
        }
    }
}
