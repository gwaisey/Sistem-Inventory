<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MasterBarangController extends Controller
{
    public function index()
    {
        $barang = DB::table('ms_barang')->get();
        return view('master.barang', compact('barang'));
    }

    // Fungsi pembantu yang nanti dipanggil StokController jika perlu
    public static function cekAtauSimpan($kode, $nama = null)
    {
        // 1. Cari barang berdasarkan KODE
        $barang = DB::table('ms_barang')->where('kode_barang', $kode)->first();

        if ($barang) {
            // 2. Jika kode ditemukan tapi NAMA-nya berbeda (dan input nama tidak kosong)
            if ($nama && $barang->nama_barang !== $nama) {
                // Kita hentikan proses dan beri peringatan
                abort(400, "Kode barang '$kode' sudah terdaftar untuk '{$barang->nama_barang}'. Tidak boleh digunakan untuk '$nama'!");
            }
            return $barang;
        }

        // 3. Jika benar-benar baru, baru simpan
        $id = DB::table('ms_barang')->insertGetId([
            'kode_barang' => $kode,
            'nama_barang' => $nama ?? $kode
        ]);
        
        return DB::table('ms_barang')->where('ID_Barang', $id)->first();
    }
}
