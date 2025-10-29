<?php

namespace App\Observers;

use App\Models\Compte;
use App\Events\CompteCreated;
use Illuminate\Support\Facades\Log;

class CompteObserver
{
    /**
     * Handle the Compte "created" event.
     */
    public function created(Compte $compte): void
    {
        Log::info('Nouveau compte créé via observer', [
            'compte_id' => $compte->id,
            'numero_compte' => $compte->numero_compte,
            'client_id' => $compte->client_id
        ]);

        // Récupérer le client associé
        $client = $compte->client;

        if ($client) {
            // Déclencher l'événement de création du compte
            CompteCreated::dispatch($compte, $client);

            Log::info('Événement CompteCreated déclenché', [
                'compte_id' => $compte->id,
                'client_id' => $client->id
            ]);
        } else {
            Log::warning('Client non trouvé pour le compte créé', [
                'compte_id' => $compte->id,
                'client_id' => $compte->client_id
            ]);
        }
    }

    /**
     * Handle the Compte "updated" event.
     */
    public function updated(Compte $compte): void
    {
        Log::info('Compte mis à jour', [
            'compte_id' => $compte->id,
            'numero_compte' => $compte->numero_compte,
            'champs_modifies' => $compte->getChanges()
        ]);
    }

    /**
     * Handle the Compte "deleted" event.
     */
    public function deleted(Compte $compte): void
    {
        Log::info('Compte supprimé', [
            'compte_id' => $compte->id,
            'numero_compte' => $compte->numero_compte
        ]);
    }

    /**
     * Handle the Compte "restored" event.
     */
    public function restored(Compte $compte): void
    {
        Log::info('Compte restauré', [
            'compte_id' => $compte->id,
            'numero_compte' => $compte->numero_compte
        ]);
    }

    /**
     * Handle the Compte "force deleted" event.
     */
    public function forceDeleted(Compte $compte): void
    {
        Log::warning('Compte supprimé définitivement', [
            'compte_id' => $compte->id,
            'numero_compte' => $compte->numero_compte
        ]);
    }
}
