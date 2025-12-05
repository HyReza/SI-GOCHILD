<x-app-layout>
    <style>
        /* Styling untuk Quill Editor */
        .ql-container.ql-snow {
            border: none !important;
            box-shadow: none !important;
        }

        .ql-editor {
            border: none !important;
            outline: none !important;
            font-size: 1.125rem;
            line-height: 1.75;
            color: #4B5563;
            /* Default text color */
        }

        .dark .ql-editor {
            color: #f8fafc;
            /* Brighter text for dark mode */
        }

        /* Mengatur Heading (H1, H2) */
        .ql-container h1 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: #1F2937;
            /* Default color */
        }

        .dark .ql-container h1 {
            color: #f8fafc;
            /* Brighter text for dark mode */
        }

        .ql-container h2 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #1F2937;
        }

        .dark .ql-container h2 {
            color: #f8fafc;
            /* Brighter text for dark mode */
        }

        /* Mengatur Paragraf dan Teks Artikel */
        .ql-container p {
            font-size: 1.125rem;
            line-height: 1.75;
            text-align: justify;
            color: #4B5563;
            /* Default color */
        }

        .dark .ql-container p {
            color: #f8fafc;
            /* Brighter text for dark mode */
        }

        /* Styling untuk Daftar Terurut (Penomoran) */
        .ql-container ol,
        .ql-container ul {
            padding-left: 1.5rem;
            font-size: 1.125rem;
            line-height: 1.75;
            color: #4B5563;
            /* Default color */
        }

        .dark .ql-container ol,
        .dark .ql-container ul {
            color: #f8fafc;
            /* Brighter text for dark mode */
        }

        /* Menambahkan margin untuk bullet dan numbered list */
        .ql-container ol li,
        .ql-container ul li {
            margin-bottom: 0.5rem;
        }

        /* Mengatur alignment untuk teks */
        .ql-align-left {
            text-align: left !important;
        }

        .ql-align-center {
            text-align: center !important;
        }

        .ql-align-right {
            text-align: right !important;
        }

        .ql-align-justify {
            text-align: justify !important;
        }


        .dark .text-gray-900 {
            color: #f8fafc;
            /* Brighter text for headings */
        }

        .dark .text-gray-600 {
            color: #f8fafc;
            /* Brighter text for body text */
        }

        .dark .text-gray-500 {
            color: #e2e8f0;
            /* Brighter text for related articles */
        }

        /* Related Articles Section */
        .dark .bg-gray-50 {
            background-color: #4a5568;
            /* Darker background */
        }

        .dark .hover\:shadow-xl:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            /* Hover shadow */
        }

        .dark .text-blue-600 {
            color: #63b3ed;
            /* Light blue for links */
        }

        .dark .hover\:underline:hover {
            text-decoration: underline;
        }

        /* Header Section */
        .dark .bg-gray-800 {
            background-color: #1a202c;
            /* Dark background */
        }

        .dark .text-gray-800 {
            color: #f8fafc;
            /* Brighter text for article header */
        }

        .dark .text-gray-400 {
            color: #e2e8f0;
            /* Brighter color for subtext */
        }
    </style>

    <div class="max-w-7xl mx-auto px-4 md:px-8 py-8 bg-white dark:bg-gray-900 rounded-lg shadow-lg mb-12">
        <!-- Header -->
        <div class="flex flex-col items-center mb-8">
            <img src="{{ asset('storage/' . $article->image) }}" alt="Article Image"
                class="w-full h-96 object-cover rounded-xl shadow-xl mb-6">
            <h1 class="text-4xl font-extrabold text-gray-900 dark:text-white mb-4">{{ $article->title }}</h1>
            <div class="flex justify-center items-center space-x-6 text-sm text-gray-600 dark:text-gray-400">
                <span class="italic">Penulis: <span
                        class="font-semibold text-gray-800 dark:text-gray-200">{{ $article->user ? $article->user->user_name : 'Tidak Diketahui' }}</span></span>
                <span class="italic">Dipublikasikan pada: <span
                        class="font-semibold text-gray-800 dark:text-gray-200">{{ $article->created_at->format('d M Y') }}</span></span>
                <span class="italic">Kategori: <span
                        class="font-semibold text-gray-800 dark:text-gray-200">{{ $article->category->category_name }}</span></span>
            </div>
        </div>

        <!-- Content -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="md:col-span-3 bg-gray-50 dark:bg-gray-800 rounded-lg shadow-md p-8">
                <div class="ql-container ql-snow prose lg:prose-xl max-w-none">
                    {!! $article->content !!}
                </div>
            </div>
        </div>

        <!-- Related Articles -->
        <div class="mt-12">
            <h2 class="text-3xl font-semibold text-gray-900 dark:text-white mb-6">Berita Terkait</h2>

            @if ($relatedArticles->isEmpty())
                <p class="text-gray-600 dark:text-gray-400">Tidak ada berita terkait untuk artikel ini.</p>
            @else
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($relatedArticles as $related)
                        <div
                            class="bg-gray-50 dark:bg-gray-800 rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-all">
                            <img src="{{ asset('storage/' . $related->image) }}" alt="Related News"
                                class="w-full h-48 object-cover">
                            <div class="p-4">
                                <a href="{{ route('articles.show', $related->id) }}"
                                    class="text-xl font-medium text-blue-600 dark:text-blue-400 hover:underline">{{ $related->title }}</a>
                                <p class="text-gray-500 dark:text-gray-400 text-sm mt-2">Dipublikasikan pada:
                                    {{ $related->created_at->format('d M Y') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
    <script>
        // Detect system dark mode preference
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            document.body.classList.add('dark');
        } else {
            document.body.classList.remove('dark');
        }
    </script>
</x-app-layout>
