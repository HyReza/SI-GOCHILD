import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

document.addEventListener('DOMContentLoaded', () => {

    // 1. GLOBAL LOADER CONTROLLER
    const loader = document.getElementById('loader-wrapper');
    const hideLoader = () => {
        if (loader) {
            loader.style.opacity = '0';
            loader.style.pointerEvents = 'none';
            // Menghapus elemen dari DOM setelah transisi opacity selesai
            setTimeout(() => loader.remove(), 800);
        }
    };

    // Jalankan saat semua aset selesai dimuat
    window.addEventListener('load', hideLoader);

    // Failsafe: Paksa tutup loader setelah 4 detik jika internet lambat
    setTimeout(hideLoader, 4000);

});
