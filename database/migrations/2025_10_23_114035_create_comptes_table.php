<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('comptes', function (Blueprint $table) {
            $table->uuid('id')->primary(); // UUID comme identifiant
            $table->string('numero_compte')->unique();
            $table->string('titulaire');
            $table->enum('type', ['EPARGNE', 'CHEQUE']);
            $table->decimal('solde', 15, 2)->default(0);
            $table->string('devise', 3)->default('EUR');
            $table->enum('statut', ['ACTIF', 'BLOQUE', 'FERME'])->default('ACTIF');
            $table->foreignId('utilisateur_id')->constrained()->onDelete('cascade');
            $table->timestamps(); // created_at et updated_at
            $table->integer('version')->default(1);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comptes');
    }
};
