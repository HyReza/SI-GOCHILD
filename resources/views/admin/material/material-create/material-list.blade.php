<!-- material-list.blade.php -->
@forelse($materials as $index => $material)
    <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-800">
        <td class="py-3 px-6 text-left whitespace-nowrap">
            {{ ($materials->currentPage() - 1) * $materials->perPage() + $index + 1 }}
        </td>
        <td class="py-3 px-6 text-left">{{ $material->material_code }}</td>
        <td class="py-3 px-6 text-left">{{ $material->material_name }}</td>
        <td class="py-3 px-6 text-left">{{ $material->subTheme->sub_theme_name }}</td>
        <td class="py-3 px-6 text-left">{{ \Illuminate\Support\Str::limit($material->material_description, 30) }}</td>
        <td class="py-3 px-6 text-left">
            @if ($material->material_document)
                <a href="{{ asset('storage/material_documents/' . basename($material->material_document)) }}"
                    target="_blank" class="text-blue-500 underline">Lihat Dokumen</a>
            @else
                Tidak Ada Dokumen
            @endif
        </td>
        <td class="py-3 px-6 text-start">
            @if ($material->material_is_active)
                <span class="px-2 py-1 rounded-full bg-green-100 text-green-700 text-xs">Aktif</span>
            @else
                <span
                    class="px-2 py-1 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs">Nonaktif</span>
            @endif
        </td>
        <td class="py-3 px-6 text-start">
            @if ($material->material_on_report)
                <span class="px-2 py-1 rounded-full bg-green-100 text-green-700 text-xs">Ya Masuk</span>
            @else
                <span
                    class="px-2 py-1 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs">Tidak
                    Masuk</span>
            @endif
        </td>
        <td class="py-3 px-6 text-center">
            <div class="flex gap-2 justify-center">
                <a href="{{ route('material.show', $material->id) }}" class="relative group">
                    <span
                        class="material-symbols-outlined bg-blue-500 px-2 py-1 rounded-md text-white text-base font-extralight">
                        visibility
                    </span>
                    <span
                        class="absolute z-50 left-0 top-full mt-1 w-max px-2 py-1 rounded-md bg-gray-800 text-white text-xs hidden group-hover:block">
                        Lihat Detail
                    </span>
                </a>
                <a href="{{ route('material.edit', $material->id) }}" class="relative group">
                    <span
                        class="material-symbols-outlined bg-green-500 px-2 py-1 rounded-md text-white text-base font-extralight">
                        edit_square
                    </span>
                    <span
                        class="absolute z-50 left-0 top-full mt-1 w-max px-2 py-1 rounded-md bg-gray-800 text-white text-xs hidden group-hover:block">
                        Edit Data
                    </span>
                </a>
                <form id="delete-form-{{ $material->id }}" action="{{ route('material.destroy', $material) }}"
                    method="POST" class="relative group delete-form"
                    data-material-name="{{ $material->material_name }}">
                    @csrf
                    @method('DELETE')
                    <button type="button" onclick="confirmDelete({{ $material->id }})"
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
        <td colspan="9" class="py-3 px-6 text-center text-gray-500">Tidak ada materi ditemukan.</td>
    </tr>
@endforelse
