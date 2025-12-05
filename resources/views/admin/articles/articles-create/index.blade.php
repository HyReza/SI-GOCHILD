<x-app-layout>
    <div class="max-w-4xl mx-auto p-8 bg-white dark:bg-gray-900 rounded-xl shadow-xl">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6 text-center">Buat Artikel Baru</h1>

        <form method="POST" action="{{ route('articles.store') }}" enctype="multipart/form-data" id="articleForm">
            @csrf

            <!-- Title -->
            <div class="mb-6">
                <label for="title" class="block text-lg font-semibold text-gray-700 dark:text-gray-200">Judul
                    Artikel</label>
                <input type="text" name="title" id="title"
                    class="w-full mt-2 p-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-800 dark:text-white rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                    required value="{{ old('title') }}">
                @error('title')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Slug -->
            <div class="mb-6">
                <label for="slug" class="block text-lg font-semibold text-gray-700 dark:text-gray-200">Slug</label>
                <input type="text" name="slug" id="slug"
                    class="w-full mt-2 p-2 border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-white rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                    readonly value="{{ old('slug') }}">
            </div>

            <!-- Content -->
            <div class="mb-6">
                <label for="content" class="block text-lg font-semibold text-gray-700 dark:text-gray-200">Konten
                    Artikel</label>
                <div id="editor"
                    class="text-base border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 rounded-b-md dark:text-white">
                    {!! old('content') !!}</div>
                <textarea name="content" id="content" class="hidden">{{ old('content') }}</textarea>
                @error('content')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Category -->
            <div class="mb-6">
                <label for="category_id"
                    class="block text-lg font-semibold text-gray-700 dark:text-gray-200">Kategori</label>
                <select name="category_id" id="category_id"
                    class="w-full mt-2 p-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-800 dark:text-white rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                    required>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->category_name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Image -->
            <div class="mb-6">
                <label for="image" class="block text-lg font-semibold text-gray-700 dark:text-gray-200">Gambar
                    Artikel</label>
                <input type="file" name="image" id="image"
                    class="w-full mt-2 p-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-800 dark:text-white rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                @error('image')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit -->
            <div class="text-end">
                <button type="submit"
                    class="px-6 py-3 bg-gray-800 text-white font-semibold rounded-md hover:bg-gray-900 transition duration-200">
                    Simpan Artikel
                </button>
            </div>
        </form>
    </div>

    <!-- Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const quill = new Quill('#editor', {
            modules: {
                toolbar: [
                    [{
                        'header': '1'
                    }, {
                        'header': '2'
                    }, {
                        'font': []
                    }],
                    [{
                        'list': 'ordered'
                    }, {
                        'list': 'bullet'
                    }],
                    ['bold', 'italic', 'underline'],
                    ['link'],
                    ['image'],
                    [{
                        'align': []
                    }],
                    ['clean']
                ]
            },
            theme: 'snow'
        });

        document.getElementById('title').addEventListener('input', function() {
            let title = this.value;
            let slug = title.toLowerCase().trim()
                .replace(/ /g, '-')
                .replace(/[^\w-]+/g, '');
            document.getElementById('slug').value = slug;
        });

        function copyQuillContentToTextarea() {
            const content = quill.root.innerHTML;
            document.getElementById('content').value = content;
            return content.trim() !== '';
        }

        document.getElementById('articleForm').addEventListener('submit', function(event) {
            event.preventDefault();
            Swal.fire({
                title: 'Apakah yang anda inputkan sudah benar ?',
                text: "Perubahan ini akan disimpan.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, simpan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Menyimpan...',
                        text: 'Artikel Anda sedang disimpan.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    if (copyQuillContentToTextarea()) {
                        this.submit();
                    } else {
                        Swal.fire('Gagal', 'Konten artikel tidak boleh kosong!', 'error');
                    }
                }
            });
        });


        // Detect system dark mode preference
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            document.body.classList.add('dark');
        } else {
            document.body.classList.remove('dark');
        }
    </script>
</x-app-layout>
