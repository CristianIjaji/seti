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
            $table->string('codigo_actividad')->uniqid();
            $table->unsignedBigInteger('id_encargado');
            $table->unsignedBigInteger('id_cliente');
            $table->unsignedBigInteger('id_tipo_actividad');
            $table->unsignedBigInteger('id_mes');
            $table->unsignedBigInteger('id_estacion');
            $table->text('descripcion');
            $table->unsignedBigInteger('id_permiso')->nullable();
            $table->date('fecha_solicitud');
            $table->date('fecha_ejecucion')->nullable();
            $table->date('fecha_finalizacion')->nullable();
            $table->unsignedBigInteger('id_estado_actividad');
            $table->unsignedBigInteger('id_cotizacion')->nullable();
            $table->unsignedBigInteger('id_orden_compra')->nullable();
            $table->unsignedBigInteger('id_informe')->nullable();
            $table->boolean('liquidado')->default(false);
            $table->unsignedBigInteger('id_responsable_cliente');
            $table->unsignedBigInteger('id_mes_consolidado');
            $table->decimal('valor', 20, 2);
            $table->text('observaciones');
            $table->boolean('inf_financiera')->default(false);
            $table->unsignedBigInteger('id_factura')->nullable();
            $table->unsignedBigInteger('id_usuareg');
            $table->timestamps();

            $table->foreign('id_encargado')->references('id_tercero')->on('tbl_terceros');
            $table->foreign('id_cliente')->references('id_tercero')->on('tbl_terceros');
            $table->foreign('id_tipo_actividad')->references('id_dominio')->on('tbl_dominios');
            $table->foreign('id_mes')->references('id_dominio')->on('tbl_dominios');
            $table->foreign('id_estacion')->references('id_punto_interes')->on('tbl_puntos_interes');
            $table->foreign('id_permiso')->references('id_permiso')->on('tbl_permisos');
            $table->foreign('id_estado_actividad')->references('id_dominio')->on('tbl_dominios');
            $table->foreign('id_cotizacion')->references('id_cotizacion')->on('tbl_cotizaciones');
            $table->foreign('id_orden_compra')->references('id_orden_compra')->on('tbl_ordenes_compra');
            $table->foreign('id_informe')->references('id_informe_actividad')->on('tbl_informes_actividades');
            $table->foreign('id_responsable_cliente')->references('id_dominio')->on('tbl_dominios');
            $table->foreign('id_mes_consolidado')->references('id_dominio')->on('tbl_dominios');
            $table->foreign('id_factura')->references('id_factura')->on('tbl_facturas');
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
        Schema::dropIfExists('tbl_actividades');
    }
}
