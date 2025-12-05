@php
    $startNo = ($activityTransactions->currentPage() - 1) * $activityTransactions->perPage();
@endphp

@forelse ($activityTransactions as $i => $activityTransaction)
    <tr class="border-b border-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:bg-opacity-50">
        <td class="py-3 px-6 text-left whitespace-nowrap">
            {{ $startNo + $i + 1 }}
        </td>
        <td class="py-3 px-6 text-left">
            {{ $activityTransaction->student->student_number }}
        </td>
        <td class="py-3 px-6 text-left">
            {{ $activityTransaction->student->student_name }}
        </td>
        <td class="py-3 px-6 text-left">
            {{ $activityTransaction->student->mother_name }}
        </td>
        <td class="py-3 px-6 text-left hidden lg:table-cell">
            {{ $activityTransaction->student->street }},
            {{ $activityTransaction->student->village }},
            {{ $activityTransaction->student->subdistrict }},
            {{ $activityTransaction->student->district }}
        </td>
        <td class="py-3 px-6 text-center">
            <div class="flex gap-2 justify-center">
                <a href="{{ route('daily-report.create', $activityTransaction->id) }}" class="relative group">
                    <div class="flex bg-green-500 px-3 py-1 text-white justify-center items-center rounded-md">
                        <span class="material-symbols-outlined text-base font-extralight mr-2">edit_square</span>
                        <p class="text-xs">Create Laporan</p>
                    </div>
                </a>

                <a href="{{ route('daily-report.history', $activityTransaction->id) }}" class="relative group">
                    <div class="flex bg-indigo-400 px-3 py-1 text-white justify-center items-center rounded-md">
                        <span
                            class="material-symbols-outlined text-white text-base font-extralight mr-2">visibility</span>
                        <p class="text-xs">History Laporan</p>
                    </div>
                </a>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="py-3 px-6 text-center text-gray-500">
            Tidak ada data siswa yang tersedia.
        </td>
    </tr>
@endforelse
