<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TblCotizacionDetalles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_cotizacion_detalles', function(Blueprint $table){
            $table->bigIncrements('id_cotizacion_detalle');
            $table->unsignedBigInteger('id_tipo_item');
            $table->unsignedBigInteger('id_lista_precio');
            $table->string('descripcion');
            $table->string('unidad');
            $table->string('valor_unitario');
            $table->string('valor_total');

            $table->foreign('id_tipo_item')->references('id_dominio')->on('tbl_dominios');
            $table->foreign('id_lista_precio')->references('id_lista_precio')->on('tbl_lista_precios');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_cotizacion_detalles');
    }
}
