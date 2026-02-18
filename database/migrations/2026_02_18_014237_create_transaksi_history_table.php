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
            $table->integer('ID_Stok'); 
            $table->string('Bukti');
            $table->date('Tgl');
            $table->time('Jam');
            $table->string('Kode_Lokasi');
            $table->string('Kode_Barang');
            $table->integer('Qty_Trn');
            $table->string('Prog');
            $table->string('User');
            $table->timestamps();
        });
    }

   

    public function down()
    {
        Schema::dropIfExists('transaksi_history');
    }
}
