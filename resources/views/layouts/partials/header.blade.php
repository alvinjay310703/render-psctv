<header id="mainHeader" class="fixed top-0 left-60 right-0 z-40 flex items-center bg-white shadow-lg px-6 py-4 border-b border-gray-100 transition-all duration-300 backdrop-blur-sm bg-white/95"
        x-data="mobileMenu()">

    <!-- Mobile Menu Toggle -->
    <button @click="toggle()"
        class="md:hidden flex items-center justify-center w-11 h-11 bg-gradient-to-r from-gray-800 to-gray-700 text-white rounded-xl hover:from-gray-700 hover:to-gray-600 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105 mr-3">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>

    <!-- Sidebar Toggle -->
    <button onclick="toggleSidebar()"
        class="hidden md:flex items-center justify-center w-11 h-11 bg-gradient-to-r from-gray-800 to-gray-700 text-white rounded-xl hover:from-gray-700 hover:to-gray-600 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>

    <!-- Title with improved typography -->
    <div class="ml-6">
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">@yield('title')</h1>

        <!-- Breadcrumbs -->
        @hasSection('breadcrumbs')
            <nav class="flex items-center text-sm text-gray-500 mt-1" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2">
                    <li>
                        <a href="{{ route('dashboard') }}" class="hover:text-gray-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                        </a>
                    </li>
                    @yield('breadcrumbs')
                </ol>
            </nav>
        @endif
    </div>

    <div class="ml-auto flex items-center space-x-6">

       <!-- 🔍 Global Search -->
<div
    x-data="{
        open: false,
        query: '',
        results: [],
        activeIndex: 0,
        loading: false,

        async search() {
            if (this.query.length < 2) {
                this.results = [];
                this.open = false;
                this.loading = false;
                return;
            }

            this.loading = true;
            try {
                const res = await fetch(`/admin/search?q=${this.query}`);
                this.results = await res.json();
                this.open = true;
                this.activeIndex = 0;
            } catch (error) {
                console.error('Search error:', error);
                this.results = [];
                this.open = false;
            } finally {
                this.loading = false;
            }
        },

        navigate(direction) {
            if (!this.open || this.results.length === 0) return;
            const max = this.results.length - 1;
            if (direction === 'up') {
                this.activeIndex = this.activeIndex > 0 ? this.activeIndex - 1 : max;
            } else {
                this.activeIndex = this.activeIndex < max ? this.activeIndex + 1 : 0;
            }
        },

        selectActive() {
            if (this.results[this.activeIndex]) {
                window.location.href = this.results[this.activeIndex].url;
            }
        }
    }"
    class="relative hidden sm:block"
>
    <div class="relative">
        <input
            type="text"
            placeholder="Search customers, technicians, or requests..."
            x-model="query"
            @input.debounce.400ms="search()"
            @keydown.arrow-down.prevent="navigate('down')"
            @keydown.arrow-up.prevent="navigate('up')"
            @keydown.enter.prevent="selectActive()"
            @click.away="open = false"
            class="pl-12 pr-12 py-3 w-80 rounded-xl border border-gray-200 bg-gray-50 text-sm focus:ring-2 focus:ring-blue-500 focus:bg-white focus:outline-none focus:border-blue-500 transition-all duration-300 shadow-sm hover:shadow-md"
        />

        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1110.5 3a7.5 7.5 0 016.15 13.65z" />
            </svg>
        </div>

        <!-- Loading Spinner -->
        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
            <svg x-show="loading" class="animate-spin h-4 w-4 text-blue-500" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
    </div>

    <!-- Dropdown Results -->
    <div
        x-show="open && results.length > 0"
        x-transition
        class="absolute top-full left-0 mt-3 w-full bg-white border border-gray-200 rounded-xl shadow-xl z-50 max-h-64 overflow-y-auto"
    >
        <template x-for="(item, index) in results" :key="item.type + item.id">
            <a :href="item.url"
               :class="{
                   'bg-blue-50 text-blue-700': index === activeIndex,
                   'hover:bg-gray-50': index !== activeIndex
               }"
               class="flex items-center px-4 py-3 text-sm text-gray-700 transition cursor-pointer rounded-lg mx-1"
            >
                <span x-text="item.icon" class="mr-3 text-lg"></span>
                <div class="flex-1">
                    <span class="font-semibold" x-text="item.name"></span>
                    <p class="text-xs text-gray-500" x-text="item.type"></p>
                </div>
            </a>
        </template>
    </div>

    <!-- Empty state -->
    <div
        x-show="open && results.length === 0 && query.length > 1"
        class="absolute top-full left-0 mt-3 w-full bg-white border border-gray-200 rounded-xl shadow-xl p-4 text-gray-500 text-sm"
    >
        No results found.
    </div>
