<x-app-layout>
    {{-- Memuat Alpine.js & SweetAlert2 --}}
    {{-- Pastikan script ini sudah ada di layout utama Anda (contoh: layouts/app.blade.php) --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] {
            display: none !important;
        }

        .animate-fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }
    </style>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Formulir Pembuatan Rapor') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Blok PHP untuk menyiapkan semua data mapping sebelum digunakan oleh Alpine.js --}}
            @php
                $totalSteps = $data['themes']->count() + 1;
                $romanNumerals = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X'];
                $alphabet = range('a', 'z');

                $subThemeToThemeMap = [];
                $materialToThemeMap = [];

                foreach ($data['themes'] as $theme) {
                    foreach ($theme->subTheme as $subTheme) {
                        $subThemeToThemeMap[$subTheme->id] = $theme->id;
                        foreach ($subTheme->material as $material) {
                            $materialToThemeMap[$material->id] = $theme->id;
                        }
                    }
                }
            @endphp

            {{-- Komponen utama Alpine.js untuk mengelola semua state dan interaktivitas form --}}
            <div x-data="{
                step: 1,
                totalSteps: {{ $totalSteps }},
                scores: {},
                themeNotes: {},
                overallSummary: '',
                subThemeMap: {{ json_encode($subThemeToThemeMap) }},
                materialToThemeMap: {{ json_encode($materialToThemeMap) }},
            
                init() {
                    let initialScores = {};
                    @foreach ($data['assessments'] as $assessment)
                        initialScores['material_{{ $assessment['material_id'] }}'] = '{{ $assessment['score'] }}'; @endforeach
                    this.scores = initialScores;
            
                    @foreach ($data['themes'] as $theme)
                        this.generateThemeNote({{ $theme->id }}); @endforeach
                    this.generateOverallSummary();
                },
            
                generateThemeNote(themeId) {
                    let themeScores = [];
                    if (this.scores['theme_' + themeId]) themeScores.push(this.scores['theme_' + themeId]);
                    Object.keys(this.scores).forEach(key => {
                        if (key.startsWith('subtheme_')) {
                            const subThemeId = key.split('_')[1];
                            if (this.subThemeMap[subThemeId] == themeId && this.scores[key]) {
                                themeScores.push(this.scores[key]);
                            }
                        }
                        if (key.startsWith('material_')) {
                            const materialId = key.split('_')[1];
                            if (this.materialToThemeMap[materialId] == themeId && this.scores[key]) {
                                themeScores.push(this.scores[key]);
                            }
                        }
                    });
            
                    if (themeScores.length === 0) { this.themeNotes['theme_' + themeId] = 'Belum ada penilaian yang diisi pada aspek ini.'; return; }
                    let counts = { BSB: 0, BSH: 0, MB: 0, BB: 0 };
                    themeScores.forEach(score => { if (score) counts[score]++; });
                    let note = 'Pada aspek ini, ananda ';
                    if (counts.BSB / themeScores.length > 0.5) note += 'menunjukkan perkembangan istimewa yang konsisten melampaui ekspektasi. ';
                    else if ((counts.BSH + counts.BSB) / themeScores.length >= 0.6) note += 'menunjukkan perkembangan yang sangat baik dan telah memenuhi harapan. ';
                    else if (counts.MB / themeScores.length > 0.5) note += 'mulai menunjukkan kemajuan yang positif dan perlu terus distimulasi. ';
                    else note += 'menunjukkan perkembangan sesuai tahapan usianya. ';
                    if (counts.BB > 0) note += `Perlu perhatian dan bimbingan lebih intensif pada ${counts.BB} indikator agar dapat berkembang lebih optimal. `;
                    else if (themeScores.length > 0 && counts.BSB === themeScores.length) note += 'Potensi ananda sangat luar biasa, pertahankan!';
                    else note += 'Dukungan positif akan sangat membantu ananda mencapai potensi terbaiknya.';
                    this.themeNotes['theme_' + themeId] = note;
                },
            
                generateOverallSummary() {
                    let allScores = Object.values(this.scores).filter(Boolean);
                    if (allScores.length < 5) { this.overallSummary = 'Mohon lengkapi penilaian untuk melihat kesimpulan otomatis.'; return; }
                    let counts = { BSB: 0, BSH: 0, MB: 0, BB: 0 };
                    allScores.forEach(score => { counts[score]++; });
                    let summary = 'Secara keseluruhan, ananda ';
                    if (counts.BSB / allScores.length > 0.4) summary += 'menunjukkan perkembangan istimewa dan konsisten melampaui harapan di berbagai aspek. ';
                    else if ((counts.BSH + counts.BSB) / allScores.length > 0.6) summary += 'menunjukkan perkembangan yang sangat baik dan telah mencapai sebagian besar tujuan pembelajaran. ';
                    else if (counts.MB / allScores.length > 0.5) summary += 'menunjukkan kemajuan yang positif dan mulai mengembangkan berbagai kemampuan dasar. ';
                    else summary += 'menunjukkan kemajuan yang baik dalam perkembangannya. ';
                    summary += `Dari total ${allScores.length} indikator yang dinilai, ananda mendapat ${counts.BSB} BSB, ${counts.BSH} BSH, ${counts.MB} MB, dan ${counts.BB} BB. `;
                    summary += 'Stimulasi yang kaya dan berkelanjutan sangat penting untuk mengoptimalkan seluruh potensi ananda.';
                    this.overallSummary = summary;
                },
            
                handleSubmit(event) {
                    Swal.fire({
                        title: 'Simpan Rapor?',
                        text: 'Pastikan semua data sudah terisi dengan benar.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#16a34a',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Simpan!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Memproses...',
                                text: 'Sedang menyimpan data rapor ke server.',
                                allowOutsideClick: false,
                                didOpen: () => { Swal.showLoading(); }
                            });
                            event.target.submit();
                        }
                    });
                }
            }" x-init="init()">

                {{-- KARTU IDENTITAS SISWA (STATIS) --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-8 border-l-4 border-indigo-500">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">IDENTITAS PESERTA DIDIK</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-x-6 gap-y-4 text-sm">
                        <span><strong class="font-medium text-gray-500 block">Nama Lengkap</strong> <span
                                class="text-gray-800">{{ $data['student']->student_name }}</span></span>
                        <span><strong class="font-medium text-gray-500 block">Nama Panggilan</strong> <span
                                class="text-gray-800">{{ $data['student']->nickname }}</span></span>
                        <span><strong class="font-medium text-gray-500 block">Nomor Induk</strong> <span
                                class="text-gray-800">{{ $data['student']->student_number }}</span></span>
                        <span><strong class="font-medium text-gray-500 block">Tanggal Lahir</strong> <span
                                class="text-gray-800">{{ \Carbon\Carbon::parse($data['student']->birth_date)->isoFormat('D MMMM Y') }}</span></span>
                        <span><strong class="font-medium text-gray-500 block">Usia</strong> <span
                                class="text-gray-800">{{ \Carbon\Carbon::parse($data['student']->birth_date)->diffForHumans(null, true) }}</span></span>
                        <span><strong class="font-medium text-gray-500 block">Periode</strong> <span
                                class="text-gray-800">{{ \Carbon\Carbon::parse($period['start_date'])->isoFormat('D MMM Y') }}
                                - {{ \Carbon\Carbon::parse($period['end_date'])->isoFormat('D MMM Y') }}</span></span>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 md:p-8">
                    {{-- Progress Bar --}}
                    <div class="mb-8">
                        <div class="flex justify-between mb-1">
                            <span class="text-base font-medium text-indigo-700"
                                x-text="step === totalSteps ? 'Ringkasan & Finalisasi' : `Aspek Perkembangan ${step} dari ${totalSteps - 1}`"></span>
                            <span class="text-sm font-medium text-indigo-700"
                                x-text="Math.round((step / totalSteps) * 100) + '%'"></span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-indigo-600 h-2.5 rounded-full transition-all duration-500"
                                :style="'width: ' + (step / totalSteps * 100) + '%'"></div>
                        </div>
                    </div>

                    <form @submit.prevent="handleSubmit" method="POST"
                        action="{{ route('reports.store', $activityTransaction) }}">
                        @csrf
                        <input type="hidden" name="start_date" value="{{ $period['start_date'] }}">
                        <input type="hidden" name="end_date" value="{{ $period['end_date'] }}">
                        <input type="hidden" name="report_title"
                            value="Laporan Perkembangan - {{ $data['student']->student_name }} - {{ \Carbon\Carbon::parse($period['end_date'])->isoFormat('MMMM Y') }}">
                        <textarea name="overall_summary" x-model="overallSummary" class="hidden"></textarea>

                        @if ($errors->any())
                            <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md"
                                role="alert">
                                <p class="font-bold">Gagal Menyimpan! Terjadi Kesalahan Validasi:</p>
                                <ul class="list-disc list-inside mt-2 text-sm">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="min-h-[450px]">
                            {{-- Inisialisasi counter SEBELUM loop --}}
                            @php $scoreCounter = 0; @endphp

                            @foreach ($data['themes'] as $theme)
                                <div x-show="step === {{ $loop->iteration }}" x-cloak
                                    class="animate-fade-in space-y-8">
                                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                                        <table class="min-w-full">
                                            <thead class="bg-gray-100">
                                                <tr>
                                                    <th
                                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-2/3 lg:w-1/2">
                                                        Aspek Pengembangan</th>
                                                    <th
                                                        class="px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        BB</th>
                                                    <th
                                                        class="px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        MB</th>
                                                    <th
                                                        class="px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        BSH</th>
                                                    <th
                                                        class="px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        BSB</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-200">
                                                <tr class="bg-indigo-50">
                                                    <td class="px-4 py-3 font-bold text-base text-indigo-800">
                                                        {{ $romanNumerals[$loop->index] }}. {{ $theme->theme_name }}
                                                    </td>
                                                    @foreach (['BB', 'MB', 'BSH', 'BSB'] as $score)
                                                        <td class="px-2 py-3 text-center">
                                                            <input type="hidden"
                                                                name="scores[{{ $scoreCounter }}][type]"
                                                                value="theme">
                                                            <input type="hidden"
                                                                name="scores[{{ $scoreCounter }}][id]"
                                                                value="{{ $theme->id }}">
                                                            <input type="radio"
                                                                class="form-radio h-5 w-5 text-indigo-600"
                                                                x-model="scores['theme_{{ $theme->id }}']"
                                                                @change="generateThemeNote({{ $theme->id }}); generateOverallSummary()"
                                                                name="scores[{{ $scoreCounter }}][score]"
                                                                value="{{ $score }}">
                                                        </td>
                                                    @endforeach
                                                </tr>
                                                @php $scoreCounter++; @endphp

                                                @foreach ($theme->subTheme as $subTheme)
                                                    <tr class="bg-gray-50">
                                                        <td
                                                            class="pl-8 pr-4 py-2.5 font-semibold text-sm text-gray-800">
                                                            {{ $loop->iteration }}. {{ $subTheme->sub_theme_name }}
                                                        </td>
                                                        @foreach (['BB', 'MB', 'BSH', 'BSB'] as $score)
                                                            <td class="px-2 py-2.5 text-center">
                                                                <input type="hidden"
                                                                    name="scores[{{ $scoreCounter }}][type]"
                                                                    value="subtheme">
                                                                <input type="hidden"
                                                                    name="scores[{{ $scoreCounter }}][id]"
                                                                    value="{{ $subTheme->id }}">
                                                                <input type="radio"
                                                                    class="form-radio h-5 w-5 text-indigo-600"
                                                                    x-model="scores['subtheme_{{ $subTheme->id }}']"
                                                                    @change="generateThemeNote({{ $theme->id }}); generateOverallSummary()"
                                                                    name="scores[{{ $scoreCounter }}][score]"
                                                                    value="{{ $score }}">
                                                            </td>
                                                        @endforeach
                                                    </tr>
                                                    @php $scoreCounter++; @endphp

                                                    @foreach ($subTheme->material as $material)
                                                        <tr>
                                                            <td class="pl-12 pr-4 py-2.5 text-sm text-gray-700">
                                                                {{ $alphabet[$loop->index] }}.
                                                                {{ $material->material_name }}</td>
                                                            @foreach (['BB', 'MB', 'BSH', 'BSB'] as $score)
                                                                <td class="px-2 py-2.5 text-center">
                                                                    <input type="hidden"
                                                                        name="scores[{{ $scoreCounter }}][type]"
                                                                        value="material">
                                                                    <input type="hidden"
                                                                        name="scores[{{ $scoreCounter }}][id]"
                                                                        value="{{ $material->id }}">
                                                                    <input type="hidden"
                                                                        name="scores[{{ $scoreCounter }}][sub_theme_id]"
                                                                        value="{{ $subTheme->id }}">
                                                                    <input type="radio"
                                                                        class="form-radio h-5 w-5 text-indigo-600"
                                                                        x-model="scores['material_{{ $material->id }}']"
                                                                        @change="generateThemeNote({{ $theme->id }}); generateOverallSummary()"
                                                                        name="scores[{{ $scoreCounter }}][score]"
                                                                        value="{{ $score }}">
                                                                </td>
                                                            @endforeach
                                                        </tr>
                                                        @php $scoreCounter++; @endphp
                                                    @endforeach
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div>
                                        <label for="note_{{ $loop->index }}"
                                            class="block font-medium text-sm text-gray-700">Catatan Narasi
                                            (Otomatis)</label>
                                        <input type="hidden" name="theme_notes[{{ $loop->index }}][theme_id]"
                                            value="{{ $theme->id }}">
                                        <textarea id="note_{{ $loop->index }}" name="theme_notes[{{ $loop->index }}][note]"
                                            x-model="themeNotes['theme_{{ $theme->id }}']" rows="4"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-50 focus:bg-white text-sm"></textarea>
                                    </div>
                                </div>
                            @endforeach

                            <div x-show="step === totalSteps" x-cloak class="animate-fade-in space-y-6">
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                                    <label for="overall_summary_display"
                                        class="block font-medium text-gray-700 font-semibold text-base">KESIMPULAN
                                        PERKEMBANGAN</label>
                                    <p class="text-sm text-gray-500 mt-1 mb-2">Narasi ini dibuat otomatis berdasarkan
                                        keseluruhan penilaian. Anda bisa menyalin dan menyesuaikannya di catatan guru.
                                    </p>
                                    <textarea id="overall_summary_display" x-model="overallSummary" rows="5"
                                        class="block w-full border-gray-300 rounded-md shadow-sm bg-white text-sm" readonly></textarea>
                                </div>
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                                    <label for="recommendations"
                                        class="block font-medium text-gray-700 font-semibold text-base">CATATAN &
                                        REKOMENDASI GURU</label>
                                    <p class="text-sm text-gray-500 mt-1 mb-2">Tuliskan rekomendasi atau catatan
                                        tambahan penting lainnya di sini.</p>
                                    <textarea id="recommendations" name="recommendations" rows="5"
                                        placeholder="Contoh: Ananda sudah sangat baik dalam aspek motorik kasar, stimulasi selanjutnya dapat difokuskan pada motorik halus seperti meronce..."
                                        class="block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                                </div>
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                                    <h3 class="text-base font-semibold text-gray-900 mb-4">KETERANGAN TAMBAHAN</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                                        <div class="space-y-4">
                                            <h4 class="font-medium text-gray-800 border-b pb-2">1. Keterangan Kesehatan
                                            </h4>
                                            @php $healthItems = ['Mata - Penglihatan', 'Telinga - Pendengaran', 'Gigi', 'Kulit', 'Kebersihan', 'Kerapian', 'Rambut', 'Kuku']; @endphp
                                            @foreach ($healthItems as $index => $item)
                                                <div class="flex items-center justify-between">
                                                    <input type="hidden"
                                                        name="health_details[{{ $index }}][item_name]"
                                                        value="{{ $item }}">
                                                    <label class="text-sm text-gray-700">{{ $item }}</label>
                                                    <select name="health_details[{{ $index }}][item_value]"
                                                        class="border-gray-300 rounded-md shadow-sm text-sm py-1"
                                                        style="width: 120px;">
                                                        <option value="Baik">Baik</option>
                                                        <option value="Cukup" selected>Cukup</option>
                                                        <option value="Kurang">Kurang</option>
                                                    </select>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="space-y-4">
                                            <h4 class="font-medium text-gray-800 border-b pb-2">2. Keterangan Presensi
                                            </h4>
                                            <div class="flex items-center justify-between text-sm">
                                                <span class="text-gray-700">Sakit</span>
                                                <span
                                                    class="font-semibold text-gray-800">{{ $data['attendance']['sick'] ?? 0 }}
                                                    hari</span>
                                            </div>
                                            <div class="flex items-center justify-between text-sm">
                                                <span class="text-gray-700">Izin</span>
                                                <span
                                                    class="font-semibold text-gray-800">{{ $data['attendance']['excused'] ?? 0 }}
                                                    hari</span>
                                            </div>
                                            <div class="flex items-center justify-between text-sm">
                                                <span class="text-gray-700">Tanpa Keterangan</span>
                                                <span
                                                    class="font-semibold text-gray-800">{{ $data['attendance']['absent'] ?? 0 }}
                                                    hari</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-between mt-8 pt-6 border-t">
                            <button type="button" x-show="step > 1" @click="step--"
                                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">
                                Kembali
                            </button>
                            <div x-show="step === 1" class="w-0"></div>
                            <button type="button" x-show="step < totalSteps" @click="step++"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                Selanjutnya
                            </button>
                            <button type="submit" x-show="step === totalSteps"
                                class="inline-flex items-center px-6 py-3 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                                Simpan Rapor
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
