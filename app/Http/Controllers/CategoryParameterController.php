<?php

namespace App\Http\Controllers;

use App\Models\CategoryParameter;
use App\Http\Requests\StoreCategory_ParameterRequest;
use App\Http\Requests\UpdateCategory_ParameterRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;


class CategoryParameterController extends Controller
{
    // /**
    //  * Display a listing of the resource.
    //  */
    // public function index()
    // {
    //     //
    // }

    // /**
    //  * Show the form for creating a new resource.
    //  */
    // public function create()
    // {
    //     //
    // }

    // /**
    //  * Store a newly created resource in storage.
    //  */
    // public function store(StoreCategory_ParameterRequest $request)
    // {
    //     //
    // }

    // /**
    //  * Display the specified resource.
    //  */
    // public function show(Category_Parameter $category_Parameter)
    // {
    //     //
    // }

    // /**
    //  * Show the form for editing the specified resource.
    //  */
    // public function edit(Category_Parameter $category_Parameter)
    // {
    //     //
    // }

    // /**
    //  * Update the specified resource in storage.
    //  */
    // public function update(UpdateCategory_ParameterRequest $request, Category_Parameter $category_Parameter)
    // {
    //     //
    // }

    // /**
    //  * Remove the specified resource from storage.
    //  */
    // public function destroy(Category_Parameter $category_Parameter)
    // {
    //     //
    // }

    /**
     * Tampilkan daftar Category Parameter dengan pencarian & pagination.
     */
    public function index(Request $request)
    {
        $search = (string) $request->get('search', '');

        $categoryParameters = CategoryParameter::query()
            ->when($search !== '', function ($q) use ($search) {
                $q->where('category_parameter_name', 'like', "%{$search}%")
                    ->orWhere('category_parameter_description', 'like', "%{$search}%");
            })
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        // Nanti view-nya kita buat di langkah berikutnya:
        // resources/views/category_parameters/index.blade.php
        return view('admin.category-parameter.category-parameter-create.index', [
            'categoryParameters' => $categoryParameters,
            'search' => $search,
        ]);
    }

    /**
     * (Opsional) Form create — jika pakai modal + fetch, biasanya tidak diperlukan.
     */
    public function create()
    {
        return view('admin.category-parameter.category-parameter-create.index');
    }

    /**
     * Simpan data baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_parameter_name' => ['required', 'string', 'max:255', 'unique:category_parameters,category_parameter_name'],
            'category_parameter_description' => ['nullable', 'string'],
        ], [
            'category_parameter_name.required' => 'Nama parameter wajib diisi.',
            'category_parameter_name.unique'   => 'Nama parameter sudah digunakan.',
        ]);

        CategoryParameter::create($validated);

        return redirect()
            ->route('category-parameter.index')
            ->with('success', 'Parameter berhasil ditambahkan.');
    }

    /**
     * Detail satu data (kembalikan JSON untuk dipakai di modal "Lihat").
     */
    public function show(CategoryParameter $category_parameter)
    {
        return response()->json([
            'id' => $category_parameter->id,
            'category_parameter_name' => $category_parameter->category_parameter_name,
            'category_parameter_description' => $category_parameter->category_parameter_description,
            'created_at' => $category_parameter->created_at,
            'updated_at' => $category_parameter->updated_at,
        ]);
    }

    /**
     * Ambil data untuk form edit (kembalikan JSON untuk dipakai di modal "Edit").
     */
    public function edit(CategoryParameter $category_parameter)
    {
        return response()->json([
            'id' => $category_parameter->id,
            'category_parameter_name' => $category_parameter->category_parameter_name,
            'category_parameter_description' => $category_parameter->category_parameter_description,
        ]);
    }

    /**
     * Update data.
     */
    public function update(Request $request, CategoryParameter $category_parameter): RedirectResponse
    {
        $validated = $request->validate([
            'category_parameter_name' => ['required', 'string', 'max:255', 'unique:category_parameters,category_parameter_name,' . $category_parameter->id],
            'category_parameter_description' => ['nullable', 'string'],
        ], [
            'category_parameter_name.required' => 'Nama parameter wajib diisi.',
            'category_parameter_name.unique'   => 'Nama parameter sudah digunakan.',
        ]);

        $category_parameter->update($validated);

        return redirect()
            ->route('category-parameter.index')
            ->with('success', 'Parameter berhasil diperbarui.');
    }

    /**
     * Hapus data.
     */
    public function destroy(CategoryParameter $category_parameter): RedirectResponse
    {
        try {
            $category_parameter->delete();

            return redirect()
                ->route('category-parameter.index')
                ->with('success', 'Parameter berhasil dihapus.');
        } catch (\Throwable $e) {
            // Tangani jika ada constraint relasi/foreign key
            return redirect()
                ->route('category-parameter.index')
                ->withErrors('Parameter tidak dapat dihapus karena sedang digunakan.');
        }
    }
}
