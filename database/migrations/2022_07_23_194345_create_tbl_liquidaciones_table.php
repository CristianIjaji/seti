<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblLiquidacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_liquidaciones', function (Blueprint $table) {
            $table->bigIncrements('id_liquidacion');
            $table->unsignedBigInteger('id_cotizacion');
            $table->decimal('valor', 20, 2)->nullable();
            $table->unsignedBigInteger('id_dominio_estado');
            $table->unsignedBigInteger('id_usuareg');
            $table->timestamps();

            $table->foreign('id_cotizacion')->references('id_cotizacion')->on('tbl_cotizaciones')
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
        Schema::dropIfExists('tbl_liquidaciones');
    }
}
