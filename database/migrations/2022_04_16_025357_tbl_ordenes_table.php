<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TblOrdenesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_ordenes', function (Blueprint $table) {
            $table->bigIncrements('id_orden');
            $table->unsignedBigInteger('id_tercero_cliente');
            $table->unsignedBigInteger('id_dominio_tipo_orden');
            $table->dateTime('fecha_inicio')->nullable();
            $table->dateTime('fecha_fin')->nullable();
            $table->unsignedBigInteger('id_habitacion')->nullable();
            $table->string('descripcion');
            $table->string('datos_cliente');
            $table->string('valor');
            $table->string('metodo_pago')->nullable();
            $table->boolean('pedir_domiciliario')->default(false);
            $table->unsignedBigInteger('id_dominio_tiempo_llegada')->nullable();
            $table->smallInteger('estado')->default(1);
            $table->unsignedBigInteger('id_usuareg');
            $table->unsignedBigInteger('id_usuario_final')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_tercero_cliente')->references('id_tercero')->on('tbl_terceros');
            $table->foreign('id_dominio_tipo_orden')->references('id_dominio')->on('tbl_dominios');
            $table->foreign('id_usuareg')->references('id_usuario')->on('tbl_usuarios');
            $table->foreign('id_usuario_final')->references('id_usuario')->on('tbl_usuarios');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_ordenes');
    }
}
