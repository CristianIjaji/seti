<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TblEstadosCotizaciones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_estado_cotizacion', function (Blueprint $table){
            $table->bigIncrements('id_estado_cotizacion');
            $table->unsignedBigInteger('id_cotizacion');
            $table->unsignedBigInteger('estado');
            $table->string('comentario');
            $table->unsignedBigInteger('id_usuareg');
            $table->timestamps();

            $table->foreign('id_cotizacion')->references('id_cotizacion')->on('tbl_cotizaciones');
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
        Schema::dropIfExists('tbl_estado_cotizacion');
    }
}
