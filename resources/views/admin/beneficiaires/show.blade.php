@extends('layouts.admin')
@section('title', $beneficiaire->nom)

@section('content')
<a href="{{ route('admin.beneficiaires.index') }}" style="color:var(--ink-soft); font-size:0.9rem;">&larr; Retour</a>
<h1 style="margin:12px 0 4px;">{{ $beneficiaire->nom }}</h1>
<p style="color:var(--ink-soft); margin-bottom:24px;">
  {{ $beneficiaire->telephone ?? '' }} {{ $beneficiaire->email ? '· '.$beneficiaire->email : '' }}
  — {{ $beneficiaire->statut_scolaire ?? 'Statut non renseigné' }} {{ $beneficiaire->niveau ? '('.$beneficiaire->niveau.')' : '' }}
</p>

@if(session('success'))
  <p style="background:var(--green-light); color:var(--green); padding:12px 18px; border-radius:8px; margin-bottom:20px;">{{ session('success') }}</p>
@endif

<div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:20px;">

  <div class="admin-card">
    <h3 style="margin-bottom:14px;">Demandes d'orientation</h3>
    @forelse($beneficiaire->demandesOrientation as $d)
      <div style="padding:10px 0; border-bottom:1px solid var(--line);">
        <span class="badge badge-{{ $d->statut === 'nouvelle' ? 'nouvelle' : 'active' }}">{{ $d->statut }}</span>
        <p style="margin-top:6px; font-size:0.9rem;">{{ Str::limit($d->besoin, 100) }}</p>
        <span style="font-size:0.78rem; color:var(--ink-soft);">{{ $d->created_at->format('d/m/Y') }}</span>
      </div>
    @empty
      <p style="color:var(--ink-soft); font-size:0.9rem;">Aucune demande.</p>
    @endforelse
  </div>

  <div class="admin-card">
    <h3 style="margin-bottom:14px;">Candidatures aux formations</h3>
    @forelse($beneficiaire->candidatures as $c)
      <div style="padding:10px 0; border-bottom:1px solid var(--line);">
        <strong style="font-size:0.9rem;">{{ $c->formation->titre }}</strong>
        <span class="badge badge-attente" style="margin-left:8px;">{{ $c->statut }}</span>
        <div style="font-size:0.78rem; color:var(--ink-soft);">{{ $c->created_at->format('d/m/Y') }}</div>
      </div>
    @empty
      <p style="color:var(--ink-soft); font-size:0.9rem;">Aucune candidature.</p>
    @endforelse
  </div>

</div>

<div class="admin-card" style="margin-bottom:20px;">
  <h3 style="margin-bottom:14px;">Séances suivies</h3>
  @forelse($beneficiaire->inscriptionsSeances as $i)
    <div style="padding:10px 0; border-bottom:1px solid var(--line); display:flex; justify-content:space-between;">
      <span style="font-size:0.9rem;">{{ $i->seance->titre }} — {{ $i->seance->date_seance->format('d/m/Y') }}</span>
      <span class="badge badge-attente">{{ $i->statut }}</span>
    </div>
  @empty
    <p style="color:var(--ink-soft); font-size:0.9rem;">Aucune séance suivie.</p>
  @endforelse
</div>

<div class="admin-card">
  <h3 style="margin-bottom:14px;">Notes de suivi</h3>

  @forelse($beneficiaire->suivis->sortByDesc('created_at') as $s)
    <div style="padding:12px 0; border-bottom:1px solid var(--line);">
      <p style="font-size:0.9rem;">{{ $s->note }}</p>
      <span style="font-size:0.78rem; color:var(--ink-soft);">{{ $s->auteur->name }} — {{ $s->created_at->format('d/m/Y H:i') }}</span>
    </div>
  @empty
    <p style="color:var(--ink-soft); font-size:0.9rem; margin-bottom:16px;">Aucune note pour l'instant.</p>
  @endforelse

  <form method="POST" action="{{ route('admin.beneficiaires.suivis.store', $beneficiaire) }}" style="margin-top:16px;">
    @csrf
    <textarea name="note" rows="3" required placeholder="Ajouter une note de suivi..."
              style="width:100%; padding:12px; border-radius:8px; border:1px solid var(--line); margin-bottom:10px;"></textarea>
    <button type="submit" class="btn btn-primary" style="border:none; cursor:pointer;">Ajouter la note</button>
  </form>
</div>
@endsection