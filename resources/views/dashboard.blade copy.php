<div :class="sidebarOpen ? 'fixed lg:fixed' : 'hidden lg:block lg:fixed lg:translate-x-0'"
    x-show="sidebarOpen || window.innerWidth >= 1024"
    @click.away="if (window.innerWidth < 1024) sidebarOpen = false"
    x-transition:enter="transition transform ease-in-out duration-300"
    x-transition:enter-start="-translate-x-full opacity-0"
    x-transition:enter-end="translate-x-0 opacity-85 lg:opacity-100"
    x-transition:leave="transition transform ease-in-out duration-300"
    x-transition:leave-start="translate-x-0 opacity-85 lg:opacity-100"
    x-transition:leave-end="-translate-x-full opacity-0"
    class="bg-white dark:bg-gray-900 h-lvh w-56 shadow-xl fixed inset-0 lg:static z-30">
    <div class="p-4 text-start justify-start">
        <div class="flex mb-6 gap-4 items-center">
            <img src="{{ asset('images/logo.svg') }}" alt="Logo" class="w-40 lg:w-52">
            <button>
                <span @click="sidebarOpen = false" class="material-symbols-outlined lg:hidden">close</span>
            </button>
        </div>
        <div class="text-xs text-gray-500 dark:text-gray-400 h-[calc(100vh-10rem)] overflow-y-auto scrollbar-hide">
            <div
                class="flex {{ request()->is('dashboard', 'dashboard_user') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }} bg-opacity-30 dark:bg-opacity-30  h-14 w-52 content-center mb-2">
                <div
                    class="flex h-full w-1 {{ request()->is('dashboard', 'dashboard_user') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }}">
                </div>
                <a href="{{ url('dashboard') }}" class="flex">
                    <div
                        class="p-2 flex gap-4 items-center hover:ml-3 hover:text-pink-500 dark:hover:text-pink-600 {{ request()->is('', 'dashboard') ? 'text-pink-500 dark:text-pink-600' : 'text-gray-500 dark:text-gray-400' }} duration-300">
                        <span class="material-symbols-outlined text-lg ">dashboard</span>
                        <h1>Dashboard</h1>
                    </div>
                </a>
            </div>
            <div x-data="{ dropdownOpen: {{ request()->is('add-book', 'books-list', 'books/*') ? 'true' : 'false' }} }" class="relative">
                <div @click="dropdownOpen = !dropdownOpen"
                    class="flex {{ request()->is('add-book', 'books-list', 'books/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }} bg-opacity-30 dark:bg-opacity-30 h-14 w-52 content-center mb-2 cursor-pointer">
                    <div
                        class="flex {{ request()->is('add-book', 'books-list', 'books/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }} h-full w-1">
                    </div>
                    <div
                        class="flex p-2 gap-4 items-center hover:ml-3 hover:text-pink-500 dark:hover:text-pink-600 {{ request()->is('add-book', 'books-list', 'books/*') ? 'text-pink-500 dark:text-pink-600' : 'text-gray-500 dark:text-gray-400' }} duration-300">
                        <span class="material-symbols-outlined text-lg">book</span>
                        <h1>Books</h1>
                        <span class="material-symbols-outlined ml-auto" x-show="!dropdownOpen">expand_more</span>
                        <span class="material-symbols-outlined ml-auto" x-show="dropdownOpen">expand_less</span>
                    </div>
                </div>
                <div x-show="dropdownOpen" x-transition class="left-0 mt-2 w-52 bg-white dark:bg-gray-900">
                    <a href="{{ url('add-book') }}"
                        class="block {{ request()->is('add-book') ? 'bg-gray-200' : 'bg-none' }} px-4 py-2 text-xs text-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 border-b-2 border-gray-100 dark:border-gray-600">add
                        book</a>
                    <a href="{{ url('books-list') }}"
                        class="block {{ request()->is('books-list', 'books/*') ? 'bg-gray-200' : 'bg-none' }} px-4 py-2 text-xs text-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 border-b-2 border-gray-100 dark:border-gray-600">book
                        list</a>
                </div>
            </div>

            {{-- Menu Category --}}
            <div
                class="flex {{ request()->is('category', 'edit-category/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }} bg-opacity-30 dark:bg-opacity-30 h-14 w-52 content-center mb-2">
                <div
                    class="flex {{ request()->is('category', 'edit-category/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }} h-full w-1">
                </div>
                <a href="{{ url('category') }}" class="flex">
                    <div
                        class="flex p-2 gap-4 items-center hover:ml-3 hover:text-pink-500 dark:hover:text-pink-600 {{ request()->is('category', 'edit-category/*') ? 'text-pink-500 dark:text-pink-600' : 'text-gray-500 dark:text-gray-400' }} duration-300">
                        <span class="material-symbols-outlined text-lg">category</span>
                        <h1>Categories</h1>
                    </div>
                </a>
            </div>

            {{-- Menu Person --}}
            <div
                class="flex {{ request()->is('profile') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }} bg-opacity-30 dark:bg-opacity-30 h-14 w-52 content-center mb-2">
                <div
                    class="flex {{ request()->is('profile') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }} h-full w-1">
                </div>
                <a href="{{ url('profile') }}" class="flex">
                    <div
                        class="flex p-2 gap-4 items-center hover:ml-3 hover:text-pink-500 dark:hover:text-pink-600 {{ request()->is('profile') ? 'text-pink-500 dark:text-pink-600' : 'text-gray-500 dark:text-gray-400' }} duration-300">
                        <span class="material-symbols-outlined text-lg">person</span>
                        <h1>Profile</h1>
                    </div>
                </a>
            </div>

            <!-- Menu tambahan untuk admin -->
            @if (Auth::user()->role === 'admin')
            <div x-data="{ dropdownOpen: {{ request()->is('user', 'edit-user/*', 'admins', 'edit-admin/*') ? 'true' : 'false' }} }" class="relative">
                <div @click="dropdownOpen = !dropdownOpen"
                    class="flex {{ request()->is('profil_user', 'edit_user/*', 'user', 'edit-user/*', 'admins', 'edit-admin/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }} bg-opacity-30 dark:bg-opacity-30 h-14 w-52 content-center mb-2 cursor-pointer">
                    <div
                        class="flex {{ request()->is('profil_user', 'edit_user/*', 'user', 'edit-user/*', 'admins', 'edit-admin/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }} h-full w-1">
                    </div>
                    <div
                        class="flex p-2 gap-4 items-center hover:ml-3 hover:text-pink-500 dark:hover:text-pink-600 {{ request()->is('profil_user', 'edit_user/*', 'user', 'edit-user/*', 'admins', 'edit-admin/*') ? 'text-pink-500 dark:text-pink-600' : 'text-gray-500 dark:text-gray-400' }} duration-300">
                        <span class="material-symbols-outlined text-lg">group</span>
                        <h1>Users</h1>
                        <span class="material-symbols-outlined ml-auto text-base"
                            x-show="!dropdownOpen">expand_more</span>
                        <span class="material-symbols-outlined ml-auto text-base"
                            x-show="dropdownOpen">expand_less</span>
                    </div>
                </div>
                <div x-show="dropdownOpen" x-transition class="left-0 mt-2 w-52 bg-white">
                    <a href="{{ url('user') }}"
                        class="{{ request()->is('user', 'edit-user/*') ? 'bg-gray-200' : 'bg-none' }} block px-8 py-2 text-xs text-gray-700 hover:bg-gray-100 border-b-2 border-gray-100">Users</a>
                    <a href="{{ url('admins') }}"
                        class="block {{ request()->is('admins', 'edit-admin/*') ? 'bg-gray-200' : 'bg-none' }} block px-8 py-2 text-xs text-gray-700 hover:bg-gray-100 border-b-2 border-gray-100">Admin</a>
                </div>
            </div>
            @endif
        </div>
        <form action="/logout" method="post">
            @csrf
            <button>
                <div
                    class="absolute flex gap-4 items-center text-gray-500 dark:text-gray-400 hover:ml-3 hover:text-pink-500 dark:hover:text-pink-600 duration-300 bottom-4 text-xs p-2 h-14 w-48 bg-white dark:bg-gray-900">
                    <span class="material-symbols-outlined text-xs">logout</span>
                    <h1>Log Out</h1>
                </div>
            </button>
        </form>
    </div>
</div>

{{-- NAVBAR MOBILE --}}
<div class="lg:hidden block w-screen h-14 content-center shadow-md bg-white dark:bg-gray-900">
    <div class="flex justify-between items-center h-full px-4">
        <div class="flex items-center space-x-2 gap-4">
            <span @click="sidebarOpen = true" class="material-symbols-outlined text-pink-500 cursor-pointer">
                menu
            </span>
            <h1 class="text-gray-600 text-xs font-semibold"></h1>
        </div>
    </div>
</div>

{{-- NAVBAR --}}
<div class="hidden lg:block w-lvw h-16 content-center bg-white dark:bg-gray-900 drop-shadow-md">
    <div class="flex justify-between items-center h-full px-4">
        <h1 class="text-gray-600 ml-60 text-2xl font-semibold"></h1>
        <div class="flex items-center space-x-2">
            <h1 class="text-gray-500 dark:text-gray-400">Hy, {{ Auth::user()->name }}</h1>
            <img src="{{ Auth::user()->foto_user ? asset('foto_user/' . Auth::user()->foto_user) : asset('images/profile-1.png') }}"
                alt="Profil Pengguna" class="profile-img h-10 w-10 rounded-full border-2 border-gray-300">
        </div>
    </div>
</div>