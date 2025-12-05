<x-app-layout>
    <x-slot:title>Katalog Layanan</x-slot:title>

    {{-- SweetAlert for Success Message --}}
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: "{{ session('success') }}",
                    showConfirmButton: false,
                    timer: 3000
                });
            });
        </script>
    @endif

    {{-- SweetAlert for Error Message --}}
    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: "{{ session('error') }}",
                    showConfirmButton: false,
                    timer: 3000
                });
            });
        </script>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-6">
        @foreach ($services as $service)
            <div
                class="relative bg-white dark:bg-gray-900 rounded-xl shadow-md p-6 border border-gray-100 dark:border-gray-700 transition duration-300 ease-in-out hover:shadow-xl hover:scale-[1.01]">

                {{-- Badge Harga --}}
                <div
                    class="absolute top-4 right-4 bg-indigo-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow dark:bg-indigo-600">
                    Rp {{ number_format($service->service_price, 0, ',', '.') }}
                </div>

                {{-- Header Card --}}
                <div class="mb-4 flex items-center gap-3">
                    <div class="bg-pink-100 dark:bg-pink-600 text-pink-700 dark:text-white p-2 rounded-full">
                        <span class="material-symbols-outlined text-xl">child_care</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white">{{ $service->service_name }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-300"><span><strong>ID:</strong>
                                {{ $service->id }}</span></p>
                    </div>
                </div>

                {{-- Info Tambahan (jika ada info lain) --}}
                <div class="text-sm text-gray-700 dark:text-gray-200 space-y-1 mt-3">
                    <p class="flex items-center gap-2">
                        <span
                            class="material-symbols-outlined text-base text-indigo-400 dark:text-indigo-300">info</span>
                        <span>{{ $service->service_description }}</span>
                    </p>
                </div>

                {{-- Tombol Edit --}}
                <div class="mt-6 flex justify-end">
                    <a href="{{ route('catalog-service.edit', $service->id) }}"
                        class="inline-flex items-center px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium rounded-md ring-offset-2 ring-yellow-300 focus:outline-none focus:ring-2 transition-all">
                        <span class="material-symbols-outlined text-base mr-2">edit</span> Edit Layanan
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</x-app-layout>
