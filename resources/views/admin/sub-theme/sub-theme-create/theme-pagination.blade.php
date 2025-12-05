@if ($subThemes->hasPages())
    <div class="grid items-end">
        {{ $subThemes->links('pagination::tailwind') }}
        {{-- {!! $subThemes->onEachSide(1)->links() !!} --}}
    </div>
@else
    <div class="text-sm text-gray-500 dark:text-gray-400">
        Menampilkan {{ $subThemes->total() }} data.
    </div>
@endif
