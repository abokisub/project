<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Login - KoboPoint</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Top Left Logo - Fixed Position -->
    <div class="fixed top-4 left-4 sm:top-6 sm:left-6 z-10">
        @if(file_exists(public_path('images/logo.png')))
            <img src="{{ asset('images/logo.png') }}" alt="KoboPoint Logo" class="h-8 sm:h-10 lg:h-12 w-auto">
        @elseif(file_exists(public_path('images/logo.svg')))
            <img src="{{ asset('images/logo.svg') }}" alt="KoboPoint Logo" class="h-8 sm:h-10 lg:h-12 w-auto">
        @elseif(file_exists(public_path('images/logo.jpg')))
            <img src="{{ asset('images/logo.jpg') }}" alt="KoboPoint Logo" class="h-8 sm:h-10 lg:h-12 w-auto">
        @else
            <div class="w-8 h-8 sm:w-10 sm:h-10 lg:w-12 lg:h-12 bg-green-600 rounded-lg flex items-center justify-center shadow-lg">
                <span class="text-white text-lg sm:text-xl lg:text-2xl font-bold">K</span>
            </div>
        @endif
    </div>

    <div class="min-h-screen flex flex-col lg:flex-row">
        <!-- Left Section - Welcome -->
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-green-50 to-emerald-100 items-center justify-center p-8 lg:p-12">
            <div class="max-w-md">
                <!-- Welcome Text -->
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900 mb-4 sm:mb-6 leading-tight">
                    Welcome to<br>KoboPoint
                </h1>
                <p class="text-sm sm:text-base lg:text-lg text-gray-700 leading-relaxed">
                    Your trusted platform for digital services. Buy data, airtime, pay bills, and more.
                </p>
            </div>
        </div>

        <!-- Right Section - Login Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-4 sm:p-6 lg:p-8 bg-white pt-16 sm:pt-20 lg:pt-8">
            <div class="w-full max-w-md">
                <!-- Title -->
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 mb-2">Sign In KoboPoint</h2>
                <p class="text-sm sm:text-base text-gray-600 mb-6 sm:mb-8">Hi! Welcome back, you've been missed</p>

                <!-- Error Messages -->
                @if ($errors->any())
                    <div class="mb-4 sm:mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 p-3 sm:p-4 rounded">
                        <div class="flex">
                            <i class="fas fa-exclamation-circle mr-2 mt-0.5 text-xs sm:text-sm"></i>
                            <div>
                                @foreach ($errors->all() as $error)
                                    <p class="text-xs sm:text-sm">{{ $error }}</p>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Success Messages -->
                @if (session('success'))
                    <div class="mb-4 sm:mb-6 bg-green-50 border-l-4 border-green-500 text-green-700 p-3 sm:p-4 rounded">
                        <div class="flex">
                            <i class="fas fa-check-circle mr-2 mt-0.5 text-xs sm:text-sm"></i>
                            <p class="text-xs sm:text-sm">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                <!-- Login Form -->
                <form method="POST" action="{{ route('admin.login') }}" id="login-form" class="space-y-4 sm:space-y-6">
                    @csrf

                    <!-- Email/Phone Field -->
                    <div>
                        <label for="email" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1.5 sm:mb-2">
                            Phone/Email
                        </label>
                        <div class="relative">
                            <input 
                                id="email" 
                                name="email" 
                                type="text" 
                                autocomplete="email" 
                                required 
                                value="{{ old('email') }}"
                                class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition text-gray-900 placeholder-gray-400"
                                placeholder="Phone/Email"
                            >
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400 text-sm"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label for="password" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1.5 sm:mb-2">
                            Password
                        </label>
                        <div class="relative">
                            <input 
                                id="password" 
                                name="password" 
                                type="password" 
                                autocomplete="current-password" 
                                required 
                                class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition text-gray-900 placeholder-gray-400 pr-10 sm:pr-12"
                                placeholder="Enter Password"
                            >
                            <button 
                                type="button" 
                                onclick="togglePassword()"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition"
                            >
                                <i id="password-icon" class="fas fa-eye-slash text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 sm:gap-0">
                        <div class="flex items-center">
                            <input 
                                id="remember" 
                                name="remember" 
                                type="checkbox" 
                                class="h-3.5 w-3.5 sm:h-4 sm:w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded"
                            >
                            <label for="remember" class="ml-2 block text-xs sm:text-sm text-gray-700">
                                Remember me
                            </label>
                        </div>
                        <div class="text-xs sm:text-sm">
                            <a href="#" class="font-medium text-green-600 hover:text-green-700">
                                Forgot Password?
                            </a>
                        </div>
                    </div>

                    <!-- Sign In Button -->
                    <button 
                        type="submit" 
                        id="login-button"
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold text-sm sm:text-base py-2.5 sm:py-3 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition duration-150 ease-in-out transform hover:scale-[1.02] shadow-lg disabled:opacity-70 disabled:cursor-not-allowed disabled:transform-none"
                    >
                        <span id="button-text">
                            <i class="fas fa-sign-in-alt mr-2"></i>Sign In
                        </span>
                        <span id="button-loader" class="hidden">
                            <i class="fas fa-spinner fa-spin mr-2"></i>Processing...
                        </span>
                    </button>
                </form>

                <!-- Divider -->
                <div class="mt-6 sm:mt-8 mb-4 sm:mb-6">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-xs sm:text-sm">
                            <span class="px-3 sm:px-4 bg-white text-gray-500">Or sign in with</span>
                        </div>
                    </div>
                </div>

                <!-- Social Login Buttons -->
                <div class="flex justify-center space-x-3 sm:space-x-4 mb-6 sm:mb-8">
                    <button 
                        type="button"
                        class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-white border border-gray-300 flex items-center justify-center hover:bg-gray-50 transition shadow-sm"
                        title="Sign in with Apple"
                    >
                        <i class="fab fa-apple text-gray-900 text-base sm:text-xl"></i>
                    </button>
                    <button 
                        type="button"
                        class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-white border border-gray-300 flex items-center justify-center hover:bg-gray-50 transition shadow-sm"
                        title="Sign in with Google"
                    >
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                    </button>
                    <button 
                        type="button"
                        class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-white border border-gray-300 flex items-center justify-center hover:bg-gray-50 transition shadow-sm"
                        title="Sign in with Facebook"
                    >
                        <i class="fab fa-facebook-f text-blue-600 text-base sm:text-xl"></i>
                    </button>
                </div>

                <!-- Footer -->
                <div class="text-center mt-6 sm:mt-8">
                    <p class="text-xs sm:text-sm text-gray-500">
                        Powered by <span class="font-bold text-green-600">KoboPoint</span> <span class="text-gray-600">Digital Media</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('password-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.classList.remove('fa-eye-slash');
                passwordIcon.classList.add('fa-eye');
            } else {
                passwordInput.type = 'password';
                passwordIcon.classList.remove('fa-eye');
                passwordIcon.classList.add('fa-eye-slash');
            }
        }

        // Handle form submission with loading indicator
        document.getElementById('login-form').addEventListener('submit', function(e) {
            const button = document.getElementById('login-button');
            const buttonText = document.getElementById('button-text');
            const buttonLoader = document.getElementById('button-loader');
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');

            // Basic validation
            if (!emailInput.value.trim() || !passwordInput.value.trim()) {
                return; // Let browser handle validation
            }

            // Show loading state
            button.disabled = true;
            buttonText.classList.add('hidden');
            buttonLoader.classList.remove('hidden');
        });
    </script>
</body>
</html>
