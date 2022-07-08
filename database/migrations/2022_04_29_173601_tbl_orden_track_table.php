<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TblOrdenTrackTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_orden_track', function (Blueprint $table) {
            $table->bigIncrements('id_orden_track');
            $table->unsignedBigInteger('id_orden');
            $table->unsignedBigInteger('id_dominio_accion');
            $table->unsignedBigInteger('id_usuareg');
            $table->timestamps();

            $table->foreign('id_dominio_accion')->references('id_dominio')->on('tbl_dominios');
            $table->foreign('id_usuareg')->references('id_usuario')->on('tbl_usuarios');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_orden_track');
    }
}
