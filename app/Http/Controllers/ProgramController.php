<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Http\Requests\StoreProgramRequest;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateProgramRequest;

class ProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $programs = Program::all();
        return view('admin.program.program-index.index', compact('programs'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProgramRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Program $program)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $program = Program::findOrFail($id);
        return view('admin.program.program-edit.index', compact('program'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validasi inputan
        $request->validate([
            'program_name' => 'required|string',
            'program_description' => 'required|string',
            'program_price' => 'required|integer',
            'start_time' => 'required',
            'end_time' => 'required|after_or_equal:start_time',
        ], [
            'end_time.after_or_equal' => 'Jam selesai tidak boleh lebih kecil dari jam mulai.',
        ]);

        // Temukan program berdasarkan id dan update
        $program = Program::findOrFail($id);
        $program->update($request->all());

        // Redirect ke halaman sebelumnya dengan pesan sukses
        return redirect()->route('catalog-programs.index')->with('success', 'Program berhasil diperbarui!');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Program $program)
    {
        //
    }
}
