<?php

namespace App\Http\Controllers;

use App\Models\GrowthStandard;
use App\Imports\GrowthStandardsImport;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class GrowthStandardController extends Controller
{
    /** LIST + FILTER (render 1 halaman index dengan modal) */
    public function index(Request $request)
    {
        $q = GrowthStandard::query()
            ->gender($request->gender)
            ->parameter($request->parameter)
            ->refType($request->reference_type);

        if ($request->filled('is_active')) {
            $q->where('is_active', (bool) $request->boolean('is_active'));
        }

        $rows = $q->orderBy('parameter')
            ->orderBy('gender')
            ->orderBy('reference_type')
            ->orderBy('age_months')
            ->orderBy('body_length')
            ->orderBy('body_height')
            ->paginate(20)
            ->withQueryString();

        // View tunggal (sudah berisi modal create/edit/import)
        return view('admin.growth-standart.growth-standart-index.index', compact('rows'));
    }

    /** CREATE (submit dari modal) */
    public function store(Request $request)
    {
        $data = $this->validatePayload($request);
        GrowthStandard::create($data);

        return redirect()
            ->route('growth-standards.index')
            ->with('success', 'Growth standard berhasil ditambahkan.');
    }

    /** UPDATE (submit dari modal) */
    public function update(Request $request, $id)
    {
        $row  = GrowthStandard::findOrFail($id);
        $data = $this->validatePayload($request);
        $row->update($data);

        return redirect()
            ->route('growth-standards.index')
            ->with('success', 'Growth standard berhasil diperbarui.');
    }

    /** DELETE (tombol di tabel) */
    public function destroy($id)
    {
        $row = GrowthStandard::findOrFail($id);
        $row->delete();

        return redirect()
            ->route('growth-standards.index')
            ->with('success', 'Growth standard berhasil dihapus.');
    }

    /** TOGGLE ACTIVE (tombol di tabel) */
    public function toggleActive($id)
    {
        $row = GrowthStandard::findOrFail($id);
        $row->is_active = ! $row->is_active;
        $row->save();

        return redirect()->back()->with('success', 'Status aktif berhasil diubah.');
    }

    /** IMPORT (submit dari modal) */
    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv'],
        ]);

        $import = new GrowthStandardsImport();
        Excel::import($import, $request->file('file'));

        $sum = $import->summary();
        $msg = "Import selesai. Baris diproses: {$sum['rows_processed']}";
        if (!empty($sum['failures'])) {
            $msg .= " | Failures: " . count($sum['failures']);
        }

        return redirect()
            ->route('growth-standards.index')
            ->with('success', $msg)
            ->with('import_failures', $sum['failures'] ?? []);
    }

    /** Validator create/update (konsisten dengan view/modal) */
    private function validatePayload(Request $request): array
    {
        $rules = [
            'gender'          => ['required', Rule::in(['male', 'female'])],
            'reference_type'  => ['required', Rule::in(['age', 'length', 'height'])],

            'age_months'      => ['nullable', 'integer', 'min:0', 'max:216'],
            'body_length'     => ['nullable', 'numeric', 'min:0', 'max:200'],
            'body_height'     => ['nullable', 'numeric', 'min:0', 'max:200'],

            'minus_3_sd'      => ['required', 'numeric'],
            'minus_2_sd'      => ['required', 'numeric'],
            'minus_1_sd'      => ['required', 'numeric'],
            'median'          => ['required', 'numeric'],
            'plus_1_sd'       => ['required', 'numeric'],
            'plus_2_sd'       => ['required', 'numeric'],
            'plus_3_sd'       => ['required', 'numeric'],

            'parameter'       => ['required', 'string', 'max:20'],
            'measurement_condition' => ['nullable', 'string', 'max:20'],
            'is_active'       => ['sometimes', 'boolean'],
        ];

        $data = $request->validate($rules);

        // Konsistensi field referensi
        $type = $data['reference_type'];
        if ($type === 'age') {
            if (is_null($data['age_months'])) {
                abort(422, 'age_months wajib untuk reference_type=age');
            }
            $data['body_length'] = null;
            $data['body_height'] = null;
        } elseif ($type === 'length') {
            if (is_null($data['body_length'])) {
                abort(422, 'body_length wajib untuk reference_type=length');
            }
            $data['age_months'] = null;
            $data['body_height'] = null;
        } else { // height
            if (is_null($data['body_height'])) {
                abort(422, 'body_height wajib untuk reference_type=height');
            }
            $data['age_months'] = null;
            $data['body_length'] = null;
        }

        return $data;
    }
}
