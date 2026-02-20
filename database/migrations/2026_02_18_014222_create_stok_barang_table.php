<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStokBarangTable extends Migration
{
  
    public function up()
    {
        Schema::create('stok_barang', function (Blueprint $table) {
            $table->id('ID_Stok'); 
            
            // Cukup simpan ID-nya saja
            $table->unsignedBigInteger('id_lokasi'); 
            $table->unsignedBigInteger('id_barang'); 

            $table->integer('saldo');
            $table->date('tgl_masuk');
            $table->timestamps(); 

            // Tambahkan relasi (Optional tapi sangat disarankan)
            $table->foreign('id_barang')->references('ID_Barang')->on('ms_barang')->onDelete('cascade');
            $table->foreign('id_lokasi')->references('ID_Lokasi')->on('ms_lokasi')->onDelete('cascade');
        });
    }

   
    public function down()
    {
        Schema::dropIfExists('stok_barang');
    }
}
