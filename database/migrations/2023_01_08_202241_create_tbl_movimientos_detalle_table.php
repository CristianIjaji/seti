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
            $table->decimal('valor_total', 20, 2);
            $table->unsignedBigInteger('id_usuareg');
            $table->timestamps();
            
            $table->foreign('id_movimiento')->references('id_movimiento')->on('tbl_movimientos')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_inventario')->references('id_inventario')->on('tbl_inventario')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_usuareg')->references('id_usuario')->on('tbl_usuarios')
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
        Schema::dropIfExists('tbl_movimientos_detalle');
    }
}
