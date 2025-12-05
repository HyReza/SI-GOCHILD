<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Imports\MmdstParameterImport;
use App\Models\CategoryParameter;
use App\Models\MmdstParameter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class MmdstParameterController extends Controller
{
    /**
     * List + search + filter aktif + pagination.
     */
    public function index(Request $request)
    {
        $search = (string) $request->get('search', '');
        $active = $request->get('active'); // '1' | '0' | null

        $query = MmdstParameter::query()
            ->with('stimulationCategory')
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('test_element_name', 'like', "%{$search}%")
                        ->orWhere('test_element_description', 'like', "%{$search}%");
                })->orWhereHas('stimulationCategory', function ($qc) use ($search) {
                    $qc->where('category_parameter_name', 'like', "%{$search}%");
                });
            })
            ->when($active !== null && $active !== '', function ($q) use ($active) {
                $q->where('parameter_is_active', (bool) $active);
            })
            ->latest('id');

        $mmdstParameters = $query->paginate(10)->withQueryString();

        return view('admin.mmdst-parameter.mmdst-parameter-index.index', [
            'mmdstParameters' => $mmdstParameters,
            'search' => $search,
            'active' => $active,
        ]);
    }

    /**
     * (Opsional) Form create — jika pakai modal, ini bisa di-skip.
     */
    public function create()
    {
        $categories = CategoryParameter::orderBy('category_parameter_name')->get(['id', 'category_parameter_name']);
        return view('mmdst_parameters.create', compact('categories'));
    }

    /**
     * Simpan data baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules(), $this->messages());

        MmdstParameter::create($validated + [
            'parameter_is_active' => (bool) $request->boolean('parameter_is_active', true),
        ]);

        return redirect()
            ->route('mmdst-parameter.index')
            ->with('success', 'Parameter MMDST berhasil ditambahkan.');
    }

    /**
     * Detail JSON (untuk modal "lihat").
     */
    public function show(MmdstParameter $mmdst_parameter)
    {
        $mmdst_parameter->load('stimulationCategory:id,category_parameter_name');
        return response()->json([
            'id' => $mmdst_parameter->id,
            'test_element_name' => $mmdst_parameter->test_element_name,
            'test_element_description' => $mmdst_parameter->test_element_description,
            'percent_25' => $mmdst_parameter->percent_25,
            'percent_50' => $mmdst_parameter->percent_50,
            'percent_75' => $mmdst_parameter->percent_75,
            'percent_100' => $mmdst_parameter->percent_100,
            'stimulation_category_id' => $mmdst_parameter->stimulation_category_id,
            'stimulation_category_name' => optional($mmdst_parameter->stimulationCategory)->category_parameter_name,
            'parameter_is_active' => (bool) $mmdst_parameter->parameter_is_active,
            'created_at' => $mmdst_parameter->created_at,
            'updated_at' => $mmdst_parameter->updated_at,
        ]);
    }

    /**
     * Prefill JSON untuk edit (modal).
     */
    public function edit(MmdstParameter $mmdst_parameter)
    {
        return response()->json([
            'id' => $mmdst_parameter->id,
            'test_element_name' => $mmdst_parameter->test_element_name,
            'test_element_description' => $mmdst_parameter->test_element_description,
            'percent_25' => $mmdst_parameter->percent_25,
            'percent_50' => $mmdst_parameter->percent_50,
            'percent_75' => $mmdst_parameter->percent_75,
            'percent_100' => $mmdst_parameter->percent_100,
            'stimulation_category_id' => $mmdst_parameter->stimulation_category_id,
            'parameter_is_active' => (bool) $mmdst_parameter->parameter_is_active,
        ]);
    }

    /**
     * Update data.
     */
    public function update(Request $request, MmdstParameter $mmdst_parameter): RedirectResponse
    {
        $validated = $request->validate($this->rules($mmdst_parameter->id), $this->messages());

        $mmdst_parameter->update($validated + [
            'parameter_is_active' => (bool) $request->boolean('parameter_is_active', true),
        ]);

        return redirect()
            ->route('mmdst-parameter.index')
            ->with('success', 'Parameter MMDST berhasil diperbarui.');
    }

    /**
     * Hapus data.
     */
    public function destroy(MmdstParameter $mmdst_parameter): RedirectResponse
    {
        try {
            $mmdst_parameter->delete();

            return redirect()
                ->route('mmdst-parameter.index')
                ->with('success', 'Parameter MMDST berhasil dihapus.');
        } catch (\Throwable $e) {
            return redirect()
                ->route('mmdst-parameter.index')
                ->withErrors('Parameter tidak dapat dihapus karena sedang digunakan.');
        }
    }

    /**
     * Import dari Excel (Maatwebsite/Excel).
     * Format header yang diharapkan:
     * nama_unsur_test | deskripsi_unsur_test | 25% | 50% | 75% | 100% | kategori_stimulasi
     */
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv'],
        ], [
            'file.required' => 'File Excel wajib diunggah.',
            'file.mimes'    => 'Format file harus .xlsx, .xls, atau .csv.',
        ]);

        $import = new MmdstParameterImport();

        try {
            Excel::import($import, $request->file('file'));
        } catch (\Throwable $e) {
            return back()->withErrors('Impor gagal: ' . $e->getMessage());
        }

        $summary = $import->result();
        $msg = "Impor selesai. Berhasil: {$summary['inserted']}, Dilewati: {$summary['skipped']}.";
        if (!empty($summary['errors'])) {
            $msg .= ' Catatan: ' . implode(' | ', array_slice($summary['errors'], 0, 5));
            if (count($summary['errors']) > 5) $msg .= ' ...';
        }

        return redirect()->route('mmdst-parameter.index')->with('success', $msg);
    }

    /**
     * Rules validasi store/update.
     */
    protected function rules(int $id = 0): array
    {
        return [
            'test_element_name'        => ['required', 'string', 'max:255'],
            'test_element_description' => ['required', 'string'],
            'percent_25'               => ['required', 'integer', 'min:0', 'max:100'],
            'percent_50'               => ['required', 'integer', 'min:0', 'max:100'],
            'percent_75'               => ['required', 'integer', 'min:0', 'max:100'],
            'percent_100'              => ['required', 'integer', 'min:0', 'max:100'],
            'stimulation_category_id'  => ['required', 'integer', 'exists:category_parameters,id'],
            'parameter_is_active'      => ['nullable', 'boolean'],
        ];
    }

    /**
     * Pesan validasi berbahasa Indonesia.
     */
    protected function messages(): array
    {
        return [
            'test_element_name.required'       => 'Nama unsur/elemen wajib diisi.',
            'percent_25.integer'               => 'Nilai 25% harus berupa angka.',
            'percent_50.integer'               => 'Nilai 50% harus berupa angka.',
            'percent_75.integer'               => 'Nilai 75% harus berupa angka.',
            'percent_100.integer'              => 'Nilai 100% harus berupa angka.',
            'stimulation_category_id.required' => 'Kategori stimulasi wajib diisi.',
            'stimulation_category_id.exists'   => 'Kategori stimulasi tidak valid.',
        ];
    }
}
