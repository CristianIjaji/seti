<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblInformesActividadesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_informes_actividades', function (Blueprint $table) {
            $table->bigIncrements('id_informe_actividad');
            $table->integer('id_actividad')->nullable();
            $table->unsignedBigInteger('id_encargado');
            $table->unsignedBigInteger('id_estado_informe');
            $table->unsignedBigInteger('id_usuareg');
            $table->timestamps();

            $table->foreign('id_encargado')->references('id_tercero')->on('tbl_terceros')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_estado_informe')->references('id_dominio')->on('tbl_dominios')
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
        Schema::dropIfExists('tbl_informes_actividades');
    }
}
