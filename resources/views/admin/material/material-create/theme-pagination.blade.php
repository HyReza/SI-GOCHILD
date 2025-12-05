@if ($materials->hasPages())
    <div class="grid items-end">
        {{ $materials->links('pagination::tailwind') }}
        {{-- {!! $material->onEachSide(1)->links() !!} --}}
    </div>
@else
    <div class="text-sm text-gray-500 dark:text-gray-400">
        Menampilkan {{ $materials->total() }} data.
    </div>
@endif
