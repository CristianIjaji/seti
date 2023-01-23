<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TblEstadosActividades extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_estado_actividad', function (Blueprint $table) {
            $table->bigIncrements('id_dominio_estado');
            $table->unsignedBigInteger('id_actividad');
            $table->unsignedBigInteger('estado');
            $table->string('comentario');
            $table->unsignedBigInteger('id_usuareg');
            $table->timestamps();

            $table->foreign('id_actividad')->references('id_actividad')->on('tbl_actividades');
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
        Schema::drop('tbl_estado_actividad');
    }
}
