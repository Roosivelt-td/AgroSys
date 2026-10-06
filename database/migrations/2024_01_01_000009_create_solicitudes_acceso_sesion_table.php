<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('solicitudes_acceso_sesion')) {
            Schema::create('solicitudes_acceso_sesion', function (Blueprint $table) {
                $table->id();
                $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
                $table->string('session_id_solicitante', 255);
                $table->string('ip_solicitante', 45)->nullable();
                $table->text('user_agent_solicitante')->nullable();
                $table->string('dispositivo_solicitante', 255)->nullable();
                $table->string('estado', 20)->default('pendiente'); // 'pendiente', 'autorizado', 'rechazado', 'expirado'
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes_acceso_sesion');
    }
};
