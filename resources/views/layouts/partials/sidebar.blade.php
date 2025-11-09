<aside id="sidebar"
    class="sidebar-expanded bg-gray-900 text-gray-200 h-screen w-64 p-5 fixed top-0 left-0 transition-all duration-300 ease-in-out overflow-y-auto shadow-lg z-50">

    <!-- Logo -->
    <div class="flex items-center justify-center mb-6 border-b border-gray-700 pb-3">
        <img src="{{ asset('images/logo3.png') }}" alt="PCTVS Logo" class="h-14 w-auto">
    </div>

    <nav>
        <ul class="space-y-2 text-sm">

            <!-- Dashboard -->
            <li>
                <a href="/dashboard" data-label="Dashboard"
                   class="flex items-center py-2 px-3 rounded-md transition
                   {{ request()->is('dashboard') ? 'bg-indigo-600 text-white font-semibold shadow' : 'hover:bg-gray-700 hover:text-white' }}">
                    <x-heroicon-o-home class="h-5 w-5 mr-3"/>
                    <span class="sidebar-text">Dashboard</span>
                </a>
            </li>

            <!-- Customers -->
            <li>
                <button type="button" data-label="Customers"
                    class="flex items-center justify-between w-full py-2 px-3 rounded-md transition
                    {{ request()->is('customers*') ? 'bg-indigo-600 text-white font-semibold shadow' : 'hover:bg-gray-700 hover:text-white' }}"
                    onclick="toggleSubmenu('customersMenu')">
                    <span class="flex items-center">
                        <x-heroicon-o-users class="h-5 w-5 mr-3"/>
                        <span class="sidebar-text">Customers</span>
                    </span>
                    <span class="caret transform transition-transform duration-300 {{ request()->is('customers*') ? 'rotate-90' : '' }}">▸</span>
                </button>
                <ul id="customersMenu" class="space-y-1 mt-1 pl-8 overflow-hidden {{ request()->is('customers*') ? 'opacity-100 max-h-96' : 'hidden opacity-0 max-h-0' }}">
                    <li>
                        <a href="{{ route('customers.create') }}" class="flex items-center py-1 px-3 rounded-md transition
                           {{ request()->is('customers/create') ? 'bg-gray-700 text-blue-300 font-semibold' : 'hover:bg-gray-700 hover:text-white' }}">
                            <x-heroicon-o-plus class="h-4 w-4 mr-2"/>
                            <span class="sidebar-text">Add Customer</span>
                        </a>
                    </li>
                    <li>
                        <a href="/customers/list" class="flex items-center py-1 px-3 rounded-md transition
                           {{ request()->is('customers/list') ? 'bg-gray-700 text-blue-300 font-semibold' : 'hover:bg-gray-700 hover:text-white' }}">
                            <x-heroicon-o-queue-list class="h-4 w-4 mr-2"/>
                            <span class="sidebar-text">Customer List</span>
                        </a>
                    </li>
                   
                </ul>
            </li>

            <!-- Subscriptions -->
            <li>
                <button type="button" data-label="Subscriptions"
                    class="flex items-center justify-between w-full py-2 px-3 rounded-md transition
                    {{ request()->is('subscriptions*') || request()->is('packages*')
                        ? 'bg-indigo-600 text-white font-semibold shadow'
                        : 'hover:bg-gray-700 hover:text-white' }}"
                    onclick="toggleSubmenu('subscriptionsMenu')">
                    <span class="flex items-center">
                        <x-heroicon-o-squares-2x2 class="h-5 w-5 mr-3"/>
                        <span class="sidebar-text">Subscriptions</span>
                    </span>
                    <span class="caret transform transition-transform duration-300
                        {{ request()->is('subscriptions*') || request()->is('packages*') ? 'rotate-90' : '' }}">▸</span>
                </button>
                <ul id="subscriptionsMenu" class="space-y-1 mt-1 pl-8 overflow-hidden {{ request()->is('subscriptions*') || request()->is('packages*') ? 'opacity-100 max-h-96' : 'hidden opacity-0 max-h-0' }}">
                    <li>
                        <a href="{{ route('subscriptions.index') }}" class="flex items-center py-1 px-3 rounded-md transition
                           {{ request()->is('subscriptions*') ? 'bg-gray-700 text-blue-300 font-semibold' : 'hover:bg-gray-700 hover:text-white' }}">
                            <x-heroicon-o-cog-6-tooth class="h-4 w-4 mr-2"/>
                            <span class="sidebar-text">Manage Subscriptions</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('packages.index') }}" class="flex items-center py-1 px-3 rounded-md transition
                           {{ request()->is('packages*') ? 'bg-gray-700 text-blue-300 font-semibold' : 'hover:bg-gray-700 hover:text-white' }}">
                            <x-heroicon-o-gift class="h-4 w-4 mr-2"/>
                            <span class="sidebar-text">Packages</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Service Requests -->
            <li>
                <a href="{{ route('service_requests.index') }}" data-label="Service Requests"
                   class="flex items-center py-2 px-3 rounded-md transition
                   {{ request()->is('service_requests*') ? 'bg-indigo-600 text-white font-semibold shadow' : 'hover:bg-gray-700 hover:text-white' }}">
                    <x-heroicon-o-wrench-screwdriver class="h-5 w-5 mr-3"/>
                    <span class="sidebar-text">Service Requests</span>
                </a>
            </li>

            <!-- Technicians -->
            <li>
                <a href="{{ route('technicians.index') }}" class="flex items-center py-2 px-3 rounded-md transition
                   {{ request()->is('technicians*') ? 'bg-indigo-600 text-white font-semibold shadow' : 'hover:bg-gray-700 hover:text-white' }}">
                    <x-heroicon-o-user-group class="h-5 w-5 mr-3"/>
                    <span class="sidebar-text">Technicians</span>
                </a>
            </li>

            <!-- Billing -->
            <li>
                <button type="button" data-label="Billing"
                    class="flex items-center justify-between w-full py-2 px-3 rounded-md transition
                    {{ request()->is('billing*') ? 'bg-indigo-600 text-white font-semibold shadow' : 'hover:bg-gray-700 hover:text-white' }}"
                    onclick="toggleSubmenu('billingMenu')">
                    <span class="flex items-center">
                        <x-heroicon-o-credit-card class="h-5 w-5 mr-3"/>
                        <span class="sidebar-text">Billing</span>
                    </span>
                    <span class="caret transform transition-transform duration-300 {{ request()->is('billing*') ? 'rotate-90' : '' }}">▸</span>
                </button>
                <ul id="billingMenu" class="space-y-1 mt-1 pl-8 overflow-hidden {{ request()->is('billing*') ? 'opacity-100 max-h-96' : 'hidden opacity-0 max-h-0' }}">
                    <li>
                        <a href="{{ route('billing.index') }}" class="flex items-center py-1 px-3 rounded-md transition
                           {{ request()->is('billing/index') ? 'bg-gray-700 text-blue-300 font-semibold' : 'hover:bg-gray-700 hover:text-white' }}">
                            <x-heroicon-o-chart-bar class="h-4 w-4 mr-2"/>
                            <span class="sidebar-text">Billing Overview</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('invoice.logs') }}" class="flex items-center py-1 px-3 rounded-md transition
                           {{ request()->is('billing/logs') ? 'bg-gray-700 text-blue-300 font-semibold' : 'hover:bg-gray-700 hover:text-white' }}">
                            <x-heroicon-o-document-text class="h-4 w-4 mr-2"/>
                            <span class="sidebar-text">Invoice Logs</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Announcements -->
            <li>
                <a href="{{ route('announcements.index') }}" data-label="Announcements"
                   class="flex items-center py-2 px-3 rounded-md transition
                          {{ request()->is('announcements*')
                              ? 'bg-indigo-600 text-white font-semibold shadow'
                              : 'hover:bg-gray-700 hover:text-white' }}">
                    <x-heroicon-o-speaker-wave class="h-5 w-5 mr-3"/>
                    <span class="sidebar-text">Announcements</span>
                </a>
            </li>

            <!-- Reports -->
            <li>
                <a href="{{ route('reports.index') }}" data-label="Reports"
                   class="flex items-center py-2 px-3 rounded-md transition
                   {{ request()->is('reports*') ? 'bg-indigo-600 text-white font-semibold shadow' : 'hover:bg-gray-700 hover:text-white' }}">
                    <x-heroicon-o-chart-bar-square class="h-5 w-5 mr-3"/>
                    <span class="sidebar-text">Reports</span>
                </a>
            </li>

            <!-- Users -->
            <li>
                <a href="{{ route('users.index') }}" data-label="Users"
                   class="flex items-center py-2 px-3 rounded-md transition
                   {{ request()->is('users*') ? 'bg-indigo-600 text-white font-semibold shadow' : 'hover:bg-gray-700 hover:text-white' }}">
                    <x-heroicon-o-shield-check class="h-5 w-5 mr-3"/>
                    <span class="sidebar-text">Users</span>
                </a>
            </li>

        </ul>
    </nav>
</aside>
