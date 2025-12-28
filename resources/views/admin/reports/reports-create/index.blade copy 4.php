<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Buat Raport Baru') }}
            </h2>
            <div class="mt-4 md:mt-0">
                <a href="{{ route('reports.selectPeriod', $student->id) }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <form action="{{ route('reports.store') }}" method="POST" enctype="multipart/form-data" id="reportForm">
                @csrf
                <input type="hidden" name="student_id" id="student_id" value="{{ $student->id }}">
                <input type="hidden" id="student_name" value="{{ $student->student_name }}">
                <input type="hidden" name="start_date" value="{{ $startDate }}">
                <input type="hidden" name="end_date" value="{{ $endDate }}">
                <textarea name="attendance_summary" style="display:none;">{{ json_encode($attendanceSummary) }}</textarea>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0 h-16 w-16">
                                    <img class="h-16 w-16 rounded-full object-cover border-2 border-indigo-500"
                                        src="https://ui-avatars.com/api/?name={{ $student->student_name }}&background=random"
                                        alt="Foto Siswa">
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">{{ $student->student_name }}</h3>
                                    <p class="text-sm text-gray-500">{{ $student->student_number }}</p>
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Aktif
                                    </span>
                                </div>
                            </div>
                            <div class="col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Judul Laporan</label>
                                    <input type="text" name="report_title" value="Laporan Capaian Pembelajaran"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                        required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tanggal Raport</label>
                                    <input type="date" name="report_date" value="{{ date('Y-m-d') }}"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                        required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-4 border-b border-gray-200 bg-white rounded-t-lg shadow-sm">
                    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="myTab" role="tablist">
                        <li class="mr-2" role="presentation">
                            <button
                                class="inline-block p-4 rounded-t-lg border-b-2 text-indigo-600 border-indigo-600 hover:text-indigo-600 hover:border-indigo-600 transition-colors duration-200 tab-btn"
                                id="profile-tab" data-tabs-target="#profile" type="button" role="tab"
                                aria-controls="profile" aria-selected="true">
                                <i class="fas fa-book-open mr-2"></i> 1. Capaian & Narasi
                            </button>
                        </li>
                        <li class="mr-2" role="presentation">
                            <button
                                class="inline-block p-4 rounded-t-lg border-b-2 border-transparent hover:text-gray-600 hover:border-gray-300 transition-colors duration-200 tab-btn"
                                id="health-tab" data-tabs-target="#health" type="button" role="tab"
                                aria-controls="health" aria-selected="false">
                                <i class="fas fa-heartbeat mr-2"></i> 2. Fisik & Kesehatan
                            </button>
                        </li>
                        <li class="mr-2" role="presentation">
                            <button
                                class="inline-block p-4 rounded-t-lg border-b-2 border-transparent hover:text-gray-600 hover:border-gray-300 transition-colors duration-200 tab-btn"
                                id="legal-tab" data-tabs-target="#legal" type="button" role="tab"
                                aria-controls="legal" aria-selected="false">
                                <i class="fas fa-file-signature mr-2"></i> 3. Pengesahan
                            </button>
                        </li>
                    </ul>
                </div>

                <div id="myTabContent">

                    <div class="p-4 rounded-lg bg-gray-50 border border-gray-200" id="profile" role="tabpanel"
                        aria-labelledby="profile-tab">

                        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-r-md">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-info-circle text-blue-500"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-700">
                                        Nilai yang terpilih otomatis adalah <strong>modus (nilai terbanyak)</strong>
                                        dari laporan harian. Jika ada label <span
                                            class="text-xs bg-red-100 text-red-800 px-1 rounded border border-red-200">Belum
                                            ada data</span>, artinya belum ada penilaian harian pada materi tersebut.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white shadow overflow-hidden sm:rounded-md mb-8">
                            <div class="px-4 py-5 sm:px-6 bg-gray-50 border-b border-gray-200">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Detail Penilaian Kurikulum</h3>
                            </div>
                            <ul class="divide-y divide-gray-200">
                                @foreach ($themes as $theme)
                                    <li class="bg-white">
                                        <div class="px-4 py-4 sm:px-6">
                                            <h4 class="text-md font-bold text-indigo-700 mb-2">
                                                {{ $theme->theme_name }}</h4>
                                            @foreach ($theme->subThemes as $subTheme)
                                                <div class="ml-4 mt-2 mb-4 border-l-2 border-indigo-200 pl-3">
                                                    <h5 class="text-sm font-semibold text-gray-700 mb-2">
                                                        {{ $subTheme->sub_theme_name }}</h5>

                                                    @foreach ($subTheme->materials as $material)
                                                        @php
                                                            $score = $calculatedScores[$material->id] ?? null;
                                                            $hasScore = !is_null($score);
                                                        @endphp
                                                        <div
                                                            class="flex flex-col md:flex-row md:items-center justify-between py-2 border-b border-gray-100 last:border-0 score-row hover:bg-gray-50">

                                                            <div class="w-full md:w-1/2 flex items-center">
                                                                <span
                                                                    class="text-sm text-gray-600 material-name mr-2">{{ $material->material_name }}</span>
                                                                @if (!$hasScore)
                                                                    <span
                                                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                                        Belum ada data harian
                                                                    </span>
                                                                @endif
                                                            </div>

                                                            <div
                                                                class="flex space-x-2 mt-2 md:mt-0 w-full md:w-1/2 justify-end">
                                                                @foreach (['BB', 'MB', 'BSH', 'BSB'] as $opt)
                                                                    <label class="cursor-pointer">
                                                                        <input type="radio"
                                                                            name="scores[{{ $material->id }}]"
                                                                            value="{{ $opt }}"
                                                                            class="peer sr-only"
                                                                            {{ $score == $opt ? 'checked' : '' }}>
                                                                        <div
                                                                            class="px-3 py-1 text-xs rounded-md border border-gray-300 text-gray-600 peer-checked:bg-indigo-600 peer-checked:text-white peer-checked:border-indigo-600 transition-all hover:bg-gray-100">
                                                                            {{ $opt }}
                                                                        </div>
                                                                    </label>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endforeach
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="space-y-8">
                            <div class="border-b-2 border-indigo-500 pb-2 mb-4">
                                <h3 class="text-xl font-bold text-gray-800"><i
                                        class="fas fa-pen-fancy mr-2"></i>Catatan Narasi Perkembangan</h3>
                                <p class="text-sm text-gray-500">Klik tombol <strong>Generate AI</strong> pada setiap
                                    aspek untuk membuat narasi otomatis berdasarkan nilai di atas.</p>
                            </div>

                            @php
                                $sections = [
                                    [
                                        'id' => 'narasi_agama',
                                        'title' => 'Nilai Agama & Budi Pekerti',
                                        'icon' => 'fas fa-praying-hands',
                                        'color' => 'text-yellow-500',
                                        'photo' => 'foto_agama',
                                    ],
                                    [
                                        'id' => 'narasi_jatidiri',
                                        'title' => 'Jati Diri',
                                        'icon' => 'fas fa-user',
                                        'color' => 'text-blue-500',
                                        'photo' => 'foto_jatidiri',
                                    ],
                                    [
                                        'id' => 'narasi_steam',
                                        'title' => 'Literasi & STEAM',
                                        'icon' => 'fas fa-flask',
                                        'color' => 'text-green-500',
                                        'photo' => 'foto_steam',
                                    ],
                                    [
                                        'id' => 'narasi_p5',
                                        'title' => 'Projek Penguatan Profil Pelajar Pancasila',
                                        'icon' => 'fas fa-flag',
                                        'color' => 'text-red-500',
                                        'photo' => 'foto_p5',
                                    ],
                                ];
                            @endphp

                            @foreach ($sections as $sec)
                                <div class="bg-white shadow-md sm:rounded-lg overflow-hidden border border-gray-200">
                                    <div
                                        class="px-4 py-4 sm:px-6 border-b border-gray-200 flex flex-col md:flex-row justify-between items-center bg-gray-50">
                                        <h3 class="text-lg leading-6 font-bold text-gray-900 mb-2 md:mb-0">
                                            <i class="{{ $sec['icon'] }} {{ $sec['color'] }} mr-2"></i>
                                            {{ $sec['title'] }}
                                        </h3>
                                        <button type="button"
                                            onclick="generateNarrativeAI('{{ $sec['title'] }}', '{{ $sec['id'] }}')"
                                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 shadow-sm focus:outline-none transition-all">
                                            <i class="fas fa-magic mr-2"></i>
                                            Generate AI
                                        </button>
                                    </div>
                                    <div class="p-6">
                                        <div id="loading_{{ $sec['id'] }}"
                                            class="hidden mb-3 text-indigo-600 text-sm font-semibold flex items-center bg-indigo-50 p-2 rounded">
                                            <i class="fas fa-spinner fa-spin mr-2"></i>
                                            Sedang menyusun narasi terbaik untuk ananda...
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div>
                                                <label class="block text-sm font-bold text-gray-700 mb-2">Narasi
                                                    Capaian</label>
                                                <textarea name="{{ $sec['id'] }}" id="{{ $sec['id'] }}" rows="6"
                                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                                    placeholder="Narasi akan muncul di sini setelah klik Generate AI, atau Anda bisa mengetiknya secara manual."></textarea>
                                            </div>

                                            <div
                                                class="bg-gray-50 p-4 rounded-lg border border-dashed border-gray-300">
                                                <label class="block text-sm font-bold text-gray-700 mb-2">
                                                    <i class="fas fa-camera mr-1"></i> Dokumentasi Kegiatan (Opsional)
                                                </label>
                                                <div
                                                    class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md bg-white hover:bg-gray-50 cursor-pointer">
                                                    <div class="space-y-1 text-center relative">
                                                        <i class="fas fa-cloud-upload-alt text-3xl text-gray-400"></i>
                                                        <div class="flex text-sm text-gray-600 justify-center">
                                                            <label for="{{ $sec['photo'] }}"
                                                                class="relative cursor-pointer rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none">
                                                                <span>Upload file</span>
                                                                <input id="{{ $sec['photo'] }}"
                                                                    name="{{ $sec['photo'] }}" type="file"
                                                                    class="sr-only">
                                                            </label>
                                                        </div>
                                                        <p class="text-xs text-gray-500">PNG, JPG, GIF up to 5MB</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <div
                                class="bg-indigo-50 shadow sm:rounded-lg overflow-hidden border border-indigo-200 mt-8">
                                <div
                                    class="px-4 py-4 sm:px-6 flex justify-between items-center bg-indigo-100 border-b border-indigo-200">
                                    <h3 class="text-lg leading-6 font-bold text-indigo-900"><i
                                            class="fas fa-comments mr-2"></i>Refleksi Orang Tua</h3>
                                    <button type="button"
                                        onclick="generateNarrativeAI('Saran Refleksi Orang Tua', 'refleksi_ortu')"
                                        class="text-xs font-semibold text-indigo-700 hover:text-indigo-900 hover:underline">
                                        <i class="fas fa-lightbulb mr-1"></i> Bantu buatkan pertanyaan pemantik
                                    </button>
                                </div>
                                <div class="p-6">
                                    <div id="loading_refleksi_ortu"
                                        class="hidden text-sm italic text-indigo-600 mb-2"><i
                                            class="fas fa-spinner fa-spin mr-1"></i> Loading...</div>
                                    <textarea name="refleksi_ortu" id="refleksi_ortu" rows="3"
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                        placeholder="Tuliskan catatan atau pertanyaan untuk orang tua..."></textarea>
                                    <div class="mt-4">
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Foto Kegiatan di
                                            Rumah (Opsional)</label>
                                        <input type="file" name="parent_reflection_photo"
                                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-100 file:text-indigo-700 hover:file:bg-indigo-200 transition-colors" />
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="health" role="tabpanel"
                        aria-labelledby="health-tab">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                            <div class="bg-white shadow-lg sm:rounded-xl p-6 border border-gray-200">
                                <h3 class="text-lg font-bold text-gray-900 border-b pb-3 mb-5">
                                    <i class="fas fa-weight mr-2 text-indigo-500"></i>Data Pertumbuhan
                                </h3>
                                <div class="grid grid-cols-3 gap-4 mb-6">
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Berat
                                            (kg)</label>
                                        <input type="number" step="0.1" name="berat_badan" id="berat_badan"
                                            class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md p-2">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Tinggi
                                            (cm)</label>
                                        <input type="number" step="0.1" name="tinggi_badan" id="tinggi_badan"
                                            class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md p-2">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Lingkar
                                            Kpl</label>
                                        <input type="number" step="0.1" name="lingkar_kepala"
                                            id="lingkar_kepala"
                                            class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md p-2">
                                    </div>
                                </div>

                                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                    <div class="flex justify-between items-center mb-2">
                                        <label class="block text-sm font-bold text-gray-700">Kesimpulan
                                            Perkembangan</label>
                                        <button type="button" onclick="generateConclusionAI()"
                                            class="text-xs bg-indigo-100 text-indigo-700 px-2 py-1 rounded hover:bg-indigo-200 transition-colors font-semibold">
                                            <i class="fas fa-robot mr-1"></i> Buat Kesimpulan Otomatis
                                        </button>
                                    </div>
                                    <div id="loading_info_perkembangan"
                                        class="hidden text-xs font-semibold text-indigo-600 mb-2">
                                        <i class="fas fa-spinner fa-spin mr-1"></i> Menganalisa data fisik...
                                    </div>
                                    <textarea name="info_perkembangan" id="info_perkembangan" rows="5"
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                        placeholder="Kesimpulan akan digenerate otomatis berdasarkan berat, tinggi, dan ceklis kesehatan..."></textarea>
                                </div>

                                <div class="mt-5">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Upload Grafik KMS
                                        (Opsional)</label>
                                    <input type="file" name="development_info_photo"
                                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200" />
                                </div>
                            </div>

                            <div class="bg-white shadow-lg sm:rounded-xl p-6 border border-gray-200">
                                <h3 class="text-lg font-bold text-gray-900 border-b pb-3 mb-5">
                                    <i class="fas fa-notes-medical mr-2 text-red-500"></i>Pemeriksaan Fisik
                                </h3>
                                <div class="space-y-4">
                                    @foreach ($healthItems as $index => $item)
                                        <div
                                            class="flex items-center justify-between pb-2 border-b border-gray-100 last:border-0">
                                            <span class="text-sm font-medium text-gray-700">{{ $item }}</span>
                                            <select name="health[{{ $item }}]"
                                                id="health_{{ $index }}"
                                                class="health-select block w-40 pl-3 pr-8 py-1 text-sm border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 rounded-md shadow-sm">
                                                <option value="Baik" selected>Baik</option>
                                                <option value="Cukup">Cukup</option>
                                                <option value="Perlu Perhatian">Perlu Perhatian</option>
                                            </select>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="legal" role="tabpanel"
                        aria-labelledby="legal-tab">
                        <div class="bg-white shadow-xl sm:rounded-xl overflow-hidden">
                            <div class="bg-gray-800 px-6 py-4 border-b border-gray-200 text-center">
                                <h3 class="text-lg leading-6 font-bold text-white uppercase tracking-wider">Lembar
                                    Pengesahan Digital</h3>
                                <p class="text-xs text-gray-400 mt-1">Silakan tanda tangan pada kotak di bawah ini.</p>
                            </div>
                            <div class="p-8 bg-gray-50">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">

                                    @php
                                        $signatures = [
                                            [
                                                'id' => 'sig_ortu',
                                                'input' => 'ttd_ortu',
                                                'name_input' => 'nama_ortu',
                                                'label' => 'Orang Tua / Wali',
                                                'default_name' => '',
                                                'placeholder' => 'Nama Orang Tua',
                                            ],
                                            [
                                                'id' => 'sig_guru',
                                                'input' => 'ttd_guru',
                                                'name_input' => 'nama_guru',
                                                'label' => 'Wali Kelas',
                                                'default_name' => $defaultTeacherName,
                                                'placeholder' => 'Nama Guru',
                                            ],
                                            [
                                                'id' => 'sig_konsultan',
                                                'input' => 'ttd_konsultan',
                                                'name_input' => 'nama_konsultan',
                                                'label' => 'Konsultan Tumbuh Kembang',
                                                'default_name' => $defaultConsultantName,
                                                'placeholder' => 'Nama Konsultan',
                                            ],
                                            [
                                                'id' => 'sig_kepsek',
                                                'input' => 'ttd_kepsek',
                                                'name_input' => 'nama_kepsek',
                                                'label' => 'Kepala Sekolah',
                                                'default_name' => $defaultPrincipalName,
                                                'placeholder' => 'Nama Kepala Sekolah',
                                            ],
                                        ];
                                    @endphp

                                    @foreach ($signatures as $sig)
                                        <div
                                            class="flex flex-col items-center bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                                            <label
                                                class="block text-sm font-bold text-gray-800 mb-3 uppercase tracking-wide">{{ $sig['label'] }}</label>

                                            <div
                                                class="relative group w-full h-48 border-2 border-dashed border-gray-400 rounded-lg bg-white hover:border-indigo-500 transition-colors">
                                                <canvas id="{{ $sig['id'] }}"
                                                    class="w-full h-full cursor-crosshair"></canvas>

                                                <button type="button"
                                                    class="absolute top-2 right-2 text-gray-400 hover:text-red-600 bg-white rounded-full p-1 shadow-sm opacity-0 group-hover:opacity-100 transition-all duration-200 clear-sig-btn border border-gray-200"
                                                    data-target="{{ $sig['id'] }}" title="Hapus Tanda Tangan">
                                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>

                                                <span
                                                    class="absolute bottom-2 left-2 text-[10px] text-gray-300 pointer-events-none">Area
                                                    Tanda Tangan</span>
                                            </div>

                                            <input type="hidden" name="{{ $sig['input'] }}"
                                                id="input_{{ $sig['id'] }}">

                                            <div class="w-full mt-4">
                                                <label class="block text-xs text-gray-500 mb-1 ml-1">Nama
                                                    Terang</label>
                                                <input type="text" name="{{ $sig['name_input'] }}"
                                                    value="{{ $sig['default_name'] }}"
                                                    placeholder="{{ $sig['placeholder'] }}"
                                                    class="text-center focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md font-semibold">
                                            </div>
                                        </div>
                                    @endforeach

                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end space-x-3">
                            <button type="button"
                                class="bg-white py-3 px-6 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Batal
                            </button>
                            <button type="submit" onclick="submitForm(event)"
                                class="inline-flex justify-center py-3 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all transform hover:scale-105">
                                <i class="fas fa-save mr-2"></i> Simpan Raport
                            </button>
                        </div>

                    </div>

                </div>
            </form>
        </div>
    </div>

    <script>
        // --- 1. TAB SWITCHING LOGIC ---
        document.addEventListener("DOMContentLoaded", function() {
            const tabs = document.querySelectorAll('.tab-btn');
            const contents = document.querySelectorAll('[role="tabpanel"]');

            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    const target = document.querySelector(tab.dataset.tabsTarget);
                    contents.forEach(c => c.classList.add('hidden'));
                    tabs.forEach(t => {
                        t.classList.remove('text-indigo-600', 'border-indigo-600');
                        t.classList.add('border-transparent', 'hover:text-gray-600',
                            'hover:border-gray-300');
                        t.setAttribute('aria-selected', 'false');
                    });
                    target.classList.remove('hidden');
                    tab.classList.remove('border-transparent', 'hover:text-gray-600',
                        'hover:border-gray-300');
                    tab.classList.add('text-indigo-600', 'border-indigo-600');
                    tab.setAttribute('aria-selected', 'true');

                    if (tab.id === 'legal-tab') {
                        resizeAllCanvases();
                    }
                });
            });
        });

        // --- 2. SIGNATURE PAD LOGIC ---
        const pads = {};
        const canvasIds = ['sig_ortu', 'sig_guru', 'sig_konsultan', 'sig_kepsek'];

        function resizeCanvas(canvas) {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
        }

        function resizeAllCanvases() {
            canvasIds.forEach(id => {
                const canvas = document.getElementById(id);
                if (canvas.offsetWidth > 0) {
                    // Cek jika canvas kosong baru di resize untuk menghindari hilangnya data saat tab switch
                    if (!pads[id] || pads[id].isEmpty()) {
                        resizeCanvas(canvas);
                    }
                }
            });
        }

        document.addEventListener("DOMContentLoaded", function() {
            canvasIds.forEach(id => {
                const canvas = document.getElementById(id);
                resizeCanvas(canvas);
                pads[id] = new SignaturePad(canvas, {
                    backgroundColor: 'rgba(255, 255, 255, 0)',
                    penColor: "rgb(0, 0, 0)"
                });
            });

            window.addEventListener("resize", resizeAllCanvases);

            document.querySelectorAll('.clear-sig-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    pads[targetId].clear();
                });
            });
        });

        // --- 3. SUBMIT HANDLER ---
        function submitForm(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Simpan Raport?',
                text: "Pastikan semua data dan tanda tangan sudah terisi dengan benar.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Simpan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    canvasIds.forEach(id => {
                        if (!pads[id].isEmpty()) {
                            document.getElementById('input_' + id).value = pads[id].toDataURL('image/png');
                        }
                    });
                    document.getElementById('reportForm').submit();
                }
            });
        }

        // --- 4. AI: GENERATE NARASI (BERDASARKAN NILAI) ---
        function generateNarrativeAI(category, targetId) {
            const studentName = document.getElementById('student_name').value;
            const textArea = document.getElementById(targetId);
            const loading = document.getElementById('loading_' + targetId);

            // Ambil semua radio button yang terceklis di halaman ini
            let scoresContext = [];
            document.querySelectorAll('input[type=radio]:checked').forEach(radio => {
                let row = radio.closest('.score-row');
                if (row) {
                    let matName = row.querySelector('.material-name').innerText;
                    scoresContext.push(`${matName}: ${radio.value}`);
                }
            });

            let scoreString = scoresContext.length > 0 ? scoresContext.join(', ') :
                "Belum ada nilai spesifik (Nilai Kosong).";

            // Tampilkan Loading
            loading.classList.remove('hidden');
            textArea.disabled = true;

            fetch("{{ route('generate.narrative') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        student_name: studentName,
                        category: category,
                        score_data: scoreString // Kirim data nilai sebagai konteks
                    })
                })
                .then(res => res.json())
                .then(data => {
                    loading.classList.add('hidden');
                    textArea.disabled = false;
                    if (data.status === 'success') {
                        textArea.value = data.text;
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Narasi berhasil dibuat oleh AI!',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                })
                .catch(err => {
                    loading.classList.add('hidden');
                    textArea.disabled = false;
                    console.error(err);
                    Swal.fire('Error', 'Gagal menghubungi server AI.', 'error');
                });
        }

        // --- 5. AI: GENERATE KESIMPULAN (BERDASARKAN FISIK) ---
        function generateConclusionAI() {
            const studentName = document.getElementById('student_name').value;
            const textArea = document.getElementById('info_perkembangan');
            const loading = document.getElementById('loading_info_perkembangan');

            // Ambil Data Fisik
            const berat = document.getElementById('berat_badan').value || 'Tidak diisi';
            const tinggi = document.getElementById('tinggi_badan').value || 'Tidak diisi';
            const lingkar = document.getElementById('lingkar_kepala').value || 'Tidak diisi';

            // Ambil Data Kesehatan (Select Options)
            let healthData = [];
            document.querySelectorAll('.health-select').forEach(select => {
                // Ambil label sebelumnya (nama aspek kesehatan)
                let label = select.closest('div').querySelector('span').innerText;
                healthData.push(`${label}: ${select.value}`);
            });
            let healthString = healthData.join(', ');

            // Buat konteks data khusus kesimpulan
            let physicalContext =
                `Berat Badan: ${berat}kg, Tinggi Badan: ${tinggi}cm, Lingkar Kepala: ${lingkar}cm. Kondisi Kesehatan: ${healthString}`;

            loading.classList.remove('hidden');
            textArea.disabled = true;

            fetch("{{ route('generate.narrative') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        student_name: studentName,
                        category: 'Kesimpulan Perkembangan Fisik dan Kesehatan', // Kategori khusus agar prompt AI menyesuaikan
                        score_data: physicalContext
                    })
                })
                .then(res => res.json())
                .then(data => {
                    loading.classList.add('hidden');
                    textArea.disabled = false;
                    if (data.status === 'success') {
                        textArea.value = data.text;
                        Swal.fire({
                            icon: 'success',
                            title: 'Selesai',
                            text: 'Kesimpulan fisik berhasil dibuat!',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                })
                .catch(err => {
                    loading.classList.add('hidden');
                    textArea.disabled = false;
                    Swal.fire('Error', 'Gagal menghubungi server.', 'error');
                });
        }
    </script>
</x-app-layout>
