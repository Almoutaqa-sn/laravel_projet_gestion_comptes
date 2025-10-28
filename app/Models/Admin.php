<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Admin extends Model
{
    use HasFactory;

    /** Utilisation d’un UUID au lieu d’un id auto-incrémenté */
    public $incrementing = false;
    protected $keyType = 'string';

    /** Champs autorisés au remplissage */
    protected $fillable = [
        'nom',
        'email',
        'telephone',
        'role',
    ];

    /** Génération automatique d’un UUID */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    /** Relations */
    public function comptes()
    {
        return $this->hasMany(Compte::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
