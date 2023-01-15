<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblMovimientosDetalleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_movimientos_detalle', function (Blueprint $table) {
            $table->bigIncrements('id_movimiento_detalle');
            $table->unsignedBigInteger('id_movimiento');
            $table->unsignedBigInteger('id_inventario');
            $table->decimal('cantidad');
            $table->decimal('valor_unitario', 20, 2);
            $table->unsignedBigInteger('iva');
            $table->decimal('valor_total', 20, 2);
            $table->unsignedBigInteger('id_usuareg');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_movimientos_detalle');
    }
}
