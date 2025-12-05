<x-guest-layout>
    @slot('seo_key', $seo_key)
    @slot('seo_description', $seo_description)
    @slot('seo_meta_title', $seo_meta_title)
    @slot('seo_title', $seo_title)

    <script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>

    <style>
        /* Styling untuk list terurut */
        .ql-container ol {
            list-style-type: decimal;
            padding-left: 1.5rem;
            margin-bottom: 1rem;
        }

        /* Styling untuk list bullet */
        .ql-container ul {
            list-style-type: disc;
            padding-left: 1.5rem;
            margin-bottom: 1rem;
        }

        /* Enhanced Dark Mode Text */
        .dark .text-gray-200 {
            color: #e5e7eb;
        }

        .dark .text-gray-400 {
            color: #cbd5e0;
        }

        /* Increased Contrast for Interactive Elements */
        .dark .text-blue-400 {
            color: #63b3ed;
        }

        /* Filter and Search Fields */
        .dark .bg-gray-700 {
            background-color: #4a5568;
        }

        .dark .bg-blue-600 {
            background-color: #3182ce;
        }

        .dark .bg-blue-400 {
            background-color: #63b3ed;
        }

        .dark .text-gray-800 {
            color: #2d3748;
        }

        .dark .border-gray-300 {
            border-color: #4a5568;
        }

        .dark input,
        .dark select {
            background-color: #2d3748;
            /* Dark background for inputs */
            color: #e5e7eb;
            /* Light text */
            border-color: #4a5568;
            /* Dark border */
        }

        /* Improved readability for blog content */
        .dark .text-gray-800 {
            color: #e5e7eb;
            /* Light color for content */
        }

        .dark .text-gray-600 {
            color: #cbd5e0;
        }

        .dark .bg-gray-800 {
            background-color: #2d3748;
        }

        .dark .text-blue-600 {
            color: #63b3ed;
        }
    </style>

    <!-- Header Section -->
    <section class="container mx-auto px-4 py-8">
        <header class="text-center mb-12">
            <h1 class="text-4xl font-extrabold text-gray-900 dark:text-gray-200">Daycare Aktivitas Blog</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">Tetap update dengan aktivitas dan acara terbaru kami
            </p>
            <div class="flex justify-center mt-4">
                <div class="h-1 w-24 bg-orange-500 dark:bg-orange-400 rounded-full"></div>
            </div>
        </header>

        <!-- Combined Search and Filter Form -->
        <div class="flex flex-col md:flex-row justify-between mb-8 space-y-6 md:space-y-0 md:space-x-6">
            <form method="GET" action="{{ route('blogs.index') }}"
                class="w-full flex flex-col md:flex-row items-center space-y-4 md:space-y-0 md:space-x-4">

                <!-- Category Filter -->
                <div class="flex flex-col md:flex-row items-center space-y-4 md:space-y-0 md:space-x-4 w-full">
                    <label for="category" class="text-gray-600 dark:text-gray-400">Filter by Kategori:</label>
                    <select name="category" id="category"
                        class="p-3 border border-gray-300 rounded-lg text-gray-800 dark:text-gray-100 dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full md:w-72">
                        <option value="">All Kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }} "
                                {{ request('category') == $category->id || old('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->category_name }}
                            </option>
                        @endforeach
                    </select>
                    <!-- Filter Button -->
                    <button type="submit" name="action" value="filter"
                        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full md:w-auto">
                        Filter
                    </button>
                </div>

                <!-- Search Input -->
                <div
                    class="flex flex-col md:flex-row items-center space-y-4 md:space-y-0 md:space-x-4 w-full md:max-w-3xl">
                    <input type="text" name="search" value="{{ old('search', request('search')) }}"
                        placeholder="Cari Artikel..."
                        class="h-12 w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-800 dark:text-gray-100 dark:bg-gray-700" />
                </div>

                <!-- Submit Button -->
                <div class="flex space-x-4 w-full md:w-auto mt-4 md:mt-0">

                    <!-- Search Button -->
                    <button type="submit" name="action" value="search"
                        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full md:w-auto">
                        Cari
                    </button>
                </div>
            </form>
        </div>

        <!-- Blog Grid -->
        <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse ($articles as $article)
                <article
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden flex flex-col transform transition-transform duration-500 hover:scale-105 hover:shadow-xl animate-on-scroll">
                    <img class="w-full h-48 object-cover" src="{{ asset('storage/' . $article->image) }}"
                        alt="{{ $article->title }}">
                    <div class="flex flex-col p-6 flex-grow">
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">{{ $article->title }}</h2>
                        <p class="text-gray-600 dark:text-gray-400 mt-2">
                            {!! Str::limit(
                                preg_replace(
                                    ['/<img[^>]+>/i', '/<ul[^>]*>.*?<\/ul>/is', '/<li[^>]*>.*?<\/li>/is', '/<ol[^>]*>.*?<\/ol>/is'],
                                    '',
                                    $article->content,
                                ),
                                200,
                            ) !!}
                        </p>
                        <a href="{{ route('blogs.show', $article->slug) }}"
                            class="mt-auto py-2 inline-block text-blue-600 dark:text-blue-400 hover:underline focus:outline-none focus:ring-2 focus:ring-blue-500">Read
                            more</a>
                    </div>
                </article>
            @empty
                <div class="col-span-3 text-center flex flex-col justify-center items-center space-y-4 py-12">
                    <dotlottie-player src="https://lottie.host/6a4c8f09-46fc-4762-b14a-d8a558d68c9e/ZNeRo4Cuud.lottie"
                        background="transparent" speed="1" style="width: 50%; max-width: 300px; height: auto;" loop
                        autoplay>
                    </dotlottie-player>
                    <p class="text-lg text-gray-600 dark:text-gray-400">No articles found. Please check back later.</p>
                </div>
            @endforelse
        </section>

        <!-- Pagination -->
        <div class="mt-8 text-center">
            {{ $articles->links() }} <!-- Pagination links -->
        </div>
    </section>

    <!-- Animation -->
    <script>
        const blogCards = document.querySelectorAll('.hover\\:scale-105');
        blogCards.forEach(card => {
            card.classList.add('transition-transform', 'duration-500', 'hover:scale-105');
        });
    </script>
</x-guest-layout>
