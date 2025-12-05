@if ($reports->hasPages())
    <div class="inline-block">
        {{ $reports->links('pagination::tailwind') }}
    </div>
@else
    <div class="text-sm text-gray-400 dark:text-gray-500">
        Menampilkan {{ $reports->total() }} laporan
    </div>
@endif
