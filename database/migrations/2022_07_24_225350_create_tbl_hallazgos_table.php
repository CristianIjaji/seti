<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblHallazgosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_hallazgos', function (Blueprint $table) {
            $table->bigIncrements('id_hallazgo');
            $table->unsignedBigInteger('id_actividad');
            $table->unsignedBigInteger('id_supervisor');
            $table->string('hallazgo');
            $table->unsignedBigInteger('id_estado_hallazgo');
            $table->unsignedBigInteger('id_usuareg');
            $table->timestamps();

            $table->foreign('id_actividad')->references('id_actividad')->on('tbl_actividades')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_supervisor')->references('id_tercero')->on('tbl_terceros')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_estado_hallazgo')->references('id_dominio')->on('tbl_dominios')
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
        Schema::dropIfExists('tbl_hallazgos');
    }
}
