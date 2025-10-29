<?php

namespace App\Events;

use App\Models\Compte;
use App\Models\Client;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CompteCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Compte $compte;
    public Client $client;

    /**
     * Create a new event instance.
     */
    public function __construct(Compte $compte, Client $client)
    {
        $this->compte = $compte;
        $this->client = $client;
    }
}
