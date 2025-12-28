<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Raport') }}
            </h2>
            <div class="flex space-x-3">
                <a href="{{ route('reports.history', $report->student_id) }}"
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

        /* Styling Radio Button Custom */
        .score-radio:checked+div {
            background-color: #4f46e5;
            color: white;
            border-color: #4f46e5;
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.3);
        }

        .material-card:hover {
            background-color: #fcfcfc;
        }

        .input-error {
            border-color: #ef4444 !important;
            background-color: #fef2f2 !important;
        }
    </style>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- ERROR HANDLING --}}
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-md shadow-sm">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-500"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Terdapat kesalahan pada inputan Anda:</h3>
                            <ul class="mt-2 list-disc list-inside text-sm text-red-700">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Menyimpan',
                            text: 'Harap periksa kembali isian form.',
                            confirmButtonColor: '#ef4444'
                        });
                    });
                </script>
            @endif

            <form action="{{ route('reports.update', $report->id) }}" method="POST" enctype="multipart/form-data"
                id="reportForm">
                @csrf
                @method('PUT')

                {{-- LOGIKA PENENTUAN NAMA PANGGILAN --}}
                @php
                    $callingName = !empty($student->nickname) ? $student->nickname : $student->student_name;
                @endphp

                <input type="hidden" name="student_id" value="{{ $student->id }}">
                <input type="hidden" id="student_calling_name" value="{{ $callingName }}">
                <input type="hidden" id="student_name" value="{{ $student->student_name }}">

                {{-- JSON Attendance Summary (Hidden) --}}
                <textarea name="attendance_summary" style="display:none;">{{ $report->attendance_summary }}</textarea>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div class="col-span-1 flex items-center space-x-4 border-r border-gray-100 pr-4">
                            <div class="flex-shrink-0">
                                @if ($student->user_photo)
                                    <img class="h-16 w-16 rounded-full object-cover border-2 border-indigo-100 shadow-sm"
                                        src="{{ asset('storage/' . $student->user_photo) }}" alt="Foto Siswa">
                                @else
                                    <img class="h-16 w-16 rounded-full object-cover border-2 border-indigo-100 shadow-sm"
                                        src="https://ui-avatars.com/api/?name={{ $student->student_name }}&background=random"
                                        alt="Foto Siswa">
                                @endif
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">{{ $student->student_name }}</h3>
                                <p class="text-sm text-gray-500">{{ $student->student_number }}</p>
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 mt-1">Edit
                                    Mode</span>
                            </div>
                        </div>
                        <div class="col-span-3 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Judul Laporan <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="report_title"
                                    value="{{ old('report_title', $report->report_title) }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Semester</label>
                                <select name="semester"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="Semester 1 (Ganjil)"
                                        {{ old('semester', $report->semester) == 'Semester 1 (Ganjil)' ? 'selected' : '' }}>
                                        Semester 1 (Ganjil)</option>
                                    <option value="Semester 2 (Genap)"
                                        {{ old('semester', $report->semester) == 'Semester 2 (Genap)' ? 'selected' : '' }}>
                                        Semester 2 (Genap)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Kelas <span
                                        class="text-red-500">*</span></label>
                                <select name="class_name" id="class_name"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    required>
                                    <option value="" disabled>-- Pilih Kelas --</option>
                                    <option value="Early Childhood Day Care A"
                                        {{ old('class_name', $report->class_name) == 'Early Childhood Day Care A' ? 'selected' : '' }}>
                                        Early Childhood Day Care A</option>
                                    <option value="Early Childhood Day Care B"
                                        {{ old('class_name', $report->class_name) == 'Early Childhood Day Care B' ? 'selected' : '' }}>
                                        Early Childhood Day Care B</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tanggal Raport <span
                                        class="text-red-500">*</span></label>
                                <input type="date" name="report_date"
                                    value="{{ old('report_date', $report->report_date->format('Y-m-d')) }}"
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

                        <div class="border border-gray-200 rounded-xl overflow-hidden mb-8 shadow-sm">
                            <div class="bg-gray-50 px-5 py-4 border-b border-gray-200 flex justify-between items-center cursor-pointer hover:bg-gray-100 transition"
                                onclick="document.getElementById('checklistArea').classList.toggle('hidden')">
                                <div>
                                    <h3 class="font-bold text-gray-800 text-lg">1. Detail Penilaian Kurikulum
                                        (Checklist)</h3>
                                    <p class="text-xs text-gray-500">Edit nilai skor per materi di bawah ini</p>
                                </div>
                                <div class="flex items-center text-sm text-gray-500">
                                    <span class="mr-2">Tampilkan/Sembunyikan</span>
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </div>

                            <div id="checklistArea" class="max-h-[800px] overflow-y-auto bg-white">
                                <ul class="divide-y divide-gray-100">
                                    @foreach ($themes as $theme)
                                        <li class="bg-white">
                                            <div class="px-5 py-4">
                                                <div class="flex items-center mb-3">
                                                    <span class="w-1.5 h-6 bg-indigo-500 rounded-full mr-2"></span>
                                                    <h4
                                                        class="text-base font-bold text-gray-800 uppercase tracking-wide">
                                                        Tema: {{ $theme->theme_name }}
                                                    </h4>
                                                </div>

                                                @foreach ($theme->subThemes as $subTheme)
                                                    <div class="ml-4 mb-4 pl-4 border-l-2 border-indigo-100">
                                                        <h5
                                                            class="text-sm font-bold text-indigo-600 mb-3 uppercase tracking-wider flex items-center">
                                                            <i class="fas fa-folder-open mr-2"></i>
                                                            {{ $subTheme->sub_theme_name }}
                                                        </h5>

                                                        @foreach ($subTheme->materials as $material)
                                                            @php
                                                                // Ambil nilai yang tersimpan
                                                                $savedScore = $savedScores[$material->id] ?? null;
                                                            @endphp

                                                            <div
                                                                class="material-card flex flex-col p-3 mb-2 rounded-lg border border-gray-100 transition hover:border-indigo-200 hover:shadow-sm">
                                                                <div
                                                                    class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                                                    <div class="w-full md:w-2/3 flex items-center">
                                                                        <i
                                                                            class="fas fa-check-circle {{ $savedScore ? 'text-green-500' : 'text-gray-300' }} mr-3 text-xs"></i>
                                                                        <div>
                                                                            <span
                                                                                class="text-sm text-gray-800 font-semibold block">{{ $material->material_name }}</span>
                                                                            @if ($savedScore)
                                                                                <span
                                                                                    class="inline-flex items-center mt-1 px-2 py-0.5 rounded text-[10px] font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                                                                    Tersimpan:
                                                                                    <b>{{ $savedScore }}</b>
                                                                                </span>
                                                                            @endif
                                                                        </div>
                                                                    </div>

                                                                    <div
                                                                        class="w-full md:w-1/3 flex justify-end items-center">
                                                                        <div
                                                                            class="flex space-x-1 bg-gray-100 p-1 rounded-lg">
                                                                            @foreach (['BB', 'MB', 'BSH', 'BSB'] as $opt)
                                                                                <label class="cursor-pointer relative">
                                                                                    <input type="radio"
                                                                                        name="scores[{{ $material->id }}]"
                                                                                        value="{{ $opt }}"
                                                                                        class="peer sr-only score-radio"
                                                                                        {{ $savedScore == $opt ? 'checked' : '' }}
                                                                                        data-material="{{ $material->material_name }}"
                                                                                        data-theme-id="{{ $theme->id }}">
                                                                                    <div
                                                                                        class="px-3 py-1.5 text-[10px] font-bold rounded-md border border-transparent text-gray-500 hover:bg-white hover:shadow-sm transition-all text-center min-w-[35px]">
                                                                                        {{ $opt }}
                                                                                    </div>
                                                                                </label>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
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
                                <p class="text-xs text-gray-500 mt-1">Anda dapat meng-generate ulang narasi berdasarkan
                                    nilai checklist terbaru.</p>
                            </div>

                            @foreach ($themes as $theme)
                                <div
                                    class="bg-white shadow-sm rounded-xl border border-gray-200 p-5 hover:border-indigo-300 transition duration-300">
                                    <div class="flex justify-between items-center mb-3">
                                        <h4 class="font-bold text-indigo-900 text-sm uppercase flex items-center">
                                            <span class="w-2 h-2 bg-indigo-500 rounded-full mr-2"></span>
                                            {{ $theme->theme_name }}
                                        </h4>
                                        <button type="button"
                                            onclick="generateThemeDraft('{{ $theme->id }}', 'theme_note_{{ $theme->id }}')"
                                            class="inline-flex items-center px-3 py-1.5 bg-indigo-50 border border-indigo-200 rounded text-xs font-bold text-indigo-700 hover:bg-indigo-100 shadow-sm transition">
                                            <i class="fas fa-sync-alt mr-1.5"></i> Update Narasi Otomatis
                                        </button>
                                    </div>
                                    <textarea name="theme_notes[{{ $theme->id }}]" id="theme_note_{{ $theme->id }}" rows="4"
                                        oninput="autoResize(this)"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-4 bg-gray-50 leading-relaxed text-gray-700"
                                        placeholder="Narasi tema...">{{ old('theme_notes.' . $theme->id, $savedThemeNotes[$theme->id] ?? '') }}</textarea>
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
                                        'id' => 'religious_values_text',
                                        'input_name' => 'narasi_agama',
                                        'title' => 'Nilai Agama & Budi Pekerti',
                                        'icon' => 'fas fa-praying-hands',
                                        'color' => 'text-yellow-500',
                                        'photo' => 'religious_values_photo',
                                        'input_photo' => 'foto_agama',
                                    ],
                                    [
                                        'id' => 'identity_text',
                                        'input_name' => 'narasi_jatidiri',
                                        'title' => 'Jati Diri',
                                        'icon' => 'fas fa-user',
                                        'color' => 'text-blue-500',
                                        'photo' => 'identity_photo',
                                        'input_photo' => 'foto_jatidiri',
                                    ],
                                    [
                                        'id' => 'literacy_steam_text',
                                        'input_name' => 'narasi_steam',
                                        'title' => 'Literasi & STEAM',
                                        'icon' => 'fas fa-flask',
                                        'color' => 'text-green-500',
                                        'photo' => 'literacy_steam_photo',
                                        'input_photo' => 'foto_steam',
                                    ],
                                    [
                                        'id' => 'p5_text',
                                        'input_name' => 'narasi_p5',
                                        'title' => 'Projek Penguatan Profil Pelajar Pancasila',
                                        'icon' => 'fas fa-flag',
                                        'color' => 'text-red-500',
                                        'photo' => 'p5_photo',
                                        'input_photo' => 'foto_p5',
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
                                            onclick="generateNarrativeAI('{{ $sec['title'] }}', '{{ $sec['input_name'] }}')"
                                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 shadow-sm transition-all">
                                            <i class="fas fa-magic mr-1.5"></i> Generate AI
                                        </button>
                                    </div>
                                    <div class="p-5">
                                        <div id="loading_{{ $sec['input_name'] }}"
                                            class="hidden mb-3 text-indigo-600 text-xs font-semibold flex items-center bg-indigo-50 p-2 rounded border border-indigo-100">
                                            <i class="fas fa-spinner fa-spin mr-2"></i> Sedang menyusun narasi...
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div>
                                                {{-- Textarea Value ambil dari $report->column_name --}}
                                                <textarea name="{{ $sec['input_name'] }}" id="{{ $sec['input_name'] }}" rows="5" oninput="autoResize(this)"
                                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-3 leading-relaxed"
                                                    placeholder="Narasi umum aspek ini...">{{ old($sec['input_name'], $report->{$sec['id']}) }}</textarea>
                                            </div>
                                            <div
                                                class="bg-gray-50 p-4 rounded-lg border border-dashed border-gray-300 flex flex-col justify-center text-center relative group hover:bg-gray-100 transition">
                                                <label
                                                    class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wide">Update
                                                    Foto</label>

                                                {{-- Preview Existing Photo --}}
                                                @if ($report->{$sec['photo']})
                                                    <div class="mb-2">
                                                        <img src="{{ asset('storage/' . $report->{$sec['photo']}) }}"
                                                            class="h-20 w-full object-cover rounded mx-auto"
                                                            alt="Existing">
                                                        <p class="text-[10px] text-gray-400 mt-1">Foto saat ini</p>
                                                    </div>
                                                @endif

                                                <div id="upload_box_{{ $sec['input_photo'] }}">
                                                    <div class="mt-2 cursor-pointer">
                                                        <i
                                                            class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2 group-hover:text-indigo-500 transition"></i>
                                                        <span class="block text-xs text-gray-500">Klik untuk ganti
                                                            foto</span>
                                                        <input type="file" name="{{ $sec['input_photo'] }}"
                                                            id="{{ $sec['input_photo'] }}" accept="image/*"
                                                            onchange="handleFilePreview(this, 'preview_container_{{ $sec['input_photo'] }}', 'upload_box_{{ $sec['input_photo'] }}')"
                                                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" />
                                                    </div>
                                                </div>
                                                <div id="preview_container_{{ $sec['input_photo'] }}"
                                                    class="hidden relative file-preview-container mt-2">
                                                    <img src="" id="img_prev_{{ $sec['input_photo'] }}"
                                                        alt="Preview"
                                                        class="shadow-sm rounded-md border border-gray-200">
                                                    <button type="button"
                                                        onclick="removeFile('{{ $sec['input_photo'] }}', 'preview_container_{{ $sec['input_photo'] }}', 'upload_box_{{ $sec['input_photo'] }}')"
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

                        <div
                            class="bg-gradient-to-r from-indigo-50 to-white rounded-xl border border-indigo-100 p-6 mb-8 shadow-sm">
                            <h3 class="text-lg font-bold text-indigo-900 mb-4 flex items-center">
                                <i class="fas fa-chalkboard-teacher mr-2"></i>4. Catatan dan Rekomendasi Guru
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Catatan Guru
                                        (Umum)</label>
                                    <textarea name="teacher_notes" rows="4" oninput="autoResize(this)"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-3 bg-white"
                                        placeholder="Contoh: Ananda sangat antusias dalam kegiatan seni...">{{ old('teacher_notes', $report->teacher_notes) }}</textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Rekomendasi / Tindak
                                        Lanjut</label>
                                    <textarea name="recommendations" rows="4" oninput="autoResize(this)"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-3 bg-white"
                                        placeholder="Contoh: Mohon Ayah/Bunda melatih kemandirian ananda di rumah...">{{ old('recommendations', $report->recommendations) }}</textarea>
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
                                placeholder="Tuliskan catatan atau pertanyaan untuk orang tua...">{{ old('refleksi_ortu', $report->parent_reflection_text) }}</textarea>
                        </div>
                    </div>

                    <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="health" role="tabpanel">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="bg-white shadow-lg sm:rounded-xl p-6 border border-gray-200">
                                <h3 class="text-lg font-bold text-gray-900 border-b pb-3 mb-5"><i
                                        class="fas fa-weight mr-2 text-indigo-500"></i>Data Pertumbuhan</h3>

                                @php
                                    $lk_val = null;
                                    if ($report->development_info_text) {
                                        preg_match(
                                            '/\((Lingkar Kepala: )([\d\.]+) cm\)/',
                                            $report->development_info_text,
                                            $matches,
                                        );
                                        $lk_val = $matches[2] ?? null;
                                    }
                                @endphp

                                <div class="grid grid-cols-3 gap-4 mb-6">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Berat
                                            (kg)</label>
                                        <input type="number" step="0.1" name="berat_badan" id="berat_badan"
                                            value="{{ old('berat_badan', $report->weight + 0) }}"
                                            class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md p-2 bg-gray-50 font-semibold">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tinggi
                                            (cm)</label>
                                        <input type="number" step="0.1" name="tinggi_badan" id="tinggi_badan"
                                            value="{{ old('tinggi_badan', $report->height + 0) }}"
                                            class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md p-2 bg-gray-50 font-semibold">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">L. Kpl
                                            (cm)</label>
                                        <input type="number" step="0.1" name="lingkar_kepala"
                                            id="lingkar_kepala" value="{{ old('lingkar_kepala', $lk_val) }}"
                                            class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md p-2 bg-gray-50 font-semibold">
                                    </div>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                    <div class="flex justify-between items-center mb-2">
                                        <label class="block text-sm font-bold text-gray-700">Kesimpulan
                                            Perkembangan</label>
                                        <button type="button" onclick="generateConclusionAI()"
                                            class="text-xs bg-indigo-100 text-indigo-700 px-2 py-1 rounded hover:bg-indigo-200 transition-colors font-semibold border border-indigo-200">
                                            <i class="fas fa-robot mr-1"></i> Perbaiki dengan AI
                                        </button>
                                    </div>
                                    <div id="loading_info_perkembangan"
                                        class="hidden text-xs font-semibold text-indigo-600 mb-2">
                                        <i class="fas fa-spinner fa-spin mr-1"></i> Menganalisa data fisik...
                                    </div>
                                    <textarea name="info_perkembangan" id="info_perkembangan" rows="5" oninput="autoResize(this)"
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md p-2"
                                        placeholder="Kesimpulan perkembangan...">{{ old('info_perkembangan', $report->development_info_text) }}</textarea>
                                </div>

                                <div class="mt-5">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Update Grafik KMS
                                        (Opsional)</label>

                                    @if ($report->development_info_photo)
                                        <div class="mb-2">
                                            <img src="{{ asset('storage/' . $report->development_info_photo) }}"
                                                class="h-20 rounded" alt="KMS">
                                        </div>
                                    @endif

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
                                                @php $val = $healthDetails[$item] ?? 'Baik'; @endphp
                                                <option value="Baik" {{ $val == 'Baik' ? 'selected' : '' }}>Baik
                                                </option>
                                                <option value="Cukup" {{ $val == 'Cukup' ? 'selected' : '' }}>Cukup
                                                </option>
                                                <option value="Perlu Perhatian"
                                                    {{ $val == 'Perlu Perhatian' ? 'selected' : '' }}>Perlu Perhatian
                                                </option>
                                                <option value="Perlu Tindakan Medis"
                                                    {{ $val == 'Perlu Tindakan Medis' ? 'selected' : '' }}>Perlu
                                                    Tindakan Medis</option>
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
                                                'db_name' => 'parent_name',
                                                'db_sig' => 'parent_signature',
                                                'placeholder' => 'Nama Orang Tua',
                                            ],
                                            [
                                                'id' => 'sig_guru',
                                                'input' => 'ttd_guru',
                                                'name_input' => 'nama_guru',
                                                'label' => 'Wali Kelas',
                                                'db_name' => 'teacher_name',
                                                'db_sig' => 'teacher_signature',
                                                'placeholder' => 'Nama Guru',
                                            ],
                                            [
                                                'id' => 'sig_konsultan',
                                                'input' => 'ttd_konsultan',
                                                'name_input' => 'nama_konsultan',
                                                'label' => 'Konsultan',
                                                'db_name' => 'consultant_name',
                                                'db_sig' => 'consultant_signature',
                                                'placeholder' => 'Nama Konsultan',
                                            ],
                                            [
                                                'id' => 'sig_kepsek',
                                                'input' => 'ttd_kepsek',
                                                'name_input' => 'nama_kepsek',
                                                'label' => 'Kepala Sekolah',
                                                'db_name' => 'principal_name',
                                                'db_sig' => 'principal_signature',
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
                                                {{-- Jika sudah ada TTD, tampilkan --}}
                                                @if ($report->{$sig['db_sig']} && Storage::disk('public')->exists($report->{$sig['db_sig']}))
                                                    <div
                                                        class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                                        <img src="{{ asset('storage/' . $report->{$sig['db_sig']}) }}"
                                                            class="max-h-full opacity-50">
                                                    </div>
                                                @endif

                                                <canvas id="{{ $sig['id'] }}"
                                                    class="w-full h-full cursor-crosshair relative z-10"></canvas>

                                                <button type="button"
                                                    class="absolute top-2 right-2 text-gray-400 hover:text-red-600 bg-white rounded-full p-1 shadow-sm z-20 transition border border-gray-200"
                                                    data-target="{{ $sig['id'] }}"
                                                    title="Hapus / Ulangi Tanda Tangan">
                                                    <i class="fas fa-eraser"></i>
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
                                                    value="{{ old($sig['name_input'], $report->{$sig['db_name']}) }}"
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
                                onclick="confirmBack('{{ route('reports.history', $student->id) }}')"
                                class="bg-white py-3 px-6 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">Batal</button>
                            <button type="submit" onclick="submitForm(event)"
                                class="inline-flex justify-center py-3 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all transform hover:scale-105">
                                <i class="fas fa-save mr-2"></i> Update Raport
                            </button>
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

        // --- 3. GENERATE DRAFT PER TEMA (PARAGRAF & OVERWRITE & NAME) ---
        function generateThemeDraft(themeId, targetId) {
            const textArea = document.getElementById(targetId);
            const scores = document.querySelectorAll(`.score-radio[data-theme-id="${themeId}"]:checked`);

            // AMBIL NAMA PANGGILAN
            const callingName = document.getElementById('student_calling_name').value;

            // Objek untuk mengelompokkan materi berdasarkan skor
            const groups = {
                'BSB': [], // Berkembang Sangat Baik
                'BSH': [], // Berkembang Sesuai Harapan
                'MB': [], // Mulai Berkembang
                'BB': [] // Belum Berkembang
            };

            const predicates = {
                'BSB': 'sangat baik dalam',
                'BSH': 'mampu',
                'MB': 'mulai mampu',
                'BB': 'perlu bimbingan dalam'
            };

            if (scores.length === 0) {
                Swal.fire('Info', 'Belum ada nilai checklist yang dipilih untuk tema ini.', 'info');
                return;
            }

            // 1. Masukkan materi ke grup yang sesuai
            scores.forEach(radio => {
                const materialName = radio.getAttribute('data-material');
                const score = radio.value;
                if (groups[score]) {
                    groups[score].push(materialName.toLowerCase());
                }
            });

            // 2. Susun Kalimat (Array of Strings)
            let sentences = [];

            // Helper untuk menggabungkan kata dengan koma dan "dan"
            const joinWords = (words) => {
                if (words.length === 0) return '';
                if (words.length === 1) return words[0];
                const last = words.pop();
                return words.join(', ') + ' dan ' + last;
            };

            if (groups['BSB'].length > 0) {
                sentences.push(`${callingName} ${predicates['BSB']} ${joinWords(groups['BSB'])}.`);
            }
            if (groups['BSH'].length > 0) {
                const prefix = sentences.length > 0 ? `Selain itu, ${callingName} ` : `${callingName} `;
                sentences.push(`${prefix}${predicates['BSH']} ${joinWords(groups['BSH'])}.`);
            }
            if (groups['MB'].length > 0) {
                const prefix = sentences.length > 0 ? `${callingName} juga ` : `${callingName} `;
                sentences.push(`${prefix}${predicates['MB']} ${joinWords(groups['MB'])}.`);
            }
            if (groups['BB'].length > 0) {
                sentences.push(`Namun, ${callingName} ${predicates['BB']} ${joinWords(groups['BB'])}.`);
            }

            // 3. Gabungkan menjadi paragraf & Overwrite
            const draftText = sentences.join(' ');
            textArea.value = draftText;
            autoResize(textArea);

            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Narasi berhasil diupdate',
                showConfirmButton: false,
                timer: 1500
            });
        }

        // --- 4. GENERATE AI (SAMA & NAME) ---
        function generateNarrativeAI(category, targetId) {
            const studentName = document.getElementById('student_calling_name').value;
            const textArea = document.getElementById(targetId);
            const loading = document.getElementById('loading_' + targetId);

            let scoresContext = [];
            document.querySelectorAll('.score-radio:checked').forEach(radio => {
                scoresContext.push(`${radio.getAttribute('data-material')}: ${radio.value}`);
            });
            let scoreString = scoresContext.length > 0 ? scoresContext.join(', ') : "Belum ada nilai.";

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
                    // Clear juga hidden input jika ada
                    // document.getElementById('input_' + this.getAttribute('data-target')).value = "";
                });
            });
        });

        // --- 7. SUBMIT & VALIDATION ---
        function confirmBack(url) {
            Swal.fire({
                title: 'Batalkan Perubahan?',
                text: "Data yang belum disimpan akan hilang.",
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

            // Client-Side Validation
            const classNameInput = document.getElementById('class_name');
            if (!classNameInput.value) {
                Swal.fire({
                    icon: 'error',
                    title: 'Data Belum Lengkap',
                    text: 'Silakan pilih Kelas terlebih dahulu.',
                    confirmButtonText: 'OK'
                });
                classNameInput.classList.add('input-error');
                return;
            } else {
                classNameInput.classList.remove('input-error');
            }

            Swal.fire({
                title: 'Simpan Perubahan?',
                text: "Pastikan data sudah benar.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Update!'
            }).then((result) => {
                if (result.isConfirmed) {
                    canvasIds.forEach(id => {
                        // Hanya update signature jika user melakukan tanda tangan baru
                        if (!pads[id].isEmpty()) {
                            document.getElementById('input_' + id).value = pads[id].toDataURL('image/png');
                        }
                    });
                    document.getElementById('reportForm').submit();
                }
            });
        }

        function generateConclusionAI() {
            generateNarrativeAI('Kesimpulan Fisik', 'info_perkembangan');
        }
    </script>
</x-app-layout>
