<?php

namespace App\Listeners;

use App\Events\CompteCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendClientNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(CompteCreated $event): void
    {
        $compte = $event->compte;
        $client = $event->client;

        Log::info('Envoi des notifications pour le nouveau compte', [
            'compte_id' => $compte->id,
            'client_id' => $client->id,
            'email' => $client->email
        ]);

        try {
            // Envoyer l'email d'authentification
            $this->sendEmailNotification($client, $compte);

            // Envoyer le SMS avec le code
            $this->sendSmsNotification($client);

            Log::info('Notifications envoyées avec succès', [
                'compte_id' => $compte->id,
                'client_email' => $client->email
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi des notifications', [
                'compte_id' => $compte->id,
                'error' => $e->getMessage()
            ]);

            // Relancer l'exception pour que le job soit retenté
            throw $e;
        }
    }

    /**
     * Envoyer l'email d'authentification
     */
    private function sendEmailNotification($client, $compte): void
    {
        // Simulation d'envoi d'email (dans un vrai projet, utiliser un service comme SendGrid, Mailgun, etc.)
        Log::info('Email d\'authentification envoyé', [
            'to' => $client->email,
            'subject' => 'Bienvenue - Vos identifiants de connexion',
            'compte' => $compte->numero_compte,
            'password' => $client->temporary_password ?? 'TEMP_PASSWORD'
        ]);

        // Ici, vous intégreriez un vrai service d'email :
        /*
        Mail::to($client->email)->send(new WelcomeEmail($client, $compte));
        */
    }

    /**
     * Envoyer le SMS avec le code de vérification
     */
    private function sendSmsNotification($client): void
    {
        // Simulation d'envoi SMS (dans un vrai projet, utiliser un service comme Twilio, Africa's Talking, etc.)
        Log::info('SMS envoyé', [
            'to' => $client->telephone,
            'message' => 'Votre code de vérification: ' . ($client->verification_code ?? 'CODE123'),
            'instructions' => 'Ce code est requis pour votre première connexion.'
        ]);

        // Ici, vous intégreriez un vrai service SMS :
        /*
        $smsService = app(SmsService::class);
        $smsService->send($client->telephone, "Votre code: {$client->verification_code}");
        */
    }
}
