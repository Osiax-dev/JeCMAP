@extends('layouts.admin')
@section('title', 'Bénéficiaires')

@section('content')
<h1 style="margin-bottom:24px;">Bénéficiaires</h1>

<div class="admin-card">
  <table>
    <tr><th>Nom</th><th>Contact</th><th>Statut scolaire</th><th>Demandes</th><th>Candidatures</th><th>Séances</th><th></th></tr>
    @forelse($beneficiaires as $b)
    <tr>
      <td>{{ $b->nom }}</td>
      <td>{{ $b->telephone ?? $b->email ?? '-' }}</td>
      <td>{{ $b->statut_scolaire ?? '-' }}</td>
      <td>{{ $b->demandes_orientation_count }}</td>
      <td>{{ $b->candidatures_count }}</td>
      <td>{{ $b->inscriptions_seances_count }}</td>
      <td><a href="{{ route('admin.beneficiaires.show', $b) }}" style="color:var(--green); font-weight:600;">Voir la fiche</a></td>
    </tr>
    @empty
    <tr><td colspan="7" style="color:var(--ink-soft);">Aucun bénéficiaire enregistré.</td></tr>
    @endforelse
  </table>
</div>
<div style="margin-top:16px;">{{ $beneficiaires->links() }}</div>
@endsection