@extends('layouts.admin')
@section('title', 'Nouvelle séance')

@section('content')
<a href="{{ route('admin.seances.index') }}" style="color:var(--ink-soft); font-size:0.9rem;">&larr; Retour</a>
<h1 style="margin:12px 0 24px;">Nouvelle séance</h1>

<div style="max-width:520px;"><x-erreurs-formulaire /></div>
<div class="admin-card" style="max-width:520px;">
  <form method="POST" action="{{ route('admin.seances.store') }}" enctype="multipart/form-data" style="display:flex; flex-direction:column; gap:14px;">
    @csrf
    <div>
      <label>Titre</label>
      <input type="text" name="titre" value="{{ old('titre') }}" required style="width:100%; padding:10px; border-radius:8px; border:1px solid var(--line);">
    </div>
    <div>
      <label>Description</label>
      <textarea name="description" rows="4" style="width:100%; padding:10px; border-radius:8px; border:1px solid var(--line);">{{ old('description') }}</textarea>
    </div>
    <div>
      <label>Date et heure</label>
      <input type="datetime-local" name="date_seance" value="{{ old('date_seance') }}" required style="width:100%; padding:10px; border-radius:8px; border:1px solid var(--line);">
    </div>
    <div>
      <label>Format</label>
      <select name="format" required style="width:100%; padding:10px; border-radius:8px; border:1px solid var(--line);">
        <option value="presentiel">Présentiel</option>
        <option value="en_ligne">En ligne</option>
      </select>
    </div>
    <div>
      <label>Lieu / lien</label>
      <input type="text" name="lieu" value="{{ old('lieu') }}" placeholder="Adresse ou lien visio" style="width:100%; padding:10px; border-radius:8px; border:1px solid var(--line);">
    </div>
    <div>
      <label>Places disponibles <small style="color:var(--ink-soft); font-weight:400;">(laisser vide = illimité)</small></label>
      <input type="number" name="places" min="1" value="{{ old('places') }}" style="width:100%; padding:10px; border-radius:8px; border:1px solid var(--line);">
    </div>
<div>
  <label>Photo</label>
  <input type="file" name="photo" accept="image/*" style="width:100%; padding:10px; border:1px solid var(--line); border-radius:8px;">
</div>
    <button type="submit" class="btn btn-primary" style="border:none; cursor:pointer;">Créer</button>
  </form>
</div>
@endsection