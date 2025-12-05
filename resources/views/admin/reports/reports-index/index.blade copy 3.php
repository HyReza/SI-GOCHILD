<x-app-layout>
    <x-slot:title>Manajemen Rapor Siswa</x-slot:title>

    {{-- SweetAlert untuk pesan keberhasilan --}}
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

    {{-- SweetAlert untuk pesan error --}}
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

    {{-- Bagian header --}}
    <div class="mb-5 flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
        <div>
            <h1 class="text-lg font-semibold">Identitas Siswa MMDST</h1>
            <p class="text-sm text-gray-500">Cari siswa, filter layanan, lalu buat laporan atau cek riwayat.</p>
        </div>

        {{-- Form filter dan pencarian --}}
        <div class="w-full md:w-auto flex flex-col md:flex-row gap-2 md:items-end">
            <div class="w-full md:w-72">
                <label class="block text-xs text-gray-500 mb-1">Cari (nama/NIS/NIK/ibu)</label>
                <input id="q" type="text" placeholder="Ketik untuk mencari…"
                    class="w-full h-10 border rounded-lg px-3 dark:bg-gray-900 dark:border-gray-700">
            </div>

            <div class="w-full md:w-64">
                <label class="block text-xs text-gray-500 mb-1">Filter Layanan</label>
                <select id="service_id"
                    class="w-full h-10 border rounded-lg px-3 dark:bg-gray-900 dark:border-gray-700">
                    <option value="">— Semua Layanan —</option>
                    @foreach ($services as $svc)
                        <option value="{{ $svc->id }}">{{ $svc->service_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Tabel daftar siswa --}}
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
                        <th class="py-3 px-3 text-left">Service</th>
                        <th class="py-3 px-3 text-left">Tanggal Masuk</th>
                        <th class="py-3 px-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tbody" class="text-sm">
                    @foreach ($students as $s)
                        @php
                            $bd = $s->birth_date ? \Illuminate\Support\Carbon::parse($s->birth_date) : null;
                            $diff = $bd ? $bd->diff(\Illuminate\Support\Carbon::today()) : null;
                            $umur = $diff ? "{$diff->y} thn {$diff->m} bln {$diff->d} hr" : '—';
                        @endphp
                        <tr
                            class="border-b border-gray-200 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="py-3 px-3">{{ $loop->iteration }}</td>
                            <td class="py-3 px-3">{{ $s->student_number }}</td>
                            <td class="py-3 px-3 font-medium">
                                {{ $s->student_name }}
                                @if (!is_null($s->gender))
                                    <span
                                        class="ml-2 text-[10px] px-2 py-0.5 rounded-full {{ $s->gender ? 'bg-blue-100 text-blue-700' : 'bg-pink-100 text-pink-700' }}">
                                        {{ $s->gender ? 'Laki-laki' : 'Perempuan' }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-3">{{ $s->mother_name ?? '—' }}</td>
                            <td class="py-3 px-3">{{ $s->program->program_name }}</td>
                            <td class="py-3 px-3">{{ $bd ? $bd->format('d M Y') : '—' }}</td>
                            <td class="py-3 px-3">
                                <div class="flex gap-2 justify-center">
                                    <button
                                        class="inline-flex items-center gap-1 bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg text-xs shadow-sm"
                                        onclick="autoReport({{ $s->id }})">
                                        <span class="material-symbols-outlined text-sm">add_task</span>
                                        Buat Laporan
                                    </button>
                                    <a href="{{ route('mmdst.history', $s) }}"
                                        class="inline-flex items-center gap-1 bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg text-xs shadow-sm">
                                        <span class="material-symbols-outlined text-sm">history</span>
                                        Riwayat
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <p id="empty" class="text-center text-gray-500 py-6 hidden">Tidak ada data.</p>
        </div>
    </div>

    <script>
        const csrf = () => document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const qEl = document.getElementById('q');
        const svcEl = document.getElementById('service_id');
        const tbody = document.getElementById('tbody');
        const empty = document.getElementById('empty');

        // Fungsi untuk menghitung umur
        function ageHuman(iso) {
            if (!iso) return '—';
            const b = new Date(iso),
                t = new Date();
            let y = t.getFullYear() - b.getFullYear();
            let m = t.getMonth() - b.getMonth();
            let d = t.getDate() - b.getDate();
            if (d < 0) {
                m -= 1;
                d += new Date(t.getFullYear(), t.getMonth(), 0).getDate();
            }
            if (m < 0) {
                y -= 1;
                m += 12;
            }
            return `${y} thn ${m} bln ${d} hr`;
        }

        // Debounce untuk menghindari terlalu banyak permintaan
        const debounce = (fn, ms = 300) => {
            let t;
            return (...a) => {
                clearTimeout(t);
                t = setTimeout(() => fn(...a), ms);
            }
        };

        // Memuat data berdasarkan filter
        async function loadData() {
            const base = new URL(@json(route('mmdst.search', [], false)), window.location.origin);

            base.searchParams.set('q', qEl.value || '');
            base.searchParams.set('service_id', svcEl.value || '');

            const res = await fetch(base.toString(), {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                cache: 'no-store'
            });

            let json;
            try {
                json = await res.json();
            } catch (e) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: `Gagal memuat data (${res.status})`
                });
                return;
            }

            if (!res.ok) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: json?.message || `Gagal memuat data (${res.status})`
                });
                return;
            }

            tbody.innerHTML = '';
            if (!json.data || json.data.length === 0) {
                empty.classList.remove('hidden');
                return;
            }
            empty.classList.add('hidden');

            json.data.forEach((s, i) => {
                const tr = document.createElement('tr');
                tr.className =
                    "border-b border-gray-200 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50";
                const dobText = s.birth_date ? new Date(s.birth_date).toLocaleDateString() : '—';
                const umur = ageHuman(s.birth_date);
                const genderBadge = (s.gender === null || s.gender === undefined) ?
                    '' :
                    `<span class="ml-2 text-[10px] px-2 py-0.5 rounded-full ${s.gender ? 'bg-blue-100 text-blue-700' : 'bg-pink-100 text-pink-700'}">
                           ${s.gender ? 'Laki-laki' : 'Perempuan' }
                       </span>`;
                tr.innerHTML = `
                    <td class="py-3 px-3">${i + 1}</td>
                    <td class="py-3 px-3">${s.student_number || '-'}</td>
                    <td class="py-3 px-3 font-medium">
                        ${s.student_name}
                        ${genderBadge}
                    </td>
                    <td class="py-3 px-3">${s.mother_name ?? '—'}</td>
                    <td class="py-3 px-3">${dobText}</td>
                    <td class="py-3 px-3">${umur}</td>
                    <td class="py-3 px-3">
                        <div class="flex gap-2 justify-center">
                            <button class="inline-flex items-center gap-1 bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg text-xs shadow-sm"
                                    onclick="autoReport(${s.id})">
                                <span class="material-symbols-outlined text-sm">add_task</span>
                                Buat Laporan
                            </button>
                            <a class="inline-flex items-center gap-1 bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg text-xs shadow-sm"
                               href="${@json(url('/mmdst'))}/${s.id}/history">
                                <span class="material-symbols-outlined text-sm">history</span>
                                Riwayat
                            </a>
                        </div>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        qEl.addEventListener('input', debounce(loadData, 250));
        svcEl.addEventListener('change', loadData);

        document.addEventListener('DOMContentLoaded', () => {
            loadData();
            qEl.focus();
        });
    </script>
</x-app-layout>
