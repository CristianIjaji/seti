<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TblUsuariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_usuarios', function (Blueprint $table) {
            $table->bigIncrements('id_usuario');
            $table->unsignedBigInteger('id_tercero')->nullable();
            $table->string('usuario')->unique();
            $table->string('email')->uniqid();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->smallInteger('intentos')->default(3);
            $table->smallInteger('estado')->default(1);
            $table->unsignedBigInteger('id_usuareg');
            $table->timestamps();

            $table->foreign('id_usuareg')->references('id_usuario')->on('tbl_usuarios')
                ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_usuarios');
    }
}
