<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TblHabitacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_habitaciones', function(Blueprint $table) {
            $table->bigIncrements('id_habitacion');
            $table->unsignedBigInteger('id_tercero_cliente');
            $table->string('nombre');
            $table->integer('cantidad');
            $table->smallInteger('estado')->default(1);
            $table->unsignedBigInteger('id_usuareg');
            $table->timestamps();

            $table->foreign('id_tercero_cliente')->references('id_tercero')->on('tbl_terceros');
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
        Schema::dropIfExists('tbl_habitaciones');
    }
}
