<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblActividadesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_actividades', function (Blueprint $table) {
            $table->bigIncrements('id_actividad');
            $table->string('ot')->uniqid();
            $table->unsignedBigInteger('id_tipo_actividad');
            $table->unsignedBigInteger("id_subsistema");
            $table->text('descripcion');
            $table->unsignedBigInteger('id_encargado_cliente');
            $table->unsignedBigInteger('id_resposable_contratista');
            $table->unsignedBigInteger('id_estacion');
            $table->string('permiso_acceso')->nullable();
            $table->date('fecha_solicitud');
            $table->date('fecha_programacion')->nullable();
            $table->date('fecha_reprogramacion')->nullable();
            $table->date('fecha_ejecucion')->nullable();
            $table->unsignedBigInteger('id_estado_actividad');
            $table->unsignedBigInteger('id_cotizacion')->nullable();
            $table->unsignedBigInteger('id_orden_compra')->nullable();
            $table->unsignedBigInteger('id_informe')->nullable();
            $table->date('fecha_liquidado')->nullable();
            $table->boolean('liquidado')->default(false);
            $table->unsignedBigInteger('id_mes_consolidado');
            $table->decimal('valor', 20, 2);
            $table->text('observaciones');
            $table->unsignedBigInteger('id_usuareg');
            $table->timestamps();

            $table->foreign('id_subsistema')->references('id_dominio')->on('tbl_dominios')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_tipo_actividad')->references('id_dominio')->on('tbl_dominios')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_estacion')->references('id_punto_interes')->on('tbl_puntos_interes')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_estado_actividad')->references('id_dominio')->on('tbl_dominios')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_cotizacion')->references('id_cotizacion')->on('tbl_cotizaciones')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_orden_compra')->references('id_orden_compra')->on('tbl_ordenes_compra')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_informe')->references('id_informe_actividad')->on('tbl_informes_actividades')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_encargado_cliente')->references('id_tercero')->on('tbl_terceros')
                ->onDelete('cascade')->onUpdate('cascade');
                $table->foreign('id_resposable_contratista')->references('id_tercero')->on('tbl_terceros')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_mes_consolidado')->references('id_dominio')->on('tbl_dominios')
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
        Schema::dropIfExists('tbl_actividades');
    }
}
