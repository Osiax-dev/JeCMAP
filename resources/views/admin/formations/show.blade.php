@extends('layouts.admin')
@section('title', $formation->titre)

@section('content')
<a href="{{ route('admin.formations.index') }}" style="color:var(--ink-soft); font-size:0.9rem;">&larr; Retour</a>
<h1 style="margin:12px 0 8px;">{{ $formation->titre }}</h1>
<p style="color:var(--ink-soft); margin-bottom:24px;">
  {{ $formation->duree_jours ? $formation->duree_jours.' jours' : 'Durée non précisée' }}
  — {{ $formation->places ? $formation->places.' places' : 'Places non précisées' }}
  — <span class="badge badge-{{ $formation->statut === 'active' ? 'active' : 'attente' }}">{{ $formation->statut }}</span>
  — <a href="{{ route('admin.formations.edit', $formation) }}" style="color:var(--green); font-weight:600;">Modifier</a>
</p>

<div class="admin-card">
  <h3 style="margin-bottom:16px;">Candidatures ({{ $formation->candidatures->count() }})</h3>
  <table>
    <tr><th>Nom</th><th>Contact</th><th>Motivation</th><th>Statut</th><th>Date</th></tr>
    @forelse($formation->candidatures as $c)
    <tr>
      <td><a href="{{ route('admin.beneficiaires.show', $c->beneficiaire) }}" style="color:var(--green); font-weight:600;">{{ $c->beneficiaire->nom }}</a></td>
      <td>{{ $c->beneficiaire->telephone ?? $c->beneficiaire->email ?? '—' }}</td>
      <td>{{ \Illuminate\Support\Str::limit($c->motivation, 80) ?: '—' }}</td>
      <td>{{ $c->statut }}</td>
      <td>{{ $c->created_at->format('d/m/Y') }}</td>
    </tr>
    @empty
    <tr><td colspan="5" style="color:var(--ink-soft);">Aucune candidature pour l'instant.</td></tr>
    @endforelse
  </table>
</div>
@endsection
