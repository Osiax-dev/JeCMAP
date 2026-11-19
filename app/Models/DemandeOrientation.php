<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DemandeOrientation extends Model
{
    protected $fillable = ['beneficiaire_id', 'besoin', 'statut', 'reponse_admin', 'traitee_le'];
    public function beneficiaire() { return $this->belongsTo(Beneficiaire::class); }
}