</div>


        <!-- 🔔 Notifications -->
        @php
            $user = Auth::user();
            $notifications = $user ? $user->unreadNotifications()->take(5)->get() : collect();
        @endphp

        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="relative flex items-center justify-center w-11 h-11 bg-gray-50 hover:bg-gray-100 text-gray-600 hover:text-gray-800 rounded-xl transition-all duration-200 shadow-sm hover:shadow-md" id="notifBell">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>

                <span id="notifCount"
                      class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full min-w-[18px] h-[18px] flex items-center justify-center font-semibold {{ $notifications->count() > 0 ? '' : 'hidden' }}">
                    {{ $notifications->count() }}
                </span>
            </button>

            <!-- Notification Dropdown -->
            <div x-show="open" @click.away="open = false" x-transition.opacity
                class="absolute right-0 mt-3 w-80 bg-white shadow-xl rounded-xl border border-gray-200 z-50 overflow-hidden transition-all duration-200">

                <div class="p-4 border-b border-gray-100 font-semibold text-gray-700 flex justify-between items-center">
                    <span>Notifications</span>
                    <a href="{{ route('notifications.index') }}" class="text-sm text-blue-600 hover:underline">View all</a>
                </div>

                <ul id="notifList" class="max-h-64 overflow-y-auto">
                    @forelse($notifications as $note)
                        <li class="px-4 py-3 hover:bg-gray-50 border-b border-gray-50 transition-colors">
                            <p class="text-sm font-semibold text-gray-800">
                                📢 {{ $note->data['title'] ?? 'Notification' }}
                            </p>
                            <p class="text-xs text-gray-600 line-clamp-2">
                                {{ $note->data['message'] ?? 'No details provided.' }}
                            </p>
                            <span class="block text-[10px] text-gray-400 mt-1">{{ $note->created_at->diffForHumans() }}</span>
                        </li>
                    @empty
                        <li class="px-4 py-3 text-sm text-gray-500 text-center">No new notifications</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- 🔴 System Status Indicator -->
        <div class="relative" x-data="systemStatus()" x-init="init()">
            <button @click="open = !open"
                class="relative flex items-center justify-center w-11 h-11 bg-gray-50 hover:bg-gray-100 text-gray-600 hover:text-gray-800 rounded-xl transition-all duration-200 shadow-sm hover:shadow-md"
                :class="{
                    'bg-green-50 text-green-600': overallStatus === 'healthy',
                    'bg-yellow-50 text-yellow-600': overallStatus === 'warning',
                    'bg-red-50 text-red-600': overallStatus === 'unhealthy'
                }"
                aria-label="System Status"
                aria-expanded="false"
                :aria-expanded="open"
                @click="open = !open">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>

                <!-- Status indicator dot -->
                <span class="absolute -top-1 -right-1 w-3 h-3 rounded-full border-2 border-white"
                      :class="{
                          'bg-green-500': overallStatus === 'healthy',
                          'bg-yellow-500': overallStatus === 'warning',
                          'bg-red-500': overallStatus === 'unhealthy'
                      }"></span>
            </button>

            <!-- Status Dropdown -->
            <div x-show="open" @click.away="open = false" x-transition.opacity
                class="absolute right-0 mt-3 w-80 bg-white border border-gray-200 rounded-xl shadow-xl z-50 overflow-hidden transition-all duration-200">

                <div class="p-4 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-gray-900">System Status</h3>
                        <span class="text-xs text-gray-500" x-text="lastUpdated"></span>
                    </div>
                    <div class="flex items-center mt-2">
                        <span class="text-sm font-medium"
                              :class="{
                                  'text-green-700': overallStatus === 'healthy',
                                  'text-yellow-700': overallStatus === 'warning',
                                  'text-red-700': overallStatus === 'unhealthy'
                              }"
                              x-text="overallStatusText"></span>
                    </div>
                </div>

                <div class="p-4 space-y-3">
                    <!-- Database Status -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2"
                                 :class="{
                                     'text-green-500': status.database?.status === 'healthy',
                                     'text-red-500': status.database?.status === 'unhealthy'
                                 }"
                                 fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                            </svg>
                            <span class="text-sm font-medium text-gray-700">Database</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-xs text-gray-500 mr-2" x-text="status.database?.response_time ? status.database.response_time.toFixed(2) + 'ms' : ''"></span>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                  :class="{
                                      'bg-green-100 text-green-800': status.database?.status === 'healthy',
                                      'bg-red-100 text-red-800': status.database?.status === 'unhealthy'
                                  }"
                                  x-text="status.database?.status || 'checking'"></span>
                        </div>
                    </div>

                    <!-- Cache Status -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2"
                                 :class="{
                                     'text-green-500': status.cache?.status === 'healthy',
                                     'text-red-500': status.cache?.status === 'unhealthy'
                                 }"
                                 fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm0 2h12v8H4V6z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm font-medium text-gray-700">Cache</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-xs text-gray-500 mr-2" x-text="status.cache?.response_time ? status.cache.response_time.toFixed(2) + 'ms' : ''"></span>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                  :class="{
                                      'bg-green-100 text-green-800': status.cache?.status === 'healthy',
                                      'bg-red-100 text-red-800': status.cache?.status === 'unhealthy'
                                  }"
                                  x-text="status.cache?.status || 'checking'"></span>
                        </div>
                    </div>

                    <!-- Queue Status -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2"
                                 :class="{
                                     'text-green-500': status.queue?.status === 'healthy',
                                     'text-red-500': status.queue?.status === 'unhealthy'
                                 }"
                                 fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-sm font-medium text-gray-700">Queue</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-xs text-gray-500 mr-2" x-text="status.queue?.response_time ? status.queue.response_time.toFixed(2) + 'ms' : ''"></span>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                  :class="{
                                      'bg-green-100 text-green-800': status.queue?.status === 'healthy',
                                      'bg-red-100 text-red-800': status.queue?.status === 'unhealthy'
                                  }"
                                  x-text="status.queue?.status || 'checking'"></span>
                        </div>
                    </div>
                </div>

                <div class="px-4 py-3 bg-gray-50 border-t border-gray-100">
                    <button @click="checkStatus()"
                        class="w-full flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200"
                        :disabled="loading"
                        x-text="loading ? 'Checking...' : 'Refresh Status'">
                    </button>
                </div>
            </div>
        </div>



        <!-- 👤 User Dropdown -->
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open"
                class="flex items-center justify-center w-11 h-11 bg-gray-50 hover:bg-gray-100 text-gray-600 hover:text-gray-800 rounded-xl transition-all duration-200 shadow-sm hover:shadow-md">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=0D8ABC&color=fff"
                    alt="Avatar" class="w-8 h-8 rounded-full" />
            </button>

            <!-- Dropdown -->
            <div x-show="open" @click.away="open = false" x-transition.opacity
                class="absolute right-0 mt-3 w-56 bg-white border border-gray-200 rounded-xl shadow-xl z-50 overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100">
                    <p class="text-sm font-semibold text-gray-900">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                </div>
                <a href="{{ Auth::user()->role === 'admin' ? route('profile.show') : route('staff.profile.show') }}"
                    class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Profile
                </a>
                <a href="{{ route('settings.index') }}"
                    class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Settings
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="flex items-center w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Mobile Menu Overlay -->
    <div x-show="isOpen"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 md:hidden"
         style="display: none;">

        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black bg-opacity-50" @click="close()"></div>

        <!-- Menu Panel -->
        <div class="absolute top-0 left-0 h-full w-80 bg-white shadow-xl transform transition-transform duration-300 ease-in-out"
             :class="{ '-translate-x-full': !isOpen, 'translate-x-0': isOpen }">

            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900">Menu</h2>
                <button @click="close()"
                        class="flex items-center justify-center w-10 h-10 bg-gray-100 hover:bg-gray-200 text-gray-600 hover:text-gray-800 rounded-lg transition-colors duration-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-6 py-6 space-y-2">
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}"
                   class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                    <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z" />
                    </svg>
                    Dashboard
                </a>

                <!-- Customers -->
                <a href="{{ route('customers.index') }}"
                   class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                    <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                    </svg>
                    Customers
                </a>

                <!-- Technicians -->
                <a href="{{ route('technicians.index') }}"
                   class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                    <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Technicians
                </a>

                <!-- Service Requests -->
                <a href="{{ route('service_requests.index') }}"
                   class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                    <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Service Requests
                </a>

                <!-- Billing -->
                <a href="{{ route('billing.index') }}"
                   class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                    <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Billing
                </a>

                <!-- Reports -->
                <a href="{{ route('reports.index') }}"
                   class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                    <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Reports
                </a>

                <!-- Announcements -->
                <a href="{{ route('announcements.index') }}"
                   class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                    <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                    </svg>
                    Announcements
                </a>

                <!-- Packages -->
                <a href="{{ route('packages.index') }}"
                   class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                    <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    Packages
                </a>
            </nav>

            <!-- Footer -->
            <div class="border-t border-gray-200 p-6">
                <div class="flex items-center">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=0D8ABC&color=fff"
                         alt="Avatar" class="w-10 h-10 rounded-full mr-3" />
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-900">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="mt-4">
                    @csrf
                    <button type="submit"
                            class="w-full flex items-center justify-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
