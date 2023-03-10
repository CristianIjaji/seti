<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_menus', function (Blueprint $table) {
            $table->bigIncrements('id_menu');
            $table->unsignedBigInteger('id_menu_padre')->nullable();
            $table->string('url')->nullable();
            $table->string('icon')->nullable();
            $table->string('nombre');
            $table->integer('orden');
            $table->smallInteger('estado')->default(1);
            $table->unsignedBigInteger('id_usuareg');
            $table->timestamps();

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
        Schema::dropIfExists('tbl_menus');
    }
}
