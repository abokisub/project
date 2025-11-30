<!-- Top Navigation -->
<header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8">
    <!-- Mobile Menu Toggle -->
    <button id="sidebar-toggle" class="lg:hidden text-gray-500 hover:text-gray-700 focus:outline-none" onclick="toggleSidebar()">
        <i class="fas fa-bars text-xl"></i>
    </button>

    <!-- Search Bar (Desktop) -->
    <div class="hidden md:flex flex-1 max-w-md mx-8">
        <div class="relative w-full">
            <input 
                type="text" 
                placeholder="Search..." 
                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm"
            >
            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
        </div>
    </div>

    <!-- Right Side Actions -->
    <div class="flex items-center space-x-4">
        <!-- Notifications -->
        <button class="relative text-gray-500 hover:text-gray-700 focus:outline-none">
            <i class="fas fa-bell text-xl"></i>
            <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-500 ring-2 ring-white"></span>
        </button>

        <!-- User Menu -->
        <div class="flex items-center space-x-3">
            <div class="hidden sm:block text-right">
                <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                <p class="text-xs text-gray-500">Administrator</p>
            </div>
            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-user text-green-600"></i>
            </div>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-red-50 transition">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="hidden sm:inline">Logout</span>
                </button>
            </form>
        </div>
    </div>
</header>

