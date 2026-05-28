<?php

namespace App\Http\Controllers;

use App\Models\Kaldik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class KaldikController extends Controller
{
    // ── Halaman utama kalender ──
    public function index(Request $request)
    {
        $tahun = (int) $request->input('tahun', date('Y'));

        $events = Kaldik::whereYear('tanggal', $tahun)
            ->orderBy('tanggal')
            ->get()
            ->groupBy(fn($e) => $e->tanggal->format('Y-m-d'));

        return view('kaldik.index', compact('tahun', 'events'));
    }

    // ── Simpan event baru ──
    public function store(Request $request)
    {
        $request->validate([
            'tanggal'    => 'required|date',
            'judul'      => 'required|string|max:255',
            'tipe'       => 'required|in:libur_nasional,cuti_bersama,libur_semester,kegiatan',
            'keterangan' => 'nullable|string|max:255',
        ]);

        Kaldik::create($request->only('tanggal', 'judul', 'tipe', 'keterangan'));

        return response()->json(['status' => 'ok']);
    }

    // ── Update event ──
    public function update(Request $request, int $id)
    {
        $request->validate([
            'tanggal'    => 'required|date',
            'judul'      => 'required|string|max:255',
            'tipe'       => 'required|in:libur_nasional,cuti_bersama,libur_semester,kegiatan',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $event = Kaldik::findOrFail($id);
        $event->update($request->only('tanggal', 'judul', 'tipe', 'keterangan'));

        return response()->json(['status' => 'ok']);
    }

    // ── Hapus event ──
    public function destroy(int $id)
    {
        Kaldik::findOrFail($id)->delete();
        return response()->json(['status' => 'ok']);
    }

    // ── API: ambil events per bulan (untuk kalender JS) ──
    public function apiEvents(Request $request)
    {
        $tahun = (int) $request->input('tahun', date('Y'));
        $bulan = (int) $request->input('bulan', date('m'));

        $events = Kaldik::whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->orderBy('tanggal')
            ->get()
            ->map(fn($e) => [
                'id'         => $e->id,
                'tanggal'    => $e->tanggal->format('Y-m-d'),
                'judul'      => $e->judul,
                'tipe'       => $e->tipe,
                'keterangan' => $e->keterangan,
                'warna'      => Kaldik::warna($e->tipe),
                'is_libur'   => in_array($e->tipe, ['libur_nasional', 'cuti_bersama', 'libur_semester']),
            ]);

        return response()->json($events);
    }

    // ── Download template Excel ──
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="template_kaldik.xlsx"',
        ];

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([
            ['tanggal', 'judul', 'tipe', 'keterangan'],
            ['2025-08-17', 'HUT Kemerdekaan RI', 'libur_nasional', 'Upacara bendera'],
            ['2025-12-25', 'Natal', 'libur_nasional', ''],
            ['2025-12-26', 'Cuti Bersama Natal', 'cuti_bersama', ''],
        ]);

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        ob_start();
        $writer->save('php://output');
        $content = ob_get_clean();

        return response($content, 200, $headers);
    }

    // ── Upload Excel ──
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $path        = $request->file('file')->getRealPath();
        $spreadsheet = IOFactory::load($path);
        $rows        = $spreadsheet->getActiveSheet()->toArray();

        $inserted = 0;
        $errors   = [];
        $tipeValid = ['libur_nasional', 'cuti_bersama', 'libur_semester', 'kegiatan'];

        foreach ($rows as $i => $row) {
            if ($i === 0) continue; // skip header

            [$tanggal, $judul, $tipe, $keterangan] = array_pad($row, 4, null);

            if (empty($tanggal) || empty($judul) || empty($tipe)) continue;
            if (!in_array($tipe, $tipeValid)) {
                $errors[] = "Baris " . ($i + 1) . ": tipe '$tipe' tidak valid";
                continue;
            }

            try {
                Kaldik::updateOrCreate(
                    ['tanggal' => $tanggal, 'judul' => $judul],
                    ['tipe' => $tipe, 'keterangan' => $keterangan]
                );
                $inserted++;
            } catch (\Exception $e) {
                $errors[] = "Baris " . ($i + 1) . ": " . $e->getMessage();
            }
        }

        return response()->json([
            'status'   => 'ok',
            'inserted' => $inserted,
            'errors'   => $errors,
        ]);
    }
}
