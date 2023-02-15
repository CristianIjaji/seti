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
            $table->string('ot_trabajo')->nullable();
            $table->unsignedBigInteger('id_tercero_cliente');
            $table->unsignedBigInteger('id_estacion');
            $table->unsignedBigInteger('id_dominio_tipo_trabajo');
            $table->date('fecha_solicitud');
            $table->dateTime('fecha_envio')->nullable();
            $table->unsignedBigInteger('id_dominio_prioridad');
            $table->unsignedBigInteger('id_dominio_estado');
            $table->unsignedBigInteger('id_tercero_responsable');
            $table->decimal('valor', 20, 2);
            $table->unsignedBigInteger('id_dominio_iva');
            $table->text('descripcion');
            $table->decimal('valor_reasignado', 20, 2)->nullable();
            $table->unsignedBigInteger('id_usuareg');
            $table->timestamps();

            $table->foreign('id_tercero_cliente')->references('id_tercero')->on('tbl_terceros')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_estacion')->references('id_punto_interes')->on('tbl_puntos_interes')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_dominio_tipo_trabajo')->references('id_dominio')->on('tbl_dominios')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_dominio_prioridad')->references('id_dominio')->on('tbl_dominios')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_dominio_estado')->references('id_dominio')->on('tbl_dominios')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_tercero_responsable')->references('id_tercero')->on('tbl_terceros')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_dominio_iva')->references('id_dominio')->on('tbl_dominios')
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
        Schema::dropIfExists('tbl_cotizaciones');
    }
}
