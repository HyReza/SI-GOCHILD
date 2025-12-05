<x-app-layout>
    <div class="max-w-4xl mx-auto p-8 bg-white dark:bg-gray-900 rounded-xl shadow-xl">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6 text-center">Edit Artikel</h1>

        <form method="POST" action="{{ route('articles.update', $article->id) }}" enctype="multipart/form-data"
            id="articleForm">
            @csrf
            @method('PUT')

            <!-- Title -->
            <div class="mb-6">
                <label for="title" class="block text-lg font-semibold text-gray-700 dark:text-gray-200">Judul
                    Artikel</label>
                <input type="text" name="title" id="title" value="{{ old('title', $article->title) }}"
                    class="w-full mt-2 p-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                    required>
                @error('title')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Slug -->
            <div class="mb-6">
                <label for="slug" class="block text-lg font-semibold text-gray-700 dark:text-gray-200">Slug</label>
                <input type="text" name="slug" id="slug" value="{{ old('slug', $article->slug) }}"
                    class="w-full mt-2 p-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                    readonly>
            </div>

            <!-- Content -->
            <div class="mb-6">
                <label for="content" class="block text-lg font-semibold text-gray-700 dark:text-gray-200">Konten
                    Artikel</label>
                <div id="editor"
                    class="text-base border border-gray-300 dark:border-gray-600 rounded-b-md bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                    {!! old('content', $article->content) !!}
                </div>
                <textarea name="content" id="content" class="hidden">{{ old('content', $article->content) }}</textarea>
                @error('content')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Category -->
            <div class="mb-6">
                <label for="category_id"
                    class="block text-lg font-semibold text-gray-700 dark:text-gray-200">Kategori</label>
                <select name="category_id" id="category_id"
                    class="w-full mt-2 p-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                    required>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}"
                            {{ $article->category_id == $category->id ? 'selected' : '' }}>
                            {{ $category->category_name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Image Upload -->
            <div class="mb-6">
                <label for="image" class="block text-lg font-semibold text-gray-700 dark:text-gray-200">Gambar
                    Artikel</label>
                <input type="file" name="image" id="image"
                    class="w-full mt-2 p-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Biarkan kosong jika tidak ingin mengganti
                    gambar.</p>
                @if ($article->image)
                    <img src="{{ asset('storage/' . $article->image) }}" alt="Article Image"
                        class="mt-4 w-32 h-32 object-cover rounded-md border dark:border-gray-700">
                @endif
                @error('image')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="text-end">
                <button type="submit"
                    class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 transition duration-200">Perbarui
                    Artikel</button>
            </div>
        </form>
    </div>

    <!-- Include Quill and SweetAlert2 -->
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
                    ['link', 'image'],
                    [{
                        'align': []
                    }],
                    ['clean']
                ]
            },
            theme: 'snow'
        });

        function copyQuillContentToTextarea() {
            const content = quill.root.innerHTML;
            document.getElementById('content').value = content;
            return content.trim() !== '';
        }

        document.getElementById('title').addEventListener('input', function() {
            let title = this.value;
            let slug = title.toLowerCase().trim().replace(/ /g, '-').replace(/[^\w-]+/g, '');
            document.getElementById('slug').value = slug;
        });

        const form = document.getElementById('articleForm');
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Perubahan ini akan disimpan.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, perbarui!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Menyimpan...',
                        text: 'Artikel Anda sedang diperbarui.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    if (copyQuillContentToTextarea()) {
                        form.submit();
                    } else {
                        Swal.fire('Gagal', 'Konten artikel tidak boleh kosong!', 'error');
                    }
                }
            });
        });
    </script>
</x-app-layout>
