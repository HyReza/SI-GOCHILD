<x-app-layout>
    <x-slot:title>Management Admin</x-slot:title>

    {{-- SweetAlert for Success Message --}}
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
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
                    title: 'Terjadi Kesalahan!',
                    text: "{{ session('error') }}",
                    showConfirmButton: false,
                    timer: 3000
                });
            });
        </script>
    @endif
    <div x-data="{ open: false, showModal: false, admin: {} }">
        <div x-data="{ open: false }">
            {{-- Header Section: Tombol Tambah & Search --}}
            <div class="mb-6 flex flex-col md:flex-row justify-between items-center gap-4">

                {{-- Tombol Tambah Admin --}}
                <button @click="open = true"
                    class="group flex items-center justify-center gap-2 bg-green-500 hover:bg-green-600 dark:bg-green-600 dark:hover:bg-green-700 text-white font-semibold h-10 w-full md:w-auto px-6 rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                    <span class="text-sm">Tambah Admin</span>
                    <span
                        class="material-symbols-outlined text-[20px] group-hover:rotate-90 transition-transform duration-200">add</span>
                </button>

                {{-- Form Search --}}
                <form method="GET" action="{{ route('admin.index') }}" class="relative w-full md:w-96 flex items-center">
                    <span class="absolute left-3 text-gray-400 material-symbols-outlined text-[20px]">search</span>
                    <input type="text" name="search" placeholder="Cari nama admin..."
                        class="h-10 w-full pl-10 pr-4 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-green-500 transition-all text-sm"
                        value="{{ request()->get('search') }}">
                    <button type="submit"
                        class="absolute right-1 top-1 bottom-1 bg-blue-500 hover:bg-blue-600 text-white text-xs px-3 rounded-md transition-colors">
                        Cari
                    </button>
                </form>
            </div>

            {{-- Modal Tambah Admin --}}
            <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center px-4 py-6 sm:px-0">

                {{-- Backdrop / Overlay dengan Blur --}}
                <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="open = false">
                </div>

                {{-- Modal Content --}}
                <div x-show="open" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl transform transition-all sm:w-full sm:max-w-md w-full relative z-10 overflow-hidden">

                    {{-- Header Modal --}}
                    <div
                        class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-800/50">
                        <h2 class="text-lg font-bold text-gray-800 dark:text-white">Tambah Admin Baru</h2>
                        <button @click="open = false"
                            class="text-gray-400 hover:text-red-500 transition-colors focus:outline-none">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    {{-- Form Body --}}
                    <div class="p-6">
                        <form id="adminForm" method="POST" action="{{ route('admin.store') }}"
                            onsubmit="confirmAdd(event)">
                            @csrf

                            {{-- Input Nama --}}
                            <div class="mb-4">
                                <x-input-label for="user_name" :value="__('Nama Lengkap')" />
                                <x-text-input id="user_name" class="block mt-1 w-full dark:bg-gray-900 dark:text-white"
                                    type="text" name="user_name" :value="old('user_name')" required
                                    placeholder="Contoh: Admin Utama" />
                                <x-input-error :messages="$errors->get('user_name')" class="mt-2" />
                            </div>

                            {{-- Input Email --}}
                            <div class="mb-4">
                                <x-input-label for="email" :value="__('Alamat Email')" />
                                <x-text-input id="email" class="block mt-1 w-full dark:bg-gray-900 dark:text-white"
                                    type="email" name="email" :value="old('email')" required
                                    placeholder="admin@daycare.com" />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            {{-- Input No HP --}}
                            <div class="mb-4">
                                <x-input-label for="phone_number" :value="__('Nomor HP / WA')" />
                                <x-text-input id="phone_number"
                                    class="block mt-1 w-full dark:bg-gray-900 dark:text-white" type="number"
                                    name="phone_number" :value="old('phone_number')" required placeholder="08xxxxxxxxxx" />
                                <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
                            </div>

                            {{-- Input Password --}}
                            <div class="mb-4" x-data="{ showPassword: false }">
                                <x-input-label for="password" :value="__('Password')" />
                                <div class="relative mt-1">
                                    <input id="password"
                                        class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white focus:border-green-500 focus:ring-green-500 shadow-sm pr-10"
                                        :type="showPassword ? 'text' : 'password'" name="password" required
                                        placeholder="******" />
                                    <button type="button" @click="showPassword = !showPassword"
                                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none">
                                        <span class="material-symbols-outlined text-[20px]"
                                            x-text="showPassword ? 'visibility_off' : 'visibility'"></span>
                                    </button>
                                </div>
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            {{-- Input Konfirmasi Password --}}
                            <div class="mb-6" x-data="{ showConfirmPassword: false }">
                                <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />
                                <div class="relative mt-1">
                                    <input id="password_confirmation"
                                        class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white focus:border-green-500 focus:ring-green-500 shadow-sm pr-10"
                                        :type="showConfirmPassword ? 'text' : 'password'" name="password_confirmation"
                                        required placeholder="******" />
                                    <button type="button" @click="showConfirmPassword = !showConfirmPassword"
                                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none">
                                        <span class="material-symbols-outlined text-[20px]"
                                            x-text="showConfirmPassword ? 'visibility_off' : 'visibility'"></span>
                                    </button>
                                </div>
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                            </div>

                            {{-- Footer Modal Action --}}
                            <div class="flex justify-end gap-3 pt-2">
                                <button type="button" @click="open = false"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 transition-colors">
                                    Batal
                                </button>
                                <button type="submit"
                                    class="px-4 py-2 text-sm font-medium text-white bg-green-500 rounded-lg hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                                    Simpan Data
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div
            class="my-6 p-8 bg-white dark:bg-gray-900 drop-shadow-lg rounded-md hover:drop-shadow-none duration-300 ease-in">
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto border-collapse mb-4">
                    <thead>
                        <tr
                            class="bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 text-sm leading-normal">
                            <th class="py-3 px-6 text-left">No</th>
                            <th class="py-3 px-6 text-left">Nama Admin</th>
                            <th class="py-3 px-6 text-left">Email</th>
                            <th class="py-3 px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 dark:text-gray-300 text-sm font-light">
                        @forelse ($admins as $admin)
                            <tr
                                class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-800">
                                <td class="py-3 px-6 text-left">{{ $loop->iteration }}</td>
                                <td class="py-3 px-6 text-left">{{ $admin->user_name }}</td>
                                <td class="py-3 px-6 text-left">{{ $admin->email }}</td>
                                <td class="py-3 px-6 text-center">
                                    <div class="flex gap-2 justify-center">
                                        <a href="#" @click="showModal = true; admin = {{ $admin->toJson() }}"
                                            class="relative group">
                                            <span
                                                class="material-symbols-outlined bg-blue-500 px-2 py-1 rounded-md text-white text-base font-extralight">visibility</span>
                                            <span
                                                class="absolute z-50 left-0 top-full mt-1 w-max px-2 py-1 rounded-md bg-gray-800 text-white text-xs hidden group-hover:block">Lihat
                                                Detail</span>
                                        </a>
                                        <a href="{{ route('admin.edit', $admin->id) }}" class="relative group">
                                            <span
                                                class="material-symbols-outlined bg-green-500 px-2 py-1 rounded-md text-white text-base font-extralight">edit_square</span>
                                            <span
                                                class="absolute z-50 left-0 top-full mt-1 w-max px-2 py-1 rounded-md bg-gray-800 text-white text-xs hidden group-hover:block">Edit
                                                Data</span>
                                        </a>
                                        <form id="delete-form-{{ $admin->id }}"
                                            action="{{ route('admin.destroy', $admin) }}" method="POST"
                                            class="relative group delete-form"
                                            data-admin-name="{{ $admin->admin_name }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" onclick="confirmDelete({{ $admin->id }})"
                                                class="material-symbols-outlined bg-red-500 px-2 py-1 rounded-md text-white text-base font-extralight delete-button">delete</button>
                                            <span
                                                class="absolute z-50 right-0 top-full mt-1 w-max px-2 py-1 rounded-md bg-gray-800 text-white text-xs hidden group-hover:block">Hapus
                                                Data</span>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-3 px-6 text-center text-gray-500 dark:text-gray-400">
                                    Tidak ada admin ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $admins->links() }}
            </div>
        </div>

        <div x-show="showModal" x-cloak
            class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 z-50 overflow-y-auto">
            <div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow-lg max-w-md w-full relative">
                <button @click="showModal = false"
                    class="absolute top-2 right-2 text-gray-500 hover:text-gray-800 dark:hover:text-white">&times;</button>
                <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">Detail admin</h2>
                <div class="mb-4 text-gray-800 dark:text-white">
                    <p><strong>Nama:</strong> <span x-text="admin.user_name"></span></p>
                    <p><strong>Email:</strong> <span x-text="admin.email"></span></p>
                    <p><strong>Nomor HP:</strong> <span x-text="admin.phone_number"></span></p>
                    <p><strong>Role:</strong> Admin</p>
                </div>
                <div class="mt-4 text-right">
                    <x-primary-button @click="showModal = false">Tutup</x-primary-button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmAdd(event) {
            event.preventDefault();
            Swal.fire({
                title: 'Tambah admin?',
                text: "Apakah Anda yakin ingin menambah admin ini?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Tambah!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Sedang Memproses...',
                        text: 'Tunggu sebentar',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    setTimeout(() => {
                        document.getElementById('adminForm').submit();
                    });
                }
            });
        }

        function confirmDelete(id) {
            Swal.fire({
                title: 'Hapus admin ini?',
                text: "Apakah Anda yakin ingin menghapus admin ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Sedang Memproses...',
                        text: 'Tunggu sebentar',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    setTimeout(() => {
                        document.getElementById(`delete-form-${id}`).submit();
                    });
                }
            });
        }
    </script>
</x-app-layout>
