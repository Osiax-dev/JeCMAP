<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeanceOrientation extends Model
{
    protected $fillable = ['titre', 'description', 'date_seance', 'lieu', 'format', 'places', 'statut', 'photo', 'created_by'];

    protected function casts(): array
    {
        return [
            'date_seance' => 'datetime',
        ];
    }

    public function inscriptions() { return $this->hasMany(InscriptionSeance::class, 'seance_id'); }
    public function createur() { return $this->belongsTo(User::class, 'created_by'); }

    /** Séance encore ouverte aux inscriptions : statut « à venir » et date pas encore passée. */
    public function inscriptionsOuvertes(): bool
    {
        return $this->statut === 'a_venir' && $this->date_seance->isFuture();
    }

    /** Nombre d'inscrits (utilise inscriptions_count si chargé par withCount). */
    public function nombreInscrits(): int
    {
        return $this->inscriptions_count ?? $this->inscriptions()->count();
    }

    /** null = places illimitées. */
    public function placesRestantes(): ?int
    {
        return $this->places === null ? null : max(0, $this->places - $this->nombreInscrits());
    }

    public function estComplete(): bool
    {
        return $this->placesRestantes() === 0;
    }

    /** Séances visibles sur le site public : à venir et date non dépassée. */
    public function scopeOuvertes($query)
    {
        return $query->where('statut', 'a_venir')->where('date_seance', '>', now());
    }
}