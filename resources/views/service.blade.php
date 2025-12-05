<x-guest-layout>
    @slot('seo_key', $seo_key)
    @slot('seo_description', $seo_description)
    @slot('seo_meta_title', $seo_meta_title)
    @slot('seo_title', $seo_title)

    <!-- Header Section -->
    <section class="mt-24">
        <h1 class="text-center text-4xl font-bold m-6 text-gray-700 dark:text-gray-300">Layanan Al Jannah</h1>
        <div class="flex justify-center">
            <div class="bg-orange-500 h-1 w-56 mb-4"></div>
        </div>
        <p class="text-sm text-center text-gray-500 dark:text-gray-400 mx-4 md:mx-52 font-semibold">
            Kami menawarkan berbagai program dan kegiatan yang dirancang untuk mengembangkan berbagai aspek perkembangan
            anak, termasuk kegiatan bermain yang edukatif, pembelajaran nilai-nilai islami, serta berbagai aktivitas
            kreatif yang merangsang imajinasi dan keterampilan motorik anak. Selain itu, kami juga menyediakan layanan
            konsultasi perkembangan anak bagi para orang tua, guna mendukung mereka dalam memahami dan memantau
            perkembangan buah hati tercinta.
        </p>
    </section>

    <!-- Services Section -->
    <section class="flex flex-wrap justify-center items-center h-full p-1 mb-8">
        <!-- Babyhood Daycare -->
        <article
            class="text-start bg-white dark:bg-gray-800 h-80 w-full md:w-60 lg:w-60 xl:w-48 2xl:w-60 m-4 rounded-md shadow-lg transform transition hover:scale-105 duration-300 border dark:border-gray-700 animate-on-scroll">
            <div class="p-2 bg-green-500 w-24 rounded-lg m-4">
                <img src="images/babyhood.svg" alt="babyhood" class="w-24">
            </div>
            <div class="h-1 w-32 bg-green-500 mx-4"></div>
            <h2
                class="block text-lg md:text-lg lg:text-lg xl:text-sm 2xl:text-lg text-gray-600 dark:text-gray-300 font-semibold m-4">
                Babyhood Daycare</h2>
            <p
                class="text-sm md:text-sm lg:text-sm xl:text-xs 2xl:text-sm text-gray-500 dark:text-gray-400 text-justify mx-4">
                Daycare yang menyediakan perawatan harian untuk bayi dan anak-anak dengan lingkungan yang aman dan penuh
                kasih sayang.
            </p>
        </article>

        <!-- Early Childhood Daycare -->
        <article
            class="text-start bg-white dark:bg-gray-800 h-80 w-full md:w-60 lg:w-60 xl:w-48 2xl:w-60 m-4 rounded-md shadow-lg transform transition hover:scale-105 duration-300 border dark:border-gray-700 animate-on-scroll delay-100">
            <div class="p-2 bg-orange-500 w-24 rounded-lg m-4">
                <img src="images/childhood.svg" alt="childhood" class="w-24">
            </div>
            <div class="h-1 w-32 bg-orange-500 mx-4"></div>
            <h2
                class="block text-lg md:text-lg lg:text-lg xl:text-sm 2xl:text-lg text-gray-600 dark:text-gray-300 font-semibold m-4">
                Early Childhood Daycare</h2>
            <p
                class="text-sm md:text-sm lg:text-sm xl:text-xs 2xl:text-sm text-gray-500 dark:text-gray-400 text-justify mx-4">
                Daycare yang fokus pada perawatan dan pendidikan dini untuk anak-anak usia pra-sekolah, mendukung secara
                holistik perkembangan mereka.
            </p>
        </article>

        <!-- Baby and Infant Massage -->
        <article
            class="text-start bg-white dark:bg-gray-800 h-80 w-full md:w-60 lg:w-60 xl:w-48 2xl:w-60 m-4 rounded-md shadow-lg transform transition hover:scale-105 duration-300 border dark:border-gray-700 animate-on-scroll delay-200">
            <div class="p-2 bg-blue-500 w-24 rounded-lg m-4">
                <img src="images/massage.svg" alt="massage" class="w-24">
            </div>
            <div class="h-1 w-32 bg-blue-500 mx-4"></div>
            <h2
                class="block text-lg md:text-lg lg:text-lg xl:text-sm 2xl:text-lg text-gray-600 dark:text-gray-300 font-semibold m-4">
                Baby and Infant Massage</h2>
            <p
                class="text-sm md:text-sm lg:text-sm xl:text-xs 2xl:text-sm text-gray-500 dark:text-gray-400 text-justify mx-4">
                Layanan pijat bayi dan balita yang membantu relaksasi, meningkatkan sirkulasi darah, dan mendukung
                perkembangan motorik.
            </p>
        </article>

        <!-- Baby and Infant Spa -->
        <article
            class="text-start bg-white dark:bg-gray-800 h-80 w-full md:w-60 lg:w-60 xl:w-48 2xl:w-60 m-4 rounded-md shadow-lg transform transition hover:scale-105 duration-300 border dark:border-gray-700 animate-on-scroll delay-300">
            <div class="p-2 bg-pink-500 w-24 rounded-lg m-4">
                <img src="images/spa.svg" alt="spa" class="w-24">
            </div>
            <div class="h-1 w-32 bg-pink-500 mx-4"></div>
            <h2
                class="block text-lg md:text-lg lg:text-lg xl:text-sm 2xl:text-lg text-gray-600 dark:text-gray-300 font-semibold m-4">
                Baby and Infant Spa</h2>
            <p
                class="text-sm md:text-sm lg:text-sm xl:text-xs 2xl:text-sm text-gray-500 dark:text-gray-400 text-justify mx-4">
                Spa khusus bayi dan balita dengan terapi air dan pijat yang menenangkan, membantu perkembangan sensorik
                dan motorik.
            </p>
        </article>

        <!-- Skrining Tumbuh Kembang -->
        <article
            class="text-start bg-white dark:bg-gray-800 h-80 w-full md:w-60 lg:w-60 xl:w-48 2xl:w-60 m-4 rounded-md shadow-lg transform transition hover:scale-105 duration-300 border dark:border-gray-700 animate-on-scroll delay-400">
            <div class="p-2 bg-green-500 w-24 rounded-lg m-4">
                <img src="images/skrining.svg" alt="skrining" class="w-24">
            </div>
            <div class="h-1 w-32 bg-green-500 mx-4"></div>
            <h2
                class="block text-lg md:text-lg lg:text-lg xl:text-sm 2xl:text-lg text-gray-600 dark:text-gray-300 font-semibold mx-4 my-2">
                Skrining Tumbuh Kembang</h2>
            <p
                class="text-sm md:text-sm lg:text-sm xl:text-xs 2xl:text-sm text-gray-500 dark:text-gray-400 text-justify mx-4">
                Layanan evaluasi menyeluruh untuk memantau pertumbuhan dan perkembangan anak.
            </p>
        </article>
    </section>
</x-guest-layout>
