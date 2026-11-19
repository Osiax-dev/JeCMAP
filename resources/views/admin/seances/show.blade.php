@extends('layouts.admin')
@section('title', $seance->titre)

@section('content')
<a href="{{ route('admin.seances.index') }}" style="color:var(--ink-soft); font-size:0.9rem;">&larr; Retour</a>
<h1 style="margin:12px 0 8px;">{{ $seance->titre }}</h1>
<p style="color:var(--ink-soft); margin-bottom:24px;">{{ $seance->date_seance->format('d/m/Y H:i') }} — {{ ucfirst(str_replace('_',' ',$seance->format)) }}</p>

<div class="admin-card">
  <h3 style="margin-bottom:16px;">Inscrits ({{ $seance->inscriptions->count() }})</h3>
  <table>
    <tr><th>Nom</th><th>Contact</th><th>Statut scolaire</th><th>Statut inscription</th></tr>
    @forelse($seance->inscriptions as $i)
    <tr>
      <td>{{ $i->beneficiaire->nom }}</td>
      <td>{{ $i->beneficiaire->telephone }}</td>
      <td>{{ $i->beneficiaire->statut_scolaire }}</td>
      <td>{{ $i->statut }}</td>
    </tr>
    @empty
    <tr><td colspan="4" style="color:var(--ink-soft);">Aucune inscription pour l'instant.</td></tr>
    @endforelse
  </table>
</div>
@endsection