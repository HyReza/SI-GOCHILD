@php
    $startNo = ($themes->currentPage() - 1) * $themes->perPage() + 1;
@endphp

@forelse ($themes as $index => $theme)
    <tr class="border-b border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700">
        <td class="py-3 px-6 text-left">{{ $startNo + $index }}</td>
        <td class="py-3 px-6 text-left">{{ $theme->theme_code }}</td>
        <td class="py-3 px-6 text-left">{{ $theme->theme_name }}</td>
        <td class="py-3 px-6 text-left">{{ \Illuminate\Support\Str::limit($theme->theme_description, 30) }}</td>
        <td class="py-3 px-6 text-left">
            @if ($theme->theme_document)
                <a href="{{ asset('storage/' . $theme->theme_document) }}" target="_blank" class="text-blue-500">Lihat</a>
            @else
                Tidak Ada Dokumen
            @endif
        </td>
        <td class="py-3 px-6 text-start">
            @if ($theme->theme_is_active)
                <span class="px-2 py-1 rounded-full bg-green-100 text-green-700 text-xs">Aktif</span>
            @else
                <span
                    class="px-2 py-1 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs">Nonaktif</span>
            @endif
        </td>
        <td class="py-3 px-6 text-start">
            @if ($theme->theme_on_report)
                <span class="px-2 py-1 rounded-full bg-green-100 text-green-700 text-xs">Ya Masuk</span>
            @else
                <span
                    class="px-2 py-1 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs">Tidak
                    Masuk</span>
            @endif
        </td>
        <td class="py-3 px-6 text-center">
            <div class="flex gap-2 justify-center">
                {{-- ACTION BUTTON --}}
                <a href="{{ route('themes.show', $theme->id) }}" class="relative group">
                    <span
                        class="material-symbols-outlined bg-blue-500 px-2 py-1 rounded-md text-white text-base font-extralight">
                        visibility
                    </span>
                    <span
                        class="absolute z-50 left-0 top-full mt-1 w-max px-2 py-1 rounded-md bg-gray-800 text-white text-xs hidden group-hover:block">
                        Lihat Detail
                    </span>
                </a>
                <a href="{{ route('themes.edit', $theme) }}" class="relative group">
                    <span
                        class="material-symbols-outlined bg-green-500 px-2 py-1 rounded-md text-white text-base font-extralight">
                        edit_square
                    </span>
                    <span
                        class="absolute z-50 left-0 top-full mt-1 w-max px-2 py-1 rounded-md bg-gray-800 text-white text-xs hidden group-hover:block">
                        Edit Data
                    </span>
                </a>
                <form id="delete-form-{{ $theme->id }}" action="{{ route('themes.destroy', $theme) }}" method="POST"
                    class="relative group delete-form" data-theme-name="{{ $theme->theme_name }}">
                    @csrf
                    @method('DELETE')
                    <button type="button" onclick="confirmDelete({{ $theme->id }})"
                        class="material-symbols-outlined bg-red-500 px-2 py-1 rounded-md text-white text-base font-extralight delete-button">
                        delete
                    </button>
                    <span
                        class="absolute z-50 right-0 top-full mt-1 w-max px-2 py-1 rounded-md bg-gray-800 text-white text-xs hidden group-hover:block">
                        Hapus Data
                    </span>
                </form>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="py-6 text-center text-gray-500 dark:text-gray-400">
            Tidak ada data untuk kata kunci tersebut.
        </td>
    </tr>
@endforelse
