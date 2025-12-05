<x-app-layout>
    <div class="container mx-auto p-6 bg-gray-50 dark:bg-gray-900 rounded-md shadow-lg">
        <div class="container mx-auto p-2 md:p-4">
            <nav aria-label="Breadcrumb" class="flex">
                <ol
                    class="flex overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400">
                    <li class="flex items-center">
                        <a href="{{ route('gallery-activity.index') }}"
                            class="flex h-10 items-center gap-1.5 bg-gray-100 dark:bg-gray-800 px-4 transition hover:text-gray-900 dark:hover:text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <span class="ms-1.5 text-xs font-medium dark:text-gray-300"> Daftar galeri aktivitas </span>
                        </a>
                    </li>
                    <li class="relative flex items-center">
                        <span
                            class="absolute inset-y-0 -start-px h-10 w-4 bg-gray-100 dark:bg-gray-800 [clip-path:_polygon(0_0,_0%_100%,_100%_50%)] rtl:rotate-180"></span>
                        <a href="#"
                            class="flex h-10 items-center bg-white dark:bg-gray-900 pe-4 ps-8 text-xs font-medium transition hover:text-gray-900 dark:hover:text-white">
                            Detail galeri aktivitas
                        </a>
                    </li>
                </ol>
            </nav>

            <!-- Gallery Information -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg my-8">
                <h2 class="text-3xl font-semibold text-gray-800 dark:text-white mb-4">{{ $gallery->gallery_title }}</h2>
                <p class="text-lg text-gray-500 dark:text-gray-300 mb-4">{{ $gallery->gallery_description }}</p>
                <p class="text-sm text-gray-400 dark:text-gray-400">Date:
                    {{ \Carbon\Carbon::parse($gallery->gallery_date)->format('d-m-Y') }}
                </p>
            </div>

            <!-- Gallery Images -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ($gallery->galleryImages as $image)
                    <div class="relative group cursor-pointer rounded-lg overflow-hidden shadow-lg transition-transform transform hover:scale-105 bg-white dark:bg-gray-800 p-2 pb-12"
                        onclick="previewImage('{{ asset('storage/' . $image->image_url) }}')">
                        <img src="{{ asset('storage/' . $image->image_url) }}" alt="Gallery Image"
                            class="w-full h-48 object-cover object-center transition-all duration-300 mb-4">
                        <div class="absolute bottom-2 left-2 p-4 w-full text-gray-500 dark:text-gray-300">
                            <p class="absolute top-2 text-sm font-semibold">Click to view full image</p>
                        </div>
                        <a href="{{ asset('storage/' . $image->image_url) }}" download
                            class="absolute button-2 right-2 bg-blue-600 text-white px-4 py-2 rounded-md text-sm mb-2 hover:bg-blue-700">
                            Download
                        </a>
                    </div>
                @endforeach
            </div>

            <!-- Image Preview Modal -->
            <div id="image-modal"
                class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex justify-center items-center hidden px-2 py-6">
                <div
                    class="bg-white dark:bg-gray-800 p-6 rounded-lg max-w-3xl w-full max-h-full flex flex-col relative">
                    <span id="close-modal"
                        class="fixed z-50 text-center top-2 right-2 m-4 bg-white dark:bg-gray-800 px-1 rounded-lg text-gray-800 dark:text-white cursor-pointer text-3xl">&times;</span>
                    <div class="relative flex justify-center items-center overflow-auto w-full h-full">
                        <img id="large-image" class="w-auto max-h-[80vh] mx-auto transition-all transform"
                            alt="Large Preview" />
                    </div>
                    <div class="flex justify-between mt-4">
                        <button onclick="zoomOut()"
                            class="bg-gray-300 dark:bg-gray-600 text-gray-800 dark:text-white px-4 py-2 rounded-md hover:bg-gray-400 dark:hover:bg-gray-700 focus:outline-none">
                            Zoom Out
                        </button>
                        <button onclick="zoomIn()"
                            class="bg-gray-300 dark:bg-gray-600 text-gray-800 dark:text-white px-4 py-2 rounded-md hover:bg-gray-400 dark:hover:bg-gray-700 focus:outline-none">
                            Zoom In
                        </button>
                    </div>
                    <a id="download-button" href="" download
                        class="mt-4 bg-blue-600 text-white px-6 py-2 rounded-md text-sm w-full text-center hover:bg-blue-700 focus:outline-none">
                        Download Image
                    </a>
                </div>
            </div>
        </div>

        <script>
            function previewImage(imageSrc) {
                const modal = document.getElementById('image-modal');
                const largeImage = document.getElementById('large-image');
                const downloadButton = document.getElementById('download-button');

                largeImage.src = imageSrc;
                modal.classList.remove('hidden');
                downloadButton.href = imageSrc;
                largeImage.style.transform = "scale(1)";
                largeImage.style.transition = "transform 0.3s ease";
            }

            document.getElementById('close-modal').addEventListener('click', () => {
                document.getElementById('image-modal').classList.add('hidden');
            });

            function zoomIn() {
                const img = document.getElementById('large-image');
                const scale = parseFloat(img.style.transform.replace('scale(', '').replace(')', '')) || 1;
                img.style.transform = `scale(${scale + 0.2})`;
            }

            function zoomOut() {
                const img = document.getElementById('large-image');
                const scale = parseFloat(img.style.transform.replace('scale(', '').replace(')', '')) || 1;
                const newScale = scale - 0.2;
                if (newScale >= 1) {
                    img.style.transform = `scale(${newScale})`;
                }
            }

            document.getElementById('large-image').addEventListener('wheel', function(event) {
                event.preventDefault();
                if (event.deltaY < 0) zoomIn();
                else zoomOut();
            });
        </script>

        <style>
            #image-modal {
                overflow-y: auto;
            }

            #image-modal img {
                object-fit: contain;
                max-width: 100%;
                max-height: 100%;
            }
        </style>
</x-app-layout>
