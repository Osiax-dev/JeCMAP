@extends('layouts.admin')
@section('title', 'Demande de '.$demande->beneficiaire->nom)

@section('content')
<a href="{{ route('admin.demandes.index') }}" style="color:var(--ink-soft); font-size:0.9rem;">&larr; Retour</a>
<h1 style="margin:12px 0 24px;">{{ $demande->beneficiaire->nom }}</h1>

@if(session('success'))
  <p style="background:var(--green-light); color:var(--green); padding:12px 18px; border-radius:8px; margin-bottom:20px;">{{ session('success') }}</p>
@endif

<div class="admin-card" style="margin-bottom:20px;">
  <p><strong>Contact :</strong> {{ $demande->beneficiaire->telephone ?? 'N/A' }} {{ $demande->beneficiaire->email ? '· '.$demande->beneficiaire->email : '' }}</p>
  <p><strong>Statut scolaire :</strong> {{ $demande->beneficiaire->statut_scolaire }} — {{ $demande->beneficiaire->niveau }}</p>
  <p style="margin-top:12px;"><strong>Besoin exprimé :</strong></p>
  <p>{{ $demande->besoin }}</p>
</div>

@if($demande->reponse_admin)
<div class="admin-card" style="margin-bottom:20px; background:var(--green-light);">
  <strong>Réponse déjà envoyée :</strong>
  <p>{{ $demande->reponse_admin }}</p>
</div>
@endif

<div class="admin-card">
  <h3 style="margin-bottom:12px;">Répondre / organiser une rencontre</h3>
  <form method="POST" action="{{ route('admin.demandes.repondre', $demande) }}">
    @csrf
    <textarea name="reponse_admin" rows="4" required placeholder="Ex : proposition de rencontre le..."
              style="width:100%; padding:12px; border-radius:8px; border:1px solid var(--line); margin-bottom:12px;">{{ old('reponse_admin') }}</textarea>
    <button type="submit" class="btn btn-primary" style="border:none; cursor:pointer;">Enregistrer la réponse</button>
  </form>
</div>
@endsection