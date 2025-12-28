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

                <div class="mb-4 border-b border-gray-200 bg-white rounded-t-lg">
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

                    <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="profile" role="tabpanel"
                        aria-labelledby="profile-tab">

                        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-700">
                                        Nilai yang terpilih otomatis adalah <strong>modus (nilai terbanyak)</strong>
                                        dari laporan harian. Anda dapat mengubahnya secara manual sebelum melakukan
                                        Generate AI.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white shadow overflow-hidden sm:rounded-md mb-6">
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
                                                        @php $score = $calculatedScores[$material->id] ?? null; @endphp
                                                        <div
                                                            class="flex flex-col md:flex-row md:items-center justify-between py-2 border-b border-gray-100 last:border-0 score-row hover:bg-gray-50">
                                                            <span
                                                                class="text-sm text-gray-600 w-full md:w-1/2 material-name">{{ $material->material_name }}</span>
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
                                                                            class="px-3 py-1 text-xs rounded-md border border-gray-300 text-gray-600 peer-checked:bg-indigo-600 peer-checked:text-white peer-checked:border-indigo-600 transition-all">
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

                        <div class="space-y-6">

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
                                <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                                    <div
                                        class="px-4 py-4 sm:px-6 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                                            <i class="{{ $sec['icon'] }} {{ $sec['color'] }} mr-2"></i>
                                            {{ $sec['title'] }}
                                        </h3>
                                        <button type="button"
                                            onclick="generateAI('{{ $sec['title'] }}', '{{ $sec['id'] }}')"
                                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-gradient-to-r from-purple-500 to-indigo-600 hover:from-purple-600 hover:to-indigo-700 shadow-sm focus:outline-none">
                                            <svg class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                                            </svg>
                                            Generate AI
                                        </button>
                                    </div>
                                    <div class="p-6">
                                        <div id="loading_{{ $sec['id'] }}"
                                            class="hidden mb-2 text-indigo-600 text-sm italic flex items-center">
                                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-indigo-600"
                                                xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                                    stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                </path>
                                            </svg>
                                            Sedang menulis narasi...
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Narasi
                                                    Capaian</label>
                                                <textarea name="{{ $sec['id'] }}" id="{{ $sec['id'] }}" rows="5"
                                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                                    placeholder="Tulis manual atau gunakan tombol Generate AI..."></textarea>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Upload
                                                    Dokumentasi (Opsional)</label>
                                                <div
                                                    class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                                    <div class="space-y-1 text-center">
                                                        <svg class="mx-auto h-12 w-12 text-gray-400"
                                                            stroke="currentColor" fill="none" viewBox="0 0 48 48"
                                                            aria-hidden="true">
                                                            <path
                                                                d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 005.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                                stroke-width="2" stroke-linecap="round"
                                                                stroke-linejoin="round" />
                                                        </svg>
                                                        <div class="flex text-sm text-gray-600">
                                                            <label for="{{ $sec['photo'] }}"
                                                                class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none">
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

                            <div class="bg-indigo-50 shadow sm:rounded-lg overflow-hidden border border-indigo-100">
                                <div class="px-4 py-4 sm:px-6 flex justify-between items-center bg-indigo-100">
                                    <h3 class="text-lg leading-6 font-medium text-indigo-900">Refleksi Orang Tua</h3>
                                    <button type="button"
                                        onclick="generateAI('Saran Refleksi Orang Tua', 'refleksi_ortu')"
                                        class="text-xs text-indigo-700 hover:text-indigo-900 underline">Bantu buatkan
                                        pertanyaan pemantik</button>
                                </div>
                                <div class="p-6">
                                    <div id="loading_refleksi_ortu"
                                        class="hidden text-sm italic text-indigo-600 mb-2">Loading...</div>
                                    <textarea name="refleksi_ortu" id="refleksi_ortu" rows="3"
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"></textarea>
                                    <div class="mt-3">
                                        <label class="block text-sm font-medium text-gray-700">Foto Kegiatan di Rumah
                                            (Opsional)</label>
                                        <input type="file" name="parent_reflection_photo"
                                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="health" role="tabpanel"
                        aria-labelledby="health-tab">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-white shadow sm:rounded-lg p-6">
                                <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Data Pertumbuhan</h3>
                                <div class="grid grid-cols-3 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Berat (kg)</label>
                                        <input type="number" step="0.1" name="berat_badan"
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Tinggi (cm)</label>
                                        <input type="number" step="0.1" name="tinggi_badan"
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Lingkar Kpl (cm)</label>
                                        <input type="number" step="0.1" name="lingkar_kepala"
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                </div>
                                <div>
                                    <div class="flex justify-between items-center mb-1">
                                        <label class="block text-sm font-medium text-gray-700">Kesimpulan
                                            Pertumbuhan</label>
                                        <button type="button"
                                            onclick="generateAI('Kesimpulan Tumbuh Kembang', 'info_perkembangan')"
                                            class="text-xs text-indigo-600 hover:underline">Generate
                                            Kesimpulan</button>
                                    </div>
                                    <div id="loading_info_perkembangan" class="hidden text-xs italic text-indigo-600">
                                        Loading...</div>
                                    <textarea name="info_perkembangan" id="info_perkembangan" rows="4"
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"></textarea>
                                </div>
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700">Upload Grafik KMS
                                        (Opsional)</label>
                                    <input type="file" name="development_info_photo"
                                        class="mt-1 block w-full text-sm" />
                                </div>
                            </div>

                            <div class="bg-white shadow sm:rounded-lg p-6">
                                <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Pemeriksaan Fisik</h3>
                                <div class="space-y-3">
                                    @foreach ($healthItems as $item)
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-gray-700">{{ $item }}</span>
                                            <select name="health[{{ $item }}]"
                                                class="block w-40 pl-3 pr-10 py-1 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
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
                        <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                            <div class="bg-gray-800 px-4 py-3 border-b border-gray-200 text-center">
                                <h3 class="text-lg leading-6 font-medium text-white">Lembar Pengesahan Digital</h3>
                                <p class="text-xs text-gray-400 mt-1">Silakan tanda tangan pada area yang disediakan.
                                </p>
                            </div>
                            <div class="p-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-8">

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
                                        <div class="flex flex-col items-center">
                                            <label
                                                class="block text-sm font-bold text-gray-700 mb-2">{{ $sig['label'] }}</label>
                                            <div class="relative group">
                                                <canvas id="{{ $sig['id'] }}"
                                                    class="border-2 border-dashed border-gray-400 rounded-lg bg-white cursor-crosshair w-full max-w-xs h-48"></canvas>
                                                <button type="button"
                                                    class="absolute top-2 right-2 text-red-500 hover:text-red-700 bg-white rounded-full p-1 shadow-sm opacity-0 group-hover:opacity-100 transition-opacity clear-sig-btn"
                                                    data-target="{{ $sig['id'] }}">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                            <input type="hidden" name="{{ $sig['input'] }}"
                                                id="input_{{ $sig['id'] }}">
                                            <input type="text" name="{{ $sig['name_input'] }}"
                                                value="{{ $sig['default_name'] }}"
                                                placeholder="{{ $sig['placeholder'] }}"
                                                class="mt-3 text-center focus:ring-indigo-500 focus:border-indigo-500 block w-full max-w-xs shadow-sm sm:text-sm border-gray-300 rounded-md">
                                        </div>
                                    @endforeach

                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end space-x-3">
                            <button type="button"
                                class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Batal
                            </button>
                            <button type="submit" onclick="submitForm(event)"
                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                </svg>
                                Simpan Raport
                            </button>
                        </div>

                    </div>

                </div>
            </form>
        </div>
    </div>

    <script>
        // --- 1. TAB SWITCHING LOGIC (Vanilla JS for Tailwind) ---
        document.addEventListener("DOMContentLoaded", function() {
            const tabs = document.querySelectorAll('.tab-btn');
            const contents = document.querySelectorAll('[role="tabpanel"]');

            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    const target = document.querySelector(tab.dataset.tabsTarget);

                    // Hide all contents
                    contents.forEach(c => c.classList.add('hidden'));
                    // Remove active style from all tabs
                    tabs.forEach(t => {
                        t.classList.remove('text-indigo-600', 'border-indigo-600');
                        t.classList.add('border-transparent', 'hover:text-gray-600',
                            'hover:border-gray-300');
                        t.setAttribute('aria-selected', 'false');
                    });

                    // Show target content
                    target.classList.remove('hidden');
                    // Add active style to clicked tab
                    tab.classList.remove('border-transparent', 'hover:text-gray-600',
                        'hover:border-gray-300');
                    tab.classList.add('text-indigo-600', 'border-indigo-600');
                    tab.setAttribute('aria-selected', 'true');

                    // Trigger canvas resize just in case (hidden canvas often has 0 width)
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
                // Only resize if visible and has width
                if (canvas.offsetWidth > 0) {
                    // Note: resizing clears canvas. In edit mode, we would need to redraw data.
                    // For create mode, it's fine, but let's check if empty to avoid clearing user input on tab switch
                    if (pads[id] && pads[id].isEmpty()) {
                        resizeCanvas(canvas);
                    } else if (!pads[id]) {
                        resizeCanvas(canvas); // Initial resize
                    }
                }
            });
        }

        document.addEventListener("DOMContentLoaded", function() {
            canvasIds.forEach(id => {
                const canvas = document.getElementById(id);
                resizeCanvas(canvas); // Initial setup
                pads[id] = new SignaturePad(canvas, {
                    backgroundColor: 'rgba(255, 255, 255, 0)', // Transparan
                    penColor: "rgb(0, 0, 0)"
                });
            });

            // Handle Window Resize
            window.addEventListener("resize", resizeAllCanvases);

            // Clear Buttons
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

            // Konfirmasi SweetAlert
            Swal.fire({
                title: 'Simpan Raport?',
                text: "Pastikan semua data dan tanda tangan sudah terisi dengan benar.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5', // Indigo 600
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Simpan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Convert Signatures to Base64
                    canvasIds.forEach(id => {
                        if (!pads[id].isEmpty()) {
                            document.getElementById('input_' + id).value = pads[id].toDataURL('image/png');
                        }
                    });

                    document.getElementById('reportForm').submit();
                }
            });
        }

        // --- 4. AI GENERATE LOGIC ---
        function generateAI(category, targetId) {
            const studentName = document.getElementById('student_name').value;
            const textArea = document.getElementById(targetId);
            const loading = document.getElementById('loading_' + targetId);

            // Context Aware Collection (Ambil nilai radio button)
            let scoresContext = [];
            // Select checked radios inside the relevant section (simplified for demo: get all checked)
            // For better context, we could filter based on tab/section logic if needed.
            document.querySelectorAll('input[type=radio]:checked').forEach(radio => {
                let row = radio.closest('.score-row');
                if (row) {
                    let matName = row.querySelector('.material-name').innerText;
                    scoresContext.push(`${matName}: ${radio.value}`);
                }
            });
            let scoreString = scoresContext.length > 0 ? scoresContext.join(', ') : "Belum ada nilai spesifik.";

            // UI Loading
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
                        score_data: scoreString
                    })
                })
                .then(res => res.json())
                .then(data => {
                    loading.classList.add('hidden');
                    textArea.disabled = false;

                    if (data.status === 'success') {
                        // Ketik effect (opsional) atau langsung value
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
                    Swal.fire('Error', 'Gagal menghubungi server.', 'error');
                });
        }
    </script>
</x-app-layout>
