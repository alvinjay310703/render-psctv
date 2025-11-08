<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PCTVS Admin - @yield('title')</title>
    <meta name="pusher-key" content="{{ config('broadcasting.connections.pusher.key') }}">
<meta name="pusher-cluster" content="{{ config('broadcasting.connections.pusher.options.cluster') }}">
<meta name="user-id" content="{{ Auth::id() }}">

<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
<link rel="manifest" href="{{ asset('site.webmanifest') }}">
<link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">


    <!-- Alpine.js & SweetAlert -->
    <script src="//unpkg.com/alpinejs" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Laravel Vite Assets -->
    @vite([
        'resources/css/app.css',
        'resources/css/admin.css',
        'resources/js/app.js',
        'resources/js/admin.js'
    ])
</head>

<body class="bg-gray-100 flex">
    <!-- Sidebar -->
    @include('layouts.partials.sidebar')

    <!-- Main Content -->
    <div id="mainContent" class="flex-1 pt-24 px-6 content-expanded transition-all duration-300">

        <!-- Header -->
        @include('layouts.partials.header')

        <!-- Page Content -->
        <main>
            {{-- ✅ Flash Messages --}}
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show"
                    x-init="setTimeout(() => show = false, 5000)"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform translate-y-2"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform translate-y-0"
                    x-transition:leave-end="opacity-0 transform -translate-y-2"
                    class="mb-6 p-4 rounded-xl bg-gradient-to-r from-emerald-50 to-green-50 border-l-4 border-emerald-500 shadow-lg backdrop-blur-sm">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-emerald-800">Success!</p>
                                <p class="text-sm text-emerald-700 mt-1">{{ session('success') }}</p>
                            </div>
                        </div>
                        <button @click="show = false" class="flex-shrink-0 ml-4 text-emerald-600 hover:text-emerald-800 transition-colors duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div x-data="{ show: true }" x-show="show"
                    x-init="setTimeout(() => show = false, 6000)"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform translate-y-2"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform translate-y-0"
                    x-transition:leave-end="opacity-0 transform -translate-y-2"
                    class="mb-6 p-4 rounded-xl bg-gradient-to-r from-red-50 to-rose-50 border-l-4 border-red-500 shadow-lg backdrop-blur-sm">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-red-800">Error!</p>
                                <p class="text-sm text-red-700 mt-1">{{ session('error') }}</p>
                            </div>
                        </div>
                        <button @click="show = false" class="flex-shrink-0 ml-4 text-red-600 hover:text-red-800 transition-colors duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            @endif

            @if(session('info'))
                <div x-data="{ show: true }" x-show="show"
                    x-init="setTimeout(() => show = false, 5000)"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform translate-y-2"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform translate-y-0"
                    x-transition:leave-end="opacity-0 transform -translate-y-2"
                    class="mb-6 p-4 rounded-xl bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-500 shadow-lg backdrop-blur-sm">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-blue-800">Information</p>
                                <p class="text-sm text-blue-700 mt-1">{{ session('info') }}</p>
                            </div>
                        </div>
                        <button @click="show = false" class="flex-shrink-0 ml-4 text-blue-600 hover:text-blue-800 transition-colors duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
<!-- Scripts -->
<script>
    // ✅ Sidebar Toggle
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');

        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('content-expanded');

        // Smooth transition effect
        sidebar.classList.add('transition-all', 'duration-300', 'ease-in-out');
        mainContent.classList.add('transition-all', 'duration-300', 'ease-in-out');
    }

    // ✅ Toggle Submenu with Smooth Transition
    window.toggleSubmenu = function(id) {
        const menu = document.getElementById(id);
        if (!menu) return;

        // Add smooth transition classes
        menu.classList.add('transition-all', 'duration-300', 'ease-in-out');

        // Toggle visibility with smooth animation
        if (menu.classList.contains('hidden')) {
            menu.classList.remove('hidden');
            // Force reflow to ensure transition works
            menu.offsetHeight;
            menu.classList.add('opacity-100', 'max-h-96');
        } else {
            menu.classList.remove('opacity-100', 'max-h-96');
            menu.classList.add('opacity-0', 'max-h-0');
            // Hide after transition
            setTimeout(() => {
                menu.classList.add('hidden');
            }, 300);
        }
    };

    // ✅ User Dropdown Menu
    function toggleUserMenu(event) {
        if (event) event.stopPropagation();
        const menu = document.getElementById('userMenu');
        if (!menu) return;

        menu.classList.toggle('opacity-100');
        menu.classList.toggle('opacity-0');
        menu.classList.toggle('translate-y-0');
        menu.classList.toggle('-translate-y-2');
        menu.classList.toggle('pointer-events-none');
        menu.classList.add('transition-all', 'duration-200', 'ease-out');
    }

    // ✅ Close user dropdown when clicking outside
    document.addEventListener('click', function (e) {
        const menu = document.getElementById('userMenu');
        const button = document.getElementById('userMenuButton');
        if (!menu) return;
        if (button.contains(e.target)) return; // click is inside button
        if (!menu.contains(e.target)) {
            menu.classList.add('pointer-events-none', 'opacity-0', '-translate-y-2');
            menu.classList.remove('opacity-100', 'translate-y-0');
        }
    });
</script>

    {{-- ✅ Render scripts pushed by child views --}}
    @stack('scripts')
</body>
</html>
