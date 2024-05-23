<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCuotasTable extends Migration
{
    public function up()
    {
        Schema::create('cuotas', function (Blueprint $table) {
            $table->bigIncrements('id'); // Esto es para la clave primaria, probablemente ya esté presente
            $table->string('id_cuota'); // Aquí define la columna 'id_cuota'
            $table->date('fechacuota');
            $table->unsignedBigInteger('prestamo_id');
            $table->boolean('pagada');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cuotas');
    }
}
