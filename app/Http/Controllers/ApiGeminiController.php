<?php

namespace App\Http\Controllers;

use App\Models\ApiGemini;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiGeminiController extends Controller
{
    /**
     * Menampilkan daftar semua konfigurasi API.
     */
    public function index()
    {
        $configs = ApiGemini::latest()->paginate(10);
        return view('admin.api-gemini.index', compact('configs'));
    }

    /**
     * Menampilkan form untuk membuat konfigurasi baru.
     */
    public function create()
    {
        return view('admin.api-gemini.create');
    }

    /**
     * Menyimpan konfigurasi baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'api_key' => 'required|string|max:255',
            'model' => 'required|string|max:100', // contoh: gemini-1.5-flash
            'is_active' => 'boolean'
        ]);

        DB::beginTransaction();
        try {
            // Jika user memilih untuk langsung mengaktifkan key ini
            if ($request->has('is_active') && $request->is_active == true) {
                // Nonaktifkan semua key lain terlebih dahulu
                ApiGemini::query()->update(['is_active' => false]);
            }

            // Jika ini adalah data pertama, otomatis set aktif
            if (ApiGemini::count() == 0) {
                $request->merge(['is_active' => true]);
            }

            ApiGemini::create([
                'name' => $request->name,
                'api_key' => $request->api_key,
                'model' => $request->model,
                'is_active' => $request->boolean('is_active'),
            ]);

            DB::commit();
            return redirect()->route('api-gemini.index')->with('success', 'Konfigurasi API berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Menampilkan form edit.
     */
    public function edit($id)
    {
        $apiGemini = ApiGemini::findOrFail($id);
        return view('admin.api-gemini.edit', compact('apiGemini'));
    }

    /**
     * Memperbarui data konfigurasi.
     */
    public function update(Request $request, $id)
    {
        $apiGemini = ApiGemini::findOrFail($id);

        $request->validate([
            'name' => 'nullable|string|max:255',
            'api_key' => 'required|string|max:255',
            'model' => 'required|string|max:100',
            'is_active' => 'boolean'
        ]);

        DB::beginTransaction();
        try {
            // Jika user mengaktifkan key ini saat edit
            if ($request->has('is_active') && $request->is_active == true) {
                // Nonaktifkan semua key lain
                ApiGemini::where('id', '!=', $id)->update(['is_active' => false]);
            }

            $apiGemini->update([
                'name' => $request->name,
                'api_key' => $request->api_key,
                'model' => $request->model,
                'is_active' => $request->boolean('is_active'),
            ]);

            DB::commit();
            return redirect()->route('api-gemini.index')->with('success', 'Konfigurasi API berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal update: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Menghapus konfigurasi.
     */
    public function destroy($id)
    {
        $apiGemini = ApiGemini::findOrFail($id);

        // Mencegah penghapusan jika sedang aktif (opsional, untuk keamanan)
        if ($apiGemini->is_active) {
            // Cek apakah ada key lain, jika ya, jangan hapus yang aktif sembarangan
            if (ApiGemini::count() > 1) {
                return redirect()->back()->with('error', 'Tidak bisa menghapus API Key yang sedang Aktif. Silakan aktifkan key lain terlebih dahulu.');
            }
        }

        $apiGemini->delete();
        return redirect()->route('api-gemini.index')->with('success', 'Konfigurasi API berhasil dihapus.');
    }

    /**
     * Fitur khusus untuk Switch Active Key (Toggle).
     * Berguna jika ingin mengubah key aktif lewat tombol di index.
     */
    public function activate($id)
    {
        DB::beginTransaction();
        try {
            // 1. Set semua jadi tidak aktif
            ApiGemini::query()->update(['is_active' => false]);

            // 2. Set id yang dipilih jadi aktif
            $target = ApiGemini::findOrFail($id);
            $target->update(['is_active' => true]);

            DB::commit();
            return redirect()->back()->with('success', "API Key '{$target->name}' berhasil diaktifkan.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mengaktifkan API Key.');
        }
    }
}
