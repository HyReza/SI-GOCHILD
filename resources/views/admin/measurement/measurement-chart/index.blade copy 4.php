<x-app-layout>
    <x-slot:title>Grafik Pertumbuhan: {{ $student->student_name }}</x-slot:title>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                Grafik Pertumbuhan: {{ $student->student_name }}
            </h2>
            <a href="{{ route('measurement.history', $activityTransaction) }}"
                class="inline-flex items-center gap-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-lg text-sm hover:bg-gray-300 dark:hover:bg-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Riwayat
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-900 shadow-md sm:rounded-lg p-6">
                {{-- Info Siswa --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 text-sm border-b dark:border-gray-700 pb-4">
                    <div><strong class="text-gray-500 dark:text-gray-400 block">Nama</strong>
                        <p class="dark:text-gray-200">{{ $student->student_name }}</p>
                    </div>
                    <div><strong class="text-gray-500 dark:text-gray-400 block">Tanggal Lahir</strong>
                        <p class="dark:text-gray-200">{{ \Carbon\Carbon::parse($student->birth_date)->format('d M Y') }}
                        </p>
                    </div>
                    <div><strong class="text-gray-500 dark:text-gray-400 block">Gender</strong>
                        <p class="dark:text-gray-200">
                            {{ $student->gender == 1 || $student->gender == 'male' ? 'Laki-laki' : 'Perempuan' }}</p>
                    </div>
                </div>

                <div class="border-b dark:border-gray-700 pb-4 mb-4">
                    <h3 class="text-md font-semibold text-gray-800 dark:text-white mb-2">Pilih Jenis Grafik</h3>
                    <div id="chart-buttons" class="flex flex-wrap gap-2"></div>
                </div>

                <div>
                    <h3 id="chart-title" class="font-semibold text-lg text-gray-800 dark:text-white mb-4 text-center">
                    </h3>
                    <div class="relative w-full h-80 sm:h-96 md:h-[32rem]">
                        <canvas id="kmsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const studentDataPoints = @json($chartData ?? []);
            const allStandards = @json($allStandardCurves ?? []);
            const studentBirthDate = '{{ $student->birth_date }}';
            const isMale = {{ $student->gender == 1 || $student->gender == 'male' ? 'true' : 'false' }};
            const studentPointColor = isMale ? 'rgba(59, 130, 246, 1)' : 'rgba(236, 72, 153, 1)';
            const canvas = document.getElementById('kmsChart');
            const chartTitleEl = document.getElementById('chart-title');
            const buttonsContainer = document.getElementById('chart-buttons');
            let currentChart = null;

            if (!studentDataPoints || studentDataPoints.length === 0) {
                chartTitleEl.textContent = 'Tidak ada data pengukuran untuk ditampilkan.';
                return;
            }

            // --- FUNGSI-FUNGSI HELPER ---
            function getFullAge(birthDateStr, measurementDateStr) {
                const birthDate = new Date(birthDateStr);
                const measurementDate = new Date(measurementDateStr);
                if (isNaN(birthDate.getTime()) || isNaN(measurementDate.getTime())) return null;
                let years = measurementDate.getFullYear() - birthDate.getFullYear();
                let months = measurementDate.getMonth() - birthDate.getMonth();
                let days = measurementDate.getDate() - birthDate.getDate();
                if (days < 0) {
                    months--;
                    days += new Date(measurementDate.getFullYear(), measurementDate.getMonth(), 0).getDate();
                }
                if (months < 0) {
                    years--;
                    months += 12;
                }
                return {
                    years,
                    months,
                    days
                };
            }

            const createLineBreaks = (points, xKey, yKey) => {
                if (!points || points.length < 2) {
                    return points.map(p => ({
                        x: p[xKey],
                        y: p[yKey]
                    }));
                }
                const processed = [];
                for (let i = 0; i < points.length; i++) {
                    if (i > 0) {
                        const prevDate = new Date(points[i - 1].date);
                        const currDate = new Date(points[i].date);
                        const diffMonths = (currDate - prevDate) / (1000 * 60 * 60 * 24 * 30.44);
                        if (diffMonths > 2) {
                            processed.push({
                                x: points[i][xKey],
                                y: NaN
                            });
                        }
                    }
                    processed.push({
                        x: points[i][xKey],
                        y: points[i][yKey]
                    });
                }
                return processed;
            };

            // --- FUNGSI UTAMA RENDER GRAFIK ---
            function renderChart(config) {
                if (currentChart) {
                    currentChart.destroy();
                }
                chartTitleEl.textContent = config.title;
                const mapStandardData = (data) => data ? Object.keys(data).map(xVal => ({
                    x: parseFloat(xVal),
                    y: data[xVal]
                })) : [];
                const boundaryLine = {
                    borderWidth: 1,
                    pointRadius: 0,
                    tension: 0.4
                };

                try {
                    currentChart = new Chart(canvas, {
                        type: 'line',
                        data: {
                            datasets: [
                                // Zona Berwarna (digambar pertama)
                                {
                                    ...boundaryLine,
                                    label: '+3 SD',
                                    data: mapStandardData(config.standard?.plus_3_sd),
                                    borderColor: 'rgba(150, 150, 150, 0.4)'
                                },
                                {
                                    ...boundaryLine,
                                    label: '+2 SD',
                                    data: mapStandardData(config.standard?.plus_2_sd),
                                    borderColor: 'rgba(239, 68, 68, 0.4)',
                                    fill: '-1',
                                    backgroundColor: 'rgba(239, 68, 68, 0.1)'
                                },
                                {
                                    ...boundaryLine,
                                    label: '+1 SD',
                                    data: mapStandardData(config.standard?.plus_1_sd),
                                    borderColor: 'rgba(234, 179, 8, 0.4)',
                                    fill: '-1',
                                    backgroundColor: 'rgba(234, 179, 8, 0.1)'
                                },
                                {
                                    ...boundaryLine,
                                    label: '-2 SD',
                                    data: mapStandardData(config.standard?.minus_2_sd),
                                    borderColor: 'rgba(34, 197, 94, 0.4)',
                                    fill: '-1',
                                    backgroundColor: 'rgba(34, 197, 94, 0.15)'
                                },
                                {
                                    ...boundaryLine,
                                    label: '-1 SD',
                                    data: mapStandardData(config.standard?.minus_1_sd),
                                    borderColor: 'rgba(34, 197, 94, 0.4)'
                                },
                                {
                                    ...boundaryLine,
                                    label: '-3 SD',
                                    data: mapStandardData(config.standard?.minus_3_sd),
                                    borderColor: 'rgba(234, 179, 8, 0.4)',
                                    fill: '-1',
                                    backgroundColor: 'rgba(234, 179, 8, 0.1)'
                                },
                                {
                                    label: 'Median',
                                    data: mapStandardData(config.standard?.median),
                                    borderColor: 'rgba(22, 163, 74, 1)',
                                    borderWidth: 2,
                                    pointRadius: 0,
                                    tension: 0.4
                                },
                                {
                                    label: 'Pengukuran Anak',
                                    data: config.studentPoints,
                                    borderColor: studentPointColor,
                                    backgroundColor: studentPointColor,
                                    pointRadius: 5,
                                    pointHoverRadius: 7,
                                    showLine: true,
                                    spanGaps: false,
                                    tension: 0.1
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                x: {
                                    type: 'linear',
                                    title: {
                                        display: true,
                                        text: config.xAxisLabel,
                                        font: {
                                            size: 14
                                        }
                                    },
                                    min: config.standard?.min,
                                    max: config.standard?.max,
                                    ticks: {
                                        stepSize: 1,
                                        autoSkip: false,
                                    }


                                },
                                y: {
                                    title: {
                                        display: true,
                                        text: config.yAxisLabel,
                                        font: {
                                            size: 14
                                        }
                                    },
                                    beginAtZero: false,
                                    ticks: {
                                        stepSize: 1,
                                        autoSkip: false,
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    callbacks: {
                                        title: () => '',
                                        label: (context) => {
                                            if (context.dataset.label === 'Pengukuran Anak') {
                                                const point = studentDataPoints[context.dataIndex];
                                                if (!point) return '';
                                                const age = getFullAge(studentBirthDate, point.date);
                                                const ageString = `${age.years} thn, ${age.months} bln`;
                                                const chartKey = config.chartKey;
                                                const sd = point.sd_category ? (point.sd_category[
                                                    chartKey] || 'N/A') : 'N/A';
                                                const status = point.status_gizi ? (point.status_gizi[
                                                    chartKey] || 'N/A') : 'N/A';
                                                return [`Tanggal: ${new Date(point.date).toLocaleDateString('id-ID', {day:'numeric', month:'long', year:'numeric'})}`,
                                                    `Umur: ${ageString}`,
                                                    `Nilai: ${context.parsed.y}`,
                                                    `Kategori SD: ${sd}`, `Status Gizi: ${status}`
                                                ];
                                            }
                                            return null;
                                        }
                                    }
                                }
                            }
                        }
                    });
                } catch (e) {
                    console.error("Gagal membuat grafik:", e);
                    chartTitleEl.textContent = "Terjadi kesalahan saat membuat grafik.";
                }
            }

            // --- KONFIGURASI DAN PEMBUATAN TOMBOL ---
            const chartConfigs = {
                'BB/U': {
                    title: 'Berat Badan menurut Umur',
                    yAxisLabel: 'Berat Badan (kg)',
                    xKey: 'age',
                    yKey: 'weight',
                    standard: allStandards['BB/U']
                },
                'PB/U': {
                    title: 'Panjang Badan menurut Umur',
                    yAxisLabel: 'Panjang Badan (cm)',
                    xKey: 'age',
                    yKey: 'height',
                    standard: allStandards['PB/U']
                },
                'TB/U': {
                    title: 'Tinggi Badan menurut Umur',
                    yAxisLabel: 'Tinggi Badan (cm)',
                    xKey: 'age',
                    yKey: 'height',
                    standard: allStandards['TB/U']
                },
                'IMT/U': {
                    title: 'Indeks Massa Tubuh menurut Umur',
                    yAxisLabel: 'IMT',
                    xKey: 'age',
                    yKey: 'bmi',
                    standard: allStandards['IMT/U']
                },
                'PB/BB': {
                    title: 'Berat Badan menurut Panjang Badan',
                    yAxisLabel: 'Berat Badan (kg)',
                    xKey: 'height',
                    yKey: 'weight',
                    standard: allStandards['PB/BB']
                },
                'TB/BB': {
                    title: 'Berat Badan menurut Tinggi Badan',
                    yAxisLabel: 'Berat Badan (kg)',
                    xKey: 'height',
                    yKey: 'weight',
                    standard: allStandards['TB/BB']
                }
            };

            const allPossibleButtons = ['BB/U', 'PB/U', 'TB/U', 'IMT/U', 'PB/BB', 'TB/BB'];
            allPossibleButtons.forEach(key => {
                if (chartConfigs[key] && chartConfigs[key].standard) {
                    const button = document.createElement('button');
                    button.textContent = key;
                    button.className =
                        'px-3 py-1.5 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 chart-btn';
                    button.dataset.chartKey = key;
                    buttonsContainer.appendChild(button);
                }
            });

            document.querySelectorAll('.chart-btn').forEach(button => {
                button.addEventListener('click', function() {
                    document.querySelectorAll('.chart-btn').forEach(btn => btn.classList.remove(
                        'bg-indigo-600', 'text-white', 'dark:bg-indigo-600'));
                    this.classList.add('bg-indigo-600', 'text-white', 'dark:bg-indigo-600');
                    const key = this.dataset.chartKey;
                    const config = chartConfigs[key];
                    if (!config) return;

                    const studentPointsWithBreaks = createLineBreaks(studentDataPoints, config.xKey,
                        config.yKey);
                    const xAxisKey = config.standard.x_axis_key;

                    renderChart({
                        chartKey: key,
                        title: config.title,
                        yAxisLabel: config.yAxisLabel,
                        xAxisLabel: xAxisKey === 'age_months' ? 'Umur (bulan)' : (
                            xAxisKey === 'body_length' ? 'Panjang Badan (cm)' :
                            'Tinggi Badan (cm)'),
                        studentPoints: studentPointsWithBreaks,
                        standard: config.standard
                    });
                });
            });

            const firstButton = document.querySelector('.chart-btn[data-chart-key="BB/U"]') || document
                .querySelector('.chart-btn');
            if (firstButton) {
                firstButton.click();
            }
        });
    </script>
</x-app-layout>
