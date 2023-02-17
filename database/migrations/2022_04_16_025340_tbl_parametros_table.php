<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TblParametrosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_parametros_aplicacion', function (Blueprint $table) {
            $table->bigIncrements('id_parametro_aplicacion');
            $table->string('llave')->unique();
            $table->string('valor');
            $table->string('descripcion');
            $table->unsignedBigInteger('id_parametro_padre')->nullable();
            $table->smallInteger('estado')->default(1);
            $table->unsignedBigInteger('id_usuareg');
            $table->timestamps();

            $table->foreign('id_parametro_padre')->references('id_parametro_aplicacion')->on('tbl_parametros_aplicacion')
                ->onDelete('set null')->onUpdate('cascade');
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
        Schema::dropIfExists('tbl_parametros_aplicacion');
    }
}
