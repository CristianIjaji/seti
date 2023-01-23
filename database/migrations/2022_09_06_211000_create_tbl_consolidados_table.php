<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblConsolidadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_consolidados', function (Blueprint $table) {
            $table->bigIncrements('id_consolidado');
            $table->unsignedBigInteger('id_tercero_cliente');
            $table->date('mes');
            $table->unsignedBigInteger('id_dominio_estado');
            $table->unsignedBigInteger('id_tercero_responsable');
            $table->unsignedBigInteger('id_usuareg');
            $table->timestamps();

            $table->foreign('id_tercero_cliente')->references('id_tercero')->on('tbl_terceros')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_dominio_estado')->references('id_dominio')->on('tbl_dominios')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_tercero_responsable')->references('id_tercero')->on('tbl_terceros')
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
        Schema::dropIfExists('tbl_consolidados');
    }
}
