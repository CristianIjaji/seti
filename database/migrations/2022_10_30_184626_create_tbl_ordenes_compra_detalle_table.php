<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblOrdenesCompraDetalleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_ordenes_compra_detalle', function (Blueprint $table) {
            $table->bigIncrements('id_orden_compra_detalle');
            $table->unsignedBigInteger('id_orden_compra');
            $table->unsignedBigInteger('id_cotizacion_detalle');
            $table->unsignedBigInteger('id_lista_precio');
            $table->string('descripcion');
            $table->decimal('cantidad');
            $table->decimal('valor_unitario', 20, 2);
            $table->decimal('valor_total', 20, 2);
            $table->timestamps();

            $table->foreign('id_orden_compra')->references('id_orden_compra')->on('tbl_ordenes_compra')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_cotizacion_detalle')->references('id_cotizacion_detalle')->on('tbl_cotizaciones_detalle')
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
        Schema::dropIfExists('tbl_ordenes_compra_detalle');
    }
}
