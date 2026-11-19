<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InscriptionSeance extends Model
{
    protected $fillable = ['seance_id', 'beneficiaire_id', 'statut'];
    public function seance() { return $this->belongsTo(SeanceOrientation::class, 'seance_id'); }
    public function beneficiaire() { return $this->belongsTo(Beneficiaire::class); }
}
