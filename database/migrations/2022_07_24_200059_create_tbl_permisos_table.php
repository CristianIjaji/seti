<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblPermisosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_permisos', function (Blueprint $table) {
            $table->bigIncrements('id_permiso');
            $table->text('descripcion');
            $table->unsignedBigInteger('id_estado_permiso');
            $table->unsignedBigInteger('id_usuareg');
            $table->timestamps();

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
        Schema::dropIfExists('tbl_permisos');
    }
}
