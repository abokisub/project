<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'KoboPoint Admin')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        @include('admin.layouts.sidebar')

        <!-- Main Content Area -->
        <div class="flex flex-col flex-1 overflow-hidden">
            <!-- Top Navigation -->
            @include('admin.layouts.topnav')

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto bg-gray-50 p-4 sm:p-6 lg:p-8">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Mobile Sidebar Overlay -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden hidden" onclick="toggleSidebar()"></div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebar-toggle');
            const overlay = document.getElementById('sidebar-overlay');
            
            if (window.innerWidth < 1024) {
                if (!sidebar.contains(event.target) && !sidebarToggle.contains(event.target)) {
                    if (!sidebar.classList.contains('-translate-x-full')) {
                        sidebar.classList.add('-translate-x-full');
                        overlay.classList.add('hidden');
                    }
                }
            }
        });

        // Toggle submenu
        function toggleSubmenu(element) {
            const submenu = element.nextElementSibling;
            const icon = element.querySelector('.submenu-icon');
            
            if (submenu) {
                submenu.classList.toggle('hidden');
                icon.classList.toggle('rotate-90');
            }
        }

        // Set active menu item based on current route
        document.addEventListener('DOMContentLoaded', function() {
            const currentPath = window.location.pathname;
            const menuItems = document.querySelectorAll('[data-route]');
            
            menuItems.forEach(item => {
                const route = item.getAttribute('data-route');
                if (currentPath.includes(route)) {
                    item.classList.add('bg-green-50', 'text-green-700', 'border-l-4', 'border-green-600');
                    // Expand parent if it's a submenu item
                    const parentMenu = item.closest('.submenu');
                    if (parentMenu) {
                        parentMenu.classList.remove('hidden');
                        const parentToggle = parentMenu.previousElementSibling;
                        if (parentToggle) {
                            parentToggle.querySelector('.submenu-icon').classList.add('rotate-90');
                        }
                    }
                }
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
