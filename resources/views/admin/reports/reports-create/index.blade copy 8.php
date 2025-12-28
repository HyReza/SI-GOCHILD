<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Buat Raport Baru') }}
            </h2>
            <div class="flex space-x-3">
                <a href="{{ route('reports.selectPeriod', $student->id) }}"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none transition ease-in-out duration-150">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        textarea {
            overflow: hidden;
            resize: none;
            min-height: 120px;
            transition: height 0.2s ease;
        }

        .file-preview-container img {
            max-height: 150px;
            width: 100%;
            object-fit: cover;
            border-radius: 0.5rem;
        }

        .tab-btn.active {
            color: #4f46e5;
            border-bottom-width: 2px;
            border-color: #4f46e5;
        }

        .score-row:hover {
            background-color: #f9fafb;
        }
    </style>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <form action="{{ route('reports.store') }}" method="POST" enctype="multipart/form-data" id="reportForm">
                @csrf
                <input type="hidden" name="student_id" id="student_id" value="{{ $student->id }}">
                <input type="hidden" id="student_name" value="{{ $student->student_name }}">
                <input type="hidden" name="start_date" value="{{ $startDate }}">
                <input type="hidden" name="end_date" value="{{ $endDate }}">
                <textarea name="attendance_summary" style="display:none;">{{ json_encode($attendanceSummary) }}</textarea>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div class="col-span-1 flex items-center space-x-4 border-r border-gray-100 pr-4">
                            <div class="flex-shrink-0">
                                <img class="h-16 w-16 rounded-full object-cover border-2 border-indigo-100"
                                    src="https://ui-avatars.com/api/?name={{ $student->student_name }}&background=random"
                                    alt="Foto Siswa">
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">{{ $student->student_name }}</h3>
                                <p class="text-sm text-gray-500">{{ $student->student_number }}</p>
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 mt-1">Aktif</span>
                            </div>
                        </div>
                        <div class="col-span-3 grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Judul
                                    Laporan</label>
                                <input type="text" name="report_title" value="Laporan Capaian Pembelajaran"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Semester</label>
                                <select name="semester"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="1 (Ganjil)">Semester 1 (Ganjil)</option>
                                    <option value="2 (Genap)">Semester 2 (Genap)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tanggal
                                    Raport</label>
                                <input type="date" name="report_date" value="{{ date('Y-m-d') }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-6 border-b border-gray-200 bg-white rounded-t-xl shadow-sm">
                    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="myTab" role="tablist">
                        <li class="mr-2">
                            <button type="button"
                                class="inline-block p-4 rounded-t-lg border-b-2 text-indigo-600 border-indigo-600 tab-btn"
                                id="profile-tab" data-tabs-target="#profile">
                                <i class="fas fa-book-open mr-2"></i> 1. Capaian & Narasi
                            </button>
                        </li>
                        <li class="mr-2">
                            <button type="button"
                                class="inline-block p-4 rounded-t-lg border-b-2 border-transparent hover:text-gray-600 tab-btn"
                                id="health-tab" data-tabs-target="#health">
                                <i class="fas fa-heartbeat mr-2"></i> 2. Fisik & Kesehatan
                            </button>
                        </li>
                        <li class="mr-2">
                            <button type="button"
                                class="inline-block p-4 rounded-t-lg border-b-2 border-transparent hover:text-gray-600 tab-btn"
                                id="legal-tab" data-tabs-target="#legal">
                                <i class="fas fa-file-signature mr-2"></i> 3. Pengesahan
                            </button>
                        </li>
                    </ul>
                </div>

                <div id="myTabContent">

                    <div class="rounded-lg bg-white shadow-sm border border-gray-100 p-6" id="profile"
                        role="tabpanel">

                        <div class="border border-gray-200 rounded-xl overflow-hidden mb-8">
                            <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex justify-between items-center cursor-pointer hover:bg-gray-100 transition"
                                onclick="document.getElementById('checklistArea').classList.toggle('hidden')">
                                <h3 class="font-bold text-gray-800">1. Detail Penilaian Kurikulum (Checklist)</h3>
                                <div class="flex items-center text-sm text-gray-500">
                                    <span class="mr-2">Buka/Tutup</span>
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </div>
                            <div id="checklistArea" class="max-h-[500px] overflow-y-auto bg-white">
                                <ul class="divide-y divide-gray-100">
                                    @foreach ($themes as $theme)
                                        <li class="bg-white">
                                            <div class="px-4 py-3">
                                                <h4
                                                    class="text-sm font-bold text-indigo-700 mb-2 uppercase tracking-wide bg-indigo-50 px-2 py-1 rounded inline-block">
                                                    {{ $theme->theme_name }}</h4>
                                                @foreach ($theme->subThemes as $subTheme)
                                                    <div class="ml-2 mb-3 border-l-2 border-indigo-100 pl-3 mt-2">
                                                        <h5
                                                            class="text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">
                                                            {{ $subTheme->sub_theme_name }}</h5>
                                                        @foreach ($subTheme->materials as $material)
                                                            @php $score = $calculatedScores[$material->id] ?? null; @endphp
                                                            <div
                                                                class="flex flex-col md:flex-row md:items-center justify-between py-1.5 border-b border-gray-50 last:border-0 score-row transition">
                                                                <div class="w-full md:w-2/3 flex items-center">
                                                                    <span
                                                                        class="text-sm text-gray-700 material-name mr-2">{{ $material->material_name }}</span>
                                                                    @if (is_null($score))
                                                                        <span
                                                                            class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-red-50 text-red-600 border border-red-100">Belum
                                                                            ada data</span>
                                                                    @endif
                                                                </div>
                                                                <div
                                                                    class="flex space-x-1 mt-1 md:mt-0 w-full md:w-1/3 justify-end">
                                                                    @foreach (['BB', 'MB', 'BSH', 'BSB'] as $opt)
                                                                        <label class="cursor-pointer relative">
                                                                            <input type="radio"
                                                                                name="scores[{{ $material->id }}]"
                                                                                value="{{ $opt }}"
                                                                                class="peer sr-only score-radio"
                                                                                {{ $score == $opt ? 'checked' : '' }}
                                                                                data-material="{{ $material->material_name }}"
                                                                                data-theme-id="{{ $theme->id }}">
                                                                            <div
                                                                                class="px-3 py-1 text-[10px] font-bold rounded border border-gray-200 text-gray-400 peer-checked:bg-indigo-600 peer-checked:text-white peer-checked:border-indigo-600 transition-all hover:bg-gray-50 hover:text-gray-600">
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
                        </div>

                        <div class="space-y-6 mb-8">
                            <div class="border-b-2 border-indigo-500 pb-2">
                                <h3 class="text-xl font-bold text-gray-800"><i class="fas fa-list-alt mr-2"></i>2.
                                    Deskripsi Capaian Pembelajaran per Tema</h3>
                                <p class="text-xs text-gray-500 mt-1">Bagian ini digunakan untuk memberikan narasi
                                    spesifik terkait materi di setiap tema.</p>
                            </div>

                            @foreach ($themes as $theme)
                                <div class="bg-gray-50 rounded-xl border border-gray-200 p-5">
                                    <div class="flex justify-between items-center mb-3">
                                        <h4 class="font-bold text-gray-800 text-sm uppercase">{{ $theme->theme_name }}
                                        </h4>

                                        <button type="button"
                                            onclick="generateThemeDraft('{{ $theme->id }}', 'theme_note_{{ $theme->id }}')"
                                            class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-300 rounded text-xs font-medium text-gray-700 hover:bg-gray-100 shadow-sm transition">
                                            <i class="fas fa-magic mr-1.5 text-indigo-500"></i> Generate dari Checklist
                                        </button>
                                    </div>

                                    <textarea name="theme_notes[{{ $theme->id }}]" id="theme_note_{{ $theme->id }}" rows="4"
                                        oninput="autoResize(this)"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-3 bg-white"
                                        placeholder="Narasi untuk tema {{ $theme->theme_name }}..."></textarea>
                                </div>
                            @endforeach
                        </div>

                        <div class="space-y-8 mb-8">
                            <div class="border-b-2 border-indigo-500 pb-2 mb-4">
                                <h3 class="text-xl font-bold text-gray-800"><i class="fas fa-pen-fancy mr-2"></i>3.
                                    Catatan Narasi Elemen (CP)</h3>
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
                                <div
                                    class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition">
                                    <div
                                        class="px-4 py-3 bg-gray-50 border-b border-gray-200 flex flex-wrap justify-between items-center gap-2">
                                        <h3 class="font-bold text-gray-900 flex items-center">
                                            <i class="{{ $sec['icon'] }} {{ $sec['color'] }} mr-2"></i>
                                            {{ $sec['title'] }}
                                        </h3>
                                        <button type="button"
                                            onclick="generateNarrativeAI('{{ $sec['title'] }}', '{{ $sec['id'] }}')"
                                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition-all">
                                            <i class="fas fa-magic mr-1.5"></i> Generate AI
                                        </button>
                                    </div>
                                    <div class="p-5">
                                        <div id="loading_{{ $sec['id'] }}"
                                            class="hidden mb-3 text-indigo-600 text-xs font-semibold flex items-center bg-indigo-50 p-2 rounded border border-indigo-100">
                                            <i class="fas fa-spinner fa-spin mr-2"></i> Sedang menyusun narasi...
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div>
                                                <textarea name="{{ $sec['id'] }}" id="{{ $sec['id'] }}" rows="5" oninput="autoResize(this)"
                                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-3 leading-relaxed"
                                                    placeholder="Narasi umum aspek ini..."></textarea>
                                            </div>
                                            <div
                                                class="bg-gray-50 p-4 rounded-lg border border-dashed border-gray-300 flex flex-col justify-center text-center relative group">
                                                <label
                                                    class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wide">Dokumentasi
                                                    Foto</label>

                                                <div id="upload_box_{{ $sec['photo'] }}">
                                                    <div class="mt-2">
                                                        <i
                                                            class="fas fa-cloud-upload-alt text-3xl text-gray-300 mb-2 group-hover:text-indigo-400 transition"></i>
                                                        <input type="file" name="{{ $sec['photo'] }}"
                                                            id="{{ $sec['photo'] }}" accept="image/*"
                                                            onchange="handleFilePreview(this, 'preview_container_{{ $sec['photo'] }}', 'upload_box_{{ $sec['photo'] }}')"
                                                            class="block w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition-colors cursor-pointer" />
                                                    </div>
                                                </div>

                                                <div id="preview_container_{{ $sec['photo'] }}"
                                                    class="hidden relative file-preview-container">
                                                    <img src="" id="img_prev_{{ $sec['photo'] }}"
                                                        alt="Preview"
                                                        class="shadow-sm rounded-md border border-gray-200">
                                                    <button type="button"
                                                        onclick="removeFile('{{ $sec['photo'] }}', 'preview_container_{{ $sec['photo'] }}', 'upload_box_{{ $sec['photo'] }}')"
                                                        class="absolute top-2 right-2 bg-white text-red-600 p-1.5 rounded-full shadow-md hover:bg-gray-100 transition border border-gray-200">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="bg-indigo-50 rounded-xl border border-indigo-200 p-6 mb-8">
                            <h3 class="text-lg font-bold text-indigo-900 mb-4 flex items-center">
                                <i class="fas fa-chalkboard-teacher mr-2"></i>4. Catatan dan Rekomendasi Guru
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Catatan Guru
                                        (Umum)</label>
                                    <textarea name="teacher_notes" rows="4" oninput="autoResize(this)"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-3 bg-white"
                                        placeholder="Contoh: Ananda sangat antusias dalam kegiatan seni, namun perlu dorongan lebih saat kegiatan fisik..."></textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Rekomendasi / Tindak
                                        Lanjut</label>
                                    <textarea name="recommendations" rows="4" oninput="autoResize(this)"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-3 bg-white"
                                        placeholder="Contoh: Mohon Ayah/Bunda melatih kemandirian ananda di rumah, seperti memakai sepatu sendiri..."></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-bold text-gray-800"><i
                                        class="fas fa-comments mr-2 text-indigo-500"></i>5. Refleksi Orang Tua</h3>
                                <button type="button"
                                    onclick="generateNarrativeAI('Saran Refleksi Orang Tua', 'refleksi_ortu')"
                                    class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 hover:underline">
                                    <i class="fas fa-lightbulb mr-1"></i> Pertanyaan Pemantik AI
                                </button>
                            </div>
                            <textarea name="refleksi_ortu" id="refleksi_ortu" rows="3" oninput="autoResize(this)"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-3"
                                placeholder="Tuliskan catatan atau pertanyaan untuk orang tua..."></textarea>
                        </div>

                    </div>

                    <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="health" role="tabpanel">

                        <div class="mb-6">
                            @if ($lastMeasurement)
                                <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-r shadow-sm">
                                    <div class="flex">
                                        <div class="flex-shrink-0"><i
                                                class="fas fa-check-circle text-green-500 text-xl"></i></div>
                                        <div class="ml-3 w-full">
                                            <h3 class="text-sm font-bold text-green-800">Data Pengukuran Terakhir
                                                Ditemukan</h3>
                                            <p class="text-sm text-green-700 mt-1">
                                                Data diambil dari pengukuran tanggal
                                                <strong>{{ \Carbon\Carbon::parse($lastMeasurement->date_measurement)->translatedFormat('d F Y') }}</strong>.
                                            </p>

                                            <div class="mt-3 grid grid-cols-1 md:grid-cols-3 gap-2 text-xs">
                                                @php
                                                    // Ambil data JSON Calculation Result
                                                    $calc = $lastMeasurement->calculation_results;
                                                    if (is_string($calc)) {
                                                        $calc = json_decode($calc, true);
                                                    }
                                                    $calc = $calc ?? [];
                                                @endphp

                                                <div class="bg-white p-2 rounded border border-green-200">
                                                    <span class="text-gray-500 block">BB/U (Berat/Umur)</span>
                                                    <span
                                                        class="font-bold text-green-900">{{ $calc['BB/U'] ?? '-' }}</span>
                                                </div>
                                                <div class="bg-white p-2 rounded border border-green-200">
                                                    <span class="text-gray-500 block">TB/U (Tinggi/Umur)</span>
                                                    <span
                                                        class="font-bold text-green-900">{{ $calc['PB/U'] ?? ($calc['TB/U'] ?? '-') }}</span>
                                                </div>
                                                <div class="bg-white p-2 rounded border border-green-200">
                                                    <span class="text-gray-500 block">BB/TB (Proporsi)</span>
                                                    <span
                                                        class="font-bold text-green-900">{{ $calc['PB/BB'] ?? ($calc['BB/TB'] ?? '-') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r shadow-sm">
                                    <div class="flex">
                                        <div class="flex-shrink-0"><i
                                                class="fas fa-exclamation-triangle text-yellow-400 text-xl"></i></div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-bold text-yellow-800">Data Pengukuran Belum
                                                Tersedia</h3>
                                            <p class="text-sm text-yellow-700 mt-1">Silakan input manual di bawah.</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="bg-white shadow-lg sm:rounded-xl p-6 border border-gray-200">
                                <h3 class="text-lg font-bold text-gray-900 border-b pb-3 mb-5"><i
                                        class="fas fa-weight mr-2 text-indigo-500"></i>Data Pertumbuhan</h3>

                                <div class="grid grid-cols-3 gap-4 mb-6">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Berat
                                            (kg)</label>
                                        <input type="number" step="0.1" name="berat_badan" id="berat_badan"
                                            value="{{ old('berat_badan', $prefillPhysical['weight'] > 0 ? $prefillPhysical['weight'] : '') }}"
                                            class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md p-2 bg-gray-50 font-semibold">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tinggi
                                            (cm)</label>
                                        <input type="number" step="0.1" name="tinggi_badan" id="tinggi_badan"
                                            value="{{ old('tinggi_badan', $prefillPhysical['height'] > 0 ? $prefillPhysical['height'] : '') }}"
                                            class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md p-2 bg-gray-50 font-semibold">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">L. Kpl
                                            (cm)</label>
                                        <input type="number" step="0.1" name="lingkar_kepala"
                                            id="lingkar_kepala"
                                            value="{{ old('lingkar_kepala', $prefillPhysical['head'] > 0 ? $prefillPhysical['head'] : '') }}"
                                            class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md p-2 bg-gray-50 font-semibold">
                                    </div>
                                </div>

                                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                    <div class="flex justify-between items-center mb-2">
                                        <label class="block text-sm font-bold text-gray-700">Kesimpulan
                                            Perkembangan</label>
                                        <button type="button" onclick="generateConclusionAI()"
                                            class="text-xs bg-indigo-100 text-indigo-700 px-2 py-1 rounded hover:bg-indigo-200 transition-colors font-semibold border border-indigo-200"><i
                                                class="fas fa-robot mr-1"></i> Perbaiki dengan AI</button>
                                    </div>
                                    <div id="loading_info_perkembangan"
                                        class="hidden text-xs font-semibold text-indigo-600 mb-2"><i
                                            class="fas fa-spinner fa-spin mr-1"></i> Menganalisa data fisik...</div>
                                    <textarea name="info_perkembangan" id="info_perkembangan" rows="5" oninput="autoResize(this)"
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md p-2"
                                        placeholder="Kesimpulan perkembangan...">{{ old('info_perkembangan', $growthNarration) }}</textarea>
                                </div>

                                <div class="mt-5">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Upload Grafik KMS
                                        (Opsional)</label>
                                    <div id="upload_box_kms">
                                        <input type="file" name="development_info_photo"
                                            id="development_info_photo" accept="image/*"
                                            onchange="handleFilePreview(this, 'preview_container_kms', 'upload_box_kms')"
                                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200" />
                                    </div>
                                    <div id="preview_container_kms"
                                        class="hidden relative file-preview-container mt-2">
                                        <img src="" id="img_prev_development_info_photo" alt="Preview KMS"
                                            class="w-full h-auto rounded shadow-sm">
                                        <button type="button"
                                            onclick="removeFile('development_info_photo', 'preview_container_kms', 'upload_box_kms')"
                                            class="absolute top-2 right-2 bg-red-600 text-white p-2 rounded-full shadow hover:bg-red-700">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white shadow-lg sm:rounded-xl p-6 border border-gray-200">
                                <h3 class="text-lg font-bold text-gray-900 border-b pb-3 mb-5"><i
                                        class="fas fa-notes-medical mr-2 text-red-500"></i>Pemeriksaan Fisik</h3>
                                <div class="space-y-4">
                                    @foreach ($healthItems as $index => $item)
                                        <div
                                            class="flex items-center justify-between pb-2 border-b border-gray-100 last:border-0 hover:bg-gray-50 px-2 rounded transition">
                                            <span class="text-sm font-medium text-gray-700">{{ $item }}</span>
                                            <select name="health[{{ $item }}]"
                                                id="health_{{ $index }}"
                                                class="health-select block w-40 pl-3 pr-8 py-1 text-sm border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 rounded-md shadow-sm">
                                                <option value="Baik" selected>Baik</option>
                                                <option value="Cukup">Cukup</option>
                                                <option value="Perlu Perhatian">Perlu Perhatian</option>
                                                <option value="Perlu Tindakan Medis">Perlu Tindakan Medis</option>
                                            </select>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="legal" role="tabpanel">
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
                                                'label' => 'Konsultan',
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
                                                    data-target="{{ $sig['id'] }}" title="Hapus Tanda Tangan"><i
                                                        class="fas fa-trash-alt"></i></button>
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
                                onclick="confirmBack('{{ route('reports.selectPeriod', $student->id) }}')"
                                class="bg-white py-3 px-6 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">Batal</button>
                            <button type="submit" onclick="submitForm(event)"
                                class="inline-flex justify-center py-3 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all transform hover:scale-105"><i
                                    class="fas fa-save mr-2"></i> Simpan Raport</button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <script>
        // --- 1. HANDLE FILE PREVIEW & REMOVE ---
        function handleFilePreview(input, previewContainerId, uploadBoxId) {
            const file = input.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById(previewContainerId).querySelector('img').src = e.target.result;
                    document.getElementById(previewContainerId).classList.remove('hidden');
                    document.getElementById(uploadBoxId).classList.add('hidden');
                }
                reader.readAsDataURL(file);
            }
        }

        function removeFile(inputId, previewContainerId, uploadBoxId) {
            document.getElementById(inputId).value = "";
            document.getElementById(previewContainerId).classList.add('hidden');
            document.getElementById(uploadBoxId).classList.remove('hidden');
        }

        // --- 2. AUTO RESIZE TEXTAREA ---
        function autoResize(el) {
            el.style.height = 'auto';
            el.style.height = el.scrollHeight + 'px';
        }

        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll('textarea').forEach(tx => autoResize(tx));
        });

        // --- 3. [FITUR BARU] GENERATE DRAFT PER TEMA (RULE BASED) ---
        function generateThemeDraft(themeId, targetId) {
            const textArea = document.getElementById(targetId);
            // Pilih radio button yang punya data-theme-id ini dan tercentang
            const scores = document.querySelectorAll(`.score-radio[data-theme-id="${themeId}"]:checked`);

            let draftText = "";

            const predicates = {
                'BB': 'perlu bimbingan dalam',
                'MB': 'mulai mampu',
                'BSH': 'mampu',
                'BSB': 'sangat baik dalam'
            };

            if (scores.length === 0) {
                Swal.fire('Info', 'Belum ada nilai checklist yang dipilih untuk tema ini.', 'info');
                return;
            }

            scores.forEach(radio => {
                const materialName = radio.getAttribute('data-material');
                const score = radio.value;
                const verb = predicates[score] || 'berkembang dalam';

                // Kalimat: "Ananda [predikat] [materi]."
                draftText += `- Ananda ${verb} ${materialName}.\n`;
            });

            // Masukkan ke Textarea
            const currentText = textArea.value;
            textArea.value = currentText ? currentText + "\n" + draftText : "Pada tema ini:\n" + draftText;
            autoResize(textArea);

            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Draft tema dibuat',
                showConfirmButton: false,
                timer: 1500
            });
        }

        // --- 4. GENERATE AI (EXISTING) ---
        function generateNarrativeAI(category, targetId) {
            const studentName = document.getElementById('student_name').value;
            const textArea = document.getElementById(targetId);
            const loading = document.getElementById('loading_' + targetId); // Pastikan ID loading ada di HTML (opsional)

            let scoresContext = [];
            document.querySelectorAll('.score-radio:checked').forEach(radio => {
                scoresContext.push(`${radio.getAttribute('data-material')}: ${radio.value}`);
            });
            let scoreString = scoresContext.length > 0 ? scoresContext.join(', ') : "Belum ada nilai.";

            // Show loading if element exists
            if (loading) loading.classList.remove('hidden');
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
                    if (loading) loading.classList.add('hidden');
                    textArea.disabled = false;
                    if (data.status === 'success') {
                        textArea.value = data.text;
                        autoResize(textArea);
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Selesai',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                }).catch(() => {
                    if (loading) loading.classList.add('hidden');
                    textArea.disabled = false;
                    Swal.fire('Error', 'Gagal koneksi.', 'error');
                });
        }

        // --- 5. TABS ---
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.tab-btn').forEach(b => {
                    b.classList.remove('text-indigo-600', 'border-indigo-600');
                    b.classList.add('border-transparent', 'hover:text-gray-600');
                });
                document.querySelectorAll('[role="tabpanel"]').forEach(p => p.classList.add('hidden'));

                btn.classList.add('text-indigo-600', 'border-indigo-600');
                btn.classList.remove('border-transparent');
                document.querySelector(btn.dataset.tabsTarget).classList.remove('hidden');

                if (btn.id === 'legal-tab') resizeAllCanvases();
            });
        });

        // --- 6. SIGNATURE ---
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
                if (canvas.offsetWidth > 0 && (!pads[id] || pads[id].isEmpty())) {
                    resizeCanvas(canvas);
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
                    pads[this.getAttribute('data-target')].clear();
                });
            });
        });

        // --- 7. SUBMIT ---
        function confirmBack(url) {
            Swal.fire({
                title: 'Batalkan?',
                text: "Data yang sudah diisi akan hilang.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Ya, Kembali'
            }).then((result) => {
                if (result.isConfirmed) window.location.href = url;
            });
        }

        function submitForm(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Simpan Raport?',
                text: "Pastikan data sudah benar.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Simpan!'
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

        // Dummy function for Fisik AI if needed
        function generateConclusionAI() {
            generateNarrativeAI('Kesimpulan Fisik', 'info_perkembangan');
        }
    </script>
</x-app-layout>
