@extends('layouts.app')

@section('title', 'Add Investor')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center">
                <a href="{{ route('investors.index') }}" class="text-gray-600 hover:text-gray-800 mr-4">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Add New Investor</h1>
                    <p class="text-gray-600">Create a new investor profile</p>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('investors.store') }}" class="space-y-6">
            @csrf
            
            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-6">Basic Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">User *</label>
                        <select name="user_id" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            <option value="">Select User</option>
                            @foreach($users ?? [] as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Investor Type *</label>
                        <select name="investor_type" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            <option value="">Select Type</option>
                            <option value="individual">Individual</option>
                            <option value="institutional">Institutional</option>
                            <option value="corporate">Corporate</option>
                            <option value="retail">Retail</option>
                        </select>
                        @error('investor_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Investment Level *</label>
                        <select name="investment_level" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            <option value="">Select Level</option>
                            <option value="beginner">Beginner</option>
                            <option value="intermediate">Intermediate</option>
                            <option value="advanced">Advanced</option>
                            <option value="expert">Expert</option>
                        </select>
                        @error('investment_level')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Risk Profile *</label>
                        <select name="risk_profile" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            <option value="">Select Risk Profile</option>
                            <option value="conservative">Conservative</option>
                            <option value="moderate">Moderate</option>
                            <option value="aggressive">Aggressive</option>
                        </select>
                        @error('risk_profile')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Investment Goals -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-6">Investment Goals</h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Investment Goals *</label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="investment_goals[]" value="long_term_growth" class="mr-2">
                                <span>Long-term Growth</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="investment_goals[]" value="income_generation" class="mr-2">
                                <span>Income Generation</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="investment_goals[]" value="capital_preservation" class="mr-2">
                                <span>Capital Preservation</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="investment_goals[]" value="diversification" class="mr-2">
                                <span>Diversification</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="investment_goals[]" value="tax_benefits" class="mr-2">
                                <span>Tax Benefits</span>
                            </label>
                        </div>
                        @error('investment_goals')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Initial Investment ($) *</label>
                            <input type="number" name="initial_investment" min="100" max="10000000" step="0.01" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            @error('initial_investment')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Expected Return (%) *</label>
                            <input type="number" name="expected_return" min="0" max="100" step="0.1" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            @error('expected_return')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Investment Horizon *</label>
                            <select name="investment_horizon" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                <option value="">Select Horizon</option>
                                <option value="short_term">Short Term (1-3 years)</option>
                                <option value="medium_term">Medium Term (3-7 years)</option>
                                <option value="long_term">Long Term (7+ years)</option>
                            </select>
                            @error('investment_horizon')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Liquidity Needs *</label>
                            <select name="liquidity_needs" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                <option value="">Select Liquidity Needs</option>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                            </select>
                            @error('liquidity_needs')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Experience & Accreditation -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-6">Experience & Accreditation</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Experience (Years) *</label>
                        <input type="number" name="experience_years" min="0" max="50" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        @error('experience_years')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Investment Knowledge *</label>
                        <select name="investment_knowledge" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            <option value="">Select Knowledge Level</option>
                            <option value="basic">Basic</option>
                            <option value="intermediate">Intermediate</option>
                            <option value="advanced">Advanced</option>
                            <option value="expert">Expert</option>
                        </select>
                        @error('investment_knowledge')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Accredited Investor *</label>
                        <select name="accredited_investor" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            <option value="">Select Status</option>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                        @error('accredited_investor')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Employment Status *</label>
                        <select name="employment_status" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            <option value="">Select Status</option>
                            <option value="employed">Employed</option>
                            <option value="self_employed">Self Employed</option>
                            <option value="retired">Retired</option>
                            <option value="student">Student</option>
                            <option value="unemployed">Unemployed</option>
                        </select>
                        @error('employment_status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Net Worth ($)</label>
                        <input type="number" name="net_worth" min="0" step="0.01" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('net_worth')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Annual Income ($)</label>
                        <input type="number" name="annual_income" min="0" step="0.01" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('annual_income')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Preferences -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-6">Communication Preferences</h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Preferred Sectors</label>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="preferred_sectors[]" value="residential" class="mr-2">
                                <span>Residential</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="preferred_sectors[]" value="commercial" class="mr-2">
                                <span>Commercial</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="preferred_sectors[]" value="industrial" class="mr-2">
                                <span>Industrial</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="preferred_sectors[]" value="retail" class="mr-2">
                                <span>Retail</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="preferred_sectors[]" value="hospitality" class="mr-2">
                                <span>Hospitality</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="preferred_sectors[]" value="healthcare" class="mr-2">
                                <span>Healthcare</span>
                            </label>
                        </div>
                        @error('preferred_sectors')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Communication Methods *</label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="communication_preferences[email]" value="1" class="mr-2" checked>
                                <span>Email Notifications</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="communication_preferences[phone]" value="1" class="mr-2">
                                <span>Phone Calls</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="communication_preferences[sms]" value="1" class="mr-2">
                                <span>SMS Messages</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="communication_preferences[newsletter]" value="1" class="mr-2" checked>
                                <span>Newsletter</span>
                            </label>
                        </div>
                        @error('communication_preferences')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-6">Additional Notes</h2>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" rows="4" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Enter any additional notes or comments...">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('investors.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        Create Investor
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const investmentGoals = document.querySelectorAll('input[name="investment_goals[]"]:checked');
    if (investmentGoals.length === 0) {
        e.preventDefault();
        alert('Please select at least one investment goal.');
        return false;
    }
});

// Real-time validation
function validateField(field, condition, message) {
    const errorElement = field.parentElement.querySelector('.text-red-600');
    if (condition) {
        field.classList.add('border-red-500');
        if (!errorElement) {
            const error = document.createElement('p');
            error.className = 'mt-1 text-sm text-red-600';
            error.textContent = message;
            field.parentElement.appendChild(error);
        }
    } else {
        field.classList.remove('border-red-500');
        if (errorElement) {
            errorElement.remove();
        }
    }
}

// Investment amount validation
document.querySelector('input[name="initial_investment"]').addEventListener('input', function(e) {
    validateField(e.target, e.target.value < 100 || e.target.value > 10000000, 'Investment must be between $100 and $10,000,000');
});

// Expected return validation
document.querySelector('input[name="expected_return"]').addEventListener('input', function(e) {
    validateField(e.target, e.target.value < 0 || e.target.value > 100, 'Expected return must be between 0% and 100%');
});

// Experience validation
document.querySelector('input[name="experience_years"]').addEventListener('input', function(e) {
    validateField(e.target, e.target.value < 0 || e.target.value > 50, 'Experience must be between 0 and 50 years');
});
</script>
@endsection
