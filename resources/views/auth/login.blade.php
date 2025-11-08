<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>PCTVS Login</title>
  
  <!-- Favicon -->
  <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
  <link rel="shortcut icon" type="image/png" href="{{ asset('images/favicon.png') }}">
  <link rel="apple-touch-icon" href="{{ asset('images/favicon.png') }}">
  
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
    
    :root {
      --primary: #10b981;
      --primary-dark: #059669;
      --primary-light: #a7f3d0;
      --surface: #ffffff;
      --text-primary: #111827;
      --text-secondary: #6b7280;
      --border: #e5e7eb;
      --error: #ef4444;
      --success: #10b981;
    }
    
    * {
      box-sizing: border-box;
      font-family: 'Inter', sans-serif;
    }
    
    body {
      margin: 0;
      padding: 0;
      min-height: 100vh;
      background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 50%, #ecfdf5 100%);
      color: var(--text-primary);
    }
    
    @keyframes float {
      0%, 100% { transform: translateY(0px); }
      50% { transform: translateY(-10px); }
    }
    
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes shimmer {
      0% { background-position: -200px 0; }
      100% { background-position: 200px 0; }
    }
    
    @keyframes pulseSoft {
      0%, 100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.2); }
      50% { box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
    }
    
    .animate-float {
      animation: float 6s ease-in-out infinite;
    }
    
    .animate-fadeIn {
      animation: fadeIn 0.6s ease-out forwards;
    }
    
    .animate-shimmer {
      background: linear-gradient(90deg, #f8fafc 0%, #f0fdf4 50%, #f8fafc 100%);
      background-size: 200% 100%;
      animation: shimmer 2s infinite linear;
    }
    
    .animate-pulse-soft {
      animation: pulseSoft 2s infinite;
    }
    
    .glass-effect {
      background: rgba(255, 255, 255, 0.7);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.3);
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    }
    
    .btn-primary {
      background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
      box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
      transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
    }
    
    .input-focus:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    }
    
    .shake {
      animation: shake 0.5s ease-in-out;
    }
    
    @keyframes shake {
      0%, 100% { transform: translateX(0); }
      20%, 60% { transform: translateX(-5px); }
      40%, 80% { transform: translateX(5px); }
    }

    /* Image fallback styles */
    .logo-fallback {
      width: 24px;
      height: 24px;
      background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
      border-radius: 6px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: bold;
      font-size: 12px;
    }
  </style>
</head>

