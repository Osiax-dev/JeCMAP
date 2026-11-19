<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Candidature extends Model
{
    protected $fillable = ['beneficiaire_id', 'formation_id', 'motivation', 'statut'];
    public function beneficiaire() { return $this->belongsTo(Beneficiaire::class); }
    public function formation() { return $this->belongsTo(Formation::class); }
}
