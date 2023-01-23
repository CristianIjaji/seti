<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblFacturasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_facturas', function (Blueprint $table) {
            $table->bigIncrements('id_factura');
            $table->string('numero_factura');
            $table->unsignedBigInteger('id_dominio_estado');
            $table->unsignedBigInteger('id_tercero_proveedor');
            $table->decimal('dias_pago');
            $table->unsignedBigInteger('id_usuareg');
            $table->timestamps();

            $table->foreign('id_dominio_estado')->references('id_dominio')->on('tbl_dominios')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_tercero_proveedor')->references('id_tercero')->on('tbl_terceros')
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
        Schema::dropIfExists('tbl_facturas');
    }
}
