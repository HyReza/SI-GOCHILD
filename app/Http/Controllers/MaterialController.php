<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\SubTheme;
use App\Http\Requests\StoreMaterialRequest;
use App\Http\Requests\UpdateMaterialRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     // Get all materials with eager loading for subTheme


    //     // Return view with materials and subThemes data
    //     return view('admin.material.material-index.index', compact('materials'));
    // }

    /**
     * Show the form for creating a new resource.
     */
    // public function create()
    // {
    //     // Fetch all SubThemes for the dropdown
    //     $subThemes = SubTheme::all();
    //     $materials = Material::with('subTheme')->orderBy('created_at', 'desc')
    //         ->paginate(10);
    //     // Return create view with subThemes data
    //     return view('admin.material.material-create.index', compact('subThemes', 'materials'));
    // }

    public function create(Request $request)
    {
        // Fetch all SubThemes for the dropdown
        $subThemes = SubTheme::where('sub_theme_is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();

        // Query to fetch the materials based on the search parameter
        $materialsQuery = Material::with('subTheme')
            ->orderBy('created_at', 'desc');

        // If a search query is provided, filter materials
        if ($request->has('search')) {
            $search = $request->input('search');
            $materialsQuery->where('material_name', 'like', "%{$search}%")
                ->orWhere('material_code', 'like', "%{$search}%")
                ->orWhere('material_description', 'like', "%{$search}%");
        }

        // Paginate the materials
        $materials = $materialsQuery->paginate(10);

        // Return the view with the required data
        if ($request->ajax()) {
            $tbodyHtml = view('admin.material.material-create.material-list', compact('materials'))->render();
            $paginationHtml = view('admin.material.material-create.theme-pagination', compact('materials'))->render();

            return response()->json([
                'tbody' => $tbodyHtml,
                'pagination' => $paginationHtml,
            ]);
        }

        // Non-AJAX request: return full view
        return view('admin.material.material-create.index', compact('subThemes', 'materials'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'material_code' => 'required|string|max:255|unique:materials,material_code',
            'material_name' => 'required|string|max:255',
            'material_description' => 'required|string',
            'sub_theme_id' => 'required|exists:sub_themes,id',
            'material_document' => 'nullable|file|mimes:pdf,docx,doc,pptx|max:10240', // Validate document
            'material_on_report' => 'required|in:1,0',
        ]);

        // Handle file upload if there is a document
        $documentPath = null;
        if ($request->hasFile('material_document')) {
            $documentPath = $request->file('material_document')->store('material_documents', 'public');
        }

        // Create the material
        Material::create([
            'material_code' => $request->material_code,
            'material_name' => $request->material_name,
            'material_description' => $request->material_description,
            'sub_theme_id' => $request->sub_theme_id,
            'material_document' => $documentPath,  // Store file path
            'material_on_report' => $request->material_on_report,
            'material_is_active' => 1,
        ]);

        // Redirect back with success message
        return redirect()->route('material.create')->with('success', 'Materi berhasil ditambahkan');
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Ambil data material berdasarkan ID, dan eager load 'subTheme'
        $material = Material::with('subTheme')->findOrFail($id);

        // Kembalikan view dengan data material
        return view('admin.material.material-show.index', compact('material'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // Fetch the material by ID
        $material = Material::findOrFail($id);

        // Fetch all SubThemes for the dropdown
        $subThemes = SubTheme::where('sub_theme_is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();

        // Return the edit view with the material and subThemes data
        return view('admin.material.material-edit.index', compact('material', 'subThemes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validate the input
        $request->validate([
            'material_code' => 'required|string|max:255|unique:materials,material_code,' . $id,
            'material_name' => 'required|string|max:255',
            'material_description' => 'required|string',
            'sub_theme_id' => 'required|exists:sub_themes,id',
            'material_document' => 'nullable|file|mimes:pdf,docx,doc,pptx|max:10240',
            'material_on_report' => 'required|in:0,1',
            'material_is_active' => 'required|in:0,1',
        ]);

        // Find the material by id
        $material = Material::findOrFail($id);

        // Handle document upload if there is a new material document
        if ($request->hasFile('material_document')) {
            // Delete the old document if exists
            if ($material->material_document && Storage::disk('public')->exists($material->material_document)) {
                Storage::disk('public')->delete($material->material_document);
            }
            // Store the new document
            $documentPath = $request->file('material_document')->store('material_documents', 'public');
        } else {
            $documentPath = $material->material_document; // Keep the existing document path
        }

        // Update the material
        $material->update([
            'material_code' => $request->material_code,
            'material_name' => $request->material_name,
            'material_description' => $request->material_description,
            'sub_theme_id' => $request->sub_theme_id,
            'material_document' => $documentPath, // Update the document path if changed
            'material_on_report' => $request->material_on_report,
            'material_is_active' => $request->material_is_active,
        ]);

        // Redirect back with success message
        return redirect()->route('material.create')->with('success', 'Materi berhasil diperbarui!');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Find the material by id
        $material = Material::findOrFail($id);

        // If material has a document, delete it
        if ($material->material_document && Storage::disk('public')->exists($material->material_document)) {
            Storage::disk('public')->delete($material->material_document);
        }

        // Delete the material
        $material->delete();

        // Redirect with success message
        return redirect()->route('material.create')->with('success', 'Materi dan dokumen terkait berhasil dihapus!');
    }

    public function generateMaterialCode($subThemeId)
    {
        // Fetch the sub-theme by ID
        $subTheme = SubTheme::findOrFail($subThemeId);
        $prefix = $subTheme->sub_theme_code;  // e.g., AE02D

        // Find the last material code based on the sub-theme
        $lastMaterial = Material::where('material_code', 'like', $prefix . '%')
            ->orderBy('material_code', 'desc')
            ->first();

        // Generate new material code
        if (!$lastMaterial) {
            $newCode = $prefix . '001';  // Start with 001
        } else {
            // Extract the numeric part of the last code (e.g., AE02D001 -> 001)
            $lastCode = substr($lastMaterial->material_code, -3);
            $nextNumber = (int)$lastCode + 1;

            // If number exceeds 999, reset to 0001
            if ($nextNumber > 999) {
                $nextNumber = 1;
            }

            $newCode = $prefix . sprintf('%03d', $nextNumber);  // Generate the next code
        }

        return response()->json(['newCode' => $newCode]);
    }
}
