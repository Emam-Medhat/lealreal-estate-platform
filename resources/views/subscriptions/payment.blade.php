@extends('layouts.app')

@section('title', 'Complete Payment')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center mb-4">
                <a href="{{ route('subscriptions.show', $subscription) }}" class="text-gray-600 hover:text-gray-800 mr-3">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Complete Payment</h1>
                    <p class="text-gray-600">Complete your subscription payment to activate your plan</p>
                </div>
            </div>
        </div>

        <!-- Subscription Summary -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Subscription Summary</h2>
            
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="bg-blue-100 rounded-full p-3 mr-4">
                        <i class="fas fa-box text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">{{ $subscription->plan->name }}</h3>
                        <p class="text-gray-600">{{ $subscription->plan->description }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-gray-800">${{ number_format($subscription->amount, 2) }}</div>
                    <div class="text-sm text-gray-600">{{ $subscription->billing_cycle_unit }}</div>
                </div>
            </div>
            
            <div class="border-t pt-4">
                <div class="flex justify-between text-sm text-gray-600 mb-2">
                    <span>Subtotal</span>
                    <span>${{ number_format($subscription->amount, 2) }}</span>
                </div>
                <div class="flex justify-between text-sm text-gray-600 mb-2">
                    <span>Tax</span>
                    <span>$0.00</span>
                </div>
                <div class="flex justify-between font-semibold text-gray-800 pt-2 border-t">
                    <span>Total</span>
                    <span>${{ number_format($subscription->amount, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Payment Methods -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-6">Select Payment Method</h2>
            
            <div class="space-y-4">
                <!-- Credit Card -->
                <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                    <input type="radio" name="payment_method" value="stripe" class="mr-4" checked>
                    <div class="flex items-center flex-1">
                        <i class="fas fa-credit-card text-blue-600 text-xl mr-3"></i>
                        <div>
                            <div class="font-medium text-gray-800">Credit Card</div>
                            <div class="text-sm text-gray-600">Pay with Visa, Mastercard, or American Express</div>
                        </div>
                    </div>
                </label>
                
                <!-- PayPal -->
                <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                    <input type="radio" name="payment_method" value="paypal" class="mr-4">
                    <div class="flex items-center flex-1">
                        <i class="fab fa-paypal text-blue-600 text-xl mr-3"></i>
                        <div>
                            <div class="font-medium text-gray-800">PayPal</div>
                            <div class="text-sm text-gray-600">Pay with your PayPal account</div>
                        </div>
                    </div>
                </label>
                
                <!-- Bank Transfer -->
                <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                    <input type="radio" name="payment_method" value="bank_transfer" class="mr-4">
                    <div class="flex items-center flex-1">
                        <i class="fas fa-university text-blue-600 text-xl mr-3"></i>
                        <div>
                            <div class="font-medium text-gray-800">Bank Transfer</div>
                            <div class="text-sm text-gray-600">Direct bank transfer (2-3 business days)</div>
                        </div>
                    </div>
                </label>
            </div>
        </div>

        <!-- Credit Card Form (shown when credit card is selected) -->
        <div id="creditCardForm" class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-6">Credit Card Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Card Number</label>
                    <input type="text" id="cardNumber" placeholder="1234 5678 9012 3456" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Expiry Date</label>
                    <input type="text" id="cardExpiry" placeholder="MM/YY" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">CVV</label>
                    <input type="text" id="cardCvv" placeholder="123" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cardholder Name</label>
                    <input type="text" id="cardName" placeholder="John Doe" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>

        <!-- Billing Address -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-6">Billing Address</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Street Address</label>
                    <input type="text" placeholder="123 Main St" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                    <input type="text" placeholder="New York" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">State/Province</label>
                    <input type="text" placeholder="NY" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">ZIP/Postal Code</label>
                    <input type="text" placeholder="10001" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Country</option>
                        <option value="US">United States</option>
                        <option value="CA">Canada</option>
                        <option value="UK">United Kingdom</option>
                        <!-- Add more countries as needed -->
                    </select>
                </div>
            </div>
        </div>

        <!-- Terms and Submit -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" class="mr-2" required>
                    <span class="text-sm text-gray-700">
                        I agree to the <a href="#" class="text-blue-600 hover:text-blue-800">Terms and Conditions</a> 
                        and <a href="#" class="text-blue-600 hover:text-blue-800">Privacy Policy</a>
                    </span>
                </label>
            </div>
            
            <div class="flex justify-between items-center">
                <a href="{{ route('subscriptions.show', $subscription) }}" class="text-gray-600 hover:text-gray-800">
                    Cancel
                </a>
                <button onclick="processPayment()" class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    <i class="fas fa-lock mr-2"></i>
                    Complete Payment - ${{ number_format($subscription->amount, 2) }}
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Payment method selection
document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const creditCardForm = document.getElementById('creditCardForm');
        if (this.value === 'stripe') {
            creditCardForm.style.display = 'block';
        } else {
            creditCardForm.style.display = 'none';
        }
    });
});

// Format card number
document.getElementById('cardNumber').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\s/g, '');
    let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
    e.target.value = formattedValue;
});

// Format expiry date
document.getElementById('cardExpiry').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length >= 2) {
        value = value.slice(0, 2) + '/' + value.slice(2, 4);
    }
    e.target.value = value;
});

// Process payment
function processPayment() {
    const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
    
    // Validate form
    if (paymentMethod === 'stripe') {
        const cardNumber = document.getElementById('cardNumber').value;
        const cardExpiry = document.getElementById('cardExpiry').value;
        const cardCvv = document.getElementById('cardCvv').value;
        const cardName = document.getElementById('cardName').value;
        
        if (!cardNumber || !cardExpiry || !cardCvv || !cardName) {
            alert('Please fill in all credit card fields');
            return;
        }
    }
    
    // Submit payment
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/subscriptions/{{ $subscription->id }}/process-payment';
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    form.appendChild(csrfToken);
    
    const paymentMethodInput = document.createElement('input');
    paymentMethodInput.type = 'hidden';
    paymentMethodInput.name = 'payment_method';
    paymentMethodInput.value = paymentMethod;
    form.appendChild(paymentMethodInput);
    
    document.body.appendChild(form);
    form.submit();
}
</script>
@endsection
