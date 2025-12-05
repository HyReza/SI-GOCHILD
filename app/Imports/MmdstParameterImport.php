<?php

namespace App\Imports;

use App\Models\CategoryParameter;
use App\Models\MmdstParameter;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use PhpOffice\PhpSpreadsheet\RichText\RichText;

class MmdstParameterImport implements ToCollection
{
    /** @var array<string,int> lowercase(category_parameter_name) => id */
    protected array $categoryMap = [];

    public int $inserted = 0;
    public int $skipped  = 0;
    public array $errors = [];

    /**
     * Alias header setelah dinormalisasi (lowercase + hapus non-alfanumerik).
     * Disesuaikan agar match: nama_unsur_test, deskripsi_unsur_test, percent_25/50/75/100, kategori_stimulasi.
     */
    protected array $aliases = [
        'name'  => ['namaunsurtest', 'unsur', 'namaunsur', 'namaunsur/elemen', 'testelementname', 'namaelemen', 'namates', 'namaujian'],
        'desc'  => ['deskripsiunsurtest', 'deskripsiunsur', 'deskripsi', 'keterangan', 'uraian', 'testelementdescription', 'description', 'ket'],
        'cat'   => ['kategoristimulasi', 'kategori', 'stimulationcategory', 'kategoristimulus'],

        // Persen (angka hari). Termasuk bentuk 'percent_25' => 'percent25'
        'p25'   => ['percent25', '25', '25%', '025', 'p25', 'nilai25', 'n25', '25pct', '25percent', 'persen25', '0.25', '0,25'],
        'p50'   => ['percent50', '50', '50%', '05', 'p50', 'nilai50', 'n50', '50pct', '50percent', 'persen50', '0.5', '0,5'],
        'p75'   => ['percent75', '75', '75%', '075', 'p75', 'nilai75', 'n75', '75pct', '75percent', 'persen75', '0.75', '0,75'],
        'p100'  => ['percent100', '100', '100%', '1', 'p100', 'nilai100', 'n100', '100pct', '100percent', 'persen100'],
    ];

    public function __construct()
    {
        // Biarkan header apa adanya (meskipun kita deteksi manual)
        HeadingRowFormatter::default('none');

        // Cache kategori (nama → id)
        $this->categoryMap = CategoryParameter::query()
            ->select('id', 'category_parameter_name')
            ->get()
            ->mapWithKeys(fn($c) => [mb_strtolower(trim($c->category_parameter_name)) => (int) $c->id])
            ->toArray();
    }

    public function collection(Collection $rows)
    {
        if ($rows->isEmpty()) return;

        // 1) Deteksi baris header
        $headerIndex = null;
        $colMap = [];
        $maxScan = min(40, $rows->count());

        for ($r = 0; $r < $maxScan; $r++) {
            $cells = $this->rowToScalarArray($rows[$r]);
            $tryMap = $this->detectHeader($cells);
            // minimal wajib: name + cat
            if (isset($tryMap['name'], $tryMap['cat'])) {
                $headerIndex = $r;
                $colMap = $tryMap;
                break;
            }
        }

        if ($headerIndex === null) {
            $this->errors[] = 'Header tidak ditemukan. Pastikan ada kolom "nama_unsur_test" & "kategori_stimulasi".';
            return;
        }

        // 2) Proses data setelah header
        for ($i = $headerIndex + 1; $i < $rows->count(); $i++) {
            $cells = $this->rowToScalarArray($rows[$i]);

            $name = $this->getCellText($cells, Arr::get($colMap, 'name'));
            $cat  = $this->getCellText($cells, Arr::get($colMap, 'cat'));
            $desc = $this->getCellText($cells, Arr::get($colMap, 'desc'));

            // Ambil nilai angka apa adanya (tanpa scaling)
            $p25  = $this->toIntAsIs($this->getCellRaw($cells, Arr::get($colMap, 'p25')));
            $p50  = $this->toIntAsIs($this->getCellRaw($cells, Arr::get($colMap, 'p50')));
            $p75  = $this->toIntAsIs($this->getCellRaw($cells, Arr::get($colMap, 'p75')));
            $p100 = $this->toIntAsIs($this->getCellRaw($cells, Arr::get($colMap, 'p100')));

            // Lewati baris kosong total
            if ($name === '' && $cat === '' && $desc === '' && $p25 === null && $p50 === null && $p75 === null && $p100 === null) {
                continue;
            }

            $line = $i + 1; // 1-based untuk pesan

            if ($name === '') {
                $this->skipped++;
                $this->errors[] = "Baris {$line}: 'nama_unsur_test' kosong.";
                continue;
            }
            if ($cat  === '') {
                $this->skipped++;
                $this->errors[] = "Baris {$line}: 'kategori_stimulasi' kosong.";
                continue;
            }

            $catId = $this->categoryMap[mb_strtolower($cat)] ?? null;
            if (!$catId) {
                $this->skipped++;
                $this->errors[] = "Baris {$line}: kategori '{$cat}' tidak ditemukan.";
                continue;
            }

            try {
                MmdstParameter::create([
                    'test_element_name'        => $name,
                    'test_element_description' => $desc ?: null,
                    'percent_25'               => $p25,
                    'percent_50'               => $p50,
                    'percent_75'               => $p75,
                    'percent_100'              => $p100,
                    'stimulation_category_id'  => $catId,
                    'parameter_is_active'      => 1,
                ]);
                $this->inserted++;
            } catch (\Throwable $e) {
                $this->skipped++;
                $this->errors[] = "Baris {$line}: gagal simpan ({$e->getMessage()}).";
            }
        }
    }

