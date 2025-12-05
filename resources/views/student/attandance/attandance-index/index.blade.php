<x-app-layout>
    <div class="space-y-6">

        {{-- 1. HEADER & FILTER --}}
        <div
            class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div>
                <h2 class="text-lg font-bold text-gray-800 dark:text-white">Riwayat Absensi</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">Memantau kehadiran dan catatan kedisiplinan</p>
            </div>

            {{-- Filter Form --}}
            <form action="{{ route('student.attendance.index') }}" method="GET" class="flex gap-2 w-full md:w-auto">
                <select name="month" onchange="this.form.submit()"
                    class="w-full md:w-32 text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 rounded-xl focus:ring-pink-500 focus:border-pink-500 cursor-pointer">
                    @foreach (range(1, 12) as $m)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>
                <select name="year" onchange="this.form.submit()"
                    class="w-24 text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 rounded-xl focus:ring-pink-500 focus:border-pink-500 cursor-pointer">
                    @foreach (range(date('Y'), 2023) as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        {{-- 2. SUMMARY CARDS (Statistik Bulanan) --}}
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
            {{-- Card Hadir --}}
            <div
                class="bg-emerald-50 dark:bg-emerald-900/20 p-4 rounded-2xl border border-emerald-100 dark:border-emerald-800">
                <div class="flex items-center gap-2 mb-1">
                    <span class="material-symbols-outlined text-emerald-600 text-lg">check_circle</span>
                    <span class="text-xs font-semibold text-emerald-700 dark:text-emerald-400 uppercase">Hadir</span>
                </div>
                <span class="text-2xl font-bold text-emerald-700 dark:text-emerald-300">{{ $summary['present'] }}</span>
            </div>

            {{-- Card Sakit --}}
            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-2xl border border-blue-100 dark:border-blue-800">
                <div class="flex items-center gap-2 mb-1">
                    <span class="material-symbols-outlined text-blue-600 text-lg">sick</span>
                    <span class="text-xs font-semibold text-blue-700 dark:text-blue-400 uppercase">Sakit</span>
                </div>
                <span class="text-2xl font-bold text-blue-700 dark:text-blue-300">{{ $summary['sick'] }}</span>
            </div>

            {{-- Card Izin --}}
            <div class="bg-amber-50 dark:bg-amber-900/20 p-4 rounded-2xl border border-amber-100 dark:border-amber-800">
                <div class="flex items-center gap-2 mb-1">
                    <span class="material-symbols-outlined text-amber-600 text-lg">assignment</span>
                    <span class="text-xs font-semibold text-amber-700 dark:text-amber-400 uppercase">Izin</span>
                </div>
                <span class="text-2xl font-bold text-amber-700 dark:text-amber-300">{{ $summary['excused'] }}</span>
            </div>

            {{-- Card Alpha --}}
            <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-2xl border border-red-100 dark:border-red-800">
                <div class="flex items-center gap-2 mb-1">
                    <span class="material-symbols-outlined text-red-600 text-lg">cancel</span>
                    <span class="text-xs font-semibold text-red-700 dark:text-red-400 uppercase">Alpha</span>
                </div>
                <span class="text-2xl font-bold text-red-700 dark:text-red-300">{{ $summary['absent'] }}</span>
            </div>

            {{-- Card Terlambat --}}
            <div
                class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-2xl border border-purple-100 dark:border-purple-800">
                <div class="flex items-center gap-2 mb-1">
                    <span class="material-symbols-outlined text-purple-600 text-lg">timer</span>
                    <span class="text-xs font-semibold text-purple-700 dark:text-purple-400 uppercase">Telat</span>
                </div>
                <span class="text-2xl font-bold text-purple-700 dark:text-purple-300">{{ $summary['late'] }}</span>
            </div>

            {{-- Card Denda --}}
            <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-2xl border border-gray-200 dark:border-gray-600">
                <div class="flex items-center gap-2 mb-1">
                    <span class="material-symbols-outlined text-gray-600 text-lg">payments</span>
                    <span class="text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Denda</span>
                </div>
                <span class="text-lg font-bold text-gray-800 dark:text-white">Rp
                    {{ number_format($summary['total_fine'], 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- 3. DATA LIST (Responsive: Card di Mobile, Tabel di Desktop) --}}
        <div
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">

            {{-- A. TAMPILAN MOBILE (Card Stack) - Visible only on mobile (< md) --}}
            <div class="block md:hidden">
                @forelse($attendances as $item)
                    @php
                        // Logic Warna Status
                        $statusClass = match ($item->check_in_status) {
                            'Present' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                            'Sick' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                            'Excused' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                            'Absent' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                            default => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
                        };
                        $statusLabel = match ($item->check_in_status) {
                            'Present' => 'Hadir',
                            'Sick' => 'Sakit',
                            'Excused' => 'Izin',
                            'Absent' => 'Alpha',
                            default => '-',
                        };
                    @endphp

                    <div class="p-4 border-b border-gray-100 dark:border-gray-700 last:border-0">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <p class="text-sm font-bold text-gray-800 dark:text-white">
                                    {{ \Carbon\Carbon::parse($item->attendanceTransaction->date_attendance)->locale('id')->translatedFormat('l, d F Y') }}
                                </p>
                                @if ($item->note)
                                    <p class="text-[10px] text-gray-500 italic mt-1">
                                        "{{ Str::limit($item->note, 30) }}"</p>
                                @endif
                            </div>
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold {{ $statusClass }}">
                                {{ $statusLabel }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div class="bg-gray-50 dark:bg-gray-700/50 p-2 rounded-lg text-center">
                                <span class="block text-[10px] text-gray-400 uppercase">Datang</span>
                                <span class="font-mono font-semibold text-gray-700 dark:text-gray-200">
                                    {{ $item->check_in_time ? \Carbon\Carbon::parse($item->check_in_time)->format('H:i') : '--:--' }}
                                </span>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700/50 p-2 rounded-lg text-center">
                                <span class="block text-[10px] text-gray-400 uppercase">Pulang</span>
                                <span class="font-mono font-semibold text-gray-700 dark:text-gray-200">
                                    {{ $item->check_out_time ? \Carbon\Carbon::parse($item->check_out_time)->format('H:i') : '--:--' }}
                                </span>
                            </div>
                        </div>

                        {{-- Info Keterlambatan / Denda Mobile --}}
                        @if ($item->check_out_status == 'late' || ($item->absenceFine && $item->absenceFine->amount > 0))
                            <div
                                class="mt-3 flex items-center justify-between text-xs bg-red-50 dark:bg-red-900/10 p-2 rounded-lg border border-red-100 dark:border-red-900/30">
                                <div class="flex items-center gap-1 text-red-600 dark:text-red-400">
                                    <span class="material-symbols-outlined text-sm">warning</span>
                                    <span>Telat {{ $item->late_duration }} menit</span>
                                </div>
                                @if ($item->absenceFine)
                                    <span class="font-bold text-red-700 dark:text-red-300">
                                        - Rp {{ number_format($item->absenceFine->amount, 0, ',', '.') }}
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="p-8 text-center">
                        <img src="{{ asset('icons/empty.svg') }}" class="h-24 w-auto mx-auto mb-3 opacity-50"
                            alt="Kosong">
                        <p class="text-sm text-gray-500">Belum ada data absensi bulan ini.</p>
                    </div>
                @endforelse
            </div>

            {{-- B. TAMPILAN DESKTOP (Table) - Visible only on desktop (>= md) --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead
                        class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300 border-b border-gray-100 dark:border-gray-600">
                        <tr>
                            <th class="px-6 py-4 font-medium">Tanggal</th>
                            <th class="px-6 py-4 font-medium">Datang</th>
                            <th class="px-6 py-4 font-medium">Pulang</th>
                            <th class="px-6 py-4 font-medium">Status</th>
                            <th class="px-6 py-4 font-medium">Keterangan</th>
                            <th class="px-6 py-4 font-medium text-right">Denda</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($attendances as $item)
                            @php
                                // Logic Warna Status Desktop
                                $badgeClass = match ($item->check_in_status) {
                                    'Present' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                    'Sick' => 'bg-blue-100 text-blue-700 border-blue-200',
                                    'Excused' => 'bg-amber-100 text-amber-700 border-amber-200',
                                    'Absent' => 'bg-red-100 text-red-700 border-red-200',
                                    default => 'bg-gray-100 text-gray-600 border-gray-200',
                                };
                                $badgeLabel = match ($item->check_in_status) {
                                    'Present' => 'Hadir',
                                    'Sick' => 'Sakit',
                                    'Excused' => 'Izin',
                                    'Absent' => 'Alpha',
                                    default => '-',
                                };
                            @endphp
                            <tr
                                class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                    {{ \Carbon\Carbon::parse($item->attendanceTransaction->date_attendance)->locale('id')->translatedFormat('d F Y') }}
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-300 font-mono">
                                    {{ $item->check_in_time ? \Carbon\Carbon::parse($item->check_in_time)->format('H:i') : '-' }}
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-300 font-mono">
                                    {{ $item->check_out_time ? \Carbon\Carbon::parse($item->check_out_time)->format('H:i') : '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $badgeClass }}">
                                        {{ $badgeLabel }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if ($item->check_out_status == 'late')
                                        <div class="flex items-center gap-1 text-red-500 text-xs font-semibold mb-1">
                                            <span class="material-symbols-outlined text-[16px]">schedule</span>
                                            Telat {{ $item->late_duration }} mnt
                                        </div>
                                    @endif
                                    <span class="text-gray-500 text-xs">{{ $item->note ?? '-' }}</span>
                                </td>
                                <td
                                    class="px-6 py-4 text-right font-medium {{ $item->absenceFine ? 'text-red-600' : 'text-gray-400' }}">
                                    {{ $item->absenceFine ? 'Rp ' . number_format($item->absenceFine->amount, 0, ',', '.') : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                                    Tidak ada data absensi pada bulan ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($attendances->hasPages())
                <div class="p-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                    {{ $attendances->links() }}
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
