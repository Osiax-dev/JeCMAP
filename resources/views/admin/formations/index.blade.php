@extends('layouts.admin')
@section('title', 'Formations')

@section('content')
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
  <h1>Formations</h1>
  <a href="{{ route('admin.formations.create') }}" class="btn btn-primary">+ Nouvelle formation</a>
</div>

@if(session('success'))
  <p style="background:var(--green-light); color:var(--green); padding:12px 18px; border-radius:8px; margin-bottom:20px;">{{ session('success') }}</p>
@endif
<x-erreurs-formulaire />

<div class="admin-card">
  <table>
    <tr><th>Photo</th><th>Titre</th><th>Durée</th><th>Candidatures</th><th>Statut</th><th></th></tr>
    @forelse($formations as $f)
    <tr>
      <td>
        @if($f->photo)
          <img src="{{ asset('storage/'.$f->photo) }}" alt="{{ $f->titre }}" style="width:60px; height:60px; object-fit:cover; border-radius:6px;">
        @else
          <span style="color:var(--ink-soft); font-size:0.8rem;">—</span>
        @endif
      </td>
      <td>{{ $f->titre }}</td>
      <td>{{ $f->duree_jours ?? '-' }} j</td>
      <td><a href="{{ route('admin.formations.show', $f) }}" style="color:var(--green); font-weight:600;">{{ $f->candidatures_count }}</a></td>
      <td><span class="badge badge-{{ $f->statut === 'active' ? 'active' : 'attente' }}">{{ $f->statut }}</span></td>
<td style="white-space:nowrap;">
  <a href="{{ route('admin.formations.edit', $f) }}" style="color:var(--green); font-weight:600; margin-right:14px;">Modifier</a>
  <form method="POST" action="{{ route('admin.formations.destroy', $f) }}" onsubmit="return confirm('Supprimer cette formation ? Cette action est irréversible.');" style="display:inline;">
    @csrf
    @method('DELETE')
    <button type="submit" style="background:none; border:none; color:#C0392B; font-weight:600; cursor:pointer; padding:0; font-size:inherit; font-family:inherit;">Supprimer</button>
  </form>
</td>
    </tr>
    @empty
    <tr><td colspan="6" style="color:var(--ink-soft);">Aucune formation.</td></tr>
    @endforelse
  </table>
</div>
@endsection