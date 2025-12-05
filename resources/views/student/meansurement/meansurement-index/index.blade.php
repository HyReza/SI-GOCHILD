<x-app-layout>
    <x-slot:title>Riwayat Pertumbuhan</x-slot:title>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        {{-- HEADER SECTION --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 mb-8">
            <div class="space-y-1">
                <h1
                    class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight leading-tight">
                    Laporan Pertumbuhan
                </h1>
                <p class="text-sm sm:text-base text-gray-500 dark:text-gray-400">
                    Rekam jejak tumbuh kembang Ananda <span
                        class="font-bold text-indigo-600 dark:text-indigo-400">{{ $student->student_name }}</span>
                </p>
            </div>

            <a href="{{ route('student.measurement.chart') }}"
                class="group inline-flex items-center justify-center gap-2.5 px-6 py-3 bg-gray-900 dark:bg-indigo-600 text-white text-sm font-semibold rounded-2xl shadow-lg shadow-gray-200/50 dark:shadow-none hover:bg-gray-800 dark:hover:bg-indigo-700 hover:-translate-y-0.5 transition-all duration-300">
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-gray-300 group-hover:text-white transition-colors" viewBox="0 0 20 20"
                    fill="currentColor">
                    <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z" />
                    <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z" />
                </svg>
                <span>Lihat Grafik KMS</span>
            </a>
        </div>

        {{-- CONTENT CONTAINER --}}
        <div
            class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl shadow-gray-100/50 dark:shadow-none border border-gray-100 dark:border-gray-700 overflow-hidden">

            {{-- DESKTOP TABLE VIEW (RE-DESIGNED) --}}
            <div class="hidden lg:block overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="bg-gray-50/80 dark:bg-gray-700/30 border-b border-gray-200 dark:border-gray-700 text-[11px] uppercase tracking-wider text-gray-500 font-bold">
                            <th class="px-8 py-5">Tanggal Pemeriksaan</th>
                            <th class="px-6 py-5 text-center border-l border-gray-100 dark:border-gray-700">Usia</th>
                            <th class="px-6 py-5 text-center border-l border-gray-100 dark:border-gray-700">Berat</th>
                            <th class="px-6 py-5 text-center border-l border-gray-100 dark:border-gray-700">Tinggi</th>
                            <th class="px-6 py-5 text-center border-l border-gray-100 dark:border-gray-700">L. Kepala
                            </th>
                            <th class="px-6 py-5 text-center border-l border-gray-100 dark:border-gray-700">L. Lengan
                            </th>
                            <th class="px-6 py-5 border-l border-gray-100 dark:border-gray-700">Status Gizi</th>
                            <th class="px-6 py-5 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($measurements as $m)
                            <tr
                                class="group hover:bg-indigo-50/30 dark:hover:bg-gray-700/50 transition-colors duration-200">
                                {{-- Tanggal --}}
                                <td class="px-8 py-5">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="w-11 h-11 rounded-xl bg-white border border-gray-100 shadow-sm flex items-center justify-center text-indigo-600 dark:bg-gray-700 dark:border-gray-600 dark:text-indigo-400 shrink-0">
                                            <span class="material-symbols-outlined text-[22px]">calendar_month</span>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-gray-900 dark:text-white">
                                                {{ \Carbon\Carbon::parse($m->date_measurement)->locale('id')->isoFormat('D MMMM Y') }}
                                            </span>
                                            <span
                                                class="text-[11px] text-gray-400 font-medium mt-0.5 uppercase tracking-wide">
                                                Pukul {{ \Carbon\Carbon::parse($m->date_measurement)->format('H:i') }}
                                                WIB
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                {{-- Usia --}}
                                <td class="px-6 py-5 text-center border-l border-gray-50 dark:border-gray-700">
                                    @php
                                        $age = \Carbon\Carbon::parse($student->birth_date)->diff(
                                            \Carbon\Carbon::parse($m->date_measurement),
                                        );
                                    @endphp
                                    <span
                                        class="inline-flex items-center justify-center px-3 py-1.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-lg text-xs font-bold font-mono">
                                        {{ $age->y }}th {{ $age->m }}bln
                                    </span>
                                </td>

                                {{-- Metrics (Centered & Clean) --}}
                                <td class="px-6 py-5 text-center border-l border-gray-50 dark:border-gray-700">
                                    <div class="flex flex-col items-center">
                                        <span
                                            class="text-lg font-black text-gray-900 dark:text-white">{{ $m->weight }}</span>
                                        <span class="text-[10px] uppercase font-bold text-gray-400">kg</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-center border-l border-gray-50 dark:border-gray-700">
                                    <div class="flex flex-col items-center">
                                        <span
                                            class="text-lg font-black text-gray-900 dark:text-white">{{ $m->height }}</span>
                                        <span class="text-[10px] uppercase font-bold text-gray-400">cm</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-center border-l border-gray-50 dark:border-gray-700">
                                    <div class="flex flex-col items-center">
                                        <span
                                            class="text-lg font-black text-gray-900 dark:text-white">{{ $m->head_circumference }}</span>
                                        <span class="text-[10px] uppercase font-bold text-gray-400">cm</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-center border-l border-gray-50 dark:border-gray-700">
                                    <div class="flex flex-col items-center">
                                        <span
                                            class="text-lg font-black text-gray-900 dark:text-white">{{ $m->arm_circumference }}</span>
                                        <span class="text-[10px] uppercase font-bold text-gray-400">cm</span>
                                    </div>
                                </td>

                                {{-- Status --}}
                                <td class="px-6 py-5 border-l border-gray-50 dark:border-gray-700">
                                    @php
                                        $isHealthy = strtolower($m->measurement_condition) == 'sehat';
                                    @endphp
                                    <span
                                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-bold border capitalize w-full justify-center
                                        {{ $isHealthy
                                            ? 'bg-emerald-50 text-emerald-700 border-emerald-100 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-800'
                                            : 'bg-amber-50 text-amber-700 border-amber-100 dark:bg-amber-900/20 dark:text-amber-400 dark:border-amber-800' }}">
                                        <span
                                            class="w-1.5 h-1.5 rounded-full {{ $isHealthy ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                                        {{ $m->measurement_condition ?? '-' }}
                                    </span>
                                </td>

                                {{-- Aksi --}}
                                <td class="px-6 py-5 text-center">
                                    <a href="{{ route('student.measurement.show', $m->id) }}"
                                        class="group/btn relative inline-flex items-center justify-center w-10 h-10 rounded-xl bg-white border border-gray-200 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 hover:border-indigo-200 transition-all duration-300 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 shadow-sm"
                                        title="Lihat Detail">
                                        <span
                                            class="material-symbols-outlined text-[20px] transition-transform group-hover/btn:scale-110">visibility</span>
                                        <span
                                            class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-[10px] py-1 px-2 rounded opacity-0 group-hover/btn:opacity-100 transition-opacity pointer-events-none whitespace-nowrap">Lihat
                                            Detail</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div
                                            class="w-20 h-20 bg-gray-50 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4 ring-8 ring-gray-50/50 dark:ring-gray-700/50">
                                            <span
                                                class="material-symbols-outlined text-4xl text-gray-300">folder_off</span>
                                        </div>
                                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Belum Ada Data
                                        </h3>
                                        <p class="text-sm text-gray-500 mt-1 max-w-xs mx-auto">Data pengukuran akan
                                            muncul di sini setelah dilakukan pemeriksaan oleh petugas.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- MOBILE CARD VIEW (Sama seperti sebelumnya, sudah rapi) --}}
            <div class="lg:hidden p-4 space-y-4 bg-gray-50 dark:bg-gray-900/50">
                @forelse($measurements as $m)
                    <div
                        class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm">

                        {{-- Header Card --}}
                        <div
                            class="flex justify-between items-start mb-4 pb-4 border-b border-gray-100 dark:border-gray-700">
                            <div>
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white">
                                    {{ \Carbon\Carbon::parse($m->date_measurement)->locale('id')->isoFormat('D MMMM Y') }}
                                </h3>
                                @php
                                    $age = \Carbon\Carbon::parse($student->birth_date)->diff(
                                        \Carbon\Carbon::parse($m->date_measurement),
                                    );
                                @endphp
                                <p class="text-xs text-gray-500 mt-0.5">Usia: {{ $age->y }}th
                                    {{ $age->m }}bln</p>
                            </div>

                            {{-- Status Badge --}}
                            @php
                                $isHealthy = strtolower($m->measurement_condition) == 'sehat';
                            @endphp
                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wide border
                                {{ $isHealthy
                                    ? 'bg-emerald-50 text-emerald-700 border-emerald-100 dark:bg-emerald-900/20 dark:text-emerald-400'
                                    : 'bg-amber-50 text-amber-700 border-amber-100 dark:bg-amber-900/20 dark:text-amber-400' }}">
                                {{ $m->measurement_condition ?? '-' }}
                            </span>
                        </div>

                        {{-- Data Grid (Warna Pastel) --}}
                        <div class="grid grid-cols-2 gap-3 mb-4">
                            {{-- Berat (Biru) --}}
                            <div
                                class="p-3 bg-blue-50 dark:bg-blue-900/10 rounded-xl text-center border border-blue-100 dark:border-blue-800/30">
                                <span
                                    class="block text-[10px] font-bold uppercase text-blue-600 dark:text-blue-400 mb-1">Berat</span>
                                <div class="text-lg font-bold text-gray-900 dark:text-white">
                                    {{ $m->weight }} <span class="text-xs font-medium text-gray-500">kg</span>
                                </div>
                            </div>

                            {{-- Tinggi (Ungu) --}}
                            <div
                                class="p-3 bg-purple-50 dark:bg-purple-900/10 rounded-xl text-center border border-purple-100 dark:border-purple-800/30">
                                <span
                                    class="block text-[10px] font-bold uppercase text-purple-600 dark:text-purple-400 mb-1">Tinggi</span>
                                <div class="text-lg font-bold text-gray-900 dark:text-white">
                                    {{ $m->height }} <span class="text-xs font-medium text-gray-500">cm</span>
                                </div>
                            </div>

                            {{-- L. Kepala (Pink) --}}
                            <div
                                class="p-3 bg-pink-50 dark:bg-pink-900/10 rounded-xl text-center border border-pink-100 dark:border-pink-800/30">
                                <span
                                    class="block text-[10px] font-bold uppercase text-pink-600 dark:text-pink-400 mb-1">L.
                                    Kepala</span>
                                <div class="text-lg font-bold text-gray-900 dark:text-white">
                                    {{ $m->head_circumference }} <span
                                        class="text-xs font-medium text-gray-500">cm</span>
                                </div>
                            </div>

                            {{-- L. Lengan (Teal) --}}
                            <div
                                class="p-3 bg-teal-50 dark:bg-teal-900/10 rounded-xl text-center border border-teal-100 dark:border-teal-800/30">
                                <span
                                    class="block text-[10px] font-bold uppercase text-teal-600 dark:text-teal-400 mb-1">L.
                                    Lengan</span>
                                <div class="text-lg font-bold text-gray-900 dark:text-white">
                                    {{ $m->arm_circumference }} <span
                                        class="text-xs font-medium text-gray-500">cm</span>
                                </div>
                            </div>
                        </div>

                        {{-- Action Button (Outline + Eye Icon) --}}
                        <a href="{{ route('student.measurement.show', $m->id) }}"
                            class="flex items-center justify-center w-full py-2.5 text-sm font-semibold text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-indigo-600 hover:border-indigo-300 transition-all duration-200 gap-2 shadow-sm">
                            <span class="material-symbols-outlined text-[18px]">visibility</span>
                            Lihat Detail Lengkap
                        </a>
                    </div>
                @empty
                    <div class="text-center py-10 px-4">
                        <div
                            class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-700 mb-3">
                            <span class="material-symbols-outlined text-gray-400">folder_off</span>
                        </div>
                        <p class="text-sm text-gray-500">Belum ada data pengukuran.</p>
                    </div>
                @endforelse
            </div>

            {{-- PAGINATION --}}
            @if ($measurements->hasPages())
                <div class="border-t border-gray-200 dark:border-gray-700 px-6 py-4 bg-white dark:bg-gray-800">
                    {{ $measurements->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Pastikan Google Fonts Material Symbols dimuat di layout utama (app.blade.php) --}}
    {{-- <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" /> --}}
</x-app-layout>
