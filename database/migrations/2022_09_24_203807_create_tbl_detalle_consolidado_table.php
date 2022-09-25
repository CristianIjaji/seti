<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblDetalleConsolidadoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_detalle_consolidado', function (Blueprint $table) {
            $table->bigIncrements('id_detalle_consolidado');
            $table->unsignedBigInteger('id_consolidado');
            $table->unsignedBigInteger('id_actividad');
            $table->string('observacion');
            $table->unsignedBigInteger('id_usuareg');
            $table->timestamps();

            $table->foreign('id_consolidado')->references('id_consolidado')->on('tbl_consolidados')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_actividad')->references('id_actividad')->on('tbl_actividades')
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
        Schema::dropIfExists('tbl_detalle_consolidado');
    }
}
