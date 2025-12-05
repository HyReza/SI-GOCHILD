@forelse ($students as $student)
    <tr class="border-b border-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:bg-opacity-50">
        <td class="py-3 px-6 text-left whitespace-nowrap">{{ $students->firstItem() + $loop->iteration - 1 }}</td>
        <td class="py-3 px-6 text-left hidden lg:table-cell">
            <div class="flex flex-col lg:flex-row lg:items-center lg:gap-4">
                <div>
                    <h1 class="font-medium text-xs">{{ $student->student_number }}</h1>
                </div>
            </div>
        </td>
        <td class="py-3 px-6 text-left">
            <div>{{ $student->student_name }}</div>
        </td>
        <td class="py-3 px-6 text-left hidden lg:table-cell">
            <div>{{ $student->mother_name }}</div>
        </td>
        <td class="py-3 px-6 text-left hidden lg:table-cell">
            <div>{{ $student->street }}, {{ $student->village }}, {{ $student->subdistrict }},
                {{ $student->district }}</div>
        </td>
        <td class="py-3 px-6 text-center">
            <div class="flex gap-2 justify-center">
                <a href="{{ route('siswa.show', $student->id) }}" class="relative group">
                    <span
                        class="material-symbols-outlined bg-indigo-400 px-2 py-1 rounded-md text-white text-base font-extralight">visibility</span>
                </a>
                <a href="{{ route('siswa.edit', $student) }}" class="relative group">
                    <span
                        class="material-symbols-outlined bg-green-500 px-2 py-1 rounded-md text-white text-base font-extralight">edit_square</span>
                </a>
                <button type="button"
                    class="material-symbols-outlined bg-red-500 px-2 py-1 rounded-md text-white text-base font-extralight delete-button"
                    data-student-id="{{ $student->id }}" data-student-name="{{ $student->student_name }}">
                    delete
                </button>
            </div>
        </td>
    </tr>
@empty
    <tr class="border-b border-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:bg-opacity-50">
        <td colspan="6" class="py-3 px-6 text-center text-gray-500">Tidak ada data siswa</td>
    </tr>
@endforelse
<script>
    $(document).ready(function() {
        // Show/Hide password
        $("span[toggle='#password']").click(function() {
            var input = $("#password");
            var icon = $(this).find('i');
            if (input.attr("type") === "password") {
                input.attr("type", "text");
                icon.text("visibility");
            } else {
                input.attr("type", "password");
                icon.text("visibility_off");
            }
        });

        // Show modal on delete button click
        $('.delete-button').on('click', function() {
            const studentId = $(this).data('student-id');
            const studentName = $(this).data('student-name');

            $('#studentName').text(studentName); // Show student name in modal
            $('#deleteForm').attr('action', `/siswa/${studentId}`); // Set form action to delete student
            $('#deleteModal').removeClass('hidden'); // Show modal
        });

        // Cancel delete
        $('#cancelDelete').on('click', function() {
            $('#deleteModal').addClass('hidden'); // Hide modal
        });
    });
</script>
