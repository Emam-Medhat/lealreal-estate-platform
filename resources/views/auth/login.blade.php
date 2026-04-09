@extends('layouts.auth')

@section('content')

    <div
        class="min-h-screen bg-gradient-to-br from-blue-600 via-cyan-500 to-blue-700 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
        <!-- Animated Background Elements -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-gradient-to-br from-cyan-400/30 to-blue-400/30 rounded-full mix-blend-screen filter blur-3xl opacity-70 animate-pulse"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-gradient-to-br from-blue-400/30 to-cyan-400/30 rounded-full mix-blend-screen filter blur-3xl opacity-70 animate-pulse" style="animation-delay: 2s"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-gradient-to-br from-sky-400/20 to-blue-400/20 rounded-full mix-blend-screen filter blur-3xl opacity-50 animate-pulse" style="animation-delay: 4s"></div>
        </div>

        <!-- Real Estate Icons -->
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-10 left-10 text-blue-200/20 text-6xl animate-pulse" style="animation-delay: 0s"><i class="fas fa-building"></i></div>
            <div class="absolute top-20 right-20 text-cyan-200/20 text-5xl animate-pulse" style="animation-delay: 1s"><i class="fas fa-home"></i></div>
            <div class="absolute bottom-20 left-20 text-blue-200/20 text-4xl animate-pulse" style="animation-delay: 2s"><i class="fas fa-city"></i></div>
            <div class="absolute bottom-40 right-40 text-cyan-200/20 text-5xl animate-pulse" style="animation-delay: 3s"><i class="fas fa-landmark"></i></div>
            <div class="absolute top-1/2 left-20 text-blue-200/20 text-4xl animate-pulse" style="animation-delay: 4s"><i class="fas fa-key"></i></div>
        </div>

        <!-- Floating Property Cards -->
        <div class="absolute top-20 left-10 w-16 h-20 bg-white/10 backdrop-blur-sm rounded-lg shadow-lg transform rotate-12 hover:rotate-0 transition-transform duration-500 border border-white/20">
            <div class="w-full h-8 bg-gradient-to-r from-blue-400 to-cyan-400 rounded-t-lg"></div>
            <div class="p-2">
                <div class="w-full h-1 bg-white/30 rounded mb-1"></div>
                <div class="w-3/4 h-1 bg-white/20 rounded"></div>
            </div>
        </div>
        <div class="absolute bottom-20 right-10 w-20 h-24 bg-white/10 backdrop-blur-sm rounded-lg shadow-lg transform -rotate-12 hover:rotate-0 transition-transform duration-500 border border-white/20" style="animation-delay: 1s">
            <div class="w-full h-10 bg-gradient-to-r from-cyan-400 to-blue-400 rounded-t-lg"></div>
            <div class="p-2">
                <div class="w-full h-1 bg-white/30 rounded mb-1"></div>
                <div class="w-3/4 h-1 bg-white/20 rounded mb-1"></div>
                <div class="w-1/2 h-1 bg-white/20 rounded"></div>
            </div>
        </div>
        <div class="absolute top-1/3 right-20 w-16 h-20 bg-white/10 backdrop-blur-sm rounded-lg shadow-lg transform rotate-45 hover:rotate-0 transition-transform duration-500 border border-white/20" style="animation-delay: 2s">
            <div class="w-full h-8 bg-gradient-to-r from-blue-400 to-sky-400 rounded-t-lg"></div>
            <div class="p-2">
                <div class="w-full h-1 bg-white/30 rounded mb-1"></div>
                <div class="w-2/3 h-1 bg-white/20 rounded"></div>
            </div>
        </div>

        <div class="max-w-md w-full relative z-10">

            <!-- Header Section -->
            <div class="text-center mb-10 relative">
                <!-- Logo with Real Estate Theme -->
                <div
                    class="inline-flex items-center justify-center w-28 h-28 bg-gradient-to-br from-blue-600 via-cyan-500 to-blue-600 rounded-3xl shadow-2xl mb-8 transform hover:scale-110 transition-all duration-500 hover:shadow-3xl relative overflow-hidden group">
                    <div class="absolute inset-0 bg-gradient-to-t from-transparent via-white/20 to-white/40 transform translate-y-full group-hover:translate-y-0 transition-transform duration-500"></div>
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-blue-600/30 to-transparent transform -translate-x-full group-hover:translate-x-full transition-transform duration-700"></div>
                    <i class="fas fa-building text-white text-5xl relative z-10"></i>
                </div>

                <!-- Welcome Text -->
                <div class="space-y-4">
                    <h1
                        class="text-6xl font-black bg-gradient-to-r from-white via-cyan-100 to-blue-100 bg-clip-text text-transparent mb-6 tracking-tight leading-tight">
                        Real Estate Portal
                    </h1>
                    <p class="text-xl text-blue-100/90 leading-relaxed font-light">
                        Your gateway to premium property management
                    </p>
                    <div class="flex items-center justify-center space-x-3 text-blue-200/80 text-sm">
                        <i class="fas fa-home text-cyan-300"></i>
                        <span>New to our platform?</span>
                        <a href="{{ route('register') }}"
                            class="text-cyan-300 hover:text-white font-semibold transition-all duration-300 hover:underline underline-offset-4 flex items-center space-x-1">
                            <span>Create Account</span>
                            <i class="fas fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Login Form -->
            <form
                class="bg-white/15 backdrop-blur-2xl rounded-3xl shadow-2xl border border-white/30 overflow-hidden transform hover:scale-[1.01] transition-all duration-300"
                action="{{ route('login') }}" method="POST">
                @csrf

                <!-- Animated Progress Bar -->
                <div class="h-1 bg-gradient-to-r from-blue-500 via-cyan-500 to-blue-500 relative overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-blue-600 to-cyan-600 transition-all duration-700 ease-out relative"
                        style="width: 0%" id="progressBar">
                        <div
                            class="absolute inset-0 bg-gradient-to-r from-transparent via-white/40 to-transparent animate-shimmer">
                        </div>
                    </div>
                </div>

                <div class="p-8 space-y-8">
                    <!-- Login Information Section -->
                    <div class="space-y-6">
                        <div class="flex items-center space-x-4">
                            <div
                                class="bg-gradient-to-r from-blue-500 to-cyan-500 rounded-2xl p-4 shadow-lg transform hover:scale-105 transition-all duration-300">
                                <i class="fas fa-key text-white text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-white">Secure Access</h3>
                                <p class="text-sm text-blue-100/70 mt-1">Login to manage your properties</p>
                            </div>
                        </div>

                        @if ($errors->any())
                            <div class="rounded-2xl bg-red-500/20 border border-red-500/40 backdrop-blur-sm p-5 mb-6 transform animate-slideDown">
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-red-500/30 rounded-full flex items-center justify-center">
                                            <i class="fas fa-exclamation-triangle text-red-300 text-lg"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="text-sm font-semibold text-red-200 mb-2">
                                            Please fix the following errors
                                        </h3>
                                        <div class="text-sm text-red-100 space-y-1">
                                            @foreach ($errors->all() as $error)
                                                <div class="flex items-start space-x-2">
                                                    <i class="fas fa-chevron-right text-xs mt-1 text-red-300"></i>
                                                    <span>{{ $error }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                            <div class="space-y-6">
                                <div class="space-y-3 group">
                                    <label for="email"
                                        class="block text-sm font-semibold text-blue-100/90 flex items-center space-x-2">
                                        <i class="fas fa-envelope text-cyan-400"></i>
                                        <span>Email Address</span>
                                        <span class="text-red-400">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i
                                                class="fas fa-at text-cyan-400/60 group-focus-within:text-cyan-300 transition-colors duration-200"></i>
                                        </div>
                                        <input id="email" name="email" type="email"
                                            class="pl-12 block w-full px-4 py-4 bg-white/10 border-2 border-blue-500/30 rounded-2xl text-white placeholder-blue-200/50 focus:ring-4 focus:ring-blue-500/30 focus:border-cyan-400 focus:bg-white/20 transition-all duration-300 text-base backdrop-blur-sm"
                                            value="{{ old('email') }}" placeholder="Enter your email address"
                                            required>
                                        <div
                                            class="absolute inset-y-0 right-0 pr-4 flex items-center opacity-0 group-focus-within:opacity-100 transition-opacity duration-300">
                                            <i class="fas fa-check-circle text-green-400"></i>
                                        </div>
                                    </div>
                                    @error('email')
                                        <p class="mt-2 text-sm text-red-200 flex items-center space-x-2 animate-slideDown">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            <span>{{ $message }}</span>
                                        </p>
                                    @enderror
                                </div>

                                <div class="space-y-3 group">
                                    <label for="password"
                                        class="block text-sm font-semibold text-blue-100/90 flex items-center space-x-2">
                                        <i class="fas fa-lock text-cyan-400"></i>
                                        <span>Password</span>
                                        <span class="text-red-400">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i
                                                class="fas fa-key text-cyan-400/60 group-focus-within:text-cyan-300 transition-colors duration-200"></i>
                                        </div>
                                        <input id="password" name="password" type="password"
                                            class="pl-12 pr-14 block w-full px-4 py-4 bg-white/10 border-2 border-blue-500/30 rounded-2xl text-white placeholder-blue-200/50 focus:ring-4 focus:ring-blue-500/30 focus:border-cyan-400 focus:bg-white/20 transition-all duration-300 text-base backdrop-blur-sm"
                                            placeholder="Enter your password" required>
                                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center space-x-2">
                                            <button type="button" id="togglePassword"
                                                class="text-cyan-400/60 hover:text-cyan-300 focus:outline-none focus:text-cyan-200 transition-colors duration-200 p-1">
                                                <i id="eyeIcon" class="fas fa-eye text-lg"></i>
                                            </button>
                                            <div class="w-px h-6 bg-blue-500/30"></div>
                                            <button type="button"
                                                class="text-cyan-400/60 hover:text-cyan-300 focus:outline-none focus:text-cyan-200 transition-colors duration-200 p-1"
                                                title="Password requirements">
                                                <i class="fas fa-info-circle text-lg"></i>
                                            </button>
                                        </div>
                                    </div>
                                    @error('password')
                                        <p class="mt-2 text-sm text-red-200 flex items-center space-x-2 animate-slideDown">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            <span>{{ $message }}</span>
                                        </p>
                                    @enderror
                                </div>

                                <div class="flex items-center justify-between py-2">
                                    <div class="flex items-center space-x-3">
                                        <input type="hidden" name="remember" value="false">
                                        <input id="remember" name="remember" type="checkbox" value="true"
                                            class="h-5 w-5 text-blue-600 focus:ring-blue-500/30 border-2 border-blue-500/30 rounded-lg transition-all duration-200 focus:ring-2 bg-white/10 backdrop-blur-sm">
                                        <label for="remember"
                                            class="block text-sm text-blue-100/80 cursor-pointer select-none hover:text-blue-100 transition-colors duration-200">
                                            Remember me for 30 days
                                        </label>
                                    </div>
                                    <div class="text-sm">
                                        <a href="{{ route('password.request') }}"
                                            class="text-cyan-300 hover:text-white font-medium transition-all duration-300 hover:underline underline-offset-4 flex items-center space-x-1">
                                            <span>Forgot password?</span>
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
                                <div class="w-full border-t border-blue-500/30"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span
                                    class="px-4 bg-transparent text-blue-200/70 font-medium">Or continue with</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <button type="button"
                                class="group flex items-center justify-center px-4 py-3 border-2 border-blue-500/30 rounded-2xl shadow-sm text-sm font-medium text-blue-100/80 bg-white/5 hover:bg-white/10 hover:border-blue-500/50 hover:shadow-md transition-all duration-300 transform hover:scale-[1.02] backdrop-blur-sm">
                                <i
                                    class="fab fa-google text-red-400 mr-2 text-lg group-hover:scale-110 transition-transform duration-200"></i>
                                <span class="font-medium">Google</span>
                            </button>
                            <button type="button"
                                class="group flex items-center justify-center px-4 py-3 border-2 border-blue-500/30 rounded-2xl shadow-sm text-sm font-medium text-blue-100/80 bg-white/5 hover:bg-white/10 hover:border-blue-500/50 hover:shadow-md transition-all duration-300 transform hover:scale-[1.02] backdrop-blur-sm">
                                <i
                                    class="fab fa-facebook text-blue-400 mr-2 text-lg group-hover:scale-110 transition-transform duration-200"></i>
                                <span class="font-medium">Facebook</span>
                            </button>
                        </div>

                        <!-- Additional Security Note -->
                        <div class="flex items-center justify-center space-x-2 text-xs text-blue-200/60">
                            <i class="fas fa-shield-alt text-green-400"></i>
                            <span>Your property data is secure</span>
                        </div>
                    </div>

                    <!-- Enhanced Submit Button -->
                    <div class="space-y-4">
                        <button type="submit"
                            class="w-full group relative flex justify-center items-center py-4 px-6 border border-transparent rounded-2xl shadow-lg text-base font-bold text-white bg-gradient-to-r from-blue-600 via-cyan-500 to-blue-600 hover:from-blue-700 hover:via-cyan-600 hover:to-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-500/25 transition-all duration-300 transform hover:scale-[1.02] hover:shadow-xl overflow-hidden">
                            <div
                                class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent transform translate-x-full group-hover:translate-x-full transition-transform duration-700">
                            </div>
                            <span class="relative flex items-center space-x-2">
                                <i class="fas fa-sign-in-alt"></i>
                                <span>Access Dashboard</span>
                            </span>
                        </button>

                        <!-- Quick Links -->
                        <div class="flex items-center justify-center space-x-6 text-xs text-blue-200/60">
                            <a href="#"
                                class="hover:text-cyan-300 transition-colors duration-300 flex items-center space-x-1">
                                <i class="fas fa-question-circle"></i>
                                <span>Help Center</span>
                            </a>
                            <a href="#"
                                class="hover:text-cyan-300 transition-colors duration-300 flex items-center space-x-1">
                                <i class="fas fa-user-shield"></i>
                                <span>Privacy Policy</span>
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
            0% {
                transform: translateX(-100%);
            }

            100% {
                transform: translateX(100%);
            }
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

        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .animate-shimmer {
            animation: shimmer 2s infinite;
        }

        .animate-slideDown {
            animation: slideDown 0.3s ease-out;
        }

        .animate-float {
            animation: float 3s ease-in-out infinite;
        }

        /* Enhanced Focus States */
        .group:focus-within .group-focus-within\:text-cyan-300 {
            color: rgb(103 232 249);
        }

        /* Glass morphism effect */
        .backdrop-blur-2xl {
            backdrop-filter: blur(40px);
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(59, 130, 246, 0.5);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(59, 130, 246, 0.7);
        }

        /* Glow effects */
        .glow-blue {
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.5);
        }

        .glow-cyan {
            box-shadow: 0 0 20px rgba(6, 182, 212, 0.5);
        }

        /* Real estate theme */
        .property-card {
            background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
        }
    </style>

    <script>
        // Enhanced Password Toggle
        document.getElementById('togglePassword').addEventListener('click', function () {
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
        document.addEventListener('DOMContentLoaded', function () {
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
        emailInput.addEventListener('blur', function () {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const checkIcon = this.parentElement.querySelector('.fa-check-circle');

            if (this.value && emailRegex.test(this.value)) {
                this.classList.remove('border-red-400', 'border-blue-400');
                this.classList.add('border-green-400');
                if (checkIcon) {
                    checkIcon.style.opacity = '1';
                }
            } else if (this.value) {
                this.classList.remove('border-green-400', 'border-blue-400');
                this.classList.add('border-red-400');
                if (checkIcon) {
                    checkIcon.style.opacity = '0';
                }
            }
        });

        // Password strength indicator
        passwordInput.addEventListener('input', function () {
            const password = this.value;
            let strength = 0;

            if (password.length >= 8) strength++;
            if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;

            // Update border color based on strength
            this.classList.remove('border-red-400', 'border-yellow-400', 'border-green-400', 'border-blue-400');
            if (password.length > 0) {
                if (strength <= 1) this.classList.add('border-red-400');
                else if (strength === 2) this.classList.add('border-yellow-400');
                else if (strength >= 3) this.classList.add('border-green-400');
            }
        });

        // Smooth scroll for error messages
        if (document.querySelector('.bg-red-500\/20')) {
            document.querySelector('.bg-red-500\/20').scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }

        // Add loading state to submit button
        form.addEventListener('submit', function (e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalContent = submitBtn.innerHTML;

            submitBtn.disabled = true;
            submitBtn.innerHTML = `
            <span class="relative flex items-center space-x-2">
                <i class="fas fa-spinner fa-spin"></i>
                <span>{{ __('auth.signing_in') }}</span>
            </span>
        `;

            // Re-enable after 5 seconds (fallback)
            setTimeout(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalContent;
            }, 5000);
        });

        // Add keyboard navigation
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' && e.ctrlKey) {
                form.submit();
            }
        });

        // Tooltip functionality
        const infoButtons = document.querySelectorAll('[title]');
        infoButtons.forEach(button => {
            button.addEventListener('mouseenter', function () {
                this.style.transform = 'scale(1.1)';
            });

            button.addEventListener('mouseleave', function () {
                this.style.transform = 'scale(1)';
            });
        });
    </script>

@endsection