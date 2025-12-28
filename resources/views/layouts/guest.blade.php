<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- SITE META --}}
    <meta name="keywords" content="{{ $seo_key ?? '' }}">
    <meta name="description" content="{{ $seo_description ?? '' }}">
    <meta property="og:title" content="{{ $seo_meta_title ?? '' }}">
    <meta property="og:image" content="{{ asset('images/logo.png') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta name="author" content="Reza Edi Saputra - SI-GoChild">

    <title>{{ $seo_title ?? config('app.name', 'SI-GoChild') }}</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo2.png') }}">

    {{-- FONTS --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"
        rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --emerald-vibrant: #10b981;
        }

        /* --- CURSOR SYSTEM --- */
        #cursor-dot {
            position: fixed;
            top: 0;
            left: 0;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            pointer-events: none;
            background-color: var(--emerald-vibrant);
            z-index: 1100000;
            transform: translate(-50%, -50%);
            transition: opacity 0.3s ease;
        }

        #cursor-ring {
            position: fixed;
            top: 0;
            left: 0;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            pointer-events: none;
            border: 1.5px solid var(--emerald-vibrant);
            z-index: 1099999;
            transform: translate(-50%, -50%);
            transition: width 0.4s cubic-bezier(0.23, 1, 0.32, 1),
                height 0.4s cubic-bezier(0.23, 1, 0.32, 1),
                background-color 0.3s ease,
                border-color 0.3s ease;
        }

        /* --- REVEAL ANIMATION --- */
        .reveal {
            opacity: 0;
            transform: translateY(40px) scale(0.98);
            transition: opacity 1s cubic-bezier(0.16, 1, 0.3, 1),
                transform 1s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .reveal.active {
            opacity: 1;
            transform: translateY(0) scale(1);
        }

        /* --- GLASSMORPHISM PREMIUM --- */
        .glass-premium {
            background: rgba(255, 255, 255, 0.7);
            border: 1px solid rgba(226, 232, 240, 0.6);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }

        .dark .glass-premium {
            background: rgba(15, 23, 42, 0.4);
            border: 1px solid rgba(30, 41, 59, 0.6);
        }

        /* Sembunyikan kursor asli di desktop untuk efek yang lebih baik */
        @media (min-width: 1024px) {

            body,
            a,
            button,
            .interactive {
                cursor: none !important;
            }
        }
    </style>
</head>

<body
    class="antialiased bg-slate-50 dark:bg-[#020617] text-slate-900 dark:text-slate-100 transition-colors duration-1000 overflow-x-hidden">

    {{-- CURSOR ELEMENTS --}}
    <div id="cursor-dot"></div>
    <div id="cursor-ring"></div>

    <x-loading></x-loading>

    <x-navbar></x-navbar>

    <main class="min-h-screen relative z-10">
        {{ $slot }}
    </main>

    <x-footer></x-footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {

            // 1. LOADER CONTROLLER
            const hideGlobalLoader = () => {
                const loader = document.getElementById('loader-wrapper');
                if (loader) {
                    loader.style.opacity = '0';
                    loader.style.transition = 'opacity 0.8s ease';
                    loader.style.pointerEvents = 'none';
                    setTimeout(() => loader.remove(), 800);
                }
            };
            window.addEventListener('load', hideGlobalLoader);
            setTimeout(hideGlobalLoader, 3000); // Failsafe 3 detik

            // 2. STABLE CURSOR ENGINE (Lerp)
            const dot = document.querySelector('#cursor-dot');
            const ring = document.querySelector('#cursor-ring');

            let mouseX = -100,
                mouseY = -100;
            let dotX = -100,
                dotY = -100;
            let ringX = -100,
                ringY = -100;

            // Sembunyikan kursor di perangkat sentuh
            if ('ontouchstart' in window) {
                if (dot) dot.style.display = 'none';
                if (ring) ring.style.display = 'none';
            }

            document.addEventListener('mousemove', (e) => {
                mouseX = e.clientX;
                mouseY = e.clientY;
            });

            const updateCursor = () => {
                dotX += (mouseX - dotX) * 0.3;
                dotY += (mouseY - dotY) * 0.3;
                ringX += (mouseX - ringX) * 0.15;
                ringY += (mouseY - ringY) * 0.15;

                if (dot) {
                    dot.style.left = `${dotX}px`;
                    dot.style.top = `${dotY}px`;
                }
                if (ring) {
                    ring.style.left = `${ringX}px`;
                    ring.style.top = `${ringY}px`;
                }
                requestAnimationFrame(updateCursor);
            };
            updateCursor();

            // Hover Interaction Logic
            const handleEnter = () => {
                if (ring) {
                    ring.style.width = '75px';
                    ring.style.height = '75px';
                    ring.style.backgroundColor = 'rgba(16, 185, 129, 0.1)';
                    ring.style.borderColor = 'rgba(16, 185, 129, 0.8)';
                    if (dot) dot.style.opacity = '0';
                }
            };

            const handleLeave = () => {
                if (ring) {
                    ring.style.width = '40px';
                    ring.style.height = '40px';
                    ring.style.backgroundColor = 'transparent';
                    ring.style.borderColor = '#10b981';
                    if (dot) dot.style.opacity = '1';
                }
            };

            const setupInteractivity = () => {
                const targets = document.querySelectorAll(
                    'a, button, .interactive, [role="button"], input, select');
                targets.forEach(el => {
                    el.addEventListener('mouseenter', handleEnter);
                    el.addEventListener('mouseleave', handleLeave);
                });
            };

            setupInteractivity();

            // Re-setup saat ada konten baru (AJAX/Modal)
            const observer = new MutationObserver(setupInteractivity);
            observer.observe(document.body, {
                childList: true,
                subtree: true
            });

            // 3. REVEAL SYSTEM (Intersection Observer)
            const revealOptions = {
                threshold: 0.15,
                rootMargin: "0px 0px -50px 0px"
            };

            const revealObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('active');
                        // Opsional: unobserve setelah muncul jika ingin animasi hanya sekali
                        // revealObserver.unobserve(entry.target);
                    }
                });
            }, revealOptions);

            document.querySelectorAll('.reveal').forEach(el => revealObserver.observe(el));
        });
    </script>
</body>

</html>
