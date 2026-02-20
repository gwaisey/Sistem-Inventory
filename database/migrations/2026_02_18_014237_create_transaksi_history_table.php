<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransaksiHistoryTable extends Migration
{
 
    public function up()
    {
        Schema::create('transaksi_history', function (Blueprint $table) {
            $table->id(); 
            
            // Gunakan unsignedBigInteger agar sinkron dengan ID_Stok di tabel stok_barang
            $table->unsignedBigInteger('ID_Stok'); 
            
            $table->string('Bukti'); 
            $table->date('Tgl');
            $table->time('Jam');

            // KOLOM KODE_BARANG & KODE_LOKASI DIHAPUS DARI SINI
            // Karena sudah ada di file Alter atau memang ingin dihilangkan
            
            $table->integer('Qty_Trn');
            $table->string('Prog');
            $table->string('User');
            $table->timestamps();

            // Opsional: Tambahkan relasi agar data history tidak 'yatim piatu'
            $table->foreign('ID_Stok')->references('ID_Stok')->on('stok_barang')->onDelete('cascade');
        });
    }

   

    public function down()
    {
        Schema::dropIfExists('transaksi_history');
       
    }
}
