<x-app-layout>

    <div class="flex flex-col md:flex-row md:items-center justify-end px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-8 ">

        <form method="GET" action="{{ route('dashboard') }}"
            class="flex items-center gap-2 bg-white dark:bg-slate-900 p-1.5 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800">
            <select name="month"
                class="bg-transparent border-none text-xs font-bold focus:ring-0 cursor-pointer text-slate-600 dark:text-slate-300">
                @for ($m = 1; $m <= 12; $m++)
                    <option value="{{ sprintf('%02d', $m) }}" {{ $month == sprintf('%02d', $m) ? 'selected' : '' }}>
                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                    </option>
                @endfor
            </select>
            <select name="year"
                class="bg-transparent border-none text-xs font-bold focus:ring-0 cursor-pointer text-slate-600 dark:text-slate-300">
                @for ($y = date('Y'); $y >= 2020; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}
                    </option>
                @endfor
            </select>
            <button type="submit"
                class="p-2 bg-emerald-500 text-white rounded-xl hover:bg-emerald-600 transition-all shadow-md active:scale-90">
                <span class="material-symbols-outlined text-sm font-bold">filter_list</span>
            </button>
        </form>
    </div>


    <div class="py-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-8 font-sans leading-normal">

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div
                class="bg-white dark:bg-slate-900 p-6 rounded-[2rem] shadow-xl border border-transparent transition-all group">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-2xl flex items-center justify-center text-blue-600">
                        <span class="material-symbols-outlined">groups</span>
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Total Anak</span>
                </div>
                <p class="text-4xl font-black text-slate-900 dark:text-white tracking-tighter">
                    {{ number_format($stats['total_students']) }}</p>
            </div>

            <div
                class="bg-white dark:bg-slate-900 p-6 rounded-[2rem] shadow-xl border border-transparent transition-all group border-emerald-500/10">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 rounded-2xl flex items-center justify-center text-emerald-600">
                        <span class="material-symbols-outlined">neurology</span>
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Skrining</span>
                </div>
                <p class="text-4xl font-black text-emerald-600 tracking-tighter">{{ $stats['assessments_count'] }}</p>
            </div>

            <div
                class="bg-white dark:bg-slate-900 p-6 rounded-[2rem] shadow-xl border border-transparent transition-all group border-amber-500/10">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="w-12 h-12 bg-amber-100 dark:bg-amber-900/30 rounded-2xl flex items-center justify-center text-amber-600">
                        <span class="material-symbols-outlined">monitor_weight</span>
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Fisik</span>
                </div>
                <p class="text-4xl font-black text-amber-600 tracking-tighter">{{ $stats['measurements_count'] }}</p>
            </div>

            <div
                class="bg-white dark:bg-slate-900 p-6 rounded-[2rem] shadow-xl border border-transparent transition-all group">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/30 rounded-2xl flex items-center justify-center text-indigo-600">
                        <span class="material-symbols-outlined">badge</span>
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Staff</span>
                </div>
                <p class="text-4xl font-black text-slate-900 dark:text-white tracking-tighter">
                    {{ $stats['total_teachers'] }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div
                class="bg-white dark:bg-slate-900 p-8 rounded-[2.5rem] shadow-xl border border-slate-100 dark:border-slate-800">
                <h4 class="text-lg font-black text-slate-800 dark:text-white uppercase tracking-widest mb-6">Peta
                    Perkembangan Anak</h4>
                <div class="h-72"><canvas id="mmdstChart"></canvas></div>
            </div>

            <div
                class="bg-white dark:bg-slate-900 p-8 rounded-[2.5rem] shadow-xl border border-slate-100 dark:border-slate-800">
                <h4 class="text-lg font-black text-slate-800 dark:text-white uppercase tracking-widest mb-6">Status Gizi
                    (BB/U)</h4>
                <div class="h-72"><canvas id="growthChart"></canvas></div>
            </div>
        </div>

        <div
            class="bg-white dark:bg-slate-900 p-8 rounded-[2.5rem] shadow-xl border border-slate-100 dark:border-slate-800">
            <h4
                class="text-lg font-black text-slate-800 dark:text-white uppercase tracking-widest mb-10 text-center lg:text-left">
                Riwayat Pemeriksaan Terkini</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($recent_activities as $item)
                    @php
                        $isMmdst = isset($item->overall_result);
                        $student = $item->activityTransaction?->student ?? null;
                        $url = $isMmdst
                            ? route('mmdst-assessments.show', $item->id)
                            : route('measurement.show', $item->id);
                    @endphp

                    <div
                        class="flex flex-col p-6 rounded-[2rem] bg-slate-50 dark:bg-slate-800/50 hover:bg-white dark:hover:bg-slate-800 transition-all border border-transparent hover:border-slate-200 dark:hover:border-slate-700 shadow-sm group">
                        <div class="flex items-center gap-4 mb-4">
                            <div
                                class="w-12 h-12 rounded-2xl flex items-center justify-center {{ $isMmdst ? 'bg-emerald-100 text-emerald-600' : 'bg-amber-100 text-amber-600' }}">
                                <span
                                    class="material-symbols-outlined">{{ $isMmdst ? 'psychology' : 'straighten' }}</span>
                            </div>
                            <div class="min-w-0">
                                <p
                                    class="text-sm font-bold text-slate-800 dark:text-white truncate uppercase tracking-tight">
                                    {{ $student->student_name ?? 'Data Terhapus' }}
                                </p>
                                <p class="text-[9px] font-black uppercase tracking-widest text-slate-400">
                                    {{ $isMmdst ? 'MMDST' : 'Antropometri' }}
                                </p>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div
                                class="flex justify-between items-center bg-white dark:bg-slate-900 p-3 rounded-2xl border border-slate-100 dark:border-slate-800">
                                <span class="text-[10px] font-bold text-slate-400 uppercase">Status</span>
                                <span @class([
                                    'text-[10px] font-black uppercase',
                                    'text-emerald-500' => in_array(
                                        $item->overall_result ?? $item->measurement_condition,
                                        ['NORMAL', 'Normal']),
                                    'text-red-500' => in_array(
                                        $item->overall_result ?? $item->measurement_condition,
                                        ['ABNORMAL', 'Sangat Kurang']),
                                    'text-amber-500' => in_array(
                                        $item->overall_result ?? $item->measurement_condition,
                                        ['QUESTIONABLE', 'Kurang']),
                                    'text-slate-500' => ($item->overall_result ?? '') == 'UNTESTABLE',
                                ])>
                                    {{ $item->overall_result ?? ($item->measurement_condition ?? '-') }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center px-1">
                                <span class="text-[9px] font-medium text-slate-400 italic">
                                    {{ \Carbon\Carbon::parse($item->assessment_date ?? $item->date_measurement)->translatedFormat('d F Y') }}
                                </span>
                                <a href="{{ $url }}"
                                    class="text-[9px] font-black text-blue-500 uppercase tracking-[0.2em] hover:underline transition-all">Detail
                                    →</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div
                        class="col-span-full py-12 text-center opacity-30 italic font-black text-xs uppercase tracking-[0.4em]">
                        Belum ada aktivitas</div>
                @endforelse
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // MMDST Chart
        new Chart(document.getElementById('mmdstChart'), {
            type: 'bar',
            data: {
                labels: ['NORMAL', 'QUESTIONABLE', 'ABNORMAL', 'UNTESTABLE'],
                datasets: [{
                    data: [{{ $mmdst_summary['NORMAL'] }}, {{ $mmdst_summary['QUESTIONABLE'] }},
                        {{ $mmdst_summary['ABNORMAL'] }}, {{ $mmdst_summary['UNTESTABLE'] }}
                    ],
                    backgroundColor: ['#10b981', '#f59e0b', '#ef4444', '#64748b'],
                    borderRadius: 12
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.03)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // ANTROPOMETRI (HORIZONTAL)
        new Chart(document.getElementById('growthChart'), {
            type: 'bar',
            data: {
                labels: ['Sangat Kurang', 'Kurang', 'Normal', 'Risiko Lebih'],
                datasets: [{
                    data: [{{ $growth_data['Sangat Kurang'] }}, {{ $growth_data['Kurang'] }},
                        {{ $growth_data['Normal'] }}, {{ $growth_data['Risiko Lebih'] }}
                    ],
                    backgroundColor: ['#ef4444', '#f59e0b', '#10b981', '#3b82f6'],
                    borderRadius: 8,
                    barThickness: 20
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                weight: 'bold',
                                size: 11
                            }
                        }
                    }
                }
            }
        });
    </script>
</x-app-layout>
