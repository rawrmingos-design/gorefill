<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - GoRefill</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center py-8">
    <div class="max-w-md w-full mx-4 animate__animated animate__fadeIn">
        <div class="bg-white rounded-lg shadow-xl p-8">
            <div class="text-center mb-8">
                <div class="inline-block p-3 bg-blue-100 rounded-full mb-4">
                    <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Create Account</h1>
                <p class="text-gray-600">Join GoRefill today</p>
            </div>

            <form id="registerForm" class="space-y-4">
                <!-- Name Field -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                           placeholder="John Doe">
                    <p class="text-red-500 text-sm mt-1 hidden" id="name-error"></p>
                </div>

                <!-- Email Field -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" id="email" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                           placeholder="your@email.com">
                    <p class="text-red-500 text-sm mt-1 hidden" id="email-error"></p>
                </div>

                <!-- Phone Field -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">
                        Phone Number <span class="text-gray-400 text-sm">(Optional)</span>
                    </label>
                    <input type="tel" name="phone" id="phone" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                           placeholder="08123456789">
                </div>

                <!-- Password Field with Strength Indicator -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" name="password" id="password" required 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition pr-12"
                               placeholder="••••••••">
                        <button type="button" id="togglePassword" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                            <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>
                    <!-- Password Strength Indicator -->
                    <div class="mt-2">
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm text-gray-600">Password strength:</span>
                            <span id="strengthText" class="text-sm font-semibold"></span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-1">
                            <div id="strengthBar" class="password-strength-bar rounded-full"></div>
                        </div>
                    </div>
                    <p class="text-gray-500 text-xs mt-1">Must be at least 8 characters</p>
                    <p class="text-red-500 text-sm mt-1 hidden" id="password-error"></p>
                </div>

                <!-- Confirm Password Field -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">
                        Confirm Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="password_confirm" id="password_confirm" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                           placeholder="••••••••">
                    <p class="text-red-500 text-sm mt-1 hidden" id="password_confirm-error"></p>
                </div>

                <!-- Submit Button -->
                <button type="submit" id="submitBtn"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition transform hover:scale-105 shadow-lg flex items-center justify-center">
                    <span id="btnText">Create Account</span>
                    <svg id="btnLoader" class="animate-spin h-5 w-5 ml-2 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-gray-600">
                    Already have an account? 
                    <a href="?route=auth.login" class="text-blue-600 hover:underline font-semibold">Login here</a>
                </p>
                <a href="?route=home" class="text-gray-500 hover:underline text-sm mt-2 inline-block">← Back to Home</a>
            </div>
        </div>

        <div class="text-center mt-4 text-gray-600 text-sm">
            <p>✨ Day 4: Enhanced Authentication UI</p>
        </div>
    </div>

    <script>
    // Password strength checker
    const passwordInput = document.getElementById('password');
    const strengthBar = document.getElementById('strengthBar');
    const strengthText = document.getElementById('strengthText');
    
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        const strength = calculatePasswordStrength(password);
        
        // Reset bar
        strengthBar.style.transition = 'all 0.3s ease';
        strengthBar.style.height = '4px';
        strengthBar.className = 'rounded-full';
        
        if (password.length === 0) {
            strengthBar.style.width = '0%';
            strengthBar.style.backgroundColor = 'transparent';
            strengthText.textContent = '';
        } else if (strength < 3) {
            // Weak - Red
            strengthBar.style.width = '33%';
            strengthBar.style.backgroundColor = '#ef4444';
            strengthText.textContent = 'Weak';
            strengthText.className = 'text-sm font-semibold text-red-500';
        } else if (strength < 5) {
            // Medium - Orange
            strengthBar.style.width = '66%';
            strengthBar.style.backgroundColor = '#f59e0b';
            strengthText.textContent = 'Medium';
            strengthText.className = 'text-sm font-semibold text-orange-500';
        } else {
            // Strong - Green
            strengthBar.style.width = '100%';
            strengthBar.style.backgroundColor = '#10b981';
            strengthText.textContent = 'Strong';
            strengthText.className = 'text-sm font-semibold text-green-500';
        }
    });
    
    function calculatePasswordStrength(password) {
        let strength = 0;
        if (password.length >= 8) strength++;
        if (password.length >= 12) strength++;
        if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
        if (/\d/.test(password)) strength++;
        if (/[^a-zA-Z\d]/.test(password)) strength++;
        return strength;
    }
    
    // Toggle password visibility
    document.getElementById('togglePassword').addEventListener('click', function() {
        const password = document.getElementById('password');
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
    });
    
    // Real-time validation
    document.getElementById('password_confirm').addEventListener('input', function() {
        const password = document.getElementById('password').value;
        const confirm = this.value;
        const error = document.getElementById('password_confirm-error');
        
        if (confirm && password !== confirm) {
            error.textContent = 'Passwords do not match';
            error.classList.remove('hidden');
            this.classList.add('border-red-500');
        } else {
            error.classList.add('hidden');
            this.classList.remove('border-red-500');
        }
    });
    
    // Form submission
    document.getElementById('registerForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const submitBtn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        const btnLoader = document.getElementById('btnLoader');
        
        // Disable button and show loader
        submitBtn.disabled = true;
        btnText.textContent = 'Creating account...';
        btnLoader.classList.remove('hidden');
        
        const formData = new FormData(e.target);
        
        try {
            const response = await fetch('?route=auth.register', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Welcome to GoRefill!',
                    text: data.message,
                    showConfirmButton: false,
                    timer: 2000
                }).then(() => {
                    window.location.href = data.redirect;
                });
            } else {
                let errorMessage = data.message || 'Registration failed';
                if (data.errors) {
                    errorMessage = Object.values(data.errors).join('<br>');
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Registration Failed',
                    html: errorMessage,
                    confirmButtonColor: '#3b82f6'
                });
                
                // Re-enable button
                submitBtn.disabled = false;
                btnText.textContent = 'Create Account';
                btnLoader.classList.add('hidden');
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Something went wrong. Please try again.',
                confirmButtonColor: '#3b82f6'
            });
            
            // Re-enable button
            submitBtn.disabled = false;
            btnText.textContent = 'Create Account';
            btnLoader.classList.add('hidden');
        }
    });
    </script>
</body>
</html>
