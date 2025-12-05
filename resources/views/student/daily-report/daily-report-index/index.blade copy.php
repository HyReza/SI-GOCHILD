<x-app-layout>
    <div class="space-y-6">

        {{-- Header & Filter --}}
        <div
            class="flex flex-col md:flex-row justify-between items-center gap-4 bg-white dark:bg-gray-900 p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800">
            <div>
                <h2 class="text-lg font-bold text-gray-800 dark:text-white">Riwayat Laporan Harian</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">Memantau aktivitas harian ananda di sekolah</p>
            </div>

            <form action="{{ route('student.daily-report.index') }}" method="GET" class="flex gap-2 w-full md:w-auto">
                <input type="date" name="date" value="{{ request('date') }}" onchange="this.form.submit()"
                    class="w-full md:w-48 text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 rounded-xl focus:ring-pink-500 focus:border-pink-500 cursor-pointer">

                @if (request('date'))
                    <a href="{{ route('student.daily-report.index') }}"
                        class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-xl hover:bg-gray-200 transition">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        {{-- LIST LAPORAN --}}
        <div
            class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden">

            {{-- Mobile View (Card) --}}
            <div class="block md:hidden divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($reports as $report)
                    <a href="{{ route('student.daily-report.show', $report->id) }}"
                        class="block p-4 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <span class="text-xs font-bold text-pink-500 mb-1 block">
                                    {{ \Carbon\Carbon::parse($report->period)->locale('id')->isoFormat('dddd, D MMMM Y') }}
                                </span>
                                <span class="text-sm font-semibold text-gray-800 dark:text-white">
                                    {{ $report->service->service_name ?? '-' }}
                                </span>
                            </div>
                            @if ($report->parent_guardian_signature)
                                <span
                                    class="px-2 py-1 bg-green-100 text-green-700 text-[10px] font-bold rounded-md">Sudah
                                    TTD</span>
                            @else
                                <span
                                    class="px-2 py-1 bg-amber-100 text-amber-700 text-[10px] font-bold rounded-md">Belum
                                    TTD</span>
                            @endif
                        </div>
                        <div class="flex gap-3 text-xs text-gray-500 dark:text-gray-400 mt-2">
                            <div class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">thermometer</span>
                                {{ $report->body_temperature ?? '-' }}°C
                            </div>
                            <div class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">medical_services</span>
                                {{ ucfirst($report->health_status) }}
                            </div>
                            <div class="flex items-center gap-1 ml-auto text-blue-500 font-medium">
                                Detail <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="p-8 text-center">
                        <img src="{{ asset('images/empty.svg') }}" class="h-20 w-auto mx-auto mb-3 opacity-50"
                            alt="Kosong">
                        <p class="text-sm text-gray-500">Belum ada laporan harian.</p>
                    </div>
                @endforelse
            </div>

            {{-- Desktop View (Table) --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead
                        class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
                        <tr>
                            <th class="px-6 py-4 font-medium">Tanggal</th>
                            <th class="px-6 py-4 font-medium">Layanan</th>
                            <th class="px-6 py-4 font-medium">Kondisi</th>
                            <th class="px-6 py-4 font-medium">Suhu</th>
                            <th class="px-6 py-4 font-medium text-center">Status TTD</th>
                            <th class="px-6 py-4 font-medium text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse($reports as $report)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                    {{ \Carbon\Carbon::parse($report->period)->locale('id')->isoFormat('d MMMM Y') }}
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-300">
                                    {{ $report->service->service_name ?? '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-3 py-1 rounded-full text-xs font-bold
                                        {{ $report->health_status == 'sehat' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ ucfirst($report->health_status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-300 font-mono">
                                    {{ $report->body_temperature ?? '-' }}°C
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if ($report->parent_guardian_signature)
                                        <span class="text-green-600 material-symbols-outlined"
                                            title="Sudah Ditandatangani">verified</span>
                                    @else
                                        <span class="text-gray-300 material-symbols-outlined"
                                            title="Belum Ditandatangani">edit_square</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('student.daily-report.show', $report->id) }}"
                                        class="inline-flex items-center gap-1 px-4 py-2 bg-pink-500 text-white text-xs font-bold rounded-lg hover:bg-pink-600 transition">
                                        Lihat
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                    Tidak ada data laporan untuk periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($reports->hasPages())
                <div class="p-4 border-t border-gray-100 dark:border-gray-700">
                    {{ $reports->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
