<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TblTercerosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_terceros', function (Blueprint $table) {
            $table->bigIncrements('id_tercero');
            $table->unsignedBigInteger('id_dominio_tipo_documento');
            $table->string('documento');
            $table->string('dv')->nullable();
            $table->string('razon_social')->nullable();
            $table->string('nombres');
            $table->string('apellidos');
            $table->string('ciudad');
            $table->string('direccion');
            $table->string('correo');
            $table->string('telefono');
            $table->unsignedBigInteger('id_dominio_tipo_tercero');
            $table->unsignedBigInteger('id_tercero_responsable')->nullable();
            $table->string('logo')->default('');
            $table->integer('estado')->default(1);
            $table->unsignedBigInteger('id_usuareg');
            $table->timestamps();

            $table->foreign('id_dominio_tipo_documento')->references('id_dominio')->on('tbl_dominios')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_dominio_tipo_tercero')->references('id_dominio')->on('tbl_dominios')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_tercero_responsable')->references('id_tercero')->on('tbl_terceros')
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
        Schema::dropIfExists('tbl_terceros');
    }
}
