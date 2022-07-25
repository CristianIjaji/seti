<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblMenuTipoTerceroTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_menu_tipo_tercero', function (Blueprint $table) {
            $table->bigIncrements('id_menu_tipo_tercero');
            $table->unsignedBigInteger('id_menu');
            $table->unsignedBigInteger('id_tipo_tercero');
            $table->boolean('crear');
            $table->boolean('editar');
            $table->boolean('ver');
            $table->boolean('importar');
            $table->boolean('exportar');
            $table->timestamps();

            $table->foreign('id_menu')->references('id_menu')->on('tbl_menus');
            $table->foreign('id_tipo_tercero')->references('id_dominio')->on('tbl_dominios');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_menu_tipo_tercero');
    }
}
