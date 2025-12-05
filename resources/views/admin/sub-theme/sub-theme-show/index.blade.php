<x-app-layout>
    <nav aria-label="Breadcrumb" class="flex">
        <ol
            class="flex overflow-hidden rounded-lg border border-gray-200 text-gray-600 dark:border-gray-700 dark:text-gray-400">
            <li class="flex items-center">
                <a href="{{ route('subthemes.create') }}"
                    class="flex h-10 items-center gap-1.5 bg-gray-100 dark:bg-gray-800 px-4 transition hover:text-gray-900 dark:hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span class="ms-1.5 text-xs font-medium dark:text-gray-300"> Daftar Sub Tema </span>
                </a>
            </li>
            <li class="relative flex items-center">
                <span
                    class="absolute inset-y-0 -start-px h-10 w-4 bg-gray-100 dark:bg-gray-800 [clip-path:_polygon(0_0,_0%_100%,_100%_50%)] rtl:rotate-180">
                </span>
                <a href="#"
                    class="flex h-10 items-center bg-white dark:bg-gray-900 pe-4 ps-8 text-xs font-medium transition hover:text-gray-900 dark:hover:text-white">
                    Detail Sub Tema
                </a>
            </li>
        </ol>
    </nav>

    <div class="my-6 p-8 bg-white dark:bg-gray-900 rounded-xl shadow-lg hover:shadow-xl duration-300 ease-in-out">
        <h2 class="text-3xl font-semibold text-gray-800 dark:text-gray-200 mb-6">Detail Sub Tema</h2>

        <!-- Grid Layout for SubTheme Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Sub Theme Code -->
            <div
                class="bg-gray-50 dark:bg-gray-800 p-6 rounded-lg shadow-md hover:shadow-lg transition duration-300 ease-in-out">
                <h3 class="text-xl font-medium text-gray-700 dark:text-gray-300">Code Sub Tema</h3>
                <p class="text-lg text-gray-900 dark:text-gray-100">{{ $subTheme->sub_theme_code }}</p>
            </div>

            <!-- Sub Theme Name -->
            <div
                class="bg-gray-50 dark:bg-gray-800 p-6 rounded-lg shadow-md hover:shadow-lg transition duration-300 ease-in-out">
                <h3 class="text-xl font-medium text-gray-700 dark:text-gray-300">Nama Sub Tema</h3>
                <p class="text-lg text-gray-900 dark:text-gray-100">{{ $subTheme->sub_theme_name }}</p>
            </div>
        </div>

        <!-- Grid Layout for Description and Document -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
            <!-- Referenced Theme Information -->
            <div
                class="bg-gray-50 dark:bg-gray-800 p-6 rounded-lg shadow-md hover:shadow-lg transition duration-300 ease-in-out">
                <h3 class="text-xl font-medium text-gray-700 dark:text-gray-300">Tema Terkait</h3>
                <h4 class="text-lg font-medium text-gray-700 dark:text-gray-200">Nama Tema</h4>
                <p class="text-base text-gray-900 dark:text-gray-100">{{ $subTheme->theme->theme_name }}</p>
                <h4 class="text-lg font-medium text-gray-700 dark:text-gray-200">Kode Tema</h4>
                <p class="text-base text-gray-900 dark:text-gray-100">{{ $subTheme->theme->theme_code }}</p>
            </div>

            <!-- Document Section -->
            <div
                class="bg-gray-50 dark:bg-gray-800 p-6 rounded-lg shadow-md hover:shadow-lg transition duration-300 ease-in-out">
                <h3 class="text-xl font-medium text-gray-700 dark:text-gray-300">Document</h3>
                @if ($subTheme->sub_theme_document)
                    <div class="mt-2 flex gap-8">
                        <!-- View Document -->
                        <a href="{{ asset('storage/sub_theme_documents/' . basename($subTheme->sub_theme_document)) }}"
                            target="_blank"
                            class="text-blue-500 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-600 text-lg font-medium relative group transition duration-300 ease-in-out">
                            <i class="fa fa-eye"></i>
                            <span
                                class="material-symbols-outlined bg-blue-500 px-2 py-1 rounded-md text-white text-2xl font-extralight">
                                visibility
                            </span>
                            <span
                                class="absolute left-0 top-full mt-1 w-max px-2 py-1 rounded-md bg-gray-800 text-white text-xs hidden group-hover:block">
                                Lihat Document
                            </span>
                        </a>
                        <!-- Download Document -->
                        <a href="{{ asset('storage/sub_theme_documents/' . basename($subTheme->sub_theme_document)) }}"
                            download
                            class="text-white dark:text-gray-200 text-xl font-medium relative group transition duration-300 ease-in-out">
                            <i class="fa fa-download"></i>
                            <span
                                class="material-symbols-outlined bg-green-500 px-2 py-1 rounded-md text-white text-2xl font-extralight">
                                download
                            </span>
                            <span
                                class="absolute left-0 top-full mt-1 w-max px-2 py-1 rounded-md bg-gray-800 text-white text-xs hidden group-hover:block">
                                Download Document
                            </span>
                        </a>
                    </div>
                @else
                    <p class="text-gray-500 dark:text-gray-400">No Document Available</p>
                @endif
            </div>
        </div>

        <!-- Description Section (Moved Below) -->
        <div class="mt-6">
            <h3 class="text-2xl font-medium text-gray-700 dark:text-gray-300 mb-2">Deskripsi Sub Tema</h3>
            <div
                class="bg-gray-50 dark:bg-gray-800 p-6 rounded-lg shadow-md hover:shadow-lg transition duration-300 ease-in-out">
                <p class="text-lg text-gray-900 dark:text-gray-100">{{ $subTheme->sub_theme_description }}</p>
            </div>
        </div>

        <!-- Back Button -->
        <div class="flex items-center justify-end mt-6">
            <x-primary-button
                class="bg-gray-700 hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 text-white transition duration-300 ease-in-out"
                onclick="window.history.back()">
                Back
            </x-primary-button>
        </div>
    </div>
</x-app-layout>