<!-- ✅ Pusher + Echo Setup -->
<script src="https://js.pusher.com/8.2/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo/dist/echo.iife.js"></script>

<script>
    // System Status Alpine.js Component
    function systemStatus() {
        return {
            open: false,
            status: {
                database: null,
                cache: null,
                queue: null
            },
            overallStatus: 'checking',
            overallStatusText: 'Checking...',
            lastUpdated: 'Never',
            loading: false,

            init() {
                this.checkStatus();
                // Auto-refresh every 30 seconds
                setInterval(() => {
                    if (!this.loading) {
                        this.checkStatus();
                    }
                }, 30000);
            },

            async checkStatus() {
                this.loading = true;
                try {
                    const response = await fetch('/api/system/status');
                    const data = await response.json();

                    this.status = data;
                    this.lastUpdated = new Date(data.timestamp).toLocaleTimeString();

                    // Determine overall status
                    const statuses = [data.database?.status, data.cache?.status, data.queue?.status];
                    if (statuses.every(s => s === 'healthy')) {
                        this.overallStatus = 'healthy';
                        this.overallStatusText = 'All Systems Operational';
                    } else if (statuses.some(s => s === 'unhealthy')) {
                        this.overallStatus = 'unhealthy';
                        this.overallStatusText = 'System Issues Detected';
                    } else {
                        this.overallStatus = 'warning';
                        this.overallStatusText = 'Some Systems Degraded';
                    }
                } catch (error) {
                    console.error('Status check failed:', error);
                    this.overallStatus = 'unhealthy';
                    this.overallStatusText = 'Unable to Check Status';
                } finally {
                    this.loading = false;
                }
            }
        }
    }

    // Make Pusher globally available
    window.Pusher = Pusher;

    // Initialize Laravel Echo
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: '{{ config("broadcasting.connections.pusher.key") }}',
        cluster: '{{ config("broadcasting.connections.pusher.options.cluster") }}',
        forceTLS: true,
    });

    // 1️⃣ Public announcement notifications
    window.Echo.channel('announcements')
        .listen('.NewAnnouncementNotification', (data) => {
            console.log("📢 New Announcement:", data);
            updateBellNotification(data);
        });

    // 2️⃣ Personal notifications (private channel)
    const userId = {{ Auth::id() }};
    window.Echo.private(`App.Models.User.${userId}`)
        .notification((data) => {
            console.log("🔔 Personal Notification:", data);
            updateBellNotification(data);
        });

    // 3️⃣ Update UI when new notification received
    function updateBellNotification(data) {
        const badge = document.querySelector('#notifCount');
        const notifList = document.querySelector('#notifList');

        if (badge) {
            const newCount = parseInt(badge.innerText || 0) + 1;
            badge.innerText = newCount;
            badge.classList.remove('hidden');
        }

        if (notifList) {
            const item = document.createElement('li');
            item.classList.add('px-4', 'py-3', 'hover:bg-gray-50', 'border-b');
            item.innerHTML = `
                <p class="text-sm font-semibold text-gray-800">📢 ${data.title ?? 'Notification'}</p>
                <p class="text-xs text-gray-600 truncate">${data.message ?? ''}</p>
                <span class="block text-[10px] text-gray-400 mt-1">Just now</span>
            `;
            notifList.prepend(item);
        }

        const bell = document.querySelector('#notifBell');
        if (bell) {
            bell.classList.add('animate-pulse');
            setTimeout(() => bell.classList.remove('animate-pulse'), 1500);
        }
    }
</script>
