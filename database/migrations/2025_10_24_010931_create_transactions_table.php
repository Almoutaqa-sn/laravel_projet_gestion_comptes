<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('compte_id');
            $table->enum('type', ['DEPOT', 'RETRAIT', 'VIREMENT', 'FRAIS']);
            $table->decimal('montant', 15, 2);
            $table->string('devise', 10)->default('XOF');
            $table->text('description')->nullable();
            $table->timestamp('date_transaction')->default(now());
            $table->enum('statut', ['EN_ATTENTE', 'VALIDEE', 'ANNULEE'])->default('EN_ATTENTE');
            $table->uuid('admin_id')->nullable();
            $table->timestamps();

            
            $table->foreign('compte_id')->references('id')->on('comptes')->onDelete('cascade');
            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('set null');
        });
    }

    public function down(): void {
        Schema::dropIfExists('transactions');
    }
};
