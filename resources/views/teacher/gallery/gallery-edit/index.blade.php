<x-app-layout>
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
                        <span class="ms-1.5 text-xs font-medium dark:text-gray-300">Daftar galeri aktivitas</span>
                    </a>
                </li>
                <li class="relative flex items-center">
                    <span
                        class="absolute inset-y-0 -start-px h-10 w-4 bg-gray-100 dark:bg-gray-800 [clip-path:_polygon(0_0,_0%_100%,_100%_50%)] rtl:rotate-180"></span>
                    <a href="#"
                        class="flex h-10 items-center bg-white dark:bg-gray-900 pe-4 ps-8 text-xs font-medium transition hover:text-gray-900 dark:hover:text-white">
                        Form edit galeri aktivitas
                    </a>
                </li>
            </ol>
        </nav>

        <!-- Gallery Information -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg my-8">
            <h2 class="text-base md:text-2xl font-semibold text-gray-800 dark:text-white mb-4">
                Edit Gallery: {{ $gallery->gallery_title }}
            </h2>

            <!-- Form to Edit Gallery -->
            <form action="{{ route('gallery-activity.update', $gallery->id) }}" method="POST"
                enctype="multipart/form-data" id="gallery-form">
                @csrf
                @method('PUT')

                <!-- Gallery Title -->
                <div class="mb-6">
                    <label for="gallery_title"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Title</label>
                    <input type="text" name="gallery_title" id="gallery_title"
                        value="{{ old('gallery_title', $gallery->gallery_title) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        required>
                </div>

                <!-- Gallery Description -->
                <div class="mb-6">
                    <label for="gallery_description"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                    <textarea name="gallery_description" id="gallery_description" rows="4"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ old('gallery_description', $gallery->gallery_description) }}</textarea>
                </div>

                <!-- Gallery Date -->
                <div class="mb-6">
                    <label for="gallery_date"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date</label>
                    <input type="date" name="gallery_date" id="gallery_date"
                        value="{{ old('gallery_date', $gallery->gallery_date) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        required>
                </div>

                <!-- Gallery Images (Drag and Drop) -->
                <div class="mb-6">
                    <label for="images" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Upload New
                        Images</label>
                    <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 p-6 text-center rounded-lg"
                        id="dropzone">
                        <p class="text-gray-500 dark:text-gray-400 flex items-center justify-center space-x-2">
                            <span class="material-symbols-outlined">upload</span>
                            <span>Drag and drop your images here, or click to select</span>
                        </p>
                        <input type="file" name="new_images[]" id="images" class="hidden" accept="image/*"
                            multiple>
                    </div>

                    <div id="image-list" class="mt-4 space-y-2"></div>
                </div>

                <!-- Delete Existing Images -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Delete Existing
                        Images</label>
                    <ul class="space-y-2">
                        @foreach ($gallery->galleryImages as $image)
                            <li
                                class="flex items-center justify-between p-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-800 shadow-sm">
                                <div class="flex items-center space-x-3">
                                    <img src="{{ asset('storage/' . $image->image_url) }}" alt="Gallery Image"
                                        class="w-16 h-16 object-cover rounded-md cursor-pointer"
                                        onclick="showPreviewModal('{{ asset('storage/' . $image->image_url) }}')">
                                    <span class="text-sm text-gray-700 dark:text-gray-200 truncate w-48">
                                        {{ basename($image->image_url) }}
                                    </span>
                                </div>
                                <input type="checkbox" name="delete_images[]" value="{{ $image->id }}"
                                    class="text-indigo-500 w-5 h-5 focus:ring-indigo-500">
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Submit Button -->
                <div class="mb-6">
                    <button type="submit" id="submit-button"
                        class="w-full py-2 px-4 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                        Update Gallery
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal for image preview -->
    <div id="image-modal" class="fixed inset-0 z-50 hidden bg-gray-900 bg-opacity-50 items-center justify-center">
        <div class="flex justify-center items-center h-full px-2">
            <div class="bg-white dark:bg-gray-800 p-4 rounded-md max-w-3xl relative">
                <span id="close-modal"
                    class="absolute top-2 right-2 text-gray-800 dark:text-gray-100 cursor-pointer text-2xl">&times;</span>
                <img id="large-image" class="w-full rounded-md" alt="Large Preview" />
            </div>
        </div>
    </div>

    <script>
        let uploadedFiles = []; // Array to store all uploaded files
        const dropzone = document.getElementById('dropzone');
        const imageInput = document.getElementById('images');
        const imageList = document.getElementById('image-list');
        const imageModal = document.getElementById('image-modal');
        const largeImage = document.getElementById('large-image');
        const closeModalButton = document.getElementById('image-modal');

        // Allow clicking dropzone to trigger file input click
        dropzone.addEventListener('click', () => imageInput.click());

        // Handle drag over event for styling
        dropzone.addEventListener('dragover', (event) => {
            event.preventDefault();
            dropzone.classList.add('border-indigo-500');
        });

        // Handle drag leave event for styling
        dropzone.addEventListener('dragleave', () => {
            dropzone.classList.remove('border-indigo-500');
        });

        // Handle drop event for files
        dropzone.addEventListener('drop', (event) => {
            event.preventDefault();
            const files = event.dataTransfer.files;
            addFilesToInput(files); // Add dropped files to input
            addFilesToList(files); // Display files in the preview list
        });

        // Handle input change event for file selection (file picker)
        imageInput.addEventListener('change', () => {
            const files = imageInput.files;
            if (files.length > 0) {
                addFilesToList(files); // Display files in the preview list
            } else {
                // If no file is selected (i.e., cancel was pressed), clear the list
                imageList.innerHTML = '';
            }
        });

        // Function to add files to the input field (hidden file input)
        function addFilesToInput(files) {
            const currentFiles = imageInput.files;
            const dataTransfer = new DataTransfer();

            // Clear the file input (replace existing files)
            for (let i = 0; i < currentFiles.length; i++) {
                dataTransfer.items.add(currentFiles[i]);
            }

            // Add the new files to the DataTransfer object (replace old files)
            for (let i = 0; i < files.length; i++) {
                dataTransfer.items.add(files[i]);
            }

            // Update the file input with the new list of files
            imageInput.files = dataTransfer.files;
        }

        // Function to add files to the list for preview
        function addFilesToList(files) {
            imageList.innerHTML = ''; // Clear existing list of images to handle replacement
            Array.from(files).forEach((file) => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const filePreview = document.createElement('div');
                    filePreview.classList.add('flex', 'items-center', 'justify-between', 'p-2', 'border',
                        'dark:border-gray-700',
                        'rounded-md', 'bg-gray-50', 'shadow-sm', 'dark:bg-gray-800');
                    filePreview.innerHTML = `
                    <div class="flex items-center space-x-2 dark:bg-gray-800 dark:text-gray-300">
                        <img src="${e.target.result}" alt="${file.name}" class="w-16 h-16 object-cover rounded-md cursor-pointer " onclick="showLargeImage('${e.target.result}')">
                        <span class="text-sm text-gray-700 dark:text-gray-300 overflow-hidden whitespace-nowrap text-ellipsis flex-1" style="max-width: 150px; display: block; text-overflow: ellipsis; overflow: hidden;">${file.name}</span>
                    </div>
                    <button type="button" class="text-red-500 mx-4 align-middle" onclick="removeFile(this, '${file.name}')">
                        <span class="material-symbols-outlined">delete</span>
                    </button>
                `;
                    imageList.appendChild(filePreview);
                };
                reader.readAsDataURL(file);
            });
        }

        // Function to remove file from list and input
        function removeFile(button, fileName) {
            const filePreview = button.closest('div');
            filePreview.remove();

            // Update the uploadedFiles array by removing the file
            uploadedFiles = uploadedFiles.filter(file => file.name !== fileName);

            // Update the file input by removing the file from the list
            const updatedFiles = Array.from(imageInput.files).filter(file => file.name !== fileName);
            const dataTransfer = new DataTransfer();

            // Add remaining files to the DataTransfer object
            updatedFiles.forEach(file => {
                dataTransfer.items.add(file);
            });

            // Update the file input with the remaining files
            imageInput.files = dataTransfer.files;
        }

        // Function to show the large image in a modal
        function showLargeImage(imageSrc) {
            largeImage.src = imageSrc; // Set the large image to the clicked image source
            imageModal.classList.remove('hidden'); // Show the modal
        }

        // Close the modal when the close button (X) is clicked
        closeModalButton.addEventListener('click', () => {
            imageModal.classList.add('hidden'); // Hide the modal
            clearFileList(); // Remove files from the list when the modal is closed
        });



        // Handle form submission confirmation with SweetAlert
        document.getElementById('gallery-form').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent default form submission

            Swal.fire({
                title: 'Confirm Submission',
                text: "Are you sure you want to update the gallery?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Update',
                cancelButtonText: 'No, Cancel',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading animation
                    Swal.fire({
                        title: 'Processing...',
                        text: 'Please wait while we update the gallery.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading(); // Show loading spinner
                        }
                    });

                    // Submit the form after a small delay to show the loading animation
                    setTimeout(() => {
                        this.submit(); // Submit the form
                    }, 1000); // Delay of 1 second
                }
            });
        });

        // Function to show the large image preview in the modal
        function showPreviewModal(imageSrc) {
            const modal = document.getElementById('image-modal');
            const largeImage = document.getElementById('large-image');

            largeImage.src = imageSrc; // Set the large image to the clicked image source
            modal.classList.remove('hidden'); // Show the modal
        }

        // Close the modal when the close button (X) is clicked
        document.getElementById('close-modal').addEventListener('click', () => {
            document.getElementById('image-modal').classList.add('hidden'); // Hide the modal
        });
    </script>

</x-app-layout>
