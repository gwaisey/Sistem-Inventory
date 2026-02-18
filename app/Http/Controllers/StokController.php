<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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

        // CEK MASTER BARANG 
        $barang = DB::table('ms_barang')->where('kode_barang', $request->kode_barang)->first();
        if (!$barang) {
            $idB = DB::table('ms_barang')->insertGetId([
                'kode_barang' => $request->kode_barang,
                'nama_barang' => $request->nama_barang ?? $request->kode_barang
            ]);
            $barang = DB::table('ms_barang')->where('ID_Barang', $idB)->first();
        }

        // CEK MASTER LOKASI 
        $lokasi = DB::table('ms_lokasi')->where('kode_lokasi', $request->kode_lokasi)->first();
        if (!$lokasi) {
            $idL = DB::table('ms_lokasi')->insertGetId([
                'kode_lokasi' => $request->kode_lokasi,
                'nama_lokasi' => 'Gudang ' . $request->kode_lokasi
            ]);
            $lokasi = DB::table('ms_lokasi')->where('ID_Lokasi', $idL)->first();
        }

        if ($jenis == 'MASUK') {
            $idStok = DB::table('stok_barang')->insertGetId([
                'id_lokasi'   => $lokasi->ID_Lokasi, 
                'id_barang'   => $barang->ID_Barang, 
                'kode_lokasi' => $request->kode_lokasi,
                'kode_barang' => $request->kode_barang,
                'saldo'       => $qtyInput,             
                'tgl_masuk'   => $tglInput,            
            ]);

            $dataHistoryMasuk = [
                'ID_Stok'     => $idStok, 
                'Bukti'       => $request->bukti,
                'Tgl'         => $tglInput,
                'Jam'         => now()->format('H:i:s'),
                'Kode_Lokasi' => $request->kode_lokasi,
                'Kode_Barang' => $request->kode_barang,
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
                    'Kode_Lokasi' => $request->kode_lokasi,
                    'Kode_Barang' => $request->kode_barang,
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

        if ($request->filled('lokasi')) $query->where('l.kode_lokasi', $request->lokasi);
        if ($request->filled('kode_barang')) $query->where('b.kode_barang', $request->kode_barang);

        $data = $query->orderBy('s.tgl_masuk', 'asc')
                    ->orderBy('s.ID_Stok', 'asc')
                    ->get();
                    
        return view('report-saldo', compact('data'));
    }

    public function reportHistory() 
    {
        return view('report-history');
    }

    public function apiHistory(Request $request) 
    {
        $query = DB::table('transaksi_history as h')
            ->select(
                'h.id as id_history', 
                'h.ID_Stok as id_stok', 
                'h.Bukti', 
                DB::raw("DATE_FORMAT(h.Tgl, '%d/%m/%Y') as Tgl"), 
                'h.Jam', 
                'h.Kode_Lokasi', 
                'h.Kode_Barang', 
                'h.Qty_Trn', 
                'h.Prog'
            );

        if ($request->filled('bukti')) {
            $query->where('h.Bukti', 'like', '%' . $request->bukti . '%');
        }
        
        if ($request->filled('tgl')) {
            $query->where('h.Tgl', $request->tgl);
        }

        $data = $query->orderBy('h.Tgl', 'asc')
                      ->orderBy('h.Jam', 'asc')
                      ->get();
        
        return response()->json($data); 
    }
}