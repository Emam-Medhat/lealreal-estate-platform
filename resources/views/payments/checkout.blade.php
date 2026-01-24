@extends('layouts.app')

@section('title', 'Payment Checkout')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Payment Checkout</h1>
            <p class="text-gray-600">Complete your payment securely</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Payment Form -->
            <div class="lg:col-span-2">
                <form id="payment-form" class="space-y-6">
                    <!-- Order Summary -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Order Summary</h2>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Subtotal</span>
                                <span class="font-medium">${{ number_format($amount, 2) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Tax</span>
                                <span class="font-medium">${{ number_format($amount * 0.08, 2) }}</span>
                            </div>
                            <div class="border-t pt-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-semibold text-gray-800">Total</span>
                                    <span class="text-lg font-bold text-green-600">${{ number_format($amount * 1.08, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method Selection -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Payment Method</h2>
                        
                        <div class="space-y-3">
                            <!-- Credit Card -->
                            <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                <input type="radio" name="payment_method" value="card" class="mr-3" checked>
                                <div class="flex-1">
                                    <div class="flex items-center">
                                        <i class="fas fa-credit-card text-blue-600 mr-3"></i>
                                        <span class="font-medium">Credit Card</span>
                                    </div>
                                </div>
                            </label>

                            <!-- Bank Transfer -->
                            <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                <input type="radio" name="payment_method" value="bank" class="mr-3">
                                <div class="flex-1">
                                    <div class="flex items-center">
                                        <i class="fas fa-university text-green-600 mr-3"></i>
                                        <span class="font-medium">Bank Transfer</span>
                                    </div>
                                </div>
                            </label>

                            <!-- Cryptocurrency -->
                            <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                <input type="radio" name="payment_method" value="crypto" class="mr-3">
                                <div class="flex-1">
                                    <div class="flex items-center">
                                        <i class="fab fa-bitcoin text-orange-500 mr-3"></i>
                                        <span class="font-medium">Cryptocurrency</span>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Card Details (shown when card is selected) -->
                    <div id="card-details" class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Card Details</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Card Number</label>
                                <input type="text" id="card-number" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="1234 5678 9012 3456" maxlength="19">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Expiry Date</label>
                                <input type="text" id="card-expiry" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="MM/YY" maxlength="5">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">CVV</label>
                                <input type="text" id="card-cvv" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="123" maxlength="4">
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Cardholder Name</label>
                            <input type="text" id="card-holder" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="John Doe">
                        </div>
                    </div>

                    <!-- Billing Information -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Billing Information</h2>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                                <input type="email" id="billing-email" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="john@example.com">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Billing Address</label>
                                <input type="text" id="billing-address" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="123 Main St">
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                                    <input type="text" id="billing-city" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="New York">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">State</label>
                                    <select id="billing-state" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="">Select State</option>
                                        <option value="NY">New York</option>
                                        <option value="CA">California</option>
                                        <option value="TX">Texas</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">ZIP Code</label>
                                    <input type="text" id="billing-zip" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="10001" maxlength="10">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <button type="submit" id="submit-payment" class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-blue-700 transition-colors flex items-center justify-center">
                            <i class="fas fa-lock mr-2"></i>
                            Complete Payment - ${{ number_format($amount * 1.08, 2) }}
                        </button>
                        
                        <div class="mt-4 flex items-center justify-center text-sm text-gray-500">
                            <i class="fas fa-shield-alt mr-2"></i>
                            Your payment information is secure and encrypted
                        </div>
                    </div>
                </form>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Security Badge -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <div class="text-center">
                        <i class="fas fa-shield-alt text-4xl text-green-500 mb-3"></i>
                        <h3 class="font-semibold text-gray-800 mb-2">Secure Payment</h3>
                        <p class="text-sm text-gray-600">Your payment is protected with 256-bit SSL encryption</p>
                    </div>
                </div>

                <!-- Accepted Cards -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h3 class="font-semibold text-gray-800 mb-3">We Accept</h3>
                    <div class="grid grid-cols-3 gap-3">
                        <div class="text-center">
                            <i class="fab fa-cc-visa text-2xl text-blue-600"></i>
                        </div>
                        <div class="text-center">
                            <i class="fab fa-cc-mastercard text-2xl text-red-600"></i>
                        </div>
                        <div class="text-center">
                            <i class="fab fa-cc-amex text-2xl text-blue-800"></i>
                        </div>
                        <div class="text-center">
                            <i class="fab fa-cc-discover text-2xl text-orange-600"></i>
                        </div>
                        <div class="text-center">
                            <i class="fab fa-cc-paypal text-2xl text-blue-500"></i>
                        </div>
                        <div class="text-center">
                            <i class="fab fa-bitcoin text-2xl text-orange-500"></i>
                        </div>
                    </div>
                </div>

                <!-- Help Section -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="font-semibold text-gray-800 mb-3">Need Help?</h3>
                    <div class="space-y-2">
                        <a href="#" class="flex items-center text-blue-600 hover:text-blue-800">
                            <i class="fas fa-phone mr-2"></i>
                            <span class="text-sm">Contact Support</span>
                        </a>
                        <a href="#" class="flex items-center text-blue-600 hover:text-blue-800">
                            <i class="fas fa-question-circle mr-2"></i>
                            <span class="text-sm">Payment FAQ</span>
                        </a>
                        <a href="#" class="flex items-center text-blue-600 hover:text-blue-800">
                            <i class="fas fa-file-invoice mr-2"></i>
                            <span class="text-sm">View Invoice</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loading-overlay" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 max-w-sm mx-4">
        <div class="text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
            <p class="text-gray-700 font-medium">Processing Payment...</p>
            <p class="text-sm text-gray-500 mt-2">Please do not close this window</p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('payment-form');
    const loadingOverlay = document.getElementById('loading-overlay');
    const cardDetails = document.getElementById('card-details');
    const paymentMethodRadios = document.querySelectorAll('input[name="payment_method"]');

    // Toggle card details based on payment method
    paymentMethodRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'card') {
                cardDetails.style.display = 'block';
            } else {
                cardDetails.style.display = 'none';
            }
        });
    });

    // Format card number
    const cardNumberInput = document.getElementById('card-number');
    cardNumberInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\s/g, '');
        let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
        e.target.value = formattedValue;
    });

    // Format expiry date
    const expiryInput = document.getElementById('card-expiry');
    expiryInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length >= 2) {
            value = value.slice(0, 2) + '/' + value.slice(2, 4);
        }
        e.target.value = value;
    });

    // Only allow numbers for CVV
    const cvvInput = document.getElementById('card-cvv');
    cvvInput.addEventListener('input', function(e) {
        e.target.value = e.target.value.replace(/\D/g, '');
    });

    // Form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Show loading overlay
        loadingOverlay.classList.remove('hidden');
        
        // Simulate payment processing
        setTimeout(function() {
            loadingOverlay.classList.add('hidden');
            
            // Show success message
            const successDiv = document.createElement('div');
            successDiv.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
            successDiv.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Payment processed successfully!';
            document.body.appendChild(successDiv);
            
            // Remove success message after 3 seconds
            setTimeout(() => {
                successDiv.remove();
                // Redirect to success page
                window.location.href = '{{ route("payments.receipts.index") }}';
            }, 3000);
        }, 2000);
    });
});
</script>
@endsection
