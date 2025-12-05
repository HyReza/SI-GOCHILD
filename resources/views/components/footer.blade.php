 {{-- FOOTER --}}
 <footer class="w-full px-4 md:px-8 py-10 bg-white border-t-2 dark:bg-gray-800 dark:border-gray-700">
     <div class="container mx-auto grid grid-cols-1 md:grid-cols-3 gap-6 p-4">
         <!-- Logo -->
         <div class="flex justify-start items-center md:justify-start -ml-8">
             <img src="{{ asset('images/logo.svg') }}" alt="Logo" class="h-24">
         </div>

         <!-- Temukan Kami -->
         <div class="text-left md:text-left">
             <h1 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Temukan Kami</h1>
             <ul class="text-sm mt-4 space-y-2">
                 <li class="dark:text-gray-300">Jl Giok no 17 Blok B-5 Perumahan Villa Pisma Asri (Berlian) Desa
                     Podo Kecamatan Kedungwuni
                     Kabupaten Pekalongan 51173</li>
                 <li>
                     <a href="https://maps.app.goo.gl/aQ3zfzQbBFZHNMRg6" target="_blank">
                         <button
                             class="flex h-8 w-28 bg-orange-500 hover:bg-orange-600 mt-4 text-white rounded-lg justify-center items-center gap-2">
                             Lokasi
                             <span class="material-symbols-outlined text-xs">pin_drop</span>
                         </button>
                     </a>
                 </li>
             </ul>
         </div>

         <!-- Hubungi Kami -->
         <div class="text-left md:text-left">
             <h1 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Hubungi Kami</h1>
             <ul class="text-sm mt-4 space-y-2 dark:text-gray-300">
                 <li class="flex gap-2 items-center">
                     <span class="material-symbols-outlined text-xs">mail</span>
                     info@aljannah.sch.id
                 </li>
                 <li class="flex gap-2 items-center mt-2">
                     <span class="material-symbols-outlined text-xs">phone_in_talk</span>
                     085602766027
                 </li>
             </ul>
         </div>
     </div>
 </footer>

 <div class="w-full h-8 text-center content-center bg-gray-200 dark:bg-gray-900 px-4">
     <h1 class="text-xs font-semibold text-gray-600 dark:text-gray-400">
         © Al Jannah Made With ❤️ By <a href="#" class="text-orange-500 hover:underline">Reza Edi Saputra</a>
     </h1>
 </div>
