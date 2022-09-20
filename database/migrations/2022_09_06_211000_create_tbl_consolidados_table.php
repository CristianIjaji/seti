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
            $table->bigInteger('id_consolidado');
            $table->unsignedBigInteger('id_cliente');
            $table->integer('anyo');
            $table->unsignedBigInteger('id_mes');
            $table->string('observacion');
            $table->unsignedBigInteger('id_estado_consolidado');
            $table->unsignedBigInteger('id_responsable_cliente');
            $table->unsignedBigInteger('id_usuareg');
            $table->timestamps();

            $table->foreign('id_cliente')->references('id_tercero')->on('tbl_terceros')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_mes')->references('id_dominio')->on('tbl_dominios')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_estado_consolidado')->references('id_dominio')->on('tbl_dominios')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_responsable_cliente')->references('id_tercero')->on('tbl_terceros')
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
