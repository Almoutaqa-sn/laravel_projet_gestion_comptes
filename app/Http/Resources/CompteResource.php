<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CompteResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'numeroCompte' => $this->numero_compte,
            'titulaire' => $this->titulaire,
            'type' => $this->type,
            'solde' => $this->solde,
            'devise' => $this->devise,
            'dateCreation' => $this->created_at,
            'statut' => $this->statut,
            'motifBlocage' => $this->motif_blocage ?? null,
            'metadata' => [
                'derniereModification' => $this->updated_at,
                'version' => $this->version,
            ],
        ];
    }
}
