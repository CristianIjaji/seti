<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblInventarioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_inventario', function (Blueprint $table) {
            $table->bigIncrements('id_inventario');
            $table->unsignedBigInteger('id_tercero_almacen');
            $table->unsignedBigInteger('id_dominio_clasificacion')->nullable();
            $table->string('descripcion');
            $table->string('marca')->default('');
            $table->unique(['id_dominio_clasificacion', 'descripcion', 'marca'], 'product_name');
            $table->decimal('cantidad')->default(0);
            $table->string('unidad');
            $table->decimal('valor_unitario', 20, 2);
            $table->string('ubicacion')->nullable();
            $table->decimal('cantidad_minima');
            $table->decimal('cantidad_maxima');
            $table->smallInteger('estado')->default(1);
            $table->unsignedBigInteger('id_usuareg');
            $table->timestamps();

            $table->foreign('id_tercero_almacen')->references('id_tercero')->on('tbl_terceros')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_dominio_clasificacion')->references('id_dominio')->on('tbl_dominios')
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
        Schema::dropIfExists('tbl_inventario');
    }
}
