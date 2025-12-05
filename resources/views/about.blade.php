<x-guest-layout>
    @slot('seo_key', $seo_key)
    @slot('seo_description', $seo_description)
    @slot('seo_meta_title', $seo_meta_title)
    @slot('seo_title', $seo_title)

    <!-- About Section -->
    <section class="grid grid-cols-12 gap-4 transition-all duration-500 ease-in-out">
        <div class="col-span-12 md:col-span-6 slide-in-left">
            <img src="images/about.svg" alt="About Us" class="w-full h-auto">
        </div>
        <div class="col-span-12 md:col-span-6 slide-in-right">
            <header>
                <h1
                    class="text-gray-700 dark:text-gray-300 text-2xl font-bold ml-0 px-4 md:ml-6 md:px-3 mt-10 animate-fadeIn">
                    SEKILAS TENTANG KAMI
                </h1>
            </header>
            <p
                class="text-gray-600 dark:text-gray-400 text-justify text-sm font-inter ml-0 px-4 md:ml-6 md:px-3 mt-8 animate-fadeIn">
                Lembaga pendidikan anak usia dini yang mempunyai visi menjadi pusat tumbuh kembang anak untuk mewujudkan
                generasi sehat jasmani, rohani, beriman dan berkarakter unggul. Untuk mewujudkan visi tersebut, Al
                Jannah Preschool and Day Care mengembangkan misi membantu wali murid untuk menjaga dan mengasuh anak
                dengan penuh kasih sayang serta memasukkan nilai Islam sejak dini, penanaman aqidah dan pembiasaan
                ibadah, menstimulasi tumbuh kembang anak sesuai tahapan usia dengan memberikan stimulasi delapan
                kecerdasan secara komprehensif dalam masa golden age period, membantu anak dalam menumbuhkan percaya
                diri dan mengembangkan kecerdasan emosi melalui sosialisasi dengan teman maupun orang dewasa,
                mengembangkan kemandirian anak dengan menggunakan pola bermain serta memberikan stimulasi pertumbuhan
                melalui sentuhan dan pijatan, memberikan komunikasi secara efektif berbasis Islamic Hypnoperenting.
                Dengan visi dan misi tersebut PAUD Al Jannah Preschool and Day Care ingin membentuk anak yang sehat,
                cerdas, dan sholeh.
            </p>
        </div>
    </section>

    <!-- Visi, Misi, Tujuan Section -->
    <section class="grid grid-cols-12 gap-4 mb-10 mt-4 md:mt-16 transition-all duration-500 ease-in-out">
        <article class="col-span-12 md:col-span-4 text-justify ml-0 px-4 md:ml-6 md:px-3 mt-0 md:mt-4 animate-slideIn">
            <header>
                <h2 class="text-gray-700 dark:text-gray-300 text-2xl font-bold">VISI</h2>
            </header>
            <p class="text-gray-600 dark:text-gray-400 text-justify text-sm font-inter mt-4">
                Meningkatkan Mutu Pendidikan dan Menjadi Pusat Tumbuh Kembang Anak untuk Mewujudkan Generasi Sehat
                Jasmani, Rohani, Beriman, dan Berkarakter Unggul.
            </p>
        </article>

        <article
            class="col-span-12 md:col-span-4 text-justify ml-0 px-4 md:ml-6 md:px-3 mt-4 animate-slideIn delay-150">
            <header>
                <h2 class="text-gray-700 dark:text-gray-300 text-2xl font-bold">MISI</h2>
            </header>
            <ul class="text-gray-600 dark:text-gray-400 text-justify text-sm font-inter mt-4">
                <li>(1) Melakukan pelayanan pengasuhan dengan penuh kasih sayang serta memasukkan nilai Islam sejak dini
                    melalui penanaman nilai aqidah dan pembiasaan ibadah.</li>
                <li>(2) Menstimulasi Tumbuh Kembang Anak sesuai tahapan usia dengan memberikan stimulasi delapan
                    kecerdasan secara komprehensif pada masa Golden Age Periode.</li>
                <li>(3) Membantu anak dalam menumbuhkan rasa percaya diri dan mengembangkan kecerdasan emosi melalui
                    sosialisasi dengan teman maupun orang dewasa.</li>
                <li>(4) Mengembangkan kemandirian anak dengan menggunakan pola bermain serta memberi stimulasi
                    pertumbuhan melalui sentuhan dan pijatan.</li>
                <li>(5) Memberikan komunikasi secara efektif berbasis Islamic Hypnoperenting.</li>
            </ul>
        </article>

        <article
            class="col-span-12 md:col-span-4 text-justify ml-0 px-4 md:ml-6 md:px-3 mt-4 animate-slideIn delay-300">
            <header>
                <h2 class="text-gray-700 dark:text-gray-300 text-2xl font-bold">TUJUAN</h2>
            </header>
            <ul class="text-gray-600 dark:text-gray-400 text-justify text-sm font-inter mt-4">
                <li>(1) Terwujudnya layanan yang aman, nyaman, dan membantu kebutuhan orang tua bekerja.</li>
                <li>(2) Meningkatkan pemahaman dan kesadaran orang tua tentang kesehatan dalam pengasuhan dan pendidikan
                    anak.</li>
                <li>(3) Terlaksananya program pengasuhan yang terencana sesuai dengan usia dan kebutuhan anak.</li>
                <li>(4) Berkembangnya kesehatan, kecerdasan, keceriaan, kreativitas, kemandirian, tanggung jawab,
                    perilaku anak yang bermain dan berakhlakul karimah.</li>
                <li>(5) Terjalinnya hubungan yang baik antara orang tua, pengasuh, pendidik, dan pengelola TPA Al Jannah
                    Preschool and Day Care.</li>
            </ul>
        </article>
    </section>
</x-guest-layout>

<!-- Tailwind Custom Animations -->
<style>
    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    @keyframes slideIn {
        from {
            transform: translateY(20px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .animate-fadeIn {
        animation: fadeIn 1s ease-in-out;
    }

    .animate-slideIn {
        animation: slideIn 1s ease-in-out;
    }

    .animate-slideIn.delay-150 {
        animation-delay: 0.15s;
    }

    .animate-slideIn.delay-300 {
        animation-delay: 0.3s;
    }
</style>
