<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblCotizacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_cotizaciones', function (Blueprint $table) {
            $table->bigIncrements('id_cotizacion');
            $table->string('ot_trabajo')->unique()->nullable();
            $table->unsignedBigInteger('id_cliente');
            $table->unsignedBigInteger('id_estacion');
            $table->unsignedBigInteger('id_tipo_trabajo');
            $table->date('fecha_solicitud');
            $table->dateTime('fecha_envio')->nullable();
            $table->unsignedBigInteger('id_prioridad');
            $table->unsignedBigInteger('estado');
            $table->unsignedBigInteger('id_responsable_cliente');
            $table->decimal('valor', 20, 2);
            $table->unsignedBigInteger('iva');
            $table->text('descripcion');
            $table->decimal('valor_reasignado', 20, 2)->nullable();
            $table->unsignedBigInteger('id_usuareg');
            $table->timestamps();

            $table->foreign('id_cliente')->references('id_tercero')->on('tbl_terceros');
            $table->foreign('id_estacion')->references('id_punto_interes')->on('tbl_puntos_interes');
            $table->foreign('id_tipo_trabajo')->references('id_dominio')->on('tbl_dominios');
            $table->foreign('id_prioridad')->references('id_dominio')->on('tbl_dominios');
            $table->foreign('estado')->references('id_dominio')->on('tbl_dominios');
            $table->foreign('id_responsable_cliente')->references('id_tercero')->on('tbl_terceros');
            $table->foreign('iva')->references('id_dominio')->on('tbl_dominios');
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
        Schema::dropIfExists('tbl_cotizaciones');
    }
}
