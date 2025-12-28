<x-app-layout>
    <x-slot:title>Daftar Siswa - Raport Tumbuh Kembang</x-slot:title>

    <div class="p-6">

        {{-- HEADER SECTION --}}
        <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                    <span class="material-symbols-outlined text-pink-500 text-3xl">child_care</span>
                    Raport Tumbuh Kembang
                </h1>
                <p class="text-sm text-gray-500 mt-1">
                    Kelola data tumbuh kembang siswa, lihat riwayat, dan buat laporan baru.
                </p>
            </div>
        </div>

        {{-- SEARCH BAR --}}
        <div
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 mb-6">
            <form action="{{ route('development-reports.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                <div class="relative flex-grow">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <span class="material-symbols-outlined">search</span>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl border-gray-200 focus:border-pink-500 focus:ring-pink-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 transition-all text-sm"
                        placeholder="Cari nama siswa, NIPD, atau nama panggilan...">
                </div>
                <button type="submit"
                    class="bg-gradient-to-r from-pink-500 to-pink-600 text-white px-6 py-2.5 rounded-xl hover:shadow-lg hover:shadow-pink-500/30 transition-all font-medium flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[20px]">search</span>
                    Cari
                </button>
            </form>
        </div>

        {{-- TABLE SISWA --}}
        <div
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead
                        class="bg-gray-50 dark:bg-gray-700/50 text-gray-500 uppercase text-[11px] font-bold border-b border-gray-100 dark:border-gray-700 tracking-wider">
                        <tr>
                            <th class="px-6 py-4 w-12 text-center">No</th>
                            <th class="px-6 py-4">Data Siswa</th>
                            <th class="px-6 py-4">Info Akademik</th>
                            <th class="px-6 py-4">Usia</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($students as $student)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">

                                {{-- NO --}}
                                <td class="px-6 py-4 text-center font-medium text-gray-500 text-sm">
                                    {{ $loop->iteration + $students->firstItem() - 1 }}
                                </td>

                                {{-- DATA SISWA --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-start gap-4">
                                        {{-- Avatar / Foto Profil --}}
                                        @if ($student->user_photo)
                                            <img src="{{ asset('storage/' . $student->user_photo) }}"
                                                alt="{{ $student->student_name }}"
                                                class="w-12 h-12 flex-shrink-0 rounded-full object-cover border-2 border-white shadow-sm group-hover:scale-105 transition-transform">
                                        @else
                                            <div
                                                class="w-12 h-12 flex-shrink-0 rounded-full bg-gradient-to-br from-pink-100 to-purple-100 flex items-center justify-center text-pink-600 font-bold uppercase text-lg border-2 border-white shadow-sm group-hover:scale-105 transition-transform">
                                                {{ substr($student->student_name, 0, 2) }}
                                            </div>
                                        @endif

                                        <div class="flex flex-col gap-1">
                                            {{-- Nama Lengkap --}}
                                            <span
                                                class="text-sm font-bold text-gray-800 dark:text-gray-100 leading-tight">
                                                {{ $student->student_name }}
                                            </span>

                                            {{-- NIPD & Panggilan --}}
                                            <div class="flex items-center gap-2 text-xs text-gray-500 font-mono">
                                                <span>NIPD: {{ $student->student_number ?? '-' }}</span>
                                                <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                                                <span class="text-gray-600 dark:text-gray-400 font-sans italic">
                                                    {{ $student->nickname ? $student->nickname : '(Tidak ada panggilan)' }}
                                                </span>
                                            </div>

                                            {{-- Gender Badge --}}
                                            <div class="mt-1">
                                                @if ($student->gender == 1 || $student->gender == 'male' || $student->gender == 'L')
                                                    <span
                                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-blue-50 text-blue-600 text-[10px] font-bold border border-blue-100 uppercase tracking-wide">
                                                        <span class="material-symbols-outlined text-[12px]">male</span>
                                                        Laki-laki
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-pink-50 text-pink-600 text-[10px] font-bold border border-pink-100 uppercase tracking-wide">
                                                        <span
                                                            class="material-symbols-outlined text-[12px]">female</span>
                                                        Perempuan
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- INFO AKADEMIK --}}
                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-1.5">
                                        {{-- Layanan --}}
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="w-6 h-6 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center">
                                                <span class="material-symbols-outlined text-[14px]">school</span>
                                            </span>
                                            <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                                                {{ $student->activityTransaction?->service?->service_name ?? 'Belum ada Layanan' }}
                                            </span>
                                        </div>

                                        {{-- Program --}}
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="w-6 h-6 rounded-full bg-orange-50 text-orange-600 flex items-center justify-center">
                                                <span class="material-symbols-outlined text-[14px]">category</span>
                                            </span>
                                            <span class="text-xs font-medium text-gray-600 dark:text-gray-400">
                                                {{ $student->activityTransaction?->program?->program_name ?? 'Belum ada Program' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                {{-- USIA --}}
                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-1">
                                        @if ($student->birth_date)
                                            @php
                                                $dob = \Carbon\Carbon::parse($student->birth_date);
                                                $now = now();
                                                // Menggunakan casting (int) untuk menghilangkan desimal
                                                $years = (int) $dob->diffInYears($now);
                                                $months = (int) $dob->diffInMonths($now) % 12;
                                            @endphp
                                            <span class="text-sm font-bold text-gray-800 dark:text-gray-200">
                                                {{ $years }} Thn {{ $months }} Bln
                                            </span>
                                            <span class="text-xs text-gray-500 flex items-center gap-1">
                                                <span class="material-symbols-outlined text-[12px]">cake</span>
                                                {{ $dob->translatedFormat('d M Y') }}
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-400 italic">Tanggal lahir belum diisi</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- AKSI --}}
                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-2 items-center">
                                        {{-- Tombol Buat Raport --}}
                                        <a href="{{ route('development-reports.select-period', $student->id) }}"
                                            class="w-full flex items-center justify-center gap-2 px-3 py-1.5 rounded-lg bg-pink-600 text-white text-xs font-bold hover:bg-pink-700 hover:shadow-md transition-all">
                                            <span class="material-symbols-outlined text-[16px]">add_circle</span>
                                            Buat Raport
                                        </a>

                                        {{-- Tombol Riwayat --}}
                                        <a href="{{ route('development-reports.history', $student->id) }}"
                                            class="w-full flex items-center justify-center gap-2 px-3 py-1.5 rounded-lg bg-white border border-gray-200 text-gray-600 text-xs font-semibold hover:bg-gray-50 hover:text-pink-600 hover:border-pink-200 transition-all">
                                            <span class="material-symbols-outlined text-[16px]">history_edu</span>
                                            Riwayat
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div
                                            class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-3">
                                            <span
                                                class="material-symbols-outlined text-gray-300 text-4xl">person_off</span>
                                        </div>
                                        <h3 class="text-md font-bold text-gray-900 dark:text-white">Tidak ada data siswa
                                        </h3>
                                        <p class="text-gray-500 text-xs mt-1">Coba ubah kata kunci pencarian Anda.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($students->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50/30">
                    {{ $students->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
