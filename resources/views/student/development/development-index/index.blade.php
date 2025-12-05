<x-app-layout>
    <x-slot:title>Laporan Perkembangan</x-slot:title>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        {{-- HEADER SECTION --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 mb-8">
            <div class="space-y-1">
                <h1
                    class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight leading-tight">
                    Laporan Perkembangan
                </h1>
                <p class="text-sm sm:text-base text-gray-500 dark:text-gray-400">
                    Evaluasi tumbuh kembang (MMDST) Ananda <span
                        class="font-bold text-indigo-600 dark:text-indigo-400">{{ $student->student_name }}</span>
                </p>
            </div>
        </div>

        {{-- CONTENT CONTAINER --}}
        <div
            class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">

            {{-- DESKTOP TABLE VIEW --}}
            <div class="hidden lg:block overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="bg-gray-50/80 dark:bg-gray-700/30 border-b border-gray-100 dark:border-gray-700 text-xs uppercase tracking-wider text-gray-500 font-bold">
                            <th class="px-8 py-5">Tanggal Tes</th>
                            <th class="px-6 py-5 text-center">Usia Saat Tes</th>
                            <th class="px-6 py-5 text-center">Hasil Akhir</th>
                            <th class="px-6 py-5">Pemeriksa</th>
                            <th class="px-6 py-5 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                        @forelse($reports as $report)
                            <tr
                                class="group hover:bg-indigo-50/40 dark:hover:bg-gray-700/50 transition-colors duration-200">
                                {{-- Tanggal --}}
                                <td class="px-8 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 rounded-full bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400 shrink-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-900 dark:text-white">
                                                {{ \Carbon\Carbon::parse($report->assessment_date)->locale('id')->isoFormat('D MMMM Y') }}
                                            </p>
                                            <p class="text-xs text-gray-400 font-mono mt-0.5">
                                                {{ \Carbon\Carbon::parse($report->assessment_date)->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Usia --}}
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="inline-flex items-center px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-lg text-xs font-semibold font-mono border border-gray-200 dark:border-gray-600">
                                        {{ $report->age_in_days }} Hari
                                    </span>
                                </td>

                                {{-- Hasil Akhir --}}
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $res = $report->overall_result;
                                        $badgeClass = match ($res) {
                                            'NORMAL' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'SUSPECT',
                                            'QUESTIONABLE'
                                                => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                                            'ABNORMAL', 'UNTESTABLE' => 'bg-red-50 text-red-700 border-red-200',
                                            default => 'bg-gray-50 text-gray-600 border-gray-200',
                                        };
                                    @endphp
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border uppercase {{ $badgeClass }}">
                                        {{ $res ?? 'Belum Ada' }}
                                    </span>
                                </td>

                                {{-- Pemeriksa --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-[10px] font-bold text-gray-600">
                                            {{ substr($report->creator->user_name ?? 'G', 0, 1) }}
                                        </div>
                                        <span
                                            class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $report->creator->user_name ?? 'Guru' }}</span>
                                    </div>
                                </td>

                                {{-- Aksi --}}
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('student.development.show', $report->id) }}"
                                        class="group/btn relative inline-flex items-center justify-center w-10 h-10 rounded-xl bg-white border border-gray-200 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 hover:border-indigo-200 transition-all duration-300 shadow-sm"
                                        title="Lihat Detail">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5 transition-transform group-hover/btn:scale-110"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                            <path fill-rule="evenodd"
                                                d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div
                                            class="w-20 h-20 bg-gray-50 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4 ring-8 ring-gray-50/50 dark:ring-gray-700/50">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                        </div>
                                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Belum Ada Laporan
                                        </h3>
                                        <p class="text-sm text-gray-500 mt-1 max-w-xs mx-auto">Data perkembangan MMDST
                                            akan muncul setelah dilakukan tes.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- MOBILE CARD VIEW --}}
            <div class="lg:hidden p-4 space-y-4 bg-gray-50 dark:bg-gray-900/30">
                @forelse($reports as $report)
                    <div
                        class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm relative overflow-hidden transition hover:shadow-md">

                        {{-- Top Section --}}
                        <div
                            class="flex justify-between items-start mb-4 pb-4 border-b border-gray-100 dark:border-gray-700">
                            <div>
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white">
                                    {{ \Carbon\Carbon::parse($report->assessment_date)->locale('id')->isoFormat('D MMMM Y') }}
                                </h3>
                                <p class="text-xs text-gray-500 mt-0.5">Usia: {{ $report->age_in_days }} Hari</p>
                            </div>

                            {{-- Result Badge --}}
                            @php
                                $res = $report->overall_result;
                                $badgeClass = match ($res) {
                                    'NORMAL' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                    'SUSPECT', 'QUESTIONABLE' => 'bg-yellow-50 text-yellow-700 border-yellow-100',
                                    'ABNORMAL', 'UNTESTABLE' => 'bg-red-50 text-red-700 border-red-100',
                                    default => 'bg-gray-50 text-gray-600 border-gray-100',
                                };
                            @endphp
                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wide border {{ $badgeClass }}">
                                {{ $res ?? '—' }}
                            </span>
                        </div>

                        {{-- Info Grid --}}
                        <div class="flex items-center justify-between mb-4 text-xs text-gray-500">
                            <div class="flex items-center gap-2">
                                <span
                                    class="w-6 h-6 rounded-full bg-gray-100 flex items-center justify-center font-bold text-gray-600 border border-gray-200">
                                    {{ substr($report->creator->user_name ?? 'G', 0, 1) }}
                                </span>
                                <span>{{ $report->creator->user_name ?? 'Guru' }}</span>
                            </div>
                            <span>{{ \Carbon\Carbon::parse($report->assessment_date)->diffForHumans() }}</span>
                        </div>

                        {{-- Action Button --}}
                        <a href="{{ route('student.development.show', $report->id) }}"
                            class="flex items-center justify-center w-full py-2.5 text-sm font-semibold text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-indigo-600 hover:border-indigo-300 transition-all duration-200 gap-2 shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                <path fill-rule="evenodd"
                                    d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                    clip-rule="evenodd" />
                            </svg>
                            Lihat Detail
                        </a>
                    </div>
                @empty
                    <div class="text-center py-10 px-4">
                        <div
                            class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-700 mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <p class="text-sm text-gray-500">Belum ada data laporan.</p>
                    </div>
                @endforelse
            </div>

            {{-- PAGINATION --}}
            @if ($reports->hasPages())
                <div class="border-t border-gray-200 dark:border-gray-700 px-6 py-4 bg-gray-50 dark:bg-gray-800">
                    {{ $reports->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
