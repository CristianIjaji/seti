<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TblCotizacionesDetalleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_cotizaciones_detalle', function(Blueprint $table){
            $table->bigIncrements('id_cotizacion_detalle');
            $table->unsignedBigInteger('id_cotizacion');
            $table->unsignedBigInteger('id_dominio_tipo_item');
            $table->unsignedBigInteger('id_lista_precio');
            $table->text('descripcion');
            $table->string('unidad');
            $table->decimal('cantidad');
            $table->decimal('valor_unitario', 20, 2);
            $table->decimal('valor_total', 20, 2);
            $table->timestamps();

            $table->foreign('id_cotizacion')->references('id_cotizacion')->on('tbl_cotizaciones')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_dominio_tipo_item')->references('id_dominio')->on('tbl_dominios')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_lista_precio')->references('id_lista_precio')->on('tbl_lista_precios')
                ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_cotizaciones_detalle');
    }
}
