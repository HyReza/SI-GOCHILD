<x-app-layout>
    <x-slot:title>Pilih Periode - {{ $student->student_name }}</x-slot:title>

    <div class="min-h-[80vh] flex flex-col items-center justify-center p-6">

        {{-- Tombol Kembali --}}
        <div class="w-full max-w-lg mb-6">
            <a href="{{ route('development-reports.index') }}"
                class="inline-flex items-center gap-2 text-gray-500 hover:text-pink-600 transition-colors font-medium">
                <span class="material-symbols-outlined text-xl">arrow_back</span>
                Kembali ke Senarai Pelajar
            </a>
        </div>

        {{-- Main Card --}}
        <div
            class="w-full max-w-lg bg-white dark:bg-gray-800 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden relative">

            {{-- Hiasan Background --}}
            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-pink-500 to-purple-600"></div>

            <div class="p-8">
                {{-- Header Icon & Info --}}
                <div class="text-center mb-8">
                    <div
                        class="w-20 h-20 bg-pink-50 dark:bg-pink-900/20 rounded-full flex items-center justify-center mx-auto mb-4 border-4 border-white dark:border-gray-700 shadow-sm">
                        <span
                            class="material-symbols-outlined text-4xl text-pink-600 dark:text-pink-400">date_range</span>
                    </div>

                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-1">
                        Pilih Periode Laporan
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Tentukan rentang masa untuk pengiraan kehadiran dan data grafik.
                    </p>

                    {{-- Student Badge --}}
                    <div
                        class="mt-6 inline-flex items-center gap-3 bg-gray-50 dark:bg-gray-700/50 px-4 py-2 rounded-full border border-gray-100 dark:border-gray-600">
                        <div
                            class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-100 to-pink-100 flex items-center justify-center text-purple-600 font-bold text-xs border border-white shadow-sm">
                            {{ substr($student->student_name, 0, 2) }}
                        </div>
                        <div class="text-left">
                            <p
                                class="text-xs font-bold text-gray-800 dark:text-gray-200 uppercase tracking-wide leading-none">
                                {{ $student->student_name }}
                            </p>
                            <p class="text-[10px] text-gray-500 dark:text-gray-400 leading-none mt-1">
                                NIPD: {{ $student->student_number ?? '-' }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Form Selection --}}
                <form action="{{ route('development-reports.create', $student->id) }}" method="GET">

                    <div class="space-y-5">

                        {{-- Row 1: Tahun & Semester --}}
                        <div class="grid grid-cols-2 gap-4">
                            {{-- Tahun Ajaran --}}
                            <div class="space-y-1.5">
                                <label
                                    class="text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider ml-1">
                                    Tahun Ajaran
                                </label>
                                <div class="relative">
                                    <span
                                        class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 material-symbols-outlined text-lg">school</span>
                                    <select name="academic_year"
                                        class="w-full pl-10 pr-4 py-2.5 rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all text-sm font-medium appearance-none">
                                        @php
                                            $currentY = date('Y');
                                            $currentM = date('n');
                                            // Jika bulan > 6 (Julai), tahun ajaran bermula tahun ini (2024/2025)
                                            // Jika bulan <= 6, tahun ajaran bermula tahun lepas (2023/2024)
                                            $startYear = $currentM > 6 ? $currentY : $currentY - 1;
                                        @endphp

                                        {{-- Paparkan pilihan: Tahun Lepas, Tahun Ini, Tahun Depan --}}
                                        @for ($i = -1; $i <= 1; $i++)
                                            @php $y = $startYear + $i; @endphp
                                            <option value="{{ $y }}/{{ $y + 1 }}"
                                                {{ $i === 0 ? 'selected' : '' }}>
                                                {{ $y }}/{{ $y + 1 }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>

                            {{-- Semester --}}
                            <div class="space-y-1.5">
                                <label
                                    class="text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider ml-1">
                                    Semester
                                </label>
                                <div class="relative">
                                    <span
                                        class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 material-symbols-outlined text-lg">filter_list</span>
                                    <select name="semester"
                                        class="w-full pl-10 pr-4 py-2.5 rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all text-sm font-medium appearance-none">
                                        {{-- Auto select semester berdasarkan bulan semasa --}}
                                        <option value="Ganjil" {{ date('n') > 6 ? 'selected' : '' }}>Semester 1
                                            (Ganjil)</option>
                                        <option value="Genap" {{ date('n') <= 6 ? 'selected' : '' }}>Semester 2
                                            (Genap)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Divider --}}
                        <div class="relative flex py-2 items-center">
                            <div class="flex-grow border-t border-gray-100 dark:border-gray-700"></div>
                            <span class="flex-shrink-0 mx-4 text-gray-300 text-xs uppercase font-bold">Rentang
                                Data</span>
                            <div class="flex-grow border-t border-gray-100 dark:border-gray-700"></div>
                        </div>

                        {{-- Row 2: Tarikh Mula & Akhir --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label
                                    class="text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider ml-1">
                                    Dari Tarikh
                                </label>
                                <input type="date" name="start_date" value="{{ date('Y-01-01') }}" required
                                    class="w-full px-4 py-2.5 rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all text-sm font-medium">
                            </div>
                            <div class="space-y-1.5">
                                <label
                                    class="text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider ml-1">
                                    Hingga Tarikh
                                </label>
                                <input type="date" name="end_date" value="{{ date('Y-m-d') }}" required
                                    class="w-full px-4 py-2.5 rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all text-sm font-medium">
                            </div>
                        </div>

                        {{-- Action Button --}}
                        <div class="pt-4">
                            <button type="submit"
                                class="group w-full flex items-center justify-center gap-2 py-3.5 px-4 bg-gradient-to-r from-pink-500 to-purple-600 text-white font-bold rounded-xl shadow-lg shadow-pink-500/30 hover:shadow-pink-500/50 hover:scale-[1.02] active:scale-[0.98] transition-all duration-200">
                                <span>Lanjut Buat Raport</span>
                                <span
                                    class="material-symbols-outlined text-xl transition-transform group-hover:translate-x-1">arrow_forward</span>
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
