<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TblListaPrecios extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_lista_precios', function (Blueprint $table) {
            $table->bigIncrements('id_lista_precio');
            $table->unsignedBigInteger('id_cliente');
            $table->unsignedBigInteger('id_tipo_item');
            $table->string('codigo');
            $table->text('descripcion');
            $table->string('unidad');
            $table->decimal('cantidad', 5, 2);
            $table->decimal('valor_unitario', 20, 2);
            $table->smallInteger('estado')->default(1);
            $table->unsignedBigInteger('id_usuareg');
            $table->timestamps();

            $table->foreign('id_cliente')->references('id_tercero')->on('tbl_terceros');
            $table->foreign('id_tipo_item')->references('id_dominio')->on('tbl_dominios');
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
        Schema::dropIfExists('tbl_lista_precios');
    }
}
