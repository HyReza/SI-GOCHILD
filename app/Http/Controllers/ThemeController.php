<?php

namespace App\Http\Controllers;

use App\Models\Theme;
use App\Models\SubTheme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ThemeController extends Controller
{
    /**
     * Tampilkan daftar tema + form create (index/create page).
     */
    public function create(Request $request)
    {
        // Ambil daftar tema + pencarian
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
            ->appends(['search' => $request->search]); // penting agar link paginasi bawa query

        // Generate unique theme code (untuk form create)
        $themeCode = $this->generateUniqueThemeCode();

        // AJAX: kembalikan partial HTML (tbody + paginasi) sebagai JSON
        if ($request->ajax()) {
            $tbodyHtml = view('admin.theme.theme-create.theme-list', compact('themes'))->render();
            $paginationHtml = view('admin.theme.theme-create.theme-pagination', compact('themes'))->render();

            return response()->json([
                'tbody'      => $tbodyHtml,
                'pagination' => $paginationHtml,
            ]);
        }

        // Non-AJAX: render halaman penuh
        return view('admin.theme.theme-create.index', compact('themes', 'themeCode'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'theme_name'        => 'required|string|max:255',
            'theme_description' => 'required|string',
            'theme_document'    => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'theme_on_report'   => 'required|in:0,1'
        ]);

        $themeCode = $this->generateUniqueThemeCode();

        $documentPath = null;
        if ($request->hasFile('theme_document')) {
            $documentPath = $request->file('theme_document')->store('theme_documents', 'public');
        }

        Theme::create([
            'theme_code'        => $themeCode,
            'theme_name'        => $request->input('theme_name'),
            'theme_description' => $request->input('theme_description'),
            'theme_document'    => $documentPath,
            'theme_is_active'   => 1,
            'theme_on_report'   => $request->input('theme_on_report'),
        ]);

        return redirect()->route('themes.create')->with('success', 'Tema berhasil ditambahkan!');
    }

    /**
     * Generate a unique theme code (AA, AB, ..., AZ, BA, ...).
     */
    private function generateUniqueThemeCode()
    {
        $lastTheme = Theme::orderBy('theme_code', 'desc')->first();

        if (!$lastTheme) {
            return 'AA';
        }

        $lastCode = $lastTheme->theme_code;
        $nextCode = $this->incrementThemeCode($lastCode);

        while (Theme::where('theme_code', $nextCode)->exists()) {
            $nextCode = $this->incrementThemeCode($nextCode);
        }

        return $nextCode;
    }

    /**
     * Increment the theme code (e.g., AA -> AB, AZ -> BA, ZZ -> AAA).
     * (Sedikit diperkuat agar aman jika melebihi 2 huruf)
     */
    private function incrementThemeCode($code)
    {
        $code = strtoupper($code);
        $letters = str_split($code);
        $i = count($letters) - 1;

        while ($i >= 0) {
            if ($letters[$i] === 'Z') {
                $letters[$i] = 'A';
                $i--;
            } else {
                $letters[$i] = chr(ord($letters[$i]) + 1);
                return implode('', $letters);
            }
        }

        // Overflow, prepend 'A'
        array_unshift($letters, 'A');
        return implode('', $letters);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $theme = Theme::findOrFail($id);

        $subThemeCount = \App\Models\SubTheme::where('theme_id', $id)->count();
        if ($subThemeCount > 0) {
            return redirect()->route('themes.create')->with('error', 'Tema ini tidak dapat dihapus karena sedang digunakan di Sub Tema');
        }

        if ($theme->theme_document && Storage::disk('public')->exists($theme->theme_document)) {
            Storage::disk('public')->delete($theme->theme_document);
        }

        $theme->delete();

        return redirect()->route('themes.create')->with('success', 'Tema dan dokumen terkait berhasil dihapus!');
    }

    public function edit($id)
    {
        // Ambil data tema berdasarkan ID
        $theme = Theme::findOrFail($id);

        return view('admin.theme.theme-edit.index', compact('theme'));
    }

    /**
     * Update data tema yang sudah ada.
     */
    public function update(Request $request, $id)
    {
        // Validasi inputan dari form
        $request->validate([
            'theme_name'        => 'required|string|max:255',
            'theme_description' => 'required|string',
            'theme_document'    => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'theme_on_report'   => 'required|in:0,1',
            'theme_is_active'   => 'required|in:0,1',
        ]);

        // Cari tema berdasarkan ID
        $theme = Theme::findOrFail($id);

        // Simpan dokumen baru jika ada
        if ($request->hasFile('theme_document')) {
            // Hapus dokumen lama jika ada
            if ($theme->theme_document && Storage::disk('public')->exists($theme->theme_document)) {
                Storage::disk('public')->delete($theme->theme_document);
            }

            // Simpan dokumen baru
            $documentPath = $request->file('theme_document')->store('theme_documents', 'public');
        } else {
            $documentPath = $theme->theme_document; // Tetap pakai dokumen lama jika tidak ada yang di-upload
        }

        // Update data tema
        $theme->update([
            'theme_name'        => $request->input('theme_name'),
            'theme_description' => $request->input('theme_description'),
            'theme_document'    => $documentPath,
            'theme_on_report'   => $request->input('theme_on_report'),
            'theme_is_active'   => $request->input('theme_is_active'),
        ]);

        // Redirect setelah update sukses
        return redirect()->route('themes.create')->with('success', 'Tema berhasil diperbarui!');
    }
    public function show($id)
    {
        // Cari tema berdasarkan ID
        $theme = Theme::findOrFail($id);

        // Kirim data tema ke view
        return view('admin.theme.theme-show.index', compact('theme'));
    }
}
