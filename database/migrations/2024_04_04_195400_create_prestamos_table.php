<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrestamosTable extends Migration
{
    public function up()
{
    Schema::create('prestamos', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('cliente_id');
        $table->foreign('cliente_id')->references('id')->on('clientes');
        $table->decimal('monto', 10, 2);
        $table->string('periodicidad');
        $table->decimal('interes', 5, 2);
        $table->integer('cuotas');
        $table->date('fecha_solicitud');
        $table->date('fecha_primera_cuota');
        $table->decimal('interestotal', 10, 2)->default(0);
        $table->decimal('total_pagar', 10, 2)->default(0);
        $table->timestamps();
    });
}
    
    

    public function down()
    {
        Schema::dropIfExists('prestamos');
    }
}
