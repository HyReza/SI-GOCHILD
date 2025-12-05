<?php

namespace App\Http\Controllers;

use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $teachers = User::where('role_id', 2)
            ->where(function ($query) use ($search) {
                $query->where('user_name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('admin.teacher.index-teacher.index', compact('teachers'));
    }


    // Show the form for creating a new teacher
    public function create()
    {
        return view('admin.teacher.teacher-create.index');
    }

    // Store a newly created teacher in storage
    public function store(Request $request)
    {
        // Validate incoming request data
        $validated = $request->validate([
            'user_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'nullable|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Create a new teacher (Pengajar) record
        User::create([
            'user_name' => $validated['user_name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'role_id' => 2,  // Automatically set to "Pengajar"
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('pengajar.index')->with('success', 'Pengajar berhasil ditambahkan!');
    }

    // Show the details of a specific teacher
    public function show($id)
    {
        $teacher = User::findOrFail($id);
        return response()->json($teacher); // For modal to show teacher's details
    }

    // Show the form for editing the specified teacher
    public function edit($id)
    {
        $teacher = User::findOrFail($id);
        return view('admin.teacher.edit-teacher.index', compact('teacher')); // Load the edit form
    }

    // Update the specified teacher in storage
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'user_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone_number' => 'nullable|string',
            'role_id' => 'required|integer', // Add validation for role
            'password' => 'nullable|string|min:8|confirmed', // Password is optional for updates
        ]);

        // Find the teacher and update the record
        $teacher = User::findOrFail($id);
        $teacher->update([
            'user_name' => $validated['user_name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'role_id' => $validated['role_id'], // Update role
            'password' => $validated['password'] ? Hash::make($validated['password']) : $teacher->password,
        ]);

        return redirect()->route('pengajar.index')->with('success', 'Pengajar berhasil diperbarui!');
    }


    // Remove the specified teacher from storage
    public function destroy($id)
    {
        $teacher = User::findOrFail($id);
        $teacher->delete();

        return redirect()->route('pengajar.index')->with('success', 'Pengajar berhasil dihapus!');
    }
}
