<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Konfigurasi API AI') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-100">

                <div
                    class="flex flex-col md:flex-row justify-between items-center p-6 border-b border-gray-100 bg-gray-50/50">
                    <div class="mb-4 md:mb-0">
                        <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <span class="material-symbols-outlined text-blue-600">vpn_key</span>
                            Daftar API Key
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">Kelola kunci API Google Gemini untuk fitur AI Generator.
                        </p>
                    </div>
                    <a href="{{ route('api-gemini.create') }}"
                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-md transition-all hover:-translate-y-0.5">
                        <span class="material-symbols-outlined text-[18px] mr-1">add</span>
                        Tambah Key Baru
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Nama Label</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Model AI</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    API Key (Preview)</th>
                                <th
                                    class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($configs as $config)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-900">
                                            {{ $config->name ?? 'Tanpa Label' }}</div>
                                        <div class="text-xs text-gray-400">ID: {{ $config->id }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-md bg-indigo-50 text-indigo-700 border border-indigo-100">
                                            {{ $config->model }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">
                                        <div
                                            class="flex items-center gap-1 bg-gray-100 w-fit px-2 py-1 rounded text-gray-600">
                                            <span class="material-symbols-outlined text-[14px]">key</span>
                                            {{ Str::mask($config->api_key, '•', 4, -4) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if ($config->is_active)
                                            <span
                                                class="inline-flex items-center gap-1 px-3 py-1 text-xs font-bold leading-5 rounded-full bg-green-100 text-green-700 border border-green-200 shadow-sm">
                                                <span
                                                    class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                                                Digunakan
                                            </span>
                                        @else
                                            <form action="{{ route('api-gemini.activate', $config->id) }}"
                                                method="POST" class="inline-block">
                                                @csrf
                                                <button type="button"
                                                    class="btn-activate group inline-flex items-center gap-1 px-3 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-500 hover:bg-blue-50 hover:text-blue-600 border border-transparent hover:border-blue-200 transition-all cursor-pointer">
                                                    <span
                                                        class="material-symbols-outlined text-[16px] text-gray-400 group-hover:text-blue-500">power_settings_new</span>
                                                    Aktifkan
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('api-gemini.edit', $config->id) }}"
                                                class="p-1.5 bg-white border border-gray-200 rounded-lg text-indigo-600 hover:bg-indigo-50 hover:border-indigo-300 transition-all shadow-sm"
                                                title="Edit">
                                                <span class="material-symbols-outlined text-[20px]">edit_square</span>
                                            </a>

                                            @if (!$config->is_active)
                                                <form action="{{ route('api-gemini.destroy', $config->id) }}"
                                                    method="POST" class="inline-block form-delete">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button"
                                                        class="p-1.5 bg-white border border-gray-200 rounded-lg text-red-600 hover:bg-red-50 hover:border-red-300 transition-all shadow-sm btn-delete"
                                                        title="Hapus">
                                                        <span
                                                            class="material-symbols-outlined text-[20px]">delete</span>
                                                    </button>
                                                </form>
                                            @else
                                                <button disabled
                                                    class="p-1.5 bg-gray-50 border border-gray-200 rounded-lg text-gray-300 cursor-not-allowed"
                                                    title="Sedang Aktif (Tidak bisa dihapus)">
                                                    <span class="material-symbols-outlined text-[20px]">delete</span>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center bg-gray-50">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="bg-white p-4 rounded-full shadow-sm mb-3">
                                                <span
                                                    class="material-symbols-outlined text-gray-300 text-5xl">smart_toy</span>
                                            </div>
                                            <h3 class="text-gray-900 font-medium">Belum ada API Key</h3>
                                            <p class="text-gray-500 text-sm mt-1 max-w-xs">Tambahkan API Key dari Google
                                                AI Studio untuk mulai menggunakan fitur AI Generator.</p>
                                            <a href="{{ route('api-gemini.create') }}"
                                                class="mt-4 text-blue-600 hover:text-blue-700 font-medium text-sm underline">
                                                + Buat Konfigurasi Pertama
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($configs->hasPages())
                    <div class="p-4 border-t border-gray-100 bg-gray-50">
                        {{ $configs->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toast Configuration
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            // 1. Notifikasi Flash Session
            @if (session('success'))
                Toast.fire({
                    icon: 'success',
                    title: "{{ session('success') }}"
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: "{{ session('error') }}",
                });
            @endif

            // 2. Konfirmasi Hapus
            const deleteButtons = document.querySelectorAll('.btn-delete');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    const form = this.closest('.form-delete');
                    Swal.fire({
                        title: 'Hapus Kunci API?',
                        text: "Tindakan ini tidak dapat dibatalkan.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Ya, Hapus',
                        cancelButtonText: 'Batal',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            // 3. Konfirmasi Aktivasi Key
            const activateButtons = document.querySelectorAll('.btn-activate');
            activateButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    const form = this.closest('form');
                    Swal.fire({
                        title: 'Aktifkan Kunci Ini?',
                        text: "Sistem akan beralih menggunakan API Key ini.",
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonColor: '#2563eb',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Ya, Aktifkan',
                        cancelButtonText: 'Batal',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
</x-app-layout>
