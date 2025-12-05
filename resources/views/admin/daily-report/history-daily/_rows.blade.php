@php
    use Illuminate\Support\Str;
    function short($v, $len = 80)
    {
        return $v ? Str::limit(strip_tags($v), $len) : '-';
    }
    $startNo = $reports->firstItem() ?? 1;
@endphp

@forelse ($reports as $r)
    @php $no = $startNo + $loop->index; @endphp
    <tr class="border-b border-gray-200 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50">
        {{-- No --}}
        <td class="py-3 px-4 align-top">{{ $no }}</td>

        {{-- Tanggal --}}
        <td class="py-3 px-4 align-top">
            <div class="font-medium">{{ \Carbon\Carbon::parse($r->period)->format('d M Y') }}</div>
        </td>

        {{-- Datang / Pulang / Suhu / Sarapan --}}
        <td class="py-3 px-4 align-top">{{ $r->arrival_time ? substr($r->arrival_time, 0, 5) : '-' }}</td>
        <td class="py-3 px-4 align-top">{{ $r->departure_time ? substr($r->departure_time, 0, 5) : '-' }}</td>
        <td class="py-3 px-4 align-top">{{ $r->body_temperature ? number_format($r->body_temperature, 1) : '-' }}</td>
        <td class="py-3 px-4 align-top capitalize">{{ $r->breakfast ?: '-' }}</td>

        {{-- Kesehatan --}}
        <td class="py-3 px-4 align-top">
            <div class="capitalize">{{ $r->health_status ?: '-' }}</div>
            @if ($r->health_status === 'sakit')
                <div class="text-xs text-gray-500">{{ short($r->sickness_description, 40) }}</div>
                <div class="text-xs text-gray-500 capitalize">{{ $r->medication_status ?: '-' }}</div>
            @endif
        </td>

        {{-- Kondisi --}}
        <td class="py-3 px-4 align-top capitalize">{{ $r->condition ?: '-' }}</td>

        {{-- Stimulasi (ringkas) --}}
        <td class="py-3 px-4 align-top hidden lg:table-cell">
            <div class="text-xs leading-snug whitespace-pre-line">
                {{ short($r->stimulation_description, 120) }}
            </div>
        </td>

        {{-- Aksi --}}
        <td class="py-3 px-4 align-top">
            <div class="flex items-center gap-2 justify-center">
                <a href="{{ route('daily-report.show', $r->id) }}"
                    class="px-2 py-1 text-xs rounded bg-indigo-600 text-white hover:bg-indigo-700 inline-flex items-center gap-1">
                    <span class="material-symbols-outlined text-base">visibility</span>
                    Lihat
                </a>

                <button type="button"
                    class="px-2 py-1 text-xs rounded bg-red-600 text-white hover:bg-red-700 inline-flex items-center gap-1 btn-delete-report"
                    data-id="{{ $r->id }}"
                    data-period="{{ \Carbon\Carbon::parse($r->period)->format('d M Y') }}">
                    <span class="material-symbols-outlined text-base">delete</span>
                    Hapus
                </button>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="10" class="py-6 px-4 text-center text-gray-500">Belum ada laporan untuk transaksi ini.</td>
    </tr>
@endforelse
