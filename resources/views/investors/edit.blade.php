@extends('layouts.app')

@section('title', 'Edit Investor')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center">
                <a href="{{ route('investors.show', $investor) }}" class="text-gray-600 hover:text-gray-800 mr-4">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Edit Investor</h1>
                    <p class="text-gray-600">Update investor profile information</p>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('investors.update', $investor) }}" class="space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-6">Basic Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Investor Type *</label>
                        <select name="investor_type" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            <option value="">Select Type</option>
                            <option value="individual" {{ $investor->investor_type === 'individual' ? 'selected' : '' }}>Individual</option>
                            <option value="institutional" {{ $investor->investor_type === 'institutional' ? 'selected' : '' }}>Institutional</option>
                            <option value="corporate" {{ $investor->investor_type === 'corporate' ? 'selected' : '' }}>Corporate</option>
                            <option value="retail" {{ $investor->investor_type === 'retail' ? 'selected' : '' }}>Retail</option>
                        </select>
                        @error('investor_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Investment Level *</label>
                        <select name="investment_level" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            <option value="">Select Level</option>
                            <option value="beginner" {{ $investor->investment_level === 'beginner' ? 'selected' : '' }}>Beginner</option>
                            <option value="intermediate" {{ $investor->investment_level === 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                            <option value="advanced" {{ $investor->investment_level === 'advanced' ? 'selected' : '' }}>Advanced</option>
                            <option value="expert" {{ $investor->investment_level === 'expert' ? 'selected' : '' }}>Expert</option>
                        </select>
                        @error('investment_level')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Risk Profile *</label>
                        <select name="risk_profile" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            <option value="">Select Risk Profile</option>
                            <option value="conservative" {{ $investor->risk_profile === 'conservative' ? 'selected' : '' }}>Conservative</option>
                            <option value="moderate" {{ $investor->risk_profile === 'moderate' ? 'selected' : '' }}>Moderate</option>
                            <option value="aggressive" {{ $investor->risk_profile === 'aggressive' ? 'selected' : '' }}>Aggressive</option>
                        </select>
                        @error('risk_profile')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="active" {{ $investor->status === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $investor->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="suspended" {{ $investor->status === 'suspended' ? 'selected' : '' }}>Suspended</option>
                            <option value="verified" {{ $investor->status === 'verified' ? 'selected' : '' }}>Verified</option>
                            <option value="restricted" {{ $investor->status === 'restricted' ? 'selected' : '' }}>Restricted</option>
                        </select>
                        @error('status')
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
                                <input type="checkbox" name="investment_goals[]" value="long_term_growth" {{ in_array('long_term_growth', $investor->investment_goals ?? []) ? 'checked' : '' }} class="mr-2">
                                <span>Long-term Growth</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="investment_goals[]" value="income_generation" {{ in_array('income_generation', $investor->investment_goals ?? []) ? 'checked' : '' }} class="mr-2">
                                <span>Income Generation</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="investment_goals[]" value="capital_preservation" {{ in_array('capital_preservation', $investor->investment_goals ?? []) ? 'checked' : '' }} class="mr-2">
                                <span>Capital Preservation</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="investment_goals[]" value="diversification" {{ in_array('diversification', $investor->investment_goals ?? []) ? 'checked' : '' }} class="mr-2">
                                <span>Diversification</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="investment_goals[]" value="tax_benefits" {{ in_array('tax_benefits', $investor->investment_goals ?? []) ? 'checked' : '' }} class="mr-2">
                                <span>Tax Benefits</span>
                            </label>
                        </div>
                        @error('investment_goals')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Expected Return (%) *</label>
                            <input type="number" name="expected_return" min="0" max="100" step="0.1" value="{{ $investor->expected_return }}" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            @error('expected_return')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Investment Horizon *</label>
                            <select name="investment_horizon" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                <option value="">Select Horizon</option>
                                <option value="short_term" {{ $investor->investment_horizon === 'short_term' ? 'selected' : '' }}>Short Term (1-3 years)</option>
                                <option value="medium_term" {{ $investor->investment_horizon === 'medium_term' ? 'selected' : '' }}>Medium Term (3-7 years)</option>
                                <option value="long_term" {{ $investor->investment_horizon === 'long_term' ? 'selected' : '' }}>Long Term (7+ years)</option>
                            </select>
                            @error('investment_horizon')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Liquidity Needs *</label>
                        <select name="liquidity_needs" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            <option value="">Select Liquidity Needs</option>
                            <option value="low" {{ $investor->liquidity_needs === 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ $investor->liquidity_needs === 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ $investor->liquidity_needs === 'high' ? 'selected' : '' }}>High</option>
                        </select>
                        @error('liquidity_needs')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Experience & Accreditation -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-6">Experience & Accreditation</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Experience (Years) *</label>
                        <input type="number" name="experience_years" min="0" max="50" value="{{ $investor->experience_years }}" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        @error('experience_years')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Investment Knowledge *</label>
                        <select name="investment_knowledge" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            <option value="">Select Knowledge Level</option>
                            <option value="basic" {{ $investor->investment_knowledge === 'basic' ? 'selected' : '' }}>Basic</option>
                            <option value="intermediate" {{ $investor->investment_knowledge === 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                            <option value="advanced" {{ $investor->investment_knowledge === 'advanced' ? 'selected' : '' }}>Advanced</option>
                            <option value="expert" {{ $investor->investment_knowledge === 'expert' ? 'selected' : '' }}>Expert</option>
                        </select>
                        @error('investment_knowledge')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Accredited Investor *</label>
                        <select name="accredited_investor" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            <option value="">Select Status</option>
                            <option value="1" {{ $investor->accredited_investor ? 'selected' : '' }}>Yes</option>
                            <option value="0" {{ !$investor->accredited_investor ? 'selected' : '' }}>No</option>
                        </select>
                        @error('accredited_investor')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Employment Status *</label>
                        <select name="employment_status" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            <option value="">Select Status</option>
                            <option value="employed" {{ $investor->employment_status === 'employed' ? 'selected' : '' }}>Employed</option>
                            <option value="self_employed" {{ $investor->employment_status === 'self_employed' ? 'selected' : '' }}>Self Employed</option>
                            <option value="retired" {{ $investor->employment_status === 'retired' ? 'selected' : '' }}>Retired</option>
                            <option value="student" {{ $investor->employment_status === 'student' ? 'selected' : '' }}>Student</option>
                            <option value="unemployed" {{ $investor->employment_status === 'unemployed' ? 'selected' : '' }}>Unemployed</option>
                        </select>
                        @error('employment_status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Net Worth ($)</label>
                        <input type="number" name="net_worth" min="0" step="0.01" value="{{ $investor->net_worth }}" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('net_worth')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Annual Income ($)</label>
                        <input type="number" name="annual_income" min="0" step="0.01" value="{{ $investor->annual_income }}" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('annual_income')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Preferences -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-6">Preferences</h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Preferred Sectors</label>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="preferred_sectors[]" value="residential" {{ in_array('residential', $investor->preferred_sectors ?? []) ? 'checked' : '' }} class="mr-2">
                                <span>Residential</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="preferred_sectors[]" value="commercial" {{ in_array('commercial', $investor->preferred_sectors ?? []) ? 'checked' : '' }} class="mr-2">
                                <span>Commercial</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="preferred_sectors[]" value="industrial" {{ in_array('industrial', $investor->preferred_sectors ?? []) ? 'checked' : '' }} class="mr-2">
                                <span>Industrial</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="preferred_sectors[]" value="retail" {{ in_array('retail', $investor->preferred_sectors ?? []) ? 'checked' : '' }} class="mr-2">
                                <span>Retail</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="preferred_sectors[]" value="hospitality" {{ in_array('hospitality', $investor->preferred_sectors ?? []) ? 'checked' : '' }} class="mr-2">
                                <span>Hospitality</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="preferred_sectors[]" value="healthcare" {{ in_array('healthcare', $investor->preferred_sectors ?? []) ? 'checked' : '' }} class="mr-2">
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
                                <input type="checkbox" name="communication_preferences[email]" value="1" {{ $investor->communication_preferences['email'] ?? false ? 'checked' : '' }} class="mr-2">
                                <span>Email Notifications</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="communication_preferences[phone]" value="1" {{ $investor->communication_preferences['phone'] ?? false ? 'checked' : '' }} class="mr-2">
                                <span>Phone Calls</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="communication_preferences[sms]" value="1" {{ $investor->communication_preferences['sms'] ?? false ? 'checked' : '' }} class="mr-2">
                                <span>SMS Messages</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="communication_preferences[newsletter]" value="1" {{ $investor->communication_preferences['newsletter'] ?? false ? 'checked' : '' }} class="mr-2">
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
                    <textarea name="notes" rows="4" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Enter any additional notes or comments...">{{ old('notes', $investor->notes) }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('investors.show', $investor) }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        Update Investor
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

// Expected return validation
document.querySelector('input[name="expected_return"]').addEventListener('input', function(e) {
    validateField(e.target, e.target.value < 0 || e.target.value > 100, 'Expected return must be between 0% and 100%');
});

// Experience validation
document.querySelector('input[name="experience_years"]').addEventListener('input', function(e) {
    validateField(e.target, e.target.value < 0 || e.target.value > 50, 'Experience must be between 0 and 50 years');
});

// Auto-save functionality
let autoSaveTimer;
document.querySelectorAll('input, select, textarea').forEach(element => {
    element.addEventListener('input', function() {
        clearTimeout(autoSaveTimer);
        autoSaveTimer = setTimeout(() => {
            // Here you could implement auto-save functionality
            console.log('Auto-saving...');
        }, 2000);
    });
});
</script>
@endsection
