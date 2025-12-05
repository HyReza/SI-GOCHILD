<x-app-layout>

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

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight flex items-center gap-2">
                <span
                    class="flex items-center justify-center w-8 h-8 rounded-full bg-indigo-600 text-white text-sm font-bold shadow-sm">1</span>
                {{ __('Pilih Siswa') }}
            </h2>

            {{-- Breadcrumb --}}
            <div class="hidden sm:flex items-center text-sm font-medium text-gray-500 dark:text-gray-400">
                <span class="text-indigo-600 dark:text-indigo-400 font-bold">Siswa</span>
                <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span>Layanan</span>
                <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span>Checkout</span>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Search Bar --}}
            <div class="mb-6 flex justify-between items-center">
                <div class="w-full max-w-md">
                    <form action="{{ route('orders.select-student') }}" method="GET" class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400 group-focus-within:text-indigo-500 transition-colors"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg leading-5 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-all shadow-sm"
                            placeholder="Cari nama, nomor induk, atau orang tua...">
                    </form>
                </div>
            </div>

            {{-- Table Container --}}
            <div
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl border border-gray-200 dark:border-gray-700">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Profil Siswa
                                </th>
                                <th scope="col"
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden md:table-cell">
                                    Program & Layanan
                                </th>
                                <th scope="col"
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden lg:table-cell">
                                    Orang Tua / Kontak
                                </th>
                                <th scope="col"
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden sm:table-cell">
                                    Status
                                </th>
                                <th scope="col"
                                    class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($activityTransactions as $transaction)
                                @php
                                    $student = $transaction->student;
                                    $program = $transaction->program;
                                    $service = $transaction->service;
                                @endphp

                                @if ($student)
                                    <tr
                                        class="group hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                                        {{-- 1. Profil Siswa --}}
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-12 w-12 relative">
                                                    @if ($student->user_photo)
                                                        <img class="h-12 w-12 rounded-full object-cover border border-gray-200 dark:border-gray-600"
                                                            src="{{ asset('storage/' . $student->user_photo) }}"
                                                            alt="{{ $student->student_name }}">
                                                    @else
                                                        <div
                                                            class="h-12 w-12 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold text-lg border border-indigo-200 dark:border-indigo-800">
                                                            {{ substr($student->student_name, 0, 1) }}
                                                        </div>
                                                    @endif

                                                    {{-- Gender Indicator (Optional Dot) --}}
                                                    <span
                                                        class="absolute bottom-0 right-0 block h-3 w-3 rounded-full ring-2 ring-white dark:ring-gray-800 {{ $student->gender ? 'bg-blue-400' : 'bg-pink-400' }}"
                                                        title="{{ $student->gender ? 'Laki-laki' : 'Perempuan' }}"></span>
                                                </div>
                                                <div class="ml-4">
                                                    <div
                                                        class="text-sm font-bold text-gray-900 dark:text-white group-hover:text-indigo-600 transition-colors">
                                                        {{ $student->student_name }}
                                                    </div>
                                                    <div
                                                        class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                                        <span>{{ $student->student_number }}</span>
                                                        @if ($student->nickname)
                                                            <span class="text-gray-300">•</span>
                                                            <span>"{{ $student->nickname }}"</span>
                                                        @endif
                                                    </div>
                                                    {{-- Tampilkan Program di Mobile --}}
                                                    <div
                                                        class="md:hidden mt-1 text-xs text-indigo-600 dark:text-indigo-400 font-medium">
                                                        {{ $program->program_name ?? '-' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- 2. Program & Layanan (Hidden on Mobile) --}}
                                        <td class="px-6 py-4 whitespace-nowrap hidden md:table-cell">
                                            <div class="flex flex-col">
                                                <span class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $program->program_name ?? 'Tidak ada program' }}
                                                </span>
                                                <span class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                                    {{ $service->service_name ?? 'Tidak ada layanan' }}
                                                </span>
                                                <span class="text-[10px] text-gray-400 mt-1">
                                                    Mulai:
                                                    {{ \Carbon\Carbon::parse($transaction->start_date)->format('d M Y') }}
                                                </span>
                                            </div>
                                        </td>

                                        {{-- 3. Orang Tua & Kontak (Hidden on Tablet/Mobile) --}}
                                        <td class="px-6 py-4 whitespace-nowrap hidden lg:table-cell">
                                            <div class="text-sm text-gray-900 dark:text-white">
                                                {{ $student->father_name ?? ($student->mother_name ?? '-') }}
                                            </div>
                                            @if ($student->phone_number)
                                                <div
                                                    class="flex items-center text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                                    </svg>
                                                    {{ $student->phone_number }}
                                                </div>
                                            @else
                                                <span class="text-xs text-gray-400 italic">No. Telepon tidak ada</span>
                                            @endif
                                        </td>

                                        {{-- 4. Status (Hidden on Mobile) --}}
                                        <td class="px-6 py-4 whitespace-nowrap hidden sm:table-cell">
                                            <div class="flex flex-col gap-1.5 items-start">
                                                {{-- Status Aktif/Tidak --}}
                                                <span
                                                    class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full {{ $transaction->student_status ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' }}">
                                                    {{ $transaction->student_status ? 'Aktif' : 'Non-Aktif' }}
                                                </span>

                                                {{-- Status Normal/Berkebutuhan Khusus --}}
                                                @if (!$transaction->student_is_normal)
                                                    <div class="flex items-center text-xs text-yellow-600 dark:text-yellow-400 bg-yellow-50 dark:bg-yellow-900/20 px-2 py-0.5 rounded-full border border-yellow-200 dark:border-yellow-800"
                                                        title="{{ $student->student_description ?? 'Siswa Berkebutuhan Khusus' }}">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        Perlu Perhatian
                                                    </div>
                                                @endif
                                            </div>
                                        </td>

                                        {{-- 5. Aksi --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('orders.catalog', $student->id) }}"
                                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-indigo-600 bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-900/30 dark:text-indigo-300 dark:hover:bg-indigo-900/50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all shadow-sm group-hover:shadow-md">
                                                Pilih
                                                <svg class="ml-2 -mr-1 w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="5"
                                        class="px-6 py-12 text-center text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="bg-gray-100 dark:bg-gray-700 rounded-full p-3 mb-3">
                                                <svg class="w-8 h-8 text-gray-400" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                                </svg>
                                            </div>
                                            <span class="text-base font-medium">Siswa tidak ditemukan</span>
                                            <span class="text-sm text-gray-400 mt-1">Pastikan data siswa sudah
                                                terdaftar di aktivitas program.</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($activityTransactions->hasPages())
                    <div
                        class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
                        {{ $activityTransactions->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
