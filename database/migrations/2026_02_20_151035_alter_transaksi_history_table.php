<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTransaksiHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transaksi_history', function (Blueprint $table) {
            $table->dropColumn(['Kode_Barang', 'Kode_Lokasi']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transaksi_history', function (Blueprint $table) {
            $table->string(['Kode_Barang','Kode_Lokasi']);
    });
    }
}