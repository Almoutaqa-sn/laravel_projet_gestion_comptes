<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('comptes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('numero_compte')->unique();
            $table->string('titulaire');
            $table->enum('type', ['EPARGNE', 'CHEQUE']);
            $table->decimal('solde', 15, 2)->default(0);
            $table->string('devise', 10)->default('XOF');
            $table->date('date_creation')->default(now());
            $table->enum('statut', ['ACTIF', 'BLOQUE', 'FERME'])->default('ACTIF');
            $table->timestamp('derniere_modification')->nullable();
            $table->integer('version')->default(1);
            $table->uuid('client_id');
            $table->uuid('admin_id')->nullable();
            $table->timestamps();

          

            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('set null');
        });
    }

    public function down(): void {
        Schema::dropIfExists('comptes');
    }
};
