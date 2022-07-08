<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TblConfiguracionClienteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_configuracion_cliente', function (Blueprint $table) {
            $table->bigIncrements('id_configuracion_cliente');
            $table->unsignedBigInteger('id_tercero_cliente');
            $table->string('impresora')->nullable();
            $table->unsignedBigInteger('id_dominio_recibo');
            $table->string('logo')->default('');
            $table->smallInteger('estado')->default(1);
            $table->string('servicios')->default('');
            $table->unsignedBigInteger('id_usuareg');
            $table->timestamps();

            $table->foreign('id_tercero_cliente')->references('id_tercero')->on('tbl_terceros');
            $table->foreign('id_dominio_recibo')->references('id_dominio')->on('tbl_dominios');
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
        Schema::dropIfExists('tbl_configuracion_cliente');
    }
}
