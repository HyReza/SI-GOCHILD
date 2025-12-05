<?php

namespace App\Http\Controllers;

use App\Models\ExtraService;
use App\Http\Requests\StoreExtraServiceRequest;
use App\Http\Requests\UpdateExtraServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ExtraServiceController extends Controller
{
    /**
     * Tampilkan daftar layanan.
     */
    public function index()
    {
        $extraServices = ExtraService::with('creator')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.extra-service.extra-service-index.index', compact('extraServices'));
    }

    /**
     * Tampilkan form create.
     */
    public function create()
    {
        return view('admin.extra-service.extra-service-create.index');
    }

    /**
     * Simpan layanan baru (termasuk upload gambar).
     */
    public function store(Request $request)
    {
        // 1. Validasi input
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:extra_services,name',
            'description' => 'nullable|string',
            'base_price' => 'required|integer|min:0',
            // Validasi file gambar (max 2MB)
            'image' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'is_active' => 'nullable|boolean',
        ]);

        try {
            // 2. Handle Upload Gambar
            $imagePath = null;
            if ($request->hasFile('image')) {
                // Simpan di folder 'public/extra-services'
                $imagePath = $request->file('image')->store('extra-services', 'public');
            }

            // 3. Simpan ke Database
            ExtraService::create([
                'name' => $validatedData['name'],
                'description' => $validatedData['description'],
                'base_price' => $validatedData['base_price'],
                'image_url' => $imagePath, // Simpan path gambar
                'is_active' => $request->has('is_active'), // Checkbox value handling
                'created_by' => Auth::id(),
            ]);

            return redirect()->route('extra-services.index')->with('success', 'Layanan tambahan berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Error storing Extra Service: ' . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan layanan. ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Tampilkan detail.
     */
    public function show(ExtraService $extraService)
    {
        return view('admin.extra-service.extra-service-show.index', compact('extraService'));
    }

    /**
     * Tampilkan form edit.
     */
    public function edit(ExtraService $extraService)
    {
        return view('admin.extra-service.extra-service-edit.index', compact('extraService'));
    }

    /**
     * Update layanan (termasuk ganti gambar).
     */
    public function update(Request $request, ExtraService $extraService)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:extra_services,name,' . $extraService->id,
            'description' => 'nullable|string',
            'base_price' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'is_active' => 'nullable|boolean',
        ]);

        try {
            $dataToUpdate = [
                'name' => $validatedData['name'],
                'description' => $validatedData['description'],
                'base_price' => $validatedData['base_price'],
                'is_active' => $request->has('is_active'),
            ];

            // 1. Cek jika ada upload gambar baru
            if ($request->hasFile('image')) {
                // Hapus gambar lama jika ada
                if ($extraService->image_url && Storage::disk('public')->exists($extraService->image_url)) {
                    Storage::disk('public')->delete($extraService->image_url);
                }
                // Upload gambar baru
                $path = $request->file('image')->store('extra-services', 'public');
                $dataToUpdate['image_url'] = $path;
            }

            $extraService->update($dataToUpdate);

            return redirect()->route('extra-services.index')->with('success', 'Layanan berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error updating Extra Service: ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui layanan.')->withInput();
        }
    }

    /**
     * Hapus layanan.
     */
    public function destroy(ExtraService $extraService)
    {
        try {
            if ($extraService->serviceOrders()->exists()) {
                return back()->with('error', 'Layanan tidak bisa dihapus karena sudah pernah dipesan.');
            }

            // Hapus gambar fisik
            if ($extraService->image_url && Storage::disk('public')->exists($extraService->image_url)) {
                Storage::disk('public')->delete($extraService->image_url);
            }

            $extraService->delete();

            return redirect()->route('extra-services.index')->with('success', 'Layanan berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Error deleting Extra Service: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus layanan.');
        }
    }
}
