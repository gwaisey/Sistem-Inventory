<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\MasterBarangController;
use App\Http\Controllers\MasterLokasiController;

class StokController extends Controller
{
    public function index()
    {
        return view('maintenance-stok');
    }

    public function store(Request $request)
    {
        $jenis = $request->jenis_transaksi; 
        $tglInput = $request->tgl_transaksi;
        $qtyInput = $request->quantity;

        // Panggil Master Controller
        $barang = MasterBarangController::cekAtauSimpan($request->kode_barang, $request->nama_barang);
        $lokasi = MasterLokasiController::cekAtauSimpan($request->kode_lokasi);

        if ($jenis == 'MASUK') {
            $idStok = DB::table('stok_barang')->insertGetId([
                'id_lokasi'   => $lokasi->ID_Lokasi, 
                'id_barang'   => $barang->ID_Barang, 
                // Kolom kode_lokasi & kode_barang dihapus dari sini karena sudah ada ID
                'saldo'       => $qtyInput,             
                'tgl_masuk'   => $tglInput,            
            ]);

            $dataHistoryMasuk = [
                'ID_Stok'     => $idStok, 
                'Bukti'       => $request->bukti,
                'Tgl'         => $tglInput,
                'Jam'         => now()->format('H:i:s'),
                // Kolom Kode_Lokasi & Kode_Barang dihapus dari history
                'Qty_Trn'     => $qtyInput,
                'Prog'        => 'MAINTENANCE_STOK',
                'User'        => 'GRACE', 
            ];

            DB::table('transaksi_history')->insert($dataHistoryMasuk);
            return redirect()->back()->with('success', 'Barang Masuk!');
        }

        if ($jenis == 'KELUAR') {
            $total = DB::table('stok_barang')
                ->where('id_barang', $barang->ID_Barang)
                ->where('id_lokasi', $lokasi->ID_Lokasi)
                ->sum('saldo'); 

            if ($total < $qtyInput) return back()->with('error', 'Saldo Tidak Cukup!');

            $stoks = DB::table('stok_barang')
                ->where('id_barang', $barang->ID_Barang)
                ->where('id_lokasi', $lokasi->ID_Lokasi)
                ->where('saldo', '>', 0) 
                ->orderBy('tgl_masuk', 'asc')
                ->orderBy('ID_Stok', 'asc')
                ->get();

            $sisaKeluar = $qtyInput;
            foreach ($stoks as $stok) {
                if ($sisaKeluar <= 0) break;
                
                $ambil = min($stok->saldo, $sisaKeluar); 

                $dataHistoryKeluar = [
                    'ID_Stok'     => $stok->ID_Stok, 
                    'Bukti'       => $request->bukti,
                    'Tgl'         => $tglInput,
                    'Jam'         => now()->format('H:i:s'),
                    'Qty_Trn'     => -$ambil, 
                    'Prog'        => 'MAINTENANCE_STOK',
                    'User'        => 'GRACE',
                ];

                DB::table('stok_barang')->where('ID_Stok', $stok->ID_Stok)->decrement('saldo', $ambil); 
                DB::table('transaksi_history')->insert($dataHistoryKeluar); 

                $sisaKeluar -= $ambil;
            }
            return redirect()->back()->with('success', 'Barang Keluar Terproses!');
        }
    }

    public function reportSaldo(Request $request)
    {
        // Menggunakan JOIN karena tabel stok_barang sekarang cuma simpan ID
        $query = DB::table('stok_barang as s')
            ->join('ms_barang as b', 's.id_barang', '=', 'b.ID_Barang')
            ->join('ms_lokasi as l', 's.id_lokasi', '=', 'l.ID_Lokasi')
            ->select(
                's.ID_Stok',
                'l.kode_lokasi as Kode_Lokasi', 
                'b.kode_barang as Kode_Barang', 
                'b.nama_barang as Nama_Barang', 
                's.saldo as Saldo', 
                's.tgl_masuk as Tgl_Masuk'
            );

        if ($request->filled('lokasi')) {
            $query->where('l.kode_lokasi', $request->lokasi);
        }

        if ($request->filled('kode_barang')) {
            $query->where('b.kode_barang', 'like', '%' . $request->kode_barang . '%');
        }

        $data = $query->orderBy('s.tgl_masuk', 'asc')->get();
        
        $listLokasi = DB::table('ms_lokasi')->get();
        $listBarang = DB::table('ms_barang')->get();

        return view('report-saldo', compact('data', 'listLokasi', 'listBarang'));
    }

    public function reportHistory() 
    {
        return view('report-history');
    }

    public function apiHistory(Request $request) 
    {
        $query = DB::table('transaksi_history as h')
            ->join('stok_barang as s', 'h.ID_Stok', '=', 's.ID_Stok')
            ->join('ms_barang as b', 's.id_barang', '=', 'b.ID_Barang')
            ->join('ms_lokasi as l', 's.id_lokasi', '=', 'l.ID_Lokasi')
            ->select(
                'h.id as id_history', 
                'h.ID_Stok as id_stok', 
                'h.Bukti', 
                DB::raw("DATE_FORMAT(h.Tgl, '%d/%m/%Y') as Tgl"), 
                'h.Jam', 
                'l.kode_lokasi as Kode_Lokasi', 
                'b.kode_barang as Kode_Barang', 
                'h.Qty_Trn', 
                'h.Prog'
            );

        // Filter yang sudah ada
        if ($request->filled('bukti')) {
            $query->where('h.Bukti', 'like', '%' . $request->bukti . '%');
        }
        if ($request->filled('tgl')) {
            $query->where('h.Tgl', $request->tgl);
        }

        // TAMBAHKAN FILTER BARU INI:
        if ($request->filled('lokasi')) {
            $query->where('l.kode_lokasi', $request->lokasi);
        }
        if ($request->filled('kode_barang')) {
            $query->where('b.kode_barang', 'like', '%' . $request->kode_barang . '%');
        }

        $data = $query->orderBy('h.Tgl', 'asc')
                    ->orderBy('h.Jam', 'asc')
                    ->get();
        
        return response()->json($data); 
    }
}