<x-app-layout>
    <nav aria-label="Breadcrumb" class="flex">
        <ol
            class="flex overflow-hidden rounded-lg border border-gray-200 text-gray-600 dark:border-gray-700 dark:text-gray-400">
            <li class="flex items-center">
                <a href="{{ route('themes.create') }}"
                    class="flex h-10 items-center gap-1.5 bg-gray-100 dark:bg-gray-800 px-4 transition hover:text-gray-900 dark:hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>

                    <span class="ms-1.5 text-xs font-medium dark:text-gray-300"> Daftar Tema </span>
                </a>
            </li>

            <li class="relative flex items-center">
                <span
                    class="absolute inset-y-0 -start-px h-10 w-4 bg-gray-100 dark:bg-gray-800 [clip-path:_polygon(0_0,_0%_100%,_100%_50%)] rtl:rotate-180">
                </span>

                <a href="#"
                    class="flex h-10 items-center bg-white dark:bg-gray-900 pe-4 ps-8 text-xs font-medium transition hover:text-gray-900 dark:hover:text-white">
                    Detail Tema
                </a>
            </li>
        </ol>
    </nav>

    <div
        class="my-6 p-8 bg-white dark:bg-gray-900 drop-shadow-lg rounded-md hover:drop-shadow-none duration-300 ease-in">
        <h2 class="text-3xl font-semibold text-gray-800 dark:text-gray-200 mb-6">Detail Tema</h2>

        <!-- Grid Layout for Theme Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Theme Code -->
            <div class="bg-gray-50 dark:bg-gray-800 p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-medium text-gray-700 dark:text-gray-300">Code Tema</h3>
                <p class="text-lg text-gray-900 dark:text-gray-100">{{ $theme->theme_code }}</p>
            </div>

            <!-- Theme Name -->
            <div class="bg-gray-50 dark:bg-gray-800 p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-medium text-gray-700 dark:text-gray-300">Nama Tema</h3>
                <p class="text-lg text-gray-900 dark:text-gray-100">{{ $theme->theme_name }}</p>
            </div>
        </div>

        <!-- Grid Layout for Description and Document -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
            <!-- Theme Description -->
            <div class="bg-gray-50 dark:bg-gray-800 p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-medium text-gray-700 dark:text-gray-300">Deskripsi Tema</h3>
                <p class="text-lg text-gray-900 dark:text-gray-100">{{ $theme->theme_description }}</p>
            </div>

            <!-- Document Section -->
            <div class="bg-gray-50 dark:bg-gray-800 p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-medium text-gray-700 dark:text-gray-300">Document</h3>
                @if ($theme->theme_document)
                    <div class="mt-2 flex gap-8">
                        <!-- View Document -->
                        <a href="{{ asset('storage/theme_documents/' . basename($theme->theme_document)) }}"
                            target="_blank"
                            class="text-blue-500 hover:text-blue-700 text-lg font-medium relative group">
                            <i class="fa fa-eye"></i>
                            <span
                                class="material-symbols-outlined bg-blue-500 px-2 py-1 rounded-md text-white text-2xl font-extralight">
                                visibility
                            </span>
                            <span
                                class="absolute left-0 top-full mt-1 w-max px-2 py-1 rounded-md bg-gray-800 text-white text-xs hidden group-hover:block">
                                Lihat Detail
                            </span>
                        </a>
                        <!-- Download Document -->
                        <a href="{{ asset('storage/theme_documents/' . basename($theme->theme_document)) }}" download
                            class="text-white text-xl font-medium relative group">
                            <i class="fa fa-download"></i>
                            <span
                                class="material-symbols-outlined bg-green-500 px-2 py-1 rounded-md text-white text-2xl font-extralight">
                                download
                            </span>
                            <span
                                class="absolute left-0 top-full mt-1 w-max px-2 py-1 rounded-md bg-gray-800 text-white text-xs hidden group-hover:block">
                                Download
                            </span>
                        </a>
                    </div>
                @else
                    <p class="text-gray-500">No Document Available</p>
                @endif
            </div>
        </div>
        <!-- Back Button -->
        <div class="flex items-center justify-end mt-6">
            <a href="{{ route('themes.create') }}">
                <x-primary-button class="bg-gray-700 hover:bg-gray-800">
                    Back
                </x-primary-button>
            </a>
        </div>
    </div>
</x-app-layout>
