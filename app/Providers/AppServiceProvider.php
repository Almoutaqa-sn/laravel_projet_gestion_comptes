<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Log des requêtes DB en développement
        if (config('app.debug')) {
            DB::listen(function ($query) {
                Log::info('DB Query', [
                    'sql' => $query->sql,
                    'bindings' => $query->bindings,
                    'time' => $query->time . 'ms'
                ]);
            });
        }

        // Vérifier la connexion DB au démarrage
        try {
            DB::connection()->getPdo();
            Log::info('Database connection established successfully');
        } catch (\Exception $e) {
            Log::error('Database connection failed: ' . $e->getMessage());
            throw $e;
        }

        // Enregistrer les observers
        \App\Models\Compte::observe(\App\Observers\CompteObserver::class);
    }
}
