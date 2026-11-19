<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Suivi extends Model
{
    protected $fillable = ['beneficiaire_id', 'user_id', 'note'];
    public function beneficiaire() { return $this->belongsTo(Beneficiaire::class); }
    public function auteur() { return $this->belongsTo(User::class, 'user_id'); }
}
