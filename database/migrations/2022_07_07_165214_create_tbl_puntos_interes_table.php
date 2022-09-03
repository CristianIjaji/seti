<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblPuntosInteresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_puntos_interes', function (Blueprint $table) {
            $table->bigIncrements('id_punto_interes');
            $table->unsignedBigInteger('id_cliente');
            $table->unsignedBigInteger('id_zona');
            $table->string('nombre');
            $table->string('latitud')->nullable();
            $table->string('longitud')->nullable();
            $table->smallInteger('estado')->default(1);
            $table->text('descripcion');
            $table->unsignedBigInteger('id_tipo_transporte');
            $table->unsignedBigInteger('id_tipo_accesso');
            $table->unsignedBigInteger('id_usuareg');
            $table->timestamps();

            $table->foreign('id_cliente')->references('id_tercero')->on('tbl_terceros')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_zona')->references('id_dominio')->on('tbl_dominios')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_tipo_transporte')->references('id_dominio')->on('tbl_dominios')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_tipo_accesso')->references('id_dominio')->on('tbl_dominios')
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
        Schema::dropIfExists('tbl_puntos_interes');
    }
}
