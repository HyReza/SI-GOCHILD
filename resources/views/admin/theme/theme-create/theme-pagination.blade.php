@if ($themes->hasPages())
    <div class="grid items-end">
        {{ $themes->links('pagination::tailwind') }}
        {{-- {!! $themes->onEachSide(1)->links() !!} --}}
    </div>
@else
    <div class="text-sm text-gray-500 dark:text-gray-400">
        Menampilkan {{ $themes->total() }} data.
    </div>
@endif
