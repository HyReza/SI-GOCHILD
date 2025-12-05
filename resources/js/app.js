import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

import $ from 'jquery';
window.$ = $;
window.jQuery = $;


// LOADER
window.addEventListener('load', function() {
    const loader = document.getElementById('loader');

    // Optional: Add dark mode class based on user preference
    if (document.documentElement.classList.contains('dark-mode')) {
        loader.classList.add('dark-mode');
    }

    // Add a delay of 3 seconds before hiding the loader
    setTimeout(() => {
        loader.style.opacity = '0';
        loader.style.visibility = 'hidden';
        setTimeout(() => loader.remove(), 300); // Ensure loader is removed after fade-out
    }); // 3000 milliseconds delay
});





// ANIMATE

document.addEventListener('DOMContentLoaded', function() {
    const observerOptions = {
        threshold: 0.1
    };

    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate');
                observer.unobserve(entry.target); // Stop observing once animated
            }
        });
    }, observerOptions);

    document.querySelectorAll('.fade-in').forEach(el => observer.observe(el));
    document.querySelectorAll('.slide-in-left').forEach(el => observer.observe(el));
    document.querySelectorAll('.slide-in-right').forEach(el => observer.observe(el));
});

document.addEventListener("DOMContentLoaded", function() {
    const observerOptions = {
        root: null,
        rootMargin: "0px",
        threshold: [0.4, 0.7], // Threshold values to detect partial visibility
    };

    const handleIntersection = (entries, observer) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                // When the section is entering the viewport
                entry.target.classList.remove("fade-out-up");
                entry.target.classList.add("fade-in-up");
            } else {
                // When the section is leaving the viewport
                entry.target.classList.remove("fade-in-up");
                entry.target.classList.add("fade-out-up");
            }
        });
    };

    const observer = new IntersectionObserver(handleIntersection, observerOptions);

    document.querySelectorAll(".animate-on-scroll").forEach((section) => {
        observer.observe(section);
    });
});
