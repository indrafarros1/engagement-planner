<?php

namespace App\Support;

use App\Enums\BudgetCategory;
use App\Enums\Payer;
use App\Models\BudgetItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Impor item anggaran dari Buku.xlsx (file rencana anggaran ragam Excel).
 * Kolom yang dikenali (cocok dengan header, case-insensitive):
 *   Item/Nama · Kategori · Harga Satuan · Jumlah/Qty · Penanggung · Kontrak · Catatan
 */
class BudgetSpreadsheetImporter
{
    public function import(string $path): Collection
    {
        $reader = IOFactory::createReaderForFile($path);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($path);
        $sheet = $spreadsheet->getActiveSheet();

        $rows = $sheet->toArray(null, true, true, true);
        if (count($rows) < 2) {
            return collect(); // hanya header
        }

        $header = array_shift($rows);
        // normalisasi nama kolom (case-insensitive) → kolom huruf (A,B,C…)
        $map = [];
        foreach ($header as $col => $name) {
            $map[strtolower(trim((string) $name))] = $col;
        }

        $normalize = function (array $mapping): array {
            $keys = [
                'name' => ['nama item', 'item', 'nama', 'uraian', 'keterangan'],
                'category' => ['kategori', 'jenis'],
                'unit_price' => ['harga satuan', 'harga', 'satuan', 'unit price', 'harga satuan (rp)'],
                'quantity' => ['jumlah', 'qty', 'jml', 'quantity'],
                'payer' => ['penanggung', 'pic', 'who', 'dibayar oleh'],
                'contract_value' => ['kontrak', 'nilai kontrak', 'harga kontrak', 'harga negosiasi', 'total kontrak'],
                'note' => ['catatan', 'notes', 'ket'],
            ];
            $out = [];
            foreach ($keys as $field => $variants) {
                foreach ($variants as $v) {
                    if (isset($mapping[$v])) {
                        $out[$field] = $mapping[$v];
                        break;
                    }
                }
            }

            return $out;
        };

        $colMap = $normalize($map);
        $created = collect();
        $errors = [];

        foreach ($rows as $i => $row) {
            $line = $i + 2; // baris excel (1-based, baris 1 = header)
            $get = function (string $field) use ($row, $colMap) {
                if (! isset($colMap[$field])) {
                    return null;
                }

                return $row[$colMap[$field]] ?? null;
            };

            $name = trim((string) ($get('name') ?? ''));
            if ($name === '' || $name === '#N/A') {
                continue; // baris kosong
            }

            $data = [
                'name' => $name,
                'unit_price' => (int) preg_replace('/[^0-9]/', '', (string) ($get('unit_price') ?? 0)),
                'quantity' => max(1, (int) ($get('quantity') ?? 1)),
                'contract_value' => null,
                'notes' => trim((string) ($get('note') ?? '')) ?: null,
            ];

            // kategori → enum value
            $categoryRaw = strtolower(trim((string) ($get('category') ?? '')));
            $data['category'] = $this->resolveCategory($categoryRaw)->value;

            // penanggung → enum value
            $payerRaw = strtolower(trim((string) ($get('payer') ?? '')));
            $data['payer'] = $this->resolvePayer($payerRaw)->value;

            // kontrak opsional
            $contractRaw = (string) ($get('contract_value') ?? '');
            if ($contractRaw !== '') {
                $data['contract_value'] = (int) preg_replace('/[^0-9]/', '', $contractRaw) ?: null;
            }

            // validasi bentuk
            $valid = Validator::make($data, [
                'name' => 'required|string|max:150',
                'unit_price' => 'integer|min:0',
                'quantity' => 'integer|min:1',
                'contract_value' => 'nullable|integer|min:1',
            ]);

            if ($valid->fails()) {
                $errors[] = "Baris {$line}: " . implode(' ', collect($valid->errors()->all())->all());
                continue;
            }

            $created->push(BudgetItem::create($data));
        }

        return collect(['created' => $created, 'errors' => $errors]);
    }

    private function resolveCategory(string $raw): BudgetCategory
    {
        $match = [
            'venue' => BudgetCategory::Venue, 'tempat' => BudgetCategory::Venue, 'gedung' => BudgetCategory::Venue,
            'catering' => BudgetCategory::Catering, 'katering' => BudgetCategory::Catering, 'makan' => BudgetCategory::Catering,
            'dekorasi' => BudgetCategory::Decoration, 'dekoration' => BudgetCategory::Decoration,
            'busana' => BudgetCategory::Attire, 'pakaian' => BudgetCategory::Attire, 'attire' => BudgetCategory::Attire,
            'undangan' => BudgetCategory::Invitation, 'invitation' => BudgetCategory::Invitation,
            'dokumentasi' => BudgetCategory::Documentation, 'documentation' => BudgetCategory::Documentation, 'foto' => BudgetCategory::Documentation,
            'hiburan' => BudgetCategory::Entertainment, 'entertainment' => BudgetCategory::Entertainment, 'musik' => BudgetCategory::Entertainment,
            'transport' => BudgetCategory::Transportation, 'transportasi' => BudgetCategory::Transportation,
            'cincin' => BudgetCategory::Ring, 'ring' => BudgetCategory::Ring,
            'lainnya' => BudgetCategory::Other, 'other' => BudgetCategory::Other, 'lain' => BudgetCategory::Other,
        ];

        // cocok kata-kunci di dalam label (case-insensitive, per kata timpa)
        $best = BudgetCategory::Other;
        $bestPos = PHP_INT_MAX;
        foreach ($match as $keyword => $category) {
            $pos = strpos($raw, $keyword);
            if ($pos !== false && $pos < $bestPos) {
                $best = $category;
                $bestPos = $pos;
            }
        }

        return $best;
    }

    private function resolvePayer(string $raw): Payer
    {
        return match ($raw) {
            'cpp', 'pria', 'lelaki', 'calon pria' => Payer::CPP,
            'cpw', 'wanita', 'perempuan', 'calon wanita' => Payer::CPW,
            'lainnya', 'other', 'lain' => Payer::Lainnya,
            default => Payer::Bersama,
        };
    }
}
