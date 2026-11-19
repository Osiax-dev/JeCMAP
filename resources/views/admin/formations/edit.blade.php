@extends('layouts.admin')
@section('title', 'Modifier '.$formation->titre)

@section('content')
<a href="{{ route('admin.formations.index') }}" style="color:var(--ink-soft); font-size:0.9rem;">&larr; Retour</a>
<h1 style="margin:12px 0 24px;">Modifier : {{ $formation->titre }}</h1>

<div style="max-width:520px;"><x-erreurs-formulaire /></div>
<div class="admin-card" style="max-width:520px;">
  <form method="POST" action="{{ route('admin.formations.update', $formation) }}" enctype="multipart/form-data" style="display:flex; flex-direction:column; gap:14px;">
    @csrf
    @method('PUT')
    <div>
      <label>Titre</label>
      <input type="text" name="titre" value="{{ old('titre', $formation->titre) }}" required style="width:100%; padding:10px; border-radius:8px; border:1px solid var(--line);">
    </div>
    <div>
      <label>Description</label>
      <textarea name="description" rows="4" style="width:100%; padding:10px; border-radius:8px; border:1px solid var(--line);">{{ old('description', $formation->description) }}</textarea>
    </div>
    <div>
      <label>Durée (jours)</label>
      <input type="number" name="duree_jours" value="{{ old('duree_jours', $formation->duree_jours) }}" style="width:100%; padding:10px; border-radius:8px; border:1px solid var(--line);">
    </div>
    <div>
      <label>Prérequis</label>
      <input type="text" name="prerequis" value="{{ old('prerequis', $formation->prerequis) }}" style="width:100%; padding:10px; border-radius:8px; border:1px solid var(--line);">
    </div>
    <div>
      <label>Places disponibles</label>
      <input type="number" name="places" min="1" value="{{ old('places', $formation->places) }}" style="width:100%; padding:10px; border-radius:8px; border:1px solid var(--line);">
    </div>
    <div>
      <label>Statut</label>
      <select name="statut" style="width:100%; padding:10px; border-radius:8px; border:1px solid var(--line);">
        <option value="active" {{ $formation->statut === 'active' ? 'selected' : '' }}>Active</option>
        <option value="cloturee" {{ $formation->statut === 'cloturee' ? 'selected' : '' }}>Clôturée</option>
      </select>
    </div>
<div>
      <label>Photo</label>
      <input type="file" name="photo" accept="image/*" style="width:100%; padding:10px; border:1px solid var(--line); border-radius:8px;">
      @if($formation->photo)
        <img src="{{ asset('storage/'.$formation->photo) }}" style="width:80px; margin-top:8px; border-radius:6px;">
      @endif
    </div>
    <button type="submit" class="btn btn-primary" style="border:none; cursor:pointer;">Mettre à jour</button>
  </form>
</div>
@endsection