    /** Deteksi baris header → mapping field → index kolom. */
    protected function detectHeader(array $cells): array
    {
        $map = [];
        foreach ($cells as $idx => $val) {
            $norm = $this->normalizeHeaderKey($val);

            // 1) Header numerik (jaga-jaga kalau suatu saat pakai 0.25/0.5/0.75/1)
            if (is_numeric($val)) {
                $f = (float) $val;
                if (!isset($map['p25'])  && abs($f - 0.25) < 1e-9) {
                    $map['p25']  = $idx;
                    continue;
                }
                if (!isset($map['p50'])  && abs($f - 0.5)  < 1e-9) {
                    $map['p50']  = $idx;
                    continue;
                }
                if (!isset($map['p75'])  && abs($f - 0.75) < 1e-9) {
                    $map['p75']  = $idx;
                    continue;
                }
                if (!isset($map['p100']) && abs($f - 1.0)  < 1e-9) {
                    $map['p100'] = $idx;
                    continue;
                }
            }

            // 2) Header teks (alias), termasuk 'percent_25' => 'percent25'
            if ($norm === '') continue;
            if (!isset($map['name'])  && $this->inAliases($norm, $this->aliases['name'])) {
                $map['name']  = $idx;
                continue;
            }
            if (!isset($map['desc'])  && $this->inAliases($norm, $this->aliases['desc'])) {
                $map['desc']  = $idx;
                continue;
            }
            if (!isset($map['cat'])   && $this->inAliases($norm, $this->aliases['cat'])) {
                $map['cat']   = $idx;
                continue;
            }
            if (!isset($map['p25'])   && $this->inAliases($norm, $this->aliases['p25'])) {
                $map['p25']   = $idx;
                continue;
            }
            if (!isset($map['p50'])   && $this->inAliases($norm, $this->aliases['p50'])) {
                $map['p50']   = $idx;
                continue;
            }
            if (!isset($map['p75'])   && $this->inAliases($norm, $this->aliases['p75'])) {
                $map['p75']   = $idx;
                continue;
            }
            if (!isset($map['p100'])  && $this->inAliases($norm, $this->aliases['p100'])) {
                $map['p100']  = $idx;
                continue;
            }
        }
        return $map;
    }

    protected function inAliases(string $key, array $list): bool
    {
        return in_array($key, array_map([$this, 'normalizeHeaderKey'], $list), true);
    }

    /** Normalisasi header: lowercase + buang semua non-alfanumerik (spasi, %, _, dll). */
    protected function normalizeHeaderKey($val): string
    {
        if ($val === null) return '';
        if ($val instanceof RichText) $val = $val->getPlainText();
        $s = trim(mb_strtolower((string) $val));
        return preg_replace('/[^a-z0-9]/', '', $s) ?: '';
    }

    /** Konversi satu baris menjadi array skalar (angka/string/null). */
    protected function rowToScalarArray($row): array
    {
        $arr = $row instanceof Collection ? $row->toArray() : (is_array($row) ? $row : []);
        $cells = [];
        foreach ($arr as $val) {
            $cells[] = $this->cellToScalar($val);
        }
        return $cells;
    }

    /** Buat sel aman (angka/string/null). */
    protected function cellToScalar($val)
    {
        if ($val === null) return null;
        if (is_scalar($val)) return $val;
        if ($val instanceof RichText) return $val->getPlainText();
        if (is_object($val) && method_exists($val, '__toString')) return (string) $val;
        if (is_array($val)) return $this->cellToScalar($val[0] ?? null);
        return null;
    }

    /** Ambil sel sebagai teks (trim) untuk field teks. */
    protected function getCellText(array $cells, $index): string
    {
        if ($index === null) return '';
        $v = $cells[$index] ?? null;
        if ($v === null) return '';
        return trim((string)$v);
    }

    /** Ambil sel raw (angka/string) untuk field numeric. */
    protected function getCellRaw(array $cells, $index)
    {
        if ($index === null) return null;
        return $cells[$index] ?? null;
    }

    /**
     * Ambil integer apa adanya (tanpa skala):
     * - numeric : (int)$v (0.0→0, 25.9→25)
     * - string  : buang spasi/%/koma, lalu (int)float
     * - kosong  : null
     */
    protected function toIntAsIs($value): ?int
    {
        if ($value === null) return null;
        if (is_numeric($value)) return (int) $value;

        $s = trim((string)$value);
        if ($s === '') return null;
        $s = str_replace(['%', ' '], '', $s);
        $s = str_replace(',', '.', $s);
        if ($s === '' || !is_numeric($s)) return null;

        return (int) ((float) $s);
    }

    /** Ringkasan hasil impor */
    public function result(): array
    {
        return [
            'inserted' => $this->inserted,
            'skipped'  => $this->skipped,
            'errors'   => $this->errors,
        ];
    }
}
