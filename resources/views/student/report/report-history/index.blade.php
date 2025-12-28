<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat Laporan & Evaluasi') }}
        </h2>
    </x-slot>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <div class="py-12 bg-gray-50" x-data="{ activeTab: 'curriculum' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl mb-8 border-l-4 border-indigo-600">
                <div class="p-6">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0 h-16 w-16">
                            @if ($student->user_photo && Storage::disk('public')->exists($student->user_photo))
                                <img class="h-16 w-16 rounded-full object-cover border-2 border-indigo-200"
                                    src="{{ asset('storage/' . $student->user_photo) }}" alt="Foto Siswa">
                            @else
                                <img class="h-16 w-16 rounded-full object-cover border-2 border-indigo-200"
                                    src="https://ui-avatars.com/api/?name={{ $student->student_name }}&background=eef2ff&color=4f46e5&bold=true"
                                    alt="Foto Siswa">
                            @endif
                        </div>
                        <div>
                            <h3 class="text-2xl font-extrabold text-gray-900">{{ $student->student_name }}</h3>
                            <p class="text-sm text-gray-500 font-mono">{{ $student->student_number }}</p>
                            <div class="mt-2 flex space-x-2">
                                <span
                                    class="px-3 py-1 text-xs rounded-full bg-indigo-100 text-indigo-700 font-semibold border border-indigo-200">
                                    <i class="fas fa-book mr-1"></i> {{ $reports->total() }} Rapor Kurikulum
                                </span>
                                <span
                                    class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-700 font-semibold border border-green-200">
                                    <i class="fas fa-seedling mr-1"></i> {{ $developmentReports->total() }} Rapor Tumbuh
                                    Kembang
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex space-x-1 rounded-xl bg-gray-200 p-1 mb-6 shadow-inner">
                <button @click="activeTab = 'curriculum'"
                    :class="{ 'bg-white shadow text-indigo-700': activeTab === 'curriculum', 'text-gray-600 hover:text-gray-900 hover:bg-gray-100': activeTab !== 'curriculum' }"
                    class="w-full rounded-lg py-3 text-sm font-bold leading-5 transition duration-200 ease-in-out focus:outline-none flex items-center justify-center">
                    <i class="fas fa-book-reader mr-2 text-lg"></i> Laporan Pembelajaran (Kurikulum)
                </button>
                <button @click="activeTab = 'development'"
                    :class="{ 'bg-white shadow text-green-700': activeTab === 'development', 'text-gray-600 hover:text-gray-900 hover:bg-gray-100': activeTab !== 'development' }"
                    class="w-full rounded-lg py-3 text-sm font-bold leading-5 transition duration-200 ease-in-out focus:outline-none flex items-center justify-center">
                    <i class="fas fa-child mr-2 text-lg"></i> Laporan Tumbuh Kembang (DDTK)
                </button>
            </div>

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl min-h-[400px]">
                <div class="p-6">

                    @if (session('success'))
                        <div class="p-4 mb-6 text-sm text-green-700 bg-green-100 rounded-lg border-l-4 border-green-500 shadow-sm flex items-center"
                            role="alert">
                            <i class="fas fa-check-circle mr-2 text-lg"></i>
                            <div><span class="font-bold">Berhasil!</span> {{ session('success') }}</div>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="p-4 mb-6 text-sm text-red-700 bg-red-100 rounded-lg border-l-4 border-red-500 shadow-sm flex items-center"
                            role="alert">
                            <i class="fas fa-exclamation-circle mr-2 text-lg"></i>
                            <div><span class="font-bold">Gagal!</span> {{ session('error') }}</div>
                        </div>
                    @endif

                    <div x-show="activeTab === 'curriculum'" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform -translate-x-2"
                        x-transition:enter-end="opacity-100 transform translate-x-0">

                        <div class="flex justify-between items-center mb-4 border-b pb-2">
                            <h3 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-book text-indigo-500 mr-2"></i> Riwayat Pembelajaran
                            </h3>
                        </div>

                        @if ($reports->count() > 0)
                            <div class="overflow-x-auto rounded-lg border border-gray-200">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-indigo-50">
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-bold text-indigo-800 uppercase tracking-wider">
                                                Judul & Periode</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-bold text-indigo-800 uppercase tracking-wider hidden sm:table-cell">
                                                Data Fisik</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-bold text-indigo-800 uppercase tracking-wider">
                                                Status Pengesahan</th>
                                            <th
                                                class="px-6 py-3 text-center text-xs font-bold text-indigo-800 uppercase tracking-wider">
                                                Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($reports as $report)
                                            @php
                                                $startDate = \Carbon\Carbon::parse($report->start_date);
                                                $startYear =
                                                    $startDate->month >= 7 ? $startDate->year : $startDate->year - 1;
                                                $academicYear = $startYear . '/' . ($startYear + 1);
                                                $isSigned = !empty($report->parent_signature);
                                            @endphp
                                            <tr class="hover:bg-indigo-50 transition-colors duration-150">
                                                <td class="px-6 py-4">
                                                    <div class="text-sm font-bold text-gray-900">
                                                        {{ $report->report_title }}</div>
                                                    <div class="text-xs text-gray-500 mt-1 flex items-center">
                                                        <span
                                                            class="bg-gray-100 px-2 py-0.5 rounded text-gray-600 font-mono mr-2">{{ $academicYear }}</span>
                                                        <span>{{ $report->semester ?? '-' }}</span>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 hidden sm:table-cell">
                                                    <div class="text-xs text-gray-600 space-y-1">
                                                        <div class="flex items-center">
                                                            <i
                                                                class="fas fa-weight w-5 text-center text-gray-400 mr-1"></i>
                                                            <span class="font-medium">{{ $report->weight ?? '-' }}
                                                                kg</span>
                                                        </div>
                                                        <div class="flex items-center">
                                                            <i
                                                                class="fas fa-ruler-vertical w-5 text-center text-gray-400 mr-1"></i>
                                                            <span class="font-medium">{{ $report->height ?? '-' }}
                                                                cm</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4">
                                                    @if ($isSigned)
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            <i class="fas fa-check-circle mr-1.5"></i>
                                                            {{ $report->parent_name }}
                                                        </span>
                                                    @else
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 animate-pulse">
                                                            <i class="fas fa-clock mr-1.5"></i> Menunggu TTD
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                                    <div class="flex justify-center space-x-2">
                                                        <a href="{{ route('student.report.show', $report->id) }}"
                                                            class="group flex items-center px-3 py-2 rounded-lg transition-all duration-200 font-semibold text-xs shadow-sm {{ $isSigned ? 'bg-indigo-600 hover:bg-indigo-700 text-white' : 'bg-yellow-500 hover:bg-yellow-600 text-white' }}">
                                                            <i
                                                                class="{{ $isSigned ? 'fas fa-eye' : 'fas fa-pen-fancy' }} mr-2"></i>
                                                            {{ $isSigned ? 'Detail' : 'Tanda Tangan' }}
                                                        </a>
                                                        <a href="{{ route('student.report.pdf', $report->id) }}"
                                                            target="_blank"
                                                            class="group flex items-center bg-red-50 text-red-600 hover:bg-red-600 hover:text-white px-3 py-2 rounded-lg transition-all duration-200 text-xs border border-red-200 shadow-sm"
                                                            title="Download PDF">
                                                            <i class="fas fa-file-pdf text-base"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-6">
                                {{ $reports->appends(['activeTab' => 'curriculum'])->links() }}
                            </div>
                        @else
                            <div class="text-center py-16 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                                <div
                                    class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-indigo-100 mb-4">
                                    <i class="fas fa-book-open text-indigo-400 text-3xl"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900">Belum ada Rapor Kurikulum</h3>
                                <p class="text-gray-500 mt-1 max-w-md mx-auto">
                                    Laporan hasil pembelajaran akan muncul di sini setelah guru menerbitkan rapor
                                    semester/akhir.
                                </p>
                            </div>
                        @endif
                    </div>

                    <div x-show="activeTab === 'development'" x-cloak
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform translate-x-2"
                        x-transition:enter-end="opacity-100 transform translate-x-0">

                        <div class="flex justify-between items-center mb-4 border-b pb-2">
                            <h3 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-chart-line text-green-500 mr-2"></i> Grafik & Diagnosa Tumbuh Kembang
                            </h3>
                        </div>

                        @if ($developmentReports->count() > 0)
                            <div class="overflow-x-auto rounded-lg border border-gray-200">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-green-50">
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-bold text-green-800 uppercase tracking-wider">
                                                Periode & Usia</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-bold text-green-800 uppercase tracking-wider hidden sm:table-cell">
                                                Hasil Pengukuran</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-bold text-green-800 uppercase tracking-wider">
                                                Status Pengesahan</th>
                                            <th
                                                class="px-6 py-3 text-center text-xs font-bold text-green-800 uppercase tracking-wider">
                                                Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($developmentReports as $devReport)
                                            @php
                                                $devIsSigned = !empty($devReport->parent_signature);
                                            @endphp
                                            <tr class="hover:bg-green-50 transition-colors duration-150">
                                                <td class="px-6 py-4">
                                                    <div class="text-sm font-bold text-gray-900">
                                                        {{ $devReport->academic_year }}
                                                        <span class="text-gray-400 mx-1">|</span>
                                                        {{ $devReport->semester }}
                                                    </div>
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        <div class="font-semibold text-green-600 mb-0.5">
                                                            Usia: {{ $devReport->age_in_months }} Bulan
                                                        </div>
                                                        <div>
                                                            <i class="far fa-calendar-alt mr-1"></i>
                                                            {{ $devReport->report_date->translatedFormat('d M Y') }}
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 hidden sm:table-cell">
                                                    <div class="flex flex-wrap gap-2 mb-2">
                                                        <span
                                                            class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                                            TB: {{ $devReport->height_cm }} cm
                                                        </span>
                                                        <span
                                                            class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-orange-50 text-orange-700 border border-orange-100">
                                                            BB: {{ $devReport->weight_kg }} kg
                                                        </span>
                                                    </div>
                                                    <div class="text-xs text-gray-700">
                                                        <span class="font-bold">Diagnosa:</span>
                                                        {{ \Illuminate\Support\Str::limit($devReport->mmdst_final_result ?? 'Belum ada data', 30) }}
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4">
                                                    @if ($devIsSigned)
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            <i class="fas fa-check-circle mr-1.5"></i>
                                                            {{ $devReport->parent_name }}
                                                        </span>
                                                    @else
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 animate-pulse">
                                                            <i class="fas fa-clock mr-1.5"></i> Menunggu TTD
                                                        </span>
                                                    @endif
                                                </td>
                                                <td
                                                    class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                                    <div class="flex justify-center space-x-2">
                                                        <a href="{{ route('student.report.development.show', $devReport->id) }}"
                                                            class="group flex items-center px-3 py-2 rounded-lg transition-all duration-200 font-semibold text-xs shadow-sm {{ $devIsSigned ? 'bg-green-600 hover:bg-green-700 text-white' : 'bg-yellow-500 hover:bg-yellow-600 text-white' }}">
                                                            <i
                                                                class="{{ $devIsSigned ? 'fas fa-eye' : 'fas fa-pen-fancy' }} mr-2"></i>
                                                            {{ $devIsSigned ? 'Detail' : 'Tanda Tangan' }}
                                                        </a>

                                                        <a href="{{ route('student.report.development.pdf', $devReport->id) }}"
                                                            target="_blank"
                                                            class="group flex items-center bg-red-50 text-red-600 hover:bg-red-600 hover:text-white px-3 py-2 rounded-lg transition-all duration-200 text-xs border border-red-200 shadow-sm"
                                                            title="Download PDF Tumbuh Kembang">
                                                            <i class="fas fa-file-pdf text-base"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-6">
                                {{ $developmentReports->appends(['activeTab' => 'development'])->links() }}
                            </div>
                        @else
                            <div
                                class="text-center py-16 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                                <div
                                    class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 mb-4">
                                    <i class="fas fa-child text-green-400 text-3xl"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900">Belum ada Laporan Tumbuh Kembang</h3>
                                <p class="text-gray-500 mt-1 max-w-md mx-auto">
                                    Data pengukuran fisik dan skrining perkembangan (DDTK/KMS) akan muncul di sini.
                                </p>
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
