<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMsBarangTable extends Migration
{
   
    public function up()
    {
        Schema::create('ms_barang', function (Blueprint $table) {
            $table->id('ID_Barang'); 
            $table->string('kode_barang')->unique(); 
            $table->string('nama_barang');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ms_barang');
    }
}
