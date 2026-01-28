<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Admin Dashboard' }} - CustomCMS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/post-editor.css') }}">
</head>
<body class="bg-gray-100" x-data="{ sidebarOpen: true, searchOpen: false, notificationOpen: false, profileOpen: false }">
    <div class="flex flex-col h-screen">
        <!-- Top Header Bar -->
        <nav class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-50">
            <div class="px-4 py-3 flex justify-between items-center">
                <!-- Left: Logo & Menu Toggle -->
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = !sidebarOpen" class="text-gray-600 hover:text-gray-900 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <h1 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                        <span class="text-2xl">⚡</span>
                        <span>CustomCMS</span>
                    </h1>
                </div>

                <!-- Center: Search Bar -->
                <div class="flex-1 max-w-xl mx-8" x-show="!searchOpen">
                    <div class="relative">
                        <input 
                            type="text" 
                            placeholder="Search posts, users, media..." 
                            class="w-full px-4 py-2 pl-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                        >
                        <svg class="w-5 h-5 absolute left-3 top-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>

                <!-- Right: Notifications & Profile -->
                <div class="flex items-center gap-3">
                    <!-- Search Icon (Mobile) -->
                    <button @click="searchOpen = !searchOpen" class="lg:hidden text-gray-600 hover:text-gray-900">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>

                    <!-- Notifications -->
                    <div class="relative">
                        <button @click="notificationOpen = !notificationOpen" class="relative text-gray-600 hover:text-gray-900 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">3</span>
                        </button>

                        <!-- Notification Dropdown -->
                        <div x-show="notificationOpen" 
                             @click.away="notificationOpen = false"
                             x-transition
                             class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 py-2">
                            <div class="px-4 py-2 border-b border-gray-200 font-semibold text-gray-900">Notifications</div>
                            <div class="max-h-96 overflow-y-auto">
                                <a href="#" class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100">
                                    <div class="flex items-start gap-3">
                                        <span class="text-2xl">📝</span>
                                        <div class="flex-1">
                                            <p class="text-sm text-gray-900">New post published</p>
                                            <p class="text-xs text-gray-500 mt-1">2 minutes ago</p>
                                        </div>
                                    </div>
                                </a>
                                <a href="#" class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100">
                                    <div class="flex items-start gap-3">
                                        <span class="text-2xl">👤</span>
                                        <div class="flex-1">
                                            <p class="text-sm text-gray-900">New user registered</p>
                                            <p class="text-xs text-gray-500 mt-1">1 hour ago</p>
                                        </div>
                                    </div>
                                </a>
                                <a href="#" class="block px-4 py-3 hover:bg-gray-50">
                                    <div class="flex items-start gap-3">
                                        <span class="text-2xl">💬</span>
                                        <div class="flex-1">
                                            <p class="text-sm text-gray-900">New comment received</p>
                                            <p class="text-xs text-gray-500 mt-1">3 hours ago</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <a href="#" class="block px-4 py-2 text-center text-sm text-blue-600 hover:bg-gray-50 border-t border-gray-200">View All</a>
                        </div>
                    </div>

                    <!-- Profile -->
                    <div class="relative">
                        <button @click="profileOpen = !profileOpen" class="flex items-center gap-2 text-gray-700 hover:text-gray-900 focus:outline-none">
                            <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center font-semibold">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                            <span class="hidden md:block text-sm font-medium">{{ auth()->user()->name }}</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <!-- Profile Dropdown -->
                        <div x-show="profileOpen" 
                             @click.away="profileOpen = false"
                             x-transition
                             class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 py-2">
                            <div class="px-4 py-3 border-b border-gray-200">
                                <p class="text-sm font-semibold text-gray-900">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                            </div>
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2 hover:bg-gray-50 text-sm">
                                <span>👤</span> Profile
                            </a>
                            <a href="#" class="flex items-center gap-2 px-4 py-2 hover:bg-gray-50 text-sm">
                                <span>⚙️</span> Settings
                            </a>
                            <div class="border-t border-gray-200 mt-2"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 hover:bg-gray-50 text-red-600 text-sm text-left">
                                    <span>🚪</span> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <div class="flex flex-1 overflow-hidden">
            <!-- Collapsible Sidebar -->
            <aside :class="sidebarOpen ? 'w-64' : 'w-20'" class="bg-gray-900 text-white flex flex-col overflow-y-auto transition-all duration-300">
                <!-- Sidebar Header -->
                <div class="p-4 border-b border-gray-700">
                    <div x-show="sidebarOpen" class="text-xs text-gray-400 uppercase tracking-wide">Menu</div>
                    <div x-show="!sidebarOpen" class="text-center text-gray-400">☰</div>
                </div>

                <!-- Dynamic Menu Items -->
                <nav class="flex-1 p-2 space-y-1">
                    @php
                        $menuItems = \App\Models\MenuItems::active()
                            ->whereNull('parent_id')
                            ->orderBy('position')
                            ->get();
                    @endphp

                    @foreach($menuItems as $item)
                        @if($item->canAccess(auth()->user()))
                            @if($item->type === 'system')
                                <!-- System Menu (e.g., Dashboard) -->
                                <a href="{{ $item->getLink() }}" 
                                   class="flex items-center gap-3 px-3 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs($item->route) ? 'bg-blue-600' : '' }}"
                                   :title="sidebarOpen ? '' : '{{ $item->title }}'">
                                    <span class="text-xl" :class="sidebarOpen ? '' : 'mx-auto'">{{ $item->icon }}</span>
                                    <span x-show="sidebarOpen" class="font-medium">{{ $item->title }}</span>
                                </a>
                            @endif
                        @endif
                    @endforeach

                    <!-- Content Section -->
                    @php
                        $contentItems = $menuItems->where('type', 'content');
                    @endphp
                    @if($contentItems->count() > 0)
                        <div class="pt-4">
                            <div x-show="sidebarOpen" class="px-3 py-2 text-xs uppercase text-gray-500 font-semibold">Content</div>
                            <div x-show="!sidebarOpen" class="border-t border-gray-700 my-2"></div>
                            
                            @foreach($contentItems as $item)
                                @if($item->canAccess(auth()->user()))
                                    <a href="{{ $item->getLink() }}" 
                                       class="flex items-center gap-3 px-3 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs($item->route) ? 'bg-blue-600' : '' }}"
                                       :title="sidebarOpen ? '' : '{{ $item->title }}'">
                                        <span class="text-xl" :class="sidebarOpen ? '' : 'mx-auto'">{{ $item->icon }}</span>
                                        <span x-show="sidebarOpen">{{ $item->title }}</span>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    @endif

                    <!-- Admin Section -->
                    @php
                        $adminItems = $menuItems->where('type', 'admin')->filter(function($item) {
                            return $item->canAccess(auth()->user());
                        });
                    @endphp
                    @if($adminItems->count() > 0)
                        <div class="pt-4">
                            <div x-show="sidebarOpen" class="px-3 py-2 text-xs uppercase text-gray-500 font-semibold">Admin</div>
                            <div x-show="!sidebarOpen" class="border-t border-gray-700 my-2"></div>
                            
                            @foreach($adminItems as $item)
                                <a href="{{ $item->getLink() }}" 
                                   class="flex items-center gap-3 px-3 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs($item->route) ? 'bg-blue-600' : '' }}"
                                   :title="sidebarOpen ? '' : '{{ $item->title }}'">
                                    <span class="text-xl" :class="sidebarOpen ? '' : 'mx-auto'">{{ $item->icon }}</span>
                                    <span x-show="sidebarOpen">{{ $item->title }}</span>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </nav>

                <!-- Sidebar Footer -->
                <div class="p-4 border-t border-gray-700">
                    <div x-show="sidebarOpen" class="text-xs text-gray-500 text-center">CustomCMS v1.0</div>
                    <div x-show="!sidebarOpen" class="text-center">
                        <span class="text-xs text-gray-500">©</span>
                    </div>
                </div>
            </aside>

            <!-- Main Content -->
            <main class="flex-1 flex flex-col overflow-hidden bg-gray-50">
                <!-- Page Header -->
                <header class="bg-white border-b border-gray-200 px-6 py-4">
                    <h2 class="text-2xl font-bold text-gray-900">{{ $title ?? 'Dashboard' }}</h2>
                    @isset($subtitle)
                    <p class="text-sm text-gray-600 mt-1">{{ $subtitle }}</p>
                    @endisset
                </header>

                <!-- Page Content -->
                <div class="flex-1 overflow-y-auto p-6">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>
</body>
</html>
