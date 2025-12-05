<?php

namespace App\Http\Controllers;

use App\Models\SubTheme;
use App\Models\Material;
use App\Models\Theme;
use App\Http\Requests\StoreSubThemeRequest;
use App\Http\Requests\UpdateSubThemeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SubThemeController extends Controller
{

    public function create(Request $request)
    {
        // Fetch available themes for the dropdown
        $themes = Theme::orderBy('theme_code', 'asc')
            ->when($request->search, function ($query) use ($request) {
                $s = trim($request->search);
                return $query->where(function ($q) use ($s) {
                    $q->where('theme_name', 'like', "%{$s}%")
                        ->orWhere('theme_code', 'like', "%{$s}%")
                        ->orWhere('theme_description', 'like', "%{$s}%");
                });
            })
            ->paginate(10)
            ->appends(['search' => $request->search]); // Ensure pagination keeps the search query

        // Fetch available themes for the dropdown
        $themes = Theme::where('theme_is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();

        $newCode = '';

        // Check if a theme is selected from the dropdown
        if ($request->theme_id) {
            // Fetch the selected theme by ID
            $theme = Theme::findOrFail($request->theme_id);
            $prefix = $theme->theme_code;  // Theme prefix (e.g., "AA")

            // Generate the new sub-theme code
            $newCode = $this->generateSubThemeCode($prefix);
        }

        // Fetch sub-themes for pagination and search functionality
        $subThemes = SubTheme::with('theme')
            ->when($request->search, function ($query) use ($request) {
                $s = trim($request->search);
                return $query->where(function ($q) use ($s) {
                    $q->where('sub_theme_name', 'like', "%{$s}%")
                        ->orWhere('sub_theme_code', 'like', "%{$s}%")
                        ->orWhere('sub_theme_description', 'like', "%{$s}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends(['search' => $request->search]);

        // AJAX: return partial HTML (tbody + pagination) as JSON
        if ($request->ajax()) {
            $tbodyHtml = view('admin.sub-theme.sub-theme-create.sub-theme-list', compact('subThemes'))->render();
            $paginationHtml = view('admin.sub-theme.sub-theme-create.theme-pagination', compact('subThemes'))->render();

            return response()->json([
                'tbody'      => $tbodyHtml,
                'pagination' => $paginationHtml,
            ]);
        }

        // Non-AJAX: render full page
        return view('admin.sub-theme.sub-theme-create.index', compact('themes', 'newCode', 'subThemes'));
    }


    public function store(Request $request)
    {
        // Validate input with unique check
        $request->validate([
            'sub_theme_code' => 'required|string|max:255|unique:sub_themes,sub_theme_code', // Check uniqueness in the database
            'sub_theme_name' => 'required|string|max:255',
            'sub_theme_description' => 'required|string',
            'sub_theme_start' => 'required|date|before_or_equal:sub_theme_end',
            'sub_theme_end' => 'required|date|after_or_equal:sub_theme_start',
            'sub_theme_document' => 'nullable|file|mimes:pdf,docx|max:10240',
            'theme_id' => 'required|exists:themes,id',
            'sub_theme_on_report' => 'required|in:0,1'
        ]);

        // Handle file upload if there's a document
        $documentPath = null;
        if ($request->hasFile('sub_theme_document')) {
            $documentPath = $request->file('sub_theme_document')->store('sub_theme_documents', 'public');
        }

        // Create the sub-theme with the submitted code from frontend
        SubTheme::create([
            'sub_theme_code' => $request->sub_theme_code, // Use the code submitted from the frontend
            'sub_theme_name' => $request->sub_theme_name,
            'sub_theme_description' => $request->sub_theme_description,
            'sub_theme_start' => $request->sub_theme_start,
            'sub_theme_end' => $request->sub_theme_end,
            'sub_theme_document' => $documentPath,
            'theme_id' => $request->theme_id,
            'sub_theme_on_report' => $request->sub_theme_on_report,
            'sub_theme_is_active' => 1,
        ]);

        return redirect()->route('subthemes.create')->with('success', 'Sub Tema Berhasil Ditambahkan!');
    }




    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Fetch the SubTheme by ID
        $subTheme = SubTheme::findOrFail($id);

        // Return the show details view with subTheme
        return view('admin.sub-theme.sub-theme-show.index', compact('subTheme'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // Fetch the SubTheme by ID
        $subTheme = SubTheme::findOrFail($id);
        $themes = Theme::where('theme_is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();

        // Return the edit view with subTheme and themes
        return view('admin.sub-theme.sub-theme-edit.index', compact('subTheme', 'themes'));
    }


    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'sub_theme_code' => 'required|string|max:255|unique:sub_themes,sub_theme_code,' . $id,
            'sub_theme_name' => 'required|string|max:255',
            'sub_theme_description' => 'required|string',
            'sub_theme_start' => 'required|date|before_or_equal:sub_theme_end',
            'sub_theme_end' => 'required|date|after_or_equal:sub_theme_start',
            'sub_theme_document' => 'nullable|file|mimes:pdf,docx|max:10240',
            'sub_theme_on_report' => 'required|in:0,1',
            'sub_theme_is_active' => 'required|in:0,1'
        ]);

        // Fetch the sub-theme by ID
        $subTheme = SubTheme::findOrFail($id);

        // Check if the sub-theme is used in materials
        $materialCount = Material::where('sub_theme_id', $id)->count();

        // Check if the theme has been changed and if the sub-theme is used in materials
        if ($subTheme->theme_id != $request->theme_id && $materialCount > 0) {
            // If the theme is changed and the sub-theme is used, prevent update
            return redirect()->route('subthemes.edit', $subTheme->id)
                ->with('error', 'Sub-theme ini tidak dapat diubah karena sedang digunakan di materi.');
        }

        // Proceed with updating the sub-theme
        $subTheme->sub_theme_code = $request->sub_theme_code;
        $subTheme->sub_theme_name = $request->sub_theme_name;
        $subTheme->sub_theme_description = $request->sub_theme_description;
        $subTheme->sub_theme_start = $request->sub_theme_start;
        $subTheme->sub_theme_end = $request->sub_theme_end;
        $subTheme->theme_id = $request->theme_id;
        $subTheme->sub_theme_on_report = $request->sub_theme_on_report;
        $subTheme->sub_theme_is_active = $request->sub_theme_is_active;

        // Handle file upload if there's a document
        if ($request->hasFile('sub_theme_document')) {
            // Delete old document if exists
            if ($subTheme->sub_theme_document && Storage::disk('public')->exists($subTheme->sub_theme_document)) {
                Storage::disk('public')->delete($subTheme->sub_theme_document);
            }

            $subTheme->sub_theme_document = $request->file('sub_theme_document')->store('sub_theme_documents', 'public');
        }

        // Save the updated sub-theme
        $subTheme->save();

        // Redirect to the edit page with success message
        return redirect()->route('subthemes.create', $subTheme->id)
            ->with('success', 'Sub Tema Berhasil Diperbarui!');
    }




    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        // Temukan sub-theme berdasarkan ID
        $subTheme = SubTheme::findOrFail($id);

        // Periksa apakah sub-theme ini sedang digunakan di material
        $materialCount = Material::where('sub_theme_id', $id)->count();

        // Jika ada material yang menggunakan sub-theme ini, tidak izinkan penghapusan dan beri peringatan
        if ($materialCount > 0) {
            return redirect()->route('subthemes.create')->with('error', 'Sub-theme ini tidak dapat dihapus karena sedang digunakan di Materi');
        }

        // Jika sub-theme memiliki dokumen, hapus dokumen tersebut
        if ($subTheme->sub_theme_document && Storage::disk('public')->exists($subTheme->sub_theme_document)) {
            Storage::disk('public')->delete($subTheme->sub_theme_document);
        }

        // Hapus sub-theme
        $subTheme->delete();

        // Redirect dengan pesan sukses
        return redirect()->route('subthemes.create')->with('success', 'Sub Tema dan dokumen terkait berhasil dihapus!');
    }

    public function generateSubThemeCode($themeId)
    {
        // Fetch the theme by ID
        $theme = Theme::findOrFail($themeId);
        $prefix = $theme->theme_code;  // Kode tema (e.g., "AA")

        // Generate sub-theme code based on the selected theme and check for existing codes
        // First, find the last code by sorting them in descending order
        $lastSubTheme = SubTheme::where('sub_theme_code', 'like', $prefix . '%')
            ->orderBy('sub_theme_code', 'desc')
            ->first();

        // If there are no sub-themes, start with "01A"
        if (!$lastSubTheme) {
            return response()->json(['newCode' => $prefix . '01A']);
        }

        // Extract the numerical part of the last code (e.g., "01A" -> 1A)
        $lastCode = substr($lastSubTheme->sub_theme_code, -3);
        $lastNumber = (int)substr($lastCode, 0, 2);  // Extract numeric part (e.g., 1 from "01A")
        $lastSuffix = substr($lastCode, 2, 1); // Extract the letter part (e.g., "A" from "01A")

        // Start from the last used number and try to find the next available code
        $nextCodeNumber = $lastNumber;
        $nextSuffix = chr(ord($lastSuffix) + 1); // Increment suffix (e.g., "A" -> "B")

        // Handle suffix overflow: if "Z" -> "A", increment number
        if ($nextSuffix > 'Z') {
            $nextSuffix = 'A';
            $nextCodeNumber++;
        }

        // Generate new code and check if it already exists
        $newCode = $prefix . sprintf('%02d', $nextCodeNumber) . $nextSuffix;

        // Check if the generated code already exists
        while (SubTheme::where('sub_theme_code', $newCode)->exists()) {
            // If the code exists, increment the suffix or number
            if ($nextSuffix == 'Z') {
                $nextCodeNumber++;
                $nextSuffix = 'A'; // Reset suffix
            } else {
                $nextSuffix = chr(ord($nextSuffix) + 1); // Increment suffix (e.g., "A" -> "B")
            }

            // Ensure code doesn't go over the 99 limit for numbers
            if ($nextCodeNumber > 99) {
                $nextCodeNumber = 1;
                $nextSuffix = chr(ord($nextSuffix) + 1); // Reset suffix and increment number
            }

            // Regenerate the new code with updated suffix and number
            $newCode = $prefix . sprintf('%02d', $nextCodeNumber) . $nextSuffix;
        }

        // Return the new code
        return response()->json(['newCode' => $newCode]);
    }
}
