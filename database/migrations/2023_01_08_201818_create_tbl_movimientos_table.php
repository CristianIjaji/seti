<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblMovimientosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_movimientos', function (Blueprint $table) {
            $table->bigIncrements('id_movimiento');
            $table->unsignedBigInteger('id_dominio_tipo_movimiento');
            $table->unsignedBigInteger('id_tercero_recibe');
            $table->unsignedBigInteger('id_tercero_entrega');
            $table->string('documento')->nullable();
            $table->string('observaciones');
            $table->unsignedBigInteger('id_dominio_iva');
            $table->decimal('total', 20, 2)->default(0);
            $table->decimal('saldo', 20, 2)->default(0);
            $table->unsignedBigInteger('id_dominio_estado');
            $table->unsignedBigInteger('id_usuareg');
            $table->timestamps();

            $table->foreign('id_dominio_tipo_movimiento')->references('id_dominio')->on('tbl_dominios')
                ->onDelete('cascade')->onUpdate('cascade'); 
            $table->foreign('id_tercero_recibe')->references('id_tercero')->on('tbl_terceros')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_tercero_entrega')->references('id_tercero')->on('tbl_terceros')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_dominio_iva')->references('id_dominio')->on('tbl_dominios')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_dominio_estado')->references('id_dominio')->on('tbl_dominios')
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
        Schema::dropIfExists('tbl_movimientos');
    }
}
