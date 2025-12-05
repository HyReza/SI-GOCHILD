@if ($activityTransactions->hasPages())
    <div class="inline-block">
        {{-- Link bawaan Tailwind. Kliknya di-intercept JS agar AJAX --}}
        {{ $activityTransactions->links('pagination::tailwind') }}
    </div>
@else
    <div class="text-sm text-gray-400 dark:text-gray-500">Menampilkan {{ $activityTransactions->total() }} data</div>
@endif
