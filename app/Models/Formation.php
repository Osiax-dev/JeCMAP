<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Formation extends Model
{
    protected $fillable = ['titre', 'description', 'duree_jours', 'prerequis', 'places', 'statut', 'photo', 'created_by'];
    public function candidatures() { return $this->hasMany(Candidature::class); }
}
