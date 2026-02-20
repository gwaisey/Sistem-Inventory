<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStokBarangTable extends Migration
{
  
    public function up()
    {
        // Tipe-tipe data 
        Schema::create('stok_barang', function (Blueprint $table) {
            $table->id('ID_Stok'); 
            
        
            $table->unsignedBigInteger('id_lokasi'); 
            $table->unsignedBigInteger('id_barang'); 

            $table->string('kode_barang');//->unique();
            $table->string('kode_lokasi');
            
            $table->integer('saldo');
            $table->date('tgl_masuk');
            
          
            $table->timestamps(); 
        });
    }

   
    public function down()
    {
        Schema::dropIfExists('stok_barang');
    }
}
