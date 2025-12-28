<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Konfigurasi API AI') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-100">
                <div class="p-8">
                    <div class="mb-6 border-b border-gray-100 pb-4 flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Edit API Key</h3>
                            <p class="text-sm text-gray-500">Perbarui kredensial Google Gemini AI Anda.</p>
                        </div>
                        <span class="bg-gray-100 text-gray-600 py-1 px-3 rounded-full text-xs font-mono">
                            ID: {{ $apiGemini->id }}
                        </span>
                    </div>

                    <form action="{{ route('api-gemini.update', $apiGemini->id) }}" method="POST" id="editApiKeyForm">
                        @csrf
                        @method('PUT')

                        <div class="mb-6">
                            <x-input-label for="name" :value="__('Label / Nama Identitas')" class="mb-1" />
                            <x-text-input id="name"
                                class="block w-full py-2.5 px-4 bg-gray-50 border-gray-200 focus:bg-white transition-colors"
                                type="text" name="name" :value="old('name', $apiGemini->name)"
                                placeholder="Contoh: Akun Sekolah Utama..." />
                            <p class="text-xs text-gray-400 mt-1">Opsional, hanya untuk memudahkan identifikasi.</p>
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <x-input-label for="model" :value="__('Nama Model AI')" class="mb-1" />
                                <div class="relative">
                                    <x-text-input id="model"
                                        class="block w-full py-2.5 px-4 font-mono text-sm bg-gray-50 border-gray-200 focus:bg-white transition-colors"
                                        type="text" name="model" :value="old('model', $apiGemini->model)" placeholder="gemini-1.5-flash"
                                        required />
                                </div>
                                <div class="mt-2 text-xs text-gray-500">
                                    <span class="font-bold">Saran Model:</span>
                                    <span
                                        class="inline-block bg-gray-100 px-1.5 py-0.5 rounded border border-gray-200 text-gray-600 mr-1 mb-1">gemini-2.5-flash</span>
                                    <span
                                        class="inline-block bg-gray-100 px-1.5 py-0.5 rounded border border-gray-200 text-gray-600 mr-1 mb-1">gemini-1.5-flash</span>
                                    <span
                                        class="inline-block bg-gray-100 px-1.5 py-0.5 rounded border border-gray-200 text-gray-600 mr-1 mb-1">gemini-1.5-pro</span>
                                </div>
                                <x-input-error :messages="$errors->get('model')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="api_key" :value="__('API Key Google')" class="mb-1" />
                                <div class="relative" x-data="{ show: false }">
                                    <x-text-input id="api_key"
                                        class="block w-full py-2.5 pl-4 pr-10 font-mono text-sm bg-gray-50 border-gray-200 focus:bg-white transition-colors"
                                        ::type="show ? 'text' : 'password'" name="api_key" :value="old('api_key', $apiGemini->api_key)" required />

                                    <button type="button" @click="show = !show"
                                        class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none transition-colors">
                                        <span class="material-symbols-outlined text-[20px]"
                                            x-text="show ? 'visibility_off' : 'visibility'"></span>
                                    </button>
                                </div>
                                <x-input-error :messages="$errors->get('api_key')" class="mt-2" />
                            </div>
                        </div>

                        <div
                            class="flex items-center justify-between bg-gray-50 p-4 rounded-xl border border-gray-100 mb-8">
                            <div>
                                <span class="block text-sm font-medium text-gray-900">Status Aktif</span>
                                <span class="block text-xs text-gray-500">Jadikan ini sebagai Key Utama sistem?</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" class="sr-only peer"
                                    {{ old('is_active', $apiGemini->is_active) ? 'checked' : '' }}>
                                <div
                                    class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                                </div>
                            </label>
                        </div>

                        <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-100">
                            <a href="{{ route('api-gemini.index') }}"
                                class="px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                                Batal
                            </a>
                            <button type="submit"
                                class="inline-flex items-center px-6 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-md hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                                <span class="material-symbols-outlined text-[18px] mr-2">save</span>
                                {{ __('Perbarui Konfigurasi') }}
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    {{-- SWEETALERT LOGIC --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('editApiKeyForm');

            // 1. Cek Error Validasi Server
            @if ($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    text: 'Mohon periksa kembali inputan Anda.',
                    confirmButtonColor: '#d33',
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#d33',
                });
            @endif

            // 2. Logic Submit Form
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }

                Swal.fire({
                    title: 'Simpan Perubahan?',
                    text: "Data API Key akan diperbarui.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#2563eb',
                    cancelButtonColor: '#9ca3af',
                    confirmButtonText: 'Ya, Simpan',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Menyimpan...',
                            text: 'Mohon tunggu sebentar',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        form.submit();
                    }
                });
            });
        });
    </script>
</x-app-layout>
