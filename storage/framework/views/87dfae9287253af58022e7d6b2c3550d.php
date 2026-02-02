

<?php $__env->startSection('content'); ?>

<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
    <!-- Decorative Elements -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-gradient-to-br from-blue-200 to-indigo-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-gradient-to-br from-cyan-200 to-blue-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse" style="animation-delay: 2s"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-gradient-to-br from-indigo-200 to-purple-200 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-pulse" style="animation-delay: 4s"></div>
    </div>
    
    <!-- Floating Cards -->
    <div class="absolute top-20 left-10 w-16 h-16 bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg transform rotate-12 hover:rotate-0 transition-transform duration-500"></div>
    <div class="absolute bottom-20 right-10 w-20 h-20 bg-blue-100/60 backdrop-blur-sm rounded-2xl shadow-lg transform -rotate-12 hover:rotate-0 transition-transform duration-500" style="animation-delay: 1s"></div>
    <div class="absolute top-1/3 right-20 w-12 h-12 bg-indigo-100/60 backdrop-blur-sm rounded-xl shadow-lg transform rotate-45 hover:rotate-0 transition-transform duration-500" style="animation-delay: 2s"></div>
    
    <div class="max-w-md w-full relative z-10">

        <!-- Header Section -->
        <div class="text-center mb-10 relative">
            <!-- Logo with Animation -->
            <div class="inline-flex items-center justify-center w-24 h-24 bg-gradient-to-br from-blue-600 via-blue-500 to-indigo-600 rounded-3xl shadow-2xl mb-8 transform hover:scale-105 transition-all duration-300 hover:shadow-3xl relative overflow-hidden group">
                <div class="absolute inset-0 bg-gradient-to-t from-transparent to-white/20 transform translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
                <i class="fas fa-building text-white text-4xl relative z-10"></i>
            </div>
            
            <!-- Welcome Text -->
            <div class="space-y-3">
                <h1 class="text-5xl font-bold bg-gradient-to-r from-gray-900 via-blue-800 to-indigo-900 bg-clip-text text-transparent mb-4 tracking-tight">
                    <?php echo e(__('Welcome Back')); ?>

                </h1>
                <p class="text-xl text-gray-600 leading-relaxed">
                    <?php echo e(__('Sign in to your Real Estate Pro account')); ?>

                </p>
                <div class="flex items-center justify-center space-x-2 text-gray-500 text-sm">
                    <span><?php echo e(__('New to Real Estate Pro?')); ?></span>
                    <a href="<?php echo e(route('register')); ?>" class="text-blue-600 hover:text-blue-700 font-semibold transition-all duration-200 hover:underline underline-offset-4">
                        <?php echo e(__('Create an account')); ?>

                    </a>
                </div>
            </div>
        </div>

        <!-- Login Form -->
        <form class="bg-white/95 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/50 overflow-hidden transform hover:scale-[1.01] transition-all duration-300" action="<?php echo e(route('login')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            
            <!-- Animated Progress Bar -->
            <div class="h-1 bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500 relative overflow-hidden">
                <div class="h-full bg-gradient-to-r from-blue-600 to-indigo-600 transition-all duration-700 ease-out relative" style="width: 0%" id="progressBar">
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent animate-shimmer"></div>
                </div>
            </div>

            <div class="p-8 space-y-8">
                <!-- Login Information Section -->
                <div class="space-y-6">
                    <div class="flex items-center space-x-4">
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl p-4 shadow-lg transform hover:scale-105 transition-all duration-300">
                            <i class="fas fa-user text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900"><?php echo e(__('Login Information')); ?></h3>
                            <p class="text-sm text-gray-500 mt-1"><?php echo e(__('Enter your credentials to continue')); ?></p>
                        </div>
                    </div>
                    
                    <?php if($errors->any()): ?>
                        <div class="rounded-2xl bg-red-50 border border-red-200 p-5 mb-6 transform animate-slideDown">
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-exclamation-circle text-red-500 text-lg"></i>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-sm font-semibold text-red-800 mb-2">
                                        <?php echo e(__('Please fix the following errors')); ?>

                                    </h3>
                                    <div class="text-sm text-red-700 space-y-1">
                                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="flex items-start space-x-2">
                                                <i class="fas fa-chevron-right text-xs mt-1 text-red-400"></i>
                                                <span><?php echo e($error); ?></span>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="space-y-6">
                        <div class="space-y-3 group">
                            <label for="email" class="block text-sm font-semibold text-gray-700 flex items-center space-x-2">
                                <i class="fas fa-envelope text-blue-500"></i>
                                <span><?php echo e(__('Email Address')); ?></span>
                                <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-at text-gray-400 group-focus-within:text-blue-500 transition-colors duration-200"></i>
                                </div>
                                <input id="email" name="email" type="email" 
                                       class="pl-12 block w-full px-4 py-4 bg-gray-50 border-2 border-gray-200 rounded-2xl text-gray-900 placeholder-gray-500 focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 focus:bg-white transition-all duration-300 text-base" 
                                       value="<?php echo e(old('email')); ?>" 
                                       placeholder="<?php echo e(__('your.email@example.com')); ?>"
                                       required>
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center opacity-0 group-focus-within:opacity-100 transition-opacity duration-300">
                                    <i class="fas fa-check-circle text-green-500"></i>
                                </div>
                            </div>
                            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-2 text-sm text-red-600 flex items-center space-x-2 animate-slideDown">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <span><?php echo e($message); ?></span>
                                </p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="space-y-3 group">
                            <label for="password" class="block text-sm font-semibold text-gray-700 flex items-center space-x-2">
                                <i class="fas fa-lock text-blue-500"></i>
                                <span><?php echo e(__('Password')); ?></span>
                                <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-key text-gray-400 group-focus-within:text-blue-500 transition-colors duration-200"></i>
                                </div>
                                <input id="password" name="password" type="password" 
                                       class="pl-12 pr-14 block w-full px-4 py-4 bg-gray-50 border-2 border-gray-200 rounded-2xl text-gray-900 placeholder-gray-500 focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 focus:bg-white transition-all duration-300 text-base" 
                                       placeholder="<?php echo e(__('Enter your password')); ?>"
                                       required>
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center space-x-2">
                                    <button type="button" id="togglePassword" class="text-gray-400 hover:text-gray-600 focus:outline-none focus:text-blue-500 transition-colors duration-200 p-1">
                                        <i id="eyeIcon" class="fas fa-eye text-lg"></i>
                                    </button>
                                    <div class="w-px h-6 bg-gray-300"></div>
                                    <button type="button" class="text-gray-400 hover:text-gray-600 focus:outline-none focus:text-blue-500 transition-colors duration-200 p-1" title="<?php echo e(__('Password requirements')); ?>">
                                        <i class="fas fa-info-circle text-lg"></i>
                                    </button>
                                </div>
                            </div>
                            <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-2 text-sm text-red-600 flex items-center space-x-2 animate-slideDown">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <span><?php echo e($message); ?></span>
                                </p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="flex items-center justify-between py-2">
                            <div class="flex items-center space-x-3">
                                <input type="hidden" name="remember" value="false">
                                <input id="remember" name="remember" type="checkbox" value="true" class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-2 border-gray-300 rounded-lg transition-all duration-200 focus:ring-2">
                                <label for="remember" class="block text-sm text-gray-700 cursor-pointer select-none hover:text-gray-900 transition-colors duration-200">
                                    <?php echo e(__('Remember me for 30 days')); ?>

                                </label>
                            </div>
                            <div class="text-sm">
                                <a href="<?php echo e(route('password.request')); ?>" class="text-blue-600 hover:text-blue-700 font-medium transition-all duration-200 hover:underline underline-offset-4 flex items-center space-x-1">
                                    <span><?php echo e(__('Forgot password?')); ?></span>
                                    <i class="fas fa-arrow-right text-xs"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Enhanced Social Login Section -->
                <div class="space-y-4">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-200"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-4 bg-white text-gray-500 font-medium"><?php echo e(__('Or continue with')); ?></span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <button type="button" class="group flex items-center justify-center px-4 py-3 border-2 border-gray-200 rounded-2xl shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-300 hover:shadow-md transition-all duration-300 transform hover:scale-[1.02]">
                            <i class="fab fa-google text-red-500 mr-2 text-lg group-hover:scale-110 transition-transform duration-200"></i>
                            <span class="font-medium">Google</span>
                        </button>
                        <button type="button" class="group flex items-center justify-center px-4 py-3 border-2 border-gray-200 rounded-2xl shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-300 hover:shadow-md transition-all duration-300 transform hover:scale-[1.02]">
                            <i class="fab fa-facebook text-blue-600 mr-2 text-lg group-hover:scale-110 transition-transform duration-200"></i>
                            <span class="font-medium">Facebook</span>
                        </button>
                    </div>
                    
                    <!-- Additional Security Note -->
                    <div class="flex items-center justify-center space-x-2 text-xs text-gray-500">
                        <i class="fas fa-shield-alt text-green-500"></i>
                        <span><?php echo e(__('Your data is secure and encrypted')); ?></span>
                    </div>
                </div>

                <!-- Enhanced Submit Button -->
                <div class="space-y-4">
                    <button type="submit" class="w-full group relative flex justify-center items-center py-4 px-6 border border-transparent rounded-2xl shadow-lg text-base font-bold text-white bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-600 hover:from-blue-700 hover:via-blue-600 hover:to-indigo-700 focus:outline-none focus:ring-4 focus:ring-blue-500/25 transition-all duration-300 transform hover:scale-[1.02] hover:shadow-xl overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent transform translate-x-full group-hover:translate-x-full transition-transform duration-700"></div>
                        <span class="relative flex items-center space-x-2">
                            <i class="fas fa-sign-in-alt"></i>
                            <span><?php echo e(__('Sign In to Dashboard')); ?></span>
                        </span>
                    </button>
                    
                    <!-- Quick Links -->
                    <div class="flex items-center justify-center space-x-6 text-xs text-gray-500">
                        <a href="#" class="hover:text-blue-600 transition-colors duration-200 flex items-center space-x-1">
                            <i class="fas fa-question-circle"></i>
                            <span><?php echo e(__('Help Center')); ?></span>
                        </a>
                        <a href="#" class="hover:text-blue-600 transition-colors duration-200 flex items-center space-x-1">
                            <i class="fas fa-user-shield"></i>
                            <span><?php echo e(__('Privacy Policy')); ?></span>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    /* Custom Animations */
    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }
    
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .animate-shimmer {
        animation: shimmer 2s infinite;
    }
    
    .animate-slideDown {
        animation: slideDown 0.3s ease-out;
    }
    
    /* Enhanced Focus States */
    .group:focus-within .group-focus-within\:text-blue-500 {
        color: rgb(59 130 246);
    }
    
    /* Smooth scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
    }
    
    ::-webkit-scrollbar-track {
        background: #f1f5f9;
    }
    
    ::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>

<script>
// Enhanced Password Toggle
document.getElementById('togglePassword').addEventListener('click', function() {
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.classList.remove('fa-eye');
        eyeIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        eyeIcon.classList.remove('fa-eye-slash');
        eyeIcon.classList.add('fa-eye');
    }
});

// Animated Progress Bar
document.addEventListener('DOMContentLoaded', function() {
    const progressBar = document.getElementById('progressBar');
    const inputs = document.querySelectorAll('input[required]');
    let filledInputs = 0;
    
    function updateProgress() {
        filledInputs = 0;
        inputs.forEach(input => {
            if (input.value.trim() !== '') {
                filledInputs++;
            }
        });
        
        const progress = (filledInputs / inputs.length) * 100;
        progressBar.style.width = progress + '%';
    }
    
    inputs.forEach(input => {
        input.addEventListener('input', updateProgress);
        input.addEventListener('change', updateProgress);
    });
    
    // Initial progress
    updateProgress();
    
    // Animate progress bar on load
    setTimeout(() => {
        progressBar.style.width = '0%';
        setTimeout(() => {
            updateProgress();
        }, 100);
    }, 500);
});

// Form Validation Enhancement
const form = document.querySelector('form');
const emailInput = document.getElementById('email');
const passwordInput = document.getElementById('password');

// Real-time email validation
emailInput.addEventListener('blur', function() {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const checkIcon = this.parentElement.querySelector('.fa-check-circle');
    
    if (this.value && emailRegex.test(this.value)) {
        this.classList.remove('border-red-300');
        this.classList.add('border-green-300');
        if (checkIcon) {
            checkIcon.style.opacity = '1';
        }
    } else if (this.value) {
        this.classList.remove('border-green-300');
        this.classList.add('border-red-300');
        if (checkIcon) {
            checkIcon.style.opacity = '0';
        }
    }
});

// Password strength indicator
passwordInput.addEventListener('input', function() {
    const password = this.value;
    let strength = 0;
    
    if (password.length >= 8) strength++;
    if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
    if (password.match(/[0-9]/)) strength++;
    if (password.match(/[^a-zA-Z0-9]/)) strength++;
    
    // Update border color based on strength
    this.classList.remove('border-red-300', 'border-yellow-300', 'border-green-300');
    if (password.length > 0) {
        if (strength <= 1) this.classList.add('border-red-300');
        else if (strength === 2) this.classList.add('border-yellow-300');
        else if (strength >= 3) this.classList.add('border-green-300');
    }
});

// Smooth scroll for error messages
if (document.querySelector('.bg-red-50')) {
    document.querySelector('.bg-red-50').scrollIntoView({ 
        behavior: 'smooth', 
        block: 'center' 
    });
}

// Add loading state to submit button
form.addEventListener('submit', function(e) {
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalContent = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = `
        <span class="relative flex items-center space-x-2">
            <i class="fas fa-spinner fa-spin"></i>
            <span><?php echo e(__('Signing In...')); ?></span>
        </span>
    `;
    
    // Re-enable after 5 seconds (fallback)
    setTimeout(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalContent;
    }, 5000);
});

// Add keyboard navigation
document.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && e.ctrlKey) {
        form.submit();
    }
});

// Tooltip functionality
const infoButtons = document.querySelectorAll('[title]');
infoButtons.forEach(button => {
    button.addEventListener('mouseenter', function() {
        this.style.transform = 'scale(1.1)';
    });
    
    button.addEventListener('mouseleave', function() {
        this.style.transform = 'scale(1)';
    });
});
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.auth', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\larvel state big\resources\views/auth/login.blade.php ENDPATH**/ ?>