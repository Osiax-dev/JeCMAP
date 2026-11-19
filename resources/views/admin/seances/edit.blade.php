@extends('layouts.admin')
@section('title', 'Modifier '.$seance->titre)

@section('content')
<a href="{{ route('admin.seances.index') }}" style="color:var(--ink-soft); font-size:0.9rem;">&larr; Retour</a>
<h1 style="margin:12px 0 24px;">Modifier : {{ $seance->titre }}</h1>

<div style="max-width:520px;"><x-erreurs-formulaire /></div>
<div class="admin-card" style="max-width:520px;">
  <form method="POST" action="{{ route('admin.seances.update', $seance) }}" enctype="multipart/form-data" style="display:flex; flex-direction:column; gap:14px;">
    @csrf
    @method('PUT')
    <div>
      <label>Titre</label>
      <input type="text" name="titre" value="{{ old('titre', $seance->titre) }}" required style="width:100%; padding:10px; border-radius:8px; border:1px solid var(--line);">
    </div>
    <div>
      <label>Description</label>
      <textarea name="description" rows="4" style="width:100%; padding:10px; border-radius:8px; border:1px solid var(--line);">{{ old('description', $seance->description) }}</textarea>
    </div>
    <div>
      <label>Date et heure</label>
      <input type="datetime-local" name="date_seance" value="{{ old('date_seance', $seance->date_seance->format('Y-m-d\TH:i')) }}" required style="width:100%; padding:10px; border-radius:8px; border:1px solid var(--line);">
    </div>
    <div>
      <label>Format</label>
      <select name="format" required style="width:100%; padding:10px; border-radius:8px; border:1px solid var(--line);">
        <option value="presentiel" {{ $seance->format === 'presentiel' ? 'selected' : '' }}>Présentiel</option>
        <option value="en_ligne" {{ $seance->format === 'en_ligne' ? 'selected' : '' }}>En ligne</option>
      </select>
    </div>
    <div>
      <label>Lieu / lien</label>
      <input type="text" name="lieu" value="{{ old('lieu', $seance->lieu) }}" style="width:100%; padding:10px; border-radius:8px; border:1px solid var(--line);">
    </div>
    <div>
      <label>Places disponibles <small style="color:var(--ink-soft); font-weight:400;">(laisser vide = illimité)</small></label>
      <input type="number" name="places" min="1" value="{{ old('places', $seance->places) }}" style="width:100%; padding:10px; border-radius:8px; border:1px solid var(--line);">
    </div>
    <div>
      <label>Statut</label>
      <select name="statut" style="width:100%; padding:10px; border-radius:8px; border:1px solid var(--line);">
        <option value="a_venir" {{ $seance->statut === 'a_venir' ? 'selected' : '' }}>À venir</option>
        <option value="passee" {{ $seance->statut === 'passee' ? 'selected' : '' }}>Passée</option>
      </select>
    </div>
    <div>
      <label>Photo</label>
      <input type="file" name="photo" accept="image/*" style="width:100%; padding:10px; border:1px solid var(--line); border-radius:8px;">
      @if($seance->photo)
        <img src="{{ asset('storage/'.$seance->photo) }}" style="width:80px; margin-top:8px; border-radius:6px;">
      @endif
    </div>
    <button type="submit" class="btn btn-primary" style="border:none; cursor:pointer;">Mettre à jour</button>
  </form>
</div>
@endsection