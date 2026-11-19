@extends('layouts.admin')
@section('title', 'Séances')

@section('content')
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
  <h1>Séances d'orientation</h1>
  <a href="{{ route('admin.seances.create') }}" class="btn btn-primary">+ Nouvelle séance</a>
</div>

@if(session('success'))
  <p style="background:var(--green-light); color:var(--green); padding:12px 18px; border-radius:8px; margin-bottom:20px;">{{ session('success') }}</p>
@endif
<x-erreurs-formulaire />

<div class="admin-card">
  <table>
    <tr><th>Photo</th><th>Titre</th><th>Date</th><th>Format</th><th>Inscrits</th><th>Statut</th><th></th></tr>
    @forelse($seances as $s)
    <tr>
      <td>
        @if($s->photo)
          <img src="{{ asset('storage/'.$s->photo) }}" alt="{{ $s->titre }}" style="width:60px; height:60px; object-fit:cover; border-radius:6px;">
        @else
          <span style="color:var(--ink-soft); font-size:0.8rem;">—</span>
        @endif
      </td>
      <td>{{ $s->titre }}</td>
      <td>{{ $s->date_seance->format('d/m/Y H:i') }}</td>
      <td>{{ ucfirst(str_replace('_', ' ', $s->format)) }}</td>
      <td><a href="{{ route('admin.seances.show', $s) }}" style="color:var(--green); font-weight:600;">{{ $s->inscriptions_count }}</a></td>
      <td><span class="badge badge-{{ $s->statut === 'a_venir' ? 'active' : 'attente' }}">{{ $s->statut }}</span></td>
      <td style="white-space:nowrap;">
        <a href="{{ route('admin.seances.edit', $s) }}" style="color:var(--green); font-weight:600; margin-right:14px;">Modifier</a>
        <form method="POST" action="{{ route('admin.seances.destroy', $s) }}" onsubmit="return confirm('Supprimer cette séance ? Cette action est irréversible.');" style="display:inline;">
            @csrf
            @method('DELETE')
            <button type="submit" style="background:none; border:none; color:#C0392B; font-weight:600; cursor:pointer; padding:0; font-size:inherit; font-family:inherit;">Supprimer</button>
        </form>
        </td>
    </tr>
    @empty
    <tr><td colspan="7" style="color:var(--ink-soft);">Aucune séance.</td></tr>
    @endforelse
  </table>
</div>
@endsection