<x-guest-layout>
    @slot('seo_key', $seo_key)
    @slot('seo_description', $seo_description)
    @slot('seo_meta_title', $seo_meta_title)
    @slot('seo_title', $seo_title)

    <!-- Background Section -->
    <section class="bg-cover bg-center h-screen" style="background-image: url('images/blogs1.jpg');">
        <div
            class="h-full w-full bg-gray-900 bg-opacity-10 dark:bg-gray-900 dark:bg-opacity-70 backdrop-blur-sm content-center">
            <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
                <div
                    class="w-full bg-white rounded-lg shadow dark:border md:mt-0 sm:max-w-md xl:p-0 dark:bg-gray-800 dark:border-gray-700">
                    <!-- Logo Section -->
                    <header class="flex justify-center mt-6">
                        <a href="#" class="flex items-center text-2xl font-semibold text-gray-900 dark:text-white">
                            <img class="w-64 mr-2" src="images/logo.svg" alt="logo">
                        </a>
                    </header>

                    <!-- Login Form Section -->
                    <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                        <h1
                            class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white">
                            Sign in to your account
                        </h1>
                        <x-auth-session-status class="mb-4" :status="session('status')" />

                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <!-- Identifier (Email or No Induk) -->
                            <div>
                                <x-input-label for="identifier" :value="__('Email or No Induk')" />
                                <x-text-input id="identifier" class="block mt-1 w-full" type="text" name="identifier"
                                    :value="old('identifier')" required autofocus />
                                <x-input-error :messages="$errors->get('identifier')" class="mt-2" />
                            </div>

                            <!-- Password Section -->
                            <div class="mt-4" x-data="{ showPassword: false }">
                                <x-input-label for="password" :value="__('Password')" />
                                <div class="relative">
                                    <input id="password"
                                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:border-indigo-300 dark:focus:ring-indigo-300 block mt-1 w-full pr-12"
                                        :type="showPassword ? 'text' : 'password'" name="password" required />

                                    <!-- Password visibility toggle -->
                                    <span @click="showPassword = !showPassword"
                                        class="absolute inset-y-0 right-0 flex items-center pr-3 mx-1 cursor-pointer">
                                        <i x-show="!showPassword"
                                            class="material-symbols-outlined text-gray-500">visibility</i>
                                        <i x-show="showPassword"
                                            class="material-symbols-outlined text-gray-500">visibility_off</i>
                                    </span>
                                </div>
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <!-- Login Button -->
                            <x-primary-button class="mt-4">
                                {{ __('Log in') }}
                            </x-primary-button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-guest-layout>
