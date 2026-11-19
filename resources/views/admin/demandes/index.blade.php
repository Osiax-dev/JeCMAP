@extends('layouts.admin')
@section('title', 'Demandes d\'orientation')

@section('content')
<h1 style="margin-bottom:24px;">Demandes d'orientation</h1>

<div class="admin-card">
  <table>
    <tr><th>Nom</th><th>Statut scolaire</th><th>Contact</th><th>État</th><th>Date</th><th></th></tr>
    @forelse($demandes as $d)
    <tr>
      <td>{{ $d->beneficiaire->nom }}</td>
      <td>{{ $d->beneficiaire->statut_scolaire }}</td>
      <td>{{ $d->beneficiaire->telephone ?? $d->beneficiaire->email }}</td>
      <td><span class="badge badge-{{ $d->statut === 'nouvelle' ? 'nouvelle' : 'active' }}">{{ $d->statut }}</span></td>
      <td>{{ $d->created_at->format('d/m/Y') }}</td>
      <td><a href="{{ route('admin.demandes.show', $d) }}" style="color:var(--green); font-weight:600;">Voir</a></td>
    </tr>
    @empty
    <tr><td colspan="6" style="color:var(--ink-soft);">Aucune demande.</td></tr>
    @endforelse
  </table>
</div>
<div style="margin-top:16px;">{{ $demandes->links() }}</div>
@endsection