<body class="min-h-screen flex items-center justify-center p-4">
  <!-- Background Elements -->
  <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
    <div class="absolute -top-40 -right-40 w-80 h-80 rounded-full bg-green-100 opacity-30 animate-float"></div>
    <div class="absolute -bottom-40 -left-40 w-80 h-80 rounded-full bg-green-50 opacity-40 animate-float" style="animation-delay: 2s;"></div>
    <div class="absolute top-1/2 left-1/4 w-20 h-20 rounded-full bg-green-200 opacity-20 animate-float" style="animation-delay: 4s;"></div>
  </div>

  <!-- Main Container -->
  <div class="w-full max-w-6xl flex flex-col md:flex-row rounded-3xl overflow-hidden glass-effect z-10 shadow-2xl">
    
    <!-- Left Branding Panel -->
    <div class="hidden md:flex w-1/2 bg-gradient-to-br from-green-600 to-emerald-800 text-white flex-col justify-center items-center p-12 relative overflow-hidden">
      <!-- Background Pattern -->
      <div class="absolute inset-0 opacity-10">
        <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
          <defs>
            <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
              <path d="M 40 0 L 0 0 0 40" fill="none" stroke="white" stroke-width="1"/>
            </pattern>
          </defs>
          <rect width="100%" height="100%" fill="url(#grid)" />
        </svg>
      </div>
      
      <!-- Floating Elements -->
      <div class="absolute top-10 left-10 w-20 h-20 rounded-full bg-white/10 animate-float"></div>
      <div class="absolute bottom-16 right-16 w-16 h-16 rounded-full bg-white/5 animate-float" style="animation-delay: 1.5s;"></div>
      <div class="absolute top-1/3 right-20 w-12 h-12 rounded-full bg-white/15 animate-float" style="animation-delay: 3s;"></div>
      
      <!-- Logo & Content -->
      <div class="relative z-10 flex flex-col items-center text-center animate-fadeIn">
        <div class="w-32 h-32 rounded-2xl bg-white flex items-center justify-center shadow-2xl mb-8 relative animate-pulse-soft">
          <img 
            src="{{ asset('/images/logo.png') }}" 
            alt="PCTVS Logo" 
            class="w-24 h-24 object-contain"
            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
          />
          <div class="logo-fallback" style="width: 80px; height: 80px; font-size: 24px; display: none;">P</div>
        </div>
        <h1 class="text-4xl font-bold mb-4">Welcome to PCTVS</h1>
        <p class="text-lg text-green-100 max-w-md mb-6">Securely access your personalized dashboard and manage your account with ease.</p>
        <div class="h-1 w-32 bg-gradient-to-r from-green-300 to-emerald-400 rounded-full shadow-lg animate-shimmer"></div>
      </div>
      
      <!-- Feature List -->
      <div class="mt-12 grid grid-cols-2 gap-6 relative z-10 w-full max-w-md">
        <div class="flex items-center space-x-3">
          <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
          </div>
          <span class="text-sm">Secure Access</span>
        </div>
        <div class="flex items-center space-x-3">
          <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
          </div>
          <span class="text-sm">Fast Performance</span>
        </div>
        <div class="flex items-center space-x-3">
          <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
            </svg>
          </div>
          <span class="text-sm">Dashboard</span>
        </div>
        <div class="flex items-center space-x-3">
          <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
          </div>
          <span class="text-sm">Privacy Focused</span>
        </div>
      </div>
    </div>

    <!-- Right Login Form -->
    <div class="w-full md:w-1/2 flex items-center justify-center p-8 md:p-12">
      <div class="w-full max-w-md bg-white/90 backdrop-blur-lg rounded-2xl p-8 relative @if ($errors->any()) shake @endif animate-fadeIn" style="animation-delay: 0.2s;">
        
        <!-- Close Button -->
        <a href="{{ route('landing') }}" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-all duration-200 p-2 rounded-full hover:bg-gray-100">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </a>

        <!-- Header -->
        <div class="text-center mb-8">
          <h2 class="text-3xl font-bold text-gray-800 mb-2">Welcome Back</h2>
          <p class="text-gray-500">Sign in to your PCTVS account</p>
        </div>

        <!-- Error Messages -->
        @if ($errors->any())
          <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl shadow-sm">
            <div class="flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span class="font-medium">Please check the following:</span>
            </div>
            <ul class="list-disc list-inside text-sm mt-2">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <!-- Login Form -->
        <form method="POST" action="/login" class="space-y-6" x-data="{ loading: false, caps: false }" @submit="loading = true">
          @csrf

          <!-- Email Field -->
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
            <div class="relative">
              <input 
                type="email" 
                id="email" 
                name="email" 
                required 
                class="w-full pl-11 pr-4 py-3.5 rounded-xl border border-gray-200 bg-white/80 shadow-sm text-sm focus:outline-none input-focus transition-all duration-200 placeholder:text-gray-400" 
                placeholder="you@example.com"
                value="{{ old('email') }}"
              />
              <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
              </span>
            </div>
          </div>

          <!-- Password Field -->
          <div x-data="{ show: false }">
            <div class="flex justify-between items-center mb-2">
              <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
              <a href="#" class="text-sm text-green-600 hover:text-green-700 font-medium transition-colors">Forgot password?</a>
            </div>
            <div class="relative">
              <input 
                :type="show ? 'text' : 'password'" 
                id="password" 
                name="password" 
                required 
                @keyup="caps = $event.getModifierState('CapsLock')" 
                class="w-full pl-11 pr-12 py-3.5 rounded-xl border border-gray-200 bg-white/80 shadow-sm text-sm focus:outline-none input-focus transition-all duration-200 placeholder:text-gray-400" 
                placeholder="Enter your password"
              />
              <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
              </span>
              <button 
                type="button" 
                @click="show = !show" 
                class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors"
              >
                <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                </svg>
              </button>
            </div>
            <p x-show="caps" class="text-xs text-red-500 mt-2 flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
              Caps Lock is on
            </p>
          </div>

          <!-- Remember Me -->
          <div class="flex items-center">
            <input 
              type="checkbox" 
              id="remember" 
              name="remember" 
              class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded"
              {{ old('remember') ? 'checked' : '' }}
            />
            <label for="remember" class="ml-2 block text-sm text-gray-700">Remember me for 30 days</label>
          </div>

          <!-- Submit Button -->
          <button 
            type="submit" 
            class="relative w-full btn-primary text-white py-3.5 rounded-xl font-semibold text-sm transition-all duration-300 flex items-center justify-center"
            :disabled="loading"
            :class="loading ? 'opacity-75 cursor-not-allowed' : ''"
          >
            <span x-show="!loading" class="flex items-center">
              Sign In
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
              </svg>
            </span>
            <svg x-show="loading" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 100 16 8 8 0 01-8-8z"></path>
            </svg>
          </button>
        </form>

        <!-- Divider -->
        <div class="my-6 flex items-center">
          <div class="flex-grow border-t border-gray-200"></div>
          <span class="mx-4 text-sm text-gray-500">or continue with</span>
          <div class="flex-grow border-t border-gray-200"></div>
        </div>

        <!-- Google Login Button -->
        <div>
          <a 
            href="{{ route('google.redirect') }}" 
            class="w-full flex items-center justify-center gap-3 border border-gray-200 py-3.5 rounded-xl font-medium text-sm text-gray-700 hover:bg-gray-50 transition-all duration-200 shadow-sm hover:shadow"
          >
            <img 
              src="https://www.svgrepo.com/show/355037/google.svg" 
              alt="Google" 
              class="w-5 h-5"
              onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
            />
            <div class="logo-fallback" style="display: none;">G</div>
            Continue with Google
          </a>
        </div>

        <!-- Footer -->
        <p class="text-xs text-gray-500 text-center mt-8">
          Protected by PCTVS Security • © 2025 • 
          <a href="#" class="text-green-600 hover:text-green-700 transition-colors">Privacy Policy</a>
        </p>
      </div>
    </div>
  </div>
</body>
</html>