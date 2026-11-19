@extends('layouts.admin')
@section('title', 'Tableau de bord')

@section('content')
<h1 style="margin-bottom:24px;">Tableau de bord</h1>

<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px,1fr)); gap:20px; margin-bottom:32px;">
  <div class="admin-card kpi-card">
    <div class="kpi-icon kpi-gold">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
    </div>
    <div>
      <div style="color:var(--ink-soft); font-size:0.85rem;">Demandes en attente</div>
      <div style="font-size:1.9rem; font-weight:700; color:var(--navy);">{{ $demandesEnAttente }}</div>
    </div>
  </div>
  <div class="admin-card kpi-card">
    <div class="kpi-icon kpi-green">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 10 12 5 2 10l10 5 10-5Z"/><path d="M6 12v5c0 1.7 2.7 3 6 3s6-1.3 6-3v-5"/></svg>
    </div>
    <div>
      <div style="color:var(--ink-soft); font-size:0.85rem;">Formations actives</div>
      <div style="font-size:1.9rem; font-weight:700; color:var(--navy);">{{ $formationsActives }}</div>
    </div>
  </div>
  <div class="admin-card kpi-card">
    <div class="kpi-icon kpi-navy">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="17" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
    </div>
    <div>
      <div style="color:var(--ink-soft); font-size:0.85rem;">Séances à venir</div>
      <div style="font-size:1.9rem; font-weight:700; color:var(--navy);">{{ $seancesAVenir->count() }}</div>
    </div>
  </div>
</div>

<div class="admin-card" style="margin-bottom:24px;">
  <h3 style="margin-bottom:16px;">Candidatures récentes</h3>
  <table>
    <tr><th>Bénéficiaire</th><th>Formation</th><th>Statut</th><th>Date</th></tr>
    @forelse($candidaturesRecentes as $c)
    <tr>
      <td>{{ $c->beneficiaire->nom }}</td>
      <td>{{ $c->formation->titre }}</td>
      <td><span class="badge badge-attente">{{ $c->statut }}</span></td>
      <td>{{ $c->created_at->format('d/m/Y') }}</td>
    </tr>
    @empty
    <tr><td colspan="4" style="color:var(--ink-soft);">Aucune candidature récente.</td></tr>
    @endforelse
  </table>
</div>

<div class="admin-card">
  <h3 style="margin-bottom:16px;">Prochaines séances</h3>
  <table>
    <tr><th>Titre</th><th>Date</th><th>Inscrits</th></tr>
    @forelse($seancesAVenir as $s)
    <tr>
      <td>{{ $s->titre }}</td>
      <td>{{ $s->date_seance->format('d/m/Y H:i') }}</td>
      <td>{{ $s->inscriptions->count() }}</td>
    </tr>
    @empty
    <tr><td colspan="3" style="color:var(--ink-soft);">Aucune séance programmée.</td></tr>
    @endforelse
  </table>
</div>
@endsection