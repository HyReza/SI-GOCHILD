<x-app-layout>
    <x-slot:title>Daftar Pengukuran Siswa</x-slot:title>

    {{-- Notifikasi Sukses/Gagal dengan SweetAlert --}}
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: @json(session('success')),
                    timer: 2000,
                    showConfirmButton: false
                });
            });
        </script>
    @endif
    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: @json($errors->first())
                });
            });
        </script>
    @endif

    {{-- Header dan Filter --}}
    <div class="mb-5 flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
        <div>
            <h1 class="text-lg font-semibold dark:text-white">Daftar Siswa untuk Pengukuran</h1>
            <p class="text-sm text-gray-500">Cari siswa, filter layanan, lalu lakukan pengukuran.</p>
        </div>

        <div class="w-full md:w-auto flex flex-col md:flex-row gap-2 md:items-end">
            {{-- Input Pencarian --}}
            <div class="w-full md:w-72">
                <label class="block text-xs text-gray-500 mb-1">Cari (nama/NIS/ibu)</label>
                <input id="q" type="text" placeholder="Ketik untuk mencari…"
                    class="w-full h-10 border rounded-lg px-3 dark:bg-gray-900 dark:border-gray-700 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            {{-- Dropdown Filter Layanan --}}
            <div class="w-full md:w-64">
                <label class="block text-xs text-gray-500 mb-1">Filter Layanan</label>
                <select id="service_id"
                    class="w-full h-10 border rounded-lg px-3 dark:bg-gray-900 dark:border-gray-700 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">— Semua Layanan —</option>
                    @foreach ($services as $svc)
                        <option value="{{ $svc->id }}">{{ $svc->service_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Konten Tabel --}}
    <div class="p-6 bg-white dark:bg-gray-900 rounded-xl shadow">
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto">
                <thead
                    class="text-xs uppercase tracking-wide bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400">
                    <tr>
                        <th class="py-3 px-3 text-left">No</th>
                        <th class="py-3 px-3 text-left">NIS</th>
                        <th class="py-3 px-3 text-left">Nama Anak</th>
                        <th class="py-3 px-3 text-left">Nama Ibu</th>
                        <th class="py-3 px-3 text-left">Tanggal Lahir</th>
                        <th class="py-3 px-3 text-left">Umur</th>
                        <th class="py-3 px-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tbody" class="text-sm dark:text-gray-300">
                    {{-- Data awal akan dimuat di sini oleh server, dan diperbarui oleh JavaScript --}}
                    @forelse ($activityTransactions as $at)
                        @php
                            $student = $at->student;
                            $bd = $student->birth_date ? \Illuminate\Support\Carbon::parse($student->birth_date) : null;
                            $diff = $bd ? $bd->diff(\Illuminate\Support\Carbon::today()) : null;
                            $umur = $diff ? "{$diff->y} thn {$diff->m} bln" : '—';
                        @endphp
                        <tr
                            class="border-b border-gray-200 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="py-3 px-3">{{ $loop->iteration }}</td>
                            <td class="py-3 px-3">{{ $student->student_number }}</td>
                            <td class="py-3 px-3 font-medium">
                                {{ $student->student_name }}
                                @if (!is_null($at->student->gender))
                                    <span
                                        class="ml-2 text-[10px] px-2 py-0.5 rounded-full {{ $at->student->gender == 1 || $at->student->gender == 'male' ? 'bg-blue-100 text-blue-700' : 'bg-pink-100 text-pink-700' }}">
                                        {{ $at->student->gender == 1 || $at->student->gender == 'male' ? 'Laki-laki' : 'Perempuan' }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-3">{{ $student->mother_name ?? '—' }}</td>
                            <td class="py-3 px-3">{{ $bd ? $bd->format('d M Y') : '—' }}</td>
                            <td class="py-3 px-3">{{ $umur }}</td>
                            <td class="py-3 px-3">
                                <div class="flex gap-2 justify-center">
                                    <a href="{{ route('measurement.create', $at->id) }}"
                                        class="inline-flex items-center gap-1 bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg text-xs shadow-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 11l3-3m0 0l3 3m-3-3v8m0-13a9 9 0 110 18 9 9 0 010-18z" />
                                        </svg>
                                        Ukur
                                    </a>
                                    <a href="{{ route('measurement.history', $at->id) }}" {{-- Ganti dengan rute riwayat jika ada --}}
                                        class="inline-flex items-center gap-1 bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg text-xs shadow-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Riwayat
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        {{-- Dihandle oleh JavaScript --}}
                    @endforelse
                </tbody>
            </table>
            <p id="empty" class="text-center text-gray-500 py-6 @if ($activityTransactions->isNotEmpty()) hidden @endif">
                Tidak ada data siswa yang tersedia.</p>
        </div>
    </div>

    <script>
        const qEl = document.getElementById('q');
        const svcEl = document.getElementById('service_id');
        const tbody = document.getElementById('tbody');
        const empty = document.getElementById('empty');

        // Fungsi untuk menghitung umur dalam format "X thn Y bln"
        function ageHuman(iso) {
            if (!iso) return '—';
            const b = new Date(iso);
            const t = new Date();
            let y = t.getFullYear() - b.getFullYear();
            let m = t.getMonth() - b.getMonth();
            if (t.getDate() < b.getDate()) {
                m--;
            }
            if (m < 0) {
                y--;
                m += 12;
            }
            return `${y} thn ${m} bln`;
        }

        // Fungsi debounce untuk menunda eksekusi saat pengguna mengetik
        const debounce = (fn, ms = 300) => {
            let t;
            return (...a) => {
                clearTimeout(t);
                t = setTimeout(() => fn(...a), ms);
            }
        };

        // Fungsi utama untuk memuat data via AJAX
        async function loadData() {
            const searchUrl = new URL(@json(route('measurement.search')), window.location.origin);
            searchUrl.searchParams.set('q', qEl.value || '');
            searchUrl.searchParams.set('service_id', svcEl.value || '');

            try {
                const response = await fetch(searchUrl.toString(), {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) throw new Error('Gagal memuat data.');

                const json = await response.json();
                renderTable(json.data);

            } catch (error) {
                tbody.innerHTML =
                    `<tr><td colspan="7" class="text-center py-6 text-red-500">${error.message}</td></tr>`;
                empty.classList.add('hidden');
            }
        }

        // Fungsi untuk me-render ulang baris tabel
        function renderTable(transactions) {
            tbody.innerHTML = ''; // Kosongkan tabel

            if (!transactions || transactions.length === 0) {
                empty.classList.remove('hidden');
                return;
            }

            empty.classList.add('hidden');

            // 1. SIAPKAN TEMPLATE URL DULU (Gunakan string unik seperti ':id')
            // Kita minta Laravel membuat URL tapi ID-nya kita isi dummy string ':id'
            const templateUkur = "{{ route('measurement.create', ':id') }}";
            const templateRiwayat = "{{ route('measurement.history', ':id') }}";

            transactions.forEach((at, index) => {
                const s = at.student;
                const tr = document.createElement('tr');
                tr.className =
                    "border-b border-gray-200 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50";

                // ... (kode perhitungan umur dan gender tetap sama) ...
                const dob = s.birth_date ? new Date(s.birth_date) : null;
                const dobText = dob ? dob.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                }) : '—';
                const umur = ageHuman(s.birth_date);

                let genderBadge = '';
                if (s.gender !== null) {
                    const isMale = s.gender == 1 || s.gender === 'male';
                    const bgColor = isMale ? 'bg-blue-100' : 'bg-pink-100';
                    const textColor = isMale ? 'text-blue-700' : 'text-pink-700';
                    const genderText = isMale ? 'Laki-laki' : 'Perempuan';
                    genderBadge =
                        `<span class="ml-2 text-[10px] px-2 py-0.5 rounded-full ${bgColor} ${textColor}">${genderText}</span>`;
                }

                // 2. REPLACE PLACEHOLDER ':id' DENGAN ID ASLI DARI JAVASCRIPT (at.id)
                // Pastikan object 'at' memiliki property 'id' dari response JSON Anda
                const urlUkur = templateUkur.replace(':id', at.id);
                const urlRiwayat = templateRiwayat.replace(':id', at.id);

                tr.innerHTML = `
            <td class="py-3 px-3">${index + 1}</td>
            <td class="py-3 px-3">${s.student_number || '—'}</td>
            <td class="py-3 px-3 font-medium">${s.student_name} ${genderBadge}</td>
            <td class="py-3 px-3">${s.mother_name || '—'}</td>
            <td class="py-3 px-3">${dobText}</td>
            <td class="py-3 px-3">${umur}</td>
            <td class="py-3 px-3">
                <div class="flex gap-2 justify-center">
                    <a href="${urlUkur}" class="inline-flex items-center gap-1 bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg text-xs shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 11l3-3m0 0l3 3m-3-3v8m0-13a9 9 0 110 18 9 9 0 010-18z" /></svg>
                        Ukur
                    </a>
                    <a href="${urlRiwayat}" class="inline-flex items-center gap-1 bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg text-xs shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Riwayat
                    </a>
                </div>
            </td>
        `;
                tbody.appendChild(tr);
            });
        }

        // Tambahkan event listener
        qEl.addEventListener('input', debounce(loadData, 300));
        svcEl.addEventListener('change', loadData);
    </script>
</x-app-layout>
