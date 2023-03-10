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
            $table->string('ot')->nullable();
            $table->unsignedBigInteger('id_tipo_actividad');
            $table->unsignedBigInteger("id_dominio_subsistema")->nullable();
            $table->text('descripcion');
            $table->unsignedBigInteger('id_tercero_encargado_cliente');
            $table->unsignedBigInteger('id_tercero_resposable_contratista');
            $table->unsignedBigInteger('id_estacion');
            $table->string('permiso_acceso')->nullable();
            $table->date('fecha_solicitud');
            $table->date('fecha_programacion')->nullable();
            $table->date('fecha_reprogramacion')->nullable();
            $table->date('fecha_ejecucion')->nullable();
            $table->unsignedBigInteger('id_dominio_estado');
            $table->unsignedBigInteger('id_cotizacion')->nullable();
            $table->unsignedBigInteger('id_informe_actividad')->nullable();
            $table->date('fecha_liquidado')->nullable();
            $table->unsignedBigInteger('id_liquidacion')->nullable();
            $table->date('mes_consolidado')->nullable();
            $table->decimal('valor', 20, 2);
            $table->text('observaciones');
            $table->unsignedBigInteger('id_usuareg');
            $table->timestamps();

            $table->foreign('id_tipo_actividad')->references('id_dominio')->on('tbl_dominios')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_dominio_subsistema')->references('id_dominio')->on('tbl_dominios')
                ->onDelete('set null')->onUpdate('cascade');
            $table->foreign('id_tercero_encargado_cliente')->references('id_tercero')->on('tbl_terceros')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_tercero_resposable_contratista')->references('id_tercero')->on('tbl_terceros')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_estacion')->references('id_punto_interes')->on('tbl_puntos_interes')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_dominio_estado')->references('id_dominio')->on('tbl_dominios')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_cotizacion')->references('id_cotizacion')->on('tbl_cotizaciones')
                ->onDelete('set null')->onUpdate('cascade');
            
            $table->foreign('id_liquidacion')->references('id_liquidacion')->on('tbl_liquidaciones')
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
        Schema::dropIfExists('tbl_actividades');
    }
}
