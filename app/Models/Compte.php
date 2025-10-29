<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Compte extends Model
{
    use HasFactory;
    protected $fillable = [
        'titulaire',
        'type',
        'solde',
        'devise',
        'statut',
        'client_id',
        'admin_id',
    ];

    protected $keyType = 'string';    // pour UUID
    public $incrementing = false;     // pas d’auto-increment

    /**
     * Générer un UUID pour la clé primaire et un numero_compte unique
     */
    protected static function booted()
    {
        static::creating(function ($compte) {
            // UUID
            $compte->id = (string) Str::uuid();

            // Numero de compte unique
            do {
                $numero = 'CPT-' . Str::upper(Str::random(8));
            } while (self::where('numero_compte', $numero)->exists());

            $compte->numero_compte = $numero;
        });
    }
    public function scopeNumero($query, $numero)
{
    return $query->where('numero_compte', $numero);
}

public function scopeClient($query, $telephone)
{
    return $query->whereHas('client', function ($q) use ($telephone) {
        $q->where('telephone', $telephone);
    });
}


    /**
     * Relation avec le client
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Relation avec l'admin
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * Relation avec les transactions
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Crédite le compte
     */
    public function crediter($montant)
    {
        $this->solde += $montant;
        $this->save();
    }

    /**
     * Débite le compte
     */
    public function debiter($montant)
    {
        if ($montant > $this->solde) {
            throw new \Exception("Solde insuffisant");
        }
        $this->solde -= $montant;
        $this->save();
    }

    /**
     * Modifier le statut
     */
    public function modifierStatut($statut)
    {
        $this->statut = $statut;
        $this->save();
    }

    /**
     * Récupérer toutes les transactions
     */
    public function getTransactions()
    {
        return $this->transactions()->get();
    }

    /**
     * Attribut personnalisé pour le solde calculé
     */
    public function getSoldeAttribute()
    {
        // Calculer le solde basé sur les transactions
        $debits = $this->transactions()
            ->where('statut', 'VALIDEE')
            ->whereIn('type', ['RETRAIT', 'FRAIS'])
            ->sum('montant');

        $credits = $this->transactions()
            ->where('statut', 'VALIDEE')
            ->whereIn('type', ['DEPOT', 'VIREMENT'])
            ->sum('montant');

        return $credits - $debits;
    }
}
