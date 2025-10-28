<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Table comptes
        Schema::table('comptes', function (Blueprint $table) {
            $table->index('client_id');
            $table->index('admin_id');
        });

        // Table transactions
        Schema::table('transactions', function (Blueprint $table) {
            $table->index('compte_id');
            $table->index('type');
            $table->index('statut');
        });
    }

    public function down(): void
    {
        // Supprimer les index si rollback
        Schema::table('comptes', function (Blueprint $table) {
            $table->dropIndex(['client_id']);
            $table->dropIndex(['admin_id']);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['compte_id']);
            $table->dropIndex(['type']);
            $table->dropIndex(['statut']);
        });
    }
};
