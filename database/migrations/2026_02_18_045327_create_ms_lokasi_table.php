<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMsLokasiTable extends Migration
{
    public function up()
    {
        Schema::create('ms_lokasi', function (Blueprint $table) {
            $table->id('ID_Lokasi'); 
            $table->string('kode_lokasi')->unique(); 
            $table->string('nama_lokasi');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ms_lokasi');
    }
}