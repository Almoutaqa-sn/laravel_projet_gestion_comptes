<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Transaction extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'compte_id',
        'type',
        'montant',
        'devise',
        'description',
        'date_transaction',
        'statut',
        'admin_id',
    ];

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
    public function compte()
    {
        return $this->belongsTo(Compte::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
