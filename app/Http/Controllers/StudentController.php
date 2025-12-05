<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Program;
use App\Models\Service;
use App\Models\ActivityTransaction;
use Illuminate\Http\Request;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    // public function index()
    // {
    //     $students = Student::with(['activityTransaction.program', 'activityTransaction.service'])
    //         ->orderBy('created_at', 'desc')
    //         ->paginate(10);

    //     return view('admin.students.index-students.index', compact('students'));
    // }

    public function index(Request $request)
    {
        // Ambil query pencarian dari inputan
        $query = $request->input('search', ''); // Menggunakan default empty string jika tidak ada query

        // Ambil data siswa berdasarkan pencarian dan pagination
        $students = Student::with(['activityTransaction.program', 'activityTransaction.service'])
            ->when($query, function ($queryBuilder) use ($query) {
                $s = trim($query);
                return $queryBuilder->where(function ($q) use ($s) {
                    $q->where('student_name', 'like', "%{$s}%")
                        ->orWhere('student_number', 'like', "%{$s}%")
                        ->orWhere('mother_name', 'like', "%{$s}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends(['search' => $query]); // Menambahkan query pencarian ke pagination

        // AJAX: Kembalikan HTML yang diperlukan untuk update tabel dan pagination
        if ($request->ajax()) {
            $tbodyHtml = view('admin.students.index-students.student_table', compact('students'))->render();
            $paginationHtml = view('admin.students.index-students.student_pagination', compact('students'))->render();

            return response()->json([
                'tbody' => $tbodyHtml,
                'pagination' => $paginationHtml,
            ]);
        }

        // Non-AJAX: Render halaman penuh
        return view('admin.students.index-students.index', compact('students'));
    }





    public function create()
    {
        $programs = Program::all();
        $services = Service::all();

        // Generate new Student Number
        $newStudentNumber = $this->generateStudentNumber();

        return view('admin.students.create-students.index', compact('programs', 'services', 'newStudentNumber'));
    }

    public function store(Request $request)
    {
        // Validate input data
        $validatedData = $request->validate([
            'national_id' => 'nullable|string',
            'student_name' => 'required|string|max:255',
            'nickname' => 'required|string|max:255',
            'gender' => 'required|boolean|in:1,0',
            'birth_place' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'father_name' => 'required|string|max:255',
            'mother_name' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'village' => 'required|string|max:255',
            'subdistrict' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'phone_number' => 'required|string',
            'user_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:10000',
            'program_id' => 'required|exists:programs,id', // Validasi program_id
            'service_id' => 'required|exists:services,id', // Validasi service_id
            'start_date' => 'date',
            'end_date' => 'date|after_or_equal:start_date',
            'password' => 'nullable|string|min:8|confirmed',
            'student_description' => 'nullable|string',
            'student_is_normal' => 'required|boolean|in:1,0'
        ]);

        // Generate unique Student Number
        $validatedData['student_number'] = $this->generateStudentNumber();

        // Handle file upload for user_photo
        if ($request->hasFile('user_photo')) {
            $validatedData['user_photo'] = $this->storeImage($request->file('user_photo'));
        }

        // Save student data excluding program_id and service_id
        $studentData = $validatedData;
        unset($studentData['program_id'], $studentData['service_id'], $studentData['start_date'], $studentData['student_is_normal']);

        $studentData['entry_date'] = now();
        // Save student data
        $studentData['password'] = bcrypt($studentData['password']);
        $student = Student::create($studentData);

        // Save activity transaction data
        ActivityTransaction::create([
            'student_id' => $student->id,
            'program_id' => $validatedData['program_id'],
            'service_id' => $validatedData['service_id'],
            'start_date' => Carbon::now(),  // Using current date for start_date
            'student_status' => true,
            'student_is_normal' => $validatedData['student_is_normal']
        ]);

        return redirect()->route('siswa.index')->with('success', 'Student successfully added.');
    }

    private function generateStudentNumber()
    {
        $year = now()->year;

        // Count the total number of students registered this year
        $studentCount = Student::whereYear('entry_date', $year)->count();

        // Generate the new Student Number based on the count
        $newStudentNumber = $year . str_pad($studentCount + 1, 4, '0', STR_PAD_LEFT);

        // Ensure Student Number is unique
        while (Student::where('student_number', $newStudentNumber)->exists()) {
            $studentCount++;
            $newStudentNumber = $year . str_pad($studentCount + 1, 4, '0', STR_PAD_LEFT);
        }

        return $newStudentNumber;
    }

    private function storeImage($image)
    {
        $uniqueName = Str::uuid()->toString() . '.' . $image->getClientOriginalExtension();
        return $image->storeAs('students', $uniqueName, 'public');
    }

    public function show($id)
    {
        // Retrieve the student along with related activityTransaction, program, and service
        $student = Student::with(['activityTransaction.program', 'activityTransaction.service'])->findOrFail($id);

        return view('admin.students.show-students.index', compact('student'));
    }

    public function edit($id)
    {
        // Get student along with related activity transaction, program and service
        $student = Student::with(['activityTransaction.program', 'activityTransaction.service'])->findOrFail($id);
        $programs = Program::all();
        $services = Service::all();

        return view('admin.students.edit-students.index', compact('student', 'programs', 'services'));
    }

    public function update(Request $request, $id)
    {
        // Temukan student berdasarkan ID
        $student = Student::findOrFail($id);

        // Validasi data input
        $validatedData = $request->validate([
            'national_id' => 'nullable|string',
            'student_name' => 'required|string|max:255',
            'nickname' => 'required|string|max:255',
            'gender' => 'required|boolean|in:1,0',
            'birth_place' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'father_name' => 'required|string|max:255',
            'mother_name' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'village' => 'required|string|max:255',
            'subdistrict' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'phone_number' => 'required|string',
            'user_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:10000',
            'program_id' => 'required|exists:programs,id', // Validasi program_id
            'service_id' => 'required|exists:services,id', // Validasi service_id
            'start_date' => 'date',
            'end_date' => 'date|after_or_equal:start_date',
            'password' => 'nullable|string|min:8|confirmed',
            'student_status' => 'required',
            'student_description' => 'nullable|string',
            'student_is_normal' => 'required|boolean|in:1,0'
        ]);

        // Jika password diubah, enkripsi password
        if ($request->filled('password')) {
            $validatedData['password'] = bcrypt($request->input('password'));
        } else {
            unset($validatedData['password']); // Jika password tidak diubah, hapus field password
        }

        // Jika ada foto yang diupload, hapus foto lama dan simpan foto baru
        if ($request->hasFile('user_photo')) {
            if ($student->user_photo) {
                Storage::disk('public')->delete($student->user_photo); // Hapus foto lama
            }

            $newPhoto = $this->storeImage($request->file('user_photo'));
            $validatedData['user_photo'] = $newPhoto;
        }

        // Update atau buat data baru di tabel activity_transactions
        ActivityTransaction::updateOrCreate(
            ['student_id' => $student->id],
            [
                'program_id' => $validatedData['program_id'],
                'service_id' => $validatedData['service_id'],
                'student_status' => $validatedData['student_status'] ?? true,
                'student_is_normal' => $validatedData['student_is_normal'] ?? true
            ]
        );

        // Menghapus 'program_id' dan 'service_id' dari data validasi
        $validatedData = array_diff_key($validatedData, array_flip(['program_id', 'service_id', 'student_status', 'student_is_normal']));

        // Lakukan update tanpa memasukkan 'program_id' dan 'service_id'
        $student->update($validatedData);

        return redirect()->route('siswa.index')->with('success', 'Student data and activity transaction successfully updated.');
    }

    public function destroy(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        // Verifikasi password pengguna yang sedang login
        if (!Hash::check($request->password, auth()->user()->password)) {
            return redirect()->route('siswa.index')->with('error', 'Password salah!');
        }

        // Hapus foto pengguna jika ada
        if ($student->user_photo && Storage::disk('public')->exists($student->user_photo)) {
            Storage::disk('public')->delete($student->user_photo);
        }

        // Hapus data siswa
        $student->delete();

        return redirect()->route('siswa.index')->with('success', 'Student successfully deleted.');
    }
}
