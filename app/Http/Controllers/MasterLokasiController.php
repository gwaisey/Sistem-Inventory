<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MasterLokasiController extends Controller
{
    public function index()
    {
        $lokasi = DB::table('ms_lokasi')->get();
        return view('master.lokasi', compact('lokasi'));
    }

    public static function cekAtauSimpan($kode)
    {
        // 1. Cari lokasi berdasarkan KODE
        $lokasi = DB::table('ms_lokasi')->where('kode_lokasi', $kode)->first();

        if ($lokasi) {
            // Karena di StokController kamu hanya mengirim kode, 
            // maka kita tinggal mengembalikan data lokasi yang sudah ada.
            return $lokasi;
        }

        // 2. Jika benar-benar baru, simpan dengan nama default
        $id = DB::table('ms_lokasi')->insertGetId([
            'kode_lokasi' => $kode,
            'nama_lokasi' => 'Gudang ' . $kode // Default name
        ]);
        
        return DB::table('ms_lokasi')->where('ID_Lokasi', $id)->first();
    }
}
