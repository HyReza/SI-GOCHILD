<x-guest-layout>
    @slot('seo_key', $seo_key)
    @slot('seo_description', $seo_description)
    @slot('seo_meta_title', $seo_meta_title)
    @slot('seo_title', $seo_title)

    <!-- Hero Section -->
    <section class="relative bg-white dark:bg-gray-900 fade-in">
        {{-- Container Partikel --}}
        <div id="particles-js" class="absolute inset-0 z-0"></div>

        <header class="relative grid grid-cols-1 md:grid-cols-2 z-10">
            <div class="relative flex items-center px-6 md:px-16 py-16 slide-in-left">
                <div class="max-w-lg mx-auto text-left">
                    <h1 class="text-orange-500 text-lg font-bold">TPA AL JANNAH</h1>
                    <h2 class="text-gray-800 dark:text-gray-100 text-3xl md:text-5xl font-bold mt-3">
                        PRESCHOOL AND DAY CARE
                    </h2>
                    <p class="text-gray-500 dark:text-gray-300 text-sm md:text-base mt-4">
                        Kami adalah lembaga pendidikan anak usia dini yang mempunyai visi menjadi pusat tumbuh
                        kembang
                        anak untuk mewujudkan generasi sehat jasmani, rohani, beriman dan berkarakter unggul.
                    </p>
                    <div class="mt-8 flex flex-wrap gap-4">
                        <a href="https://dapo.kemdikbud.go.id/sekolah/A78AA36B8AF5C5D55670" target="_blank"
                            class="h-12 p-2 w-full md:w-auto bg-orange-500 text-white rounded-md flex items-center justify-center hover:bg-white hover:border-orange-500 hover:border-2 hover:text-orange-500 transition duration-300 ease-in-out">
                            <span class="font-semibold">Laman Kemendikbud</span>
                        </a>
                        <a href="https://wa.me/085602766027" target="_blank"
                            class="h-12 p-4 w-full md:w-auto text-orange-500 bg-white rounded-md border-orange-500 border-2 flex items-center justify-center hover:bg-orange-500 hover:border-none hover:text-white transition duration-300 ease-in-out">
                            <span class="font-semibold">Hubungi Kami</span>
                        </a>
                    </div>
                </div>
            </div>
            <div
                class="relative bg-blue-800 flex justify-center items-center h-auto md:h-auto dark:bg-blue-950 slide-in-right">
                <img src="images/hero.png" alt="Hero Image" class="h-full md:h-auto object-contain">
            </div>
        </header>
    </section>

    {{-- PROGRAMS SECTION --}}
    <section class="py-16 bg-gray-50 dark:bg-gray-800">
        <div class="max-w-screen-xl mx-auto px-6 md:px-16 grid grid-cols-1 md:grid-cols-3 gap-8">
            <article
                class="bg-white dark:bg-gray-900 rounded-lg p-6 shadow-xl hover:shadow-lg transition duration-300 animate-on-scroll">
                <div class="bg-green-500 h-16 w-16 p-4 rounded-lg flex items-center justify-center">
                    <img src="images/sehat.svg" alt="Sehat Icon">
                </div>
                <h3 class="text-xl text-gray-700 dark:text-gray-100 font-bold mt-4">Anak Tumbuh Sehat</h3>
                <span class="block h-1 w-24 bg-green-500 mt-2"></span>
                <p class="text-gray-500 dark:text-gray-300 mt-4">
                    Program ini dirancang untuk memberikan pendidikan kesehatan dasar, gizi seimbang, dan aktivitas
                    fisik yang menyenangkan bagi anak-anak usia dini.
                </p>
            </article>
            <article
                class="bg-white dark:bg-gray-900 rounded-lg p-6 shadow-xl hover:shadow-lg transition duration-300 animate-on-scroll">
                <div class="bg-blue-500 h-16 w-16 p-4 rounded-lg flex items-center justify-center">
                    <img src="images/cerdas.svg" alt="Cerdas Icon">
                </div>
                <h3 class="text-xl text-gray-700 dark:text-gray-100 font-bold mt-4">Anak Tumbuh Cerdas</h3>
                <span class="block h-1 w-24 bg-blue-500 mt-2"></span>
                <p class="text-gray-500 dark:text-gray-300 mt-4">
                    Program ini dirancang untuk merangsang rasa ingin tahu, keterampilan berpikir kritis, dan kemampuan
                    problem-solving melalui berbagai aktivitas edukatif.
                </p>
            </article>
            <article
                class="bg-white dark:bg-gray-900 rounded-lg p-6 shadow-xl hover:shadow-lg transition duration-300 animate-on-scroll">
                <div class="bg-orange-500 h-16 w-16 p-4 rounded-lg flex items-center justify-center">
                    <img src="images/sholeh.svg" alt="Sholeh Icon">
                </div>
                <h3 class="text-xl text-gray-700 dark:text-gray-100 font-bold mt-4">Anak Tumbuh Sholeh</h3>
                <span class="block h-1 w-24 bg-orange-500 mt-2"></span>
                <p class="text-gray-500 dark:text-gray-300 mt-4">
                    Program ini dirancang untuk menanamkan nilai-nilai keagamaan, akhlak mulia, dan rasa kasih sayang
                    melalui kegiatan edukatif dan interaktif.
                </p>
            </article>
        </div>
    </section>

    {{-- OWNER SECTION --}}
    <section class="py-16 bg-white dark:bg-gray-900">
        <div class="max-w-screen-xl mx-auto px-6 md:px-16 grid grid-cols-1 md:grid-cols-2 items-center">
            <figure class="flex justify-center mb-8 md:mb-0 animate-on-scroll">
                <img src="images/profil.svg" alt="Owner Profile" class="w-3/4 md:w-full h-auto">
            </figure>
            <div class="text-left animate-on-scroll">
                <div class="bg-orange-500 h-2 w-48 mb-6"></div>
                <h2 class="text-gray-800 dark:text-gray-100 text-3xl md:text-4xl font-bold mb-4">Salam Hangat
                    Owner<br>Paud Al Jannah</h2>
                <p class="text-gray-500 dark:text-gray-300 text-base leading-relaxed">
                    Dari owner TPA Aljannah merupakan taman penitipan anak yang menyelenggarakan layanan pendidikan anak
                    usia dini dengan mengedepankan stimulasi 10 kecerdasan anak dengan metode Montessori dilengkapi
                    layanan komplementar dengan pijat bayi dan minyak aromaterapi untuk optimalisasi tumbuh kembang
                    anak. Disini tumbuh kembang anak bapak dan ibu akan di pantau setiap bulan dengan instrumen Mddst
                    yang terstandar internasional. Kami mendukung generasi yang sehat, cerdas dan Soleh.
                </p>
                <p class="mt-6 text-orange-500 text-base font-semibold">- Bd. Nur Chabibah, S.Keb.,MPH</p>
            </div>
        </div>
    </section>
    <script>
        // PARTICLES JS
        document.addEventListener('DOMContentLoaded', function() {
            particlesJS('particles-js', {
                particles: {
                    number: {
                        value: 100,
                        density: {
                            enable: true,
                            value_area: 800
                        }
                    },
                    color: {
                        value: getComputedStyle(document.documentElement).getPropertyValue(
                            '--particle-color') || '#000000'
                    },
                    shape: {
                        type: 'circle',
                        stroke: {
                            width: 0,
                            color: '#000000'
                        }
                    },
                    opacity: {
                        value: 0.5,
                        random: true
                    },
                    size: {
                        value: 3,
                        random: true
                    },
                    line_linked: {
                        enable: true,
                        distance: 150,
                        color: getComputedStyle(document.documentElement).getPropertyValue(
                            '--particle-color') || '#000000',
                        opacity: 0.4,
                        width: 1
                    },
                    move: {
                        enable: true,
                        speed: 2,
                        direction: 'none',
                        random: false,
                        straight: false,
                        out_mode: 'out',
                        bounce: false,
                        attract: {
                            enable: false,
                            rotateX: 600,
                            rotateY: 1200
                        }
                    }
                },
                interactivity: {
                    detect_on: 'canvas',
                    events: {
                        onhover: {
                            enable: true,
                            mode: 'repulse'
                        },
                        onclick: {
                            enable: true,
                            mode: 'push'
                        },
                        resize: true
                    },
                    modes: {
                        grab: {
                            distance: 400,
                            line_linked: {
                                opacity: 1
                            }
                        },
                        bubble: {
                            distance: 400,
                            size: 40,
                            duration: 2,
                            opacity: 8,
                            speed: 3
                        },
                        repulse: {
                            distance: 200,
                            duration: 0.4
                        },
                        push: {
                            particles_nb: 4
                        },
                        remove: {
                            particles_nb: 2
                        }
                    }
                },
                retina_detect: true
            });
        });
    </script>
</x-guest-layout>
