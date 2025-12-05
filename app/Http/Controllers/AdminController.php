<?php

namespace App\Http\Controllers;

use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $admins = User::where('role_id', 1)
            ->where(function ($query) use ($search) {
                $query->where('user_name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('admin.admin.index-admin.index', compact('admins'));
    }


    // Show the form for creating a new admin
    public function create()
    {
        return view('admin.admin.admin-create.index');
    }

    // Store a newly created admin in storage
    public function store(Request $request)
    {
        // Validate incoming request data
        $validated = $request->validate([
            'user_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'nullable|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Create a new Admin  record
        User::create([
            'user_name' => $validated['user_name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'role_id' => 1,  // Automatically set to "Admin"
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('admin.index')->with('success', 'Admin berhasil ditambahkan!');
    }

    // Show the details of a specific admin
    public function show($id)
    {
        $admins = User::findOrFail($id);
        return response()->json($admins); // For modal to show teacher's details
    }

    // Show the form for editing the specified admin
    public function edit($id)
    {
        $admin = User::findOrFail($id);
        return view('admin.admin.edit-admin.index', compact('admin')); // Load the edit form
    }

    // Update the specified admin in storage
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'user_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone_number' => 'nullable|string',
            'role_id' => 'required|integer', // Add validation for role
            'password' => 'nullable|string|min:8|confirmed', // Password is optional for updates
        ]);

        // Find the admin and update the record
        $admin = User::findOrFail($id);
        $admin->update([
            'user_name' => $validated['user_name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'role_id' => $validated['role_id'], // Update role
            'password' => $validated['password'] ? Hash::make($validated['password']) : $admin->password,
        ]);

        return redirect()->route('admin.index')->with('success', 'Admin berhasil diperbarui!');
    }


    // Remove the specified admin from storage
    public function destroy($id)
    {
        $admin = User::findOrFail($id);
        $admin->delete();

        return redirect()->route('admin.index')->with('success', 'admin berhasil dihapus!');
    }
}
