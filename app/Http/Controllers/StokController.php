<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

        $barang = DB::table('ms_barang')->where('kode_barang', $request->kode_barang)->first();
        if (!$barang) {
            $idBarang = DB::table('ms_barang')->insertGetId([
                'kode_barang' => $request->kode_barang,
                'nama_barang' => $request->nama_barang ?? $request->kode_barang,
            ]);
            $barang = DB::table('ms_barang')->where('ID_Barang', $idBarang)->first();
        }

        $lokasi = DB::table('ms_lokasi')->where('kode_lokasi', $request->kode_lokasi)->first();
        if (!$lokasi) {
            $idLokasi = DB::table('ms_lokasi')->insertGetId([
                'kode_lokasi' => $request->kode_lokasi,
                'nama_lokasi' => 'Gudang ' . $request->kode_lokasi,
            ]);
            $lokasi = DB::table('ms_lokasi')->where('ID_Lokasi', $idLokasi)->first();
        }

        $lastStok = DB::table('stok_barang')
                    ->where('id_barang', $barang->ID_Barang)
                    ->where('id_lokasi', $lokasi->ID_Lokasi)
                    ->orderBy('Tgl_Masuk', 'desc')
                    ->first();

        if ($lastStok && $tglInput < $lastStok->Tgl_Masuk) {
            return back()->with('error', 'Ditolak! Sudah ada saldo pada tanggal yang lebih baru.');
        }

        if ($jenis == 'MASUK') {
            $idStok = DB::table('stok_barang')->insertGetId([
                'id_lokasi'   => $lokasi->ID_Lokasi, 
                'id_barang'   => $barang->ID_Barang, 
                'Kode_Lokasi' => $request->kode_lokasi,
                'Kode_Barang' => $request->kode_barang,
                'Saldo'       => $qtyInput,
                'Tgl_Masuk'   => $tglInput,
            ]);

            DB::table('transaksi_history')->insert([
                'ID_Stok'     => $idStok, 
                'Bukti'       => $request->bukti,
                'Tgl'         => $tglInput,
                'Jam'         => now()->format('H:i:s'),
                'Kode_Lokasi' => $request->kode_lokasi,
                'Kode_Barang' => $request->kode_barang,
                'Qty_Trn'     => $qtyInput,
                'Prog'        => 'MAINTENANCE_STOK',
                'User'        => 'GRACE', 
            ]);

            return redirect()->back()->with('success', 'Sukses Posting Barang Masuk!');
        }

        if ($jenis == 'KELUAR') {
            $totalSaldo = DB::table('stok_barang')
                            ->where('id_barang', $barang->ID_Barang)
                            ->where('id_lokasi', $lokasi->ID_Lokasi)
                            ->sum('Saldo');

            if ($totalSaldo < $qtyInput) {
                return back()->with('error', 'Ditolak! Saldo tidak mencukupi.');
            }

            $stoks = DB::table('stok_barang')
                        ->where('id_barang', $barang->ID_Barang)
                        ->where('id_lokasi', $lokasi->ID_Lokasi)
                        ->where('Saldo', '>', 0)
                        ->orderBy('Tgl_Masuk', 'asc') 
                        ->get();

            $sisaKeluar = $qtyInput;
            foreach ($stoks as $stok) {
                if ($sisaKeluar <= 0) break;
                $ambil = min($stok->Saldo, $sisaKeluar);

                DB::table('stok_barang')->where('ID_Stok', $stok->ID_Stok)->decrement('Saldo', $ambil);

                DB::table('transaksi_history')->insert([
                    'ID_Stok'     => $stok->ID_Stok,
                    'Bukti'       => $request->bukti,
                    'Tgl'         => $tglInput,
                    'Jam'         => now()->format('H:i:s'),
                    'Kode_Lokasi' => $request->kode_lokasi,
                    'Kode_Barang' => $request->kode_barang,
                    'Qty_Trn'     => -$ambil, 
                    'Prog'        => 'MAINTENANCE_STOK',
                    'User'        => 'GRACE',
                ]);

                $sisaKeluar -= $ambil;
            }
            return redirect()->back()->with('success', 'Sukses Mengeluarkan Barang!');
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
            's.Saldo', 
            's.Tgl_Masuk'
        );

        if ($request->filled('lokasi')) $query->where('l.kode_lokasi', $request->lokasi);
        if ($request->filled('kode_barang')) $query->where('b.kode_barang', $request->kode_barang);

        $data = $query->orderBy('s.Tgl_Masuk', 'asc')->get();
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
                'h.Tgl', 
                'h.Jam', 
                'h.Kode_Lokasi', 
                'h.Kode_Barang', 
                'h.Qty_Trn', 
                'h.Prog'
            );

        // Hanya filter jika inputnya TIDAK kosong
        if ($request->filled('bukti')) {
            $query->where('h.Bukti', 'like', '%' . $request->bukti . '%');
        }
        
        if ($request->filled('tgl')) {
            $query->where('h.Tgl', $request->tgl);
        }

        // Mengambil data dengan urutan terbaru agar Grace langsung lihat hasil inputnya
        $data = $query->orderBy('h.Tgl', 'asc')
                    ->orderBy('h.Jam', 'asc')
                    ->get();
        
        return response()->json($data); 
    }
}