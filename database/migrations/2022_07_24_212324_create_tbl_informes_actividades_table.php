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
            $table->unsignedBigInteger('id_actividad')->nullable();
            $table->unsignedBigInteger('id_usuareg');
            $table->string('link');
            $table->timestamps();

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
        Schema::dropIfExists('tbl_informes_actividades');
    }
}
