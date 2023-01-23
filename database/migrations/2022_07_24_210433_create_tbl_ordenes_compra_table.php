<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblOrdenesCompraTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_ordenes_compra', function (Blueprint $table) {
            $table->bigIncrements('id_orden_compra');
            $table->unsignedBigInteger('id_tercero_almacen');
            $table->unsignedBigInteger('id_tercero_proveedor');
            $table->unsignedBigInteger('id_dominio_tipo');
            $table->text('descripcion');
            $table->unsignedBigInteger('id_dominio_modalidad_pago');
            $table->unsignedBigInteger('id_dominio_iva');
            $table->unsignedBigInteger('id_dominio_estado');
            $table->unsignedBigInteger('id_tercero_asesor');
            $table->date('vencimiento')->nullable();
            $table->decimal('cupo_actual', 20, 2);
            $table->unsignedBigInteger('id_usuareg');
            $table->timestamps();

            $table->foreign('id_tercero_almacen')->references('id_tercero')->on('tbl_terceros')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_tercero_proveedor')->references('id_tercero')->on('tbl_terceros')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_dominio_tipo')->references('id_dominio')->on('tbl_dominios')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_dominio_modalidad_pago')->references('id_dominio')->on('tbl_dominios')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_dominio_iva')->references('id_dominio')->on('tbl_dominios')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_dominio_estado')->references('id_dominio')->on('tbl_dominios')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_tercero_asesor')->references('id_tercero')->on('tbl_terceros')
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
        Schema::dropIfExists('tbl_ordenes_compra');
    }
}
