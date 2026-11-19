@extends('layouts.admin')
@section('title', 'Mon profil')

@section('content')
<h1 style="margin:0 0 24px;">Mon profil</h1>

<div class="admin-card" style="max-width:520px; margin-bottom:24px;">
  <h3 style="margin-bottom:16px;">Informations du compte</h3>

  @if (session('status') === 'profile-updated')
    <p role="status" style="background:var(--green-light); color:var(--green); padding:12px 18px; border-radius:8px; margin-bottom:16px;">Profil mis à jour.</p>
  @endif
  <x-erreurs-formulaire />

  <form method="POST" action="{{ route('profile.update') }}" style="display:flex; flex-direction:column; gap:14px;">
    @csrf
    @method('PATCH')
    <div>
      <label for="name">Nom</label>
      <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required autocomplete="name" style="width:100%; padding:10px; border-radius:8px; border:1px solid var(--line);">
    </div>
    <div>
      <label for="email">E-mail de connexion</label>
      <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="username" style="width:100%; padding:10px; border-radius:8px; border:1px solid var(--line);">
    </div>
    <button type="submit" class="btn btn-primary" style="border:none; cursor:pointer;">Enregistrer</button>
  </form>
</div>

<div class="admin-card" style="max-width:520px;">
  <h3 style="margin-bottom:16px;">Changer le mot de passe</h3>

  @if (session('status') === 'password-updated')
    <p role="status" style="background:var(--green-light); color:var(--green); padding:12px 18px; border-radius:8px; margin-bottom:16px;">Mot de passe modifié.</p>
  @endif
  @if ($errors->updatePassword->any())
    <div role="alert" style="background:#FDECEC; color:#9B1C1C; padding:14px 20px; border-radius:10px; margin-bottom:16px;">
      <ul style="margin:0; padding-left:18px;">
        @foreach ($errors->updatePassword->all() as $erreur)
          <li>{{ $erreur }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('password.update') }}" style="display:flex; flex-direction:column; gap:14px;">
    @csrf
    @method('PUT')
    <div>
      <label for="current_password">Mot de passe actuel</label>
      <input id="current_password" type="password" name="current_password" required autocomplete="current-password" style="width:100%; padding:10px; border-radius:8px; border:1px solid var(--line);">
    </div>
    <div>
      <label for="password">Nouveau mot de passe</label>
      <input id="password" type="password" name="password" required autocomplete="new-password" style="width:100%; padding:10px; border-radius:8px; border:1px solid var(--line);">
    </div>
    <div>
      <label for="password_confirmation">Confirmer le nouveau mot de passe</label>
      <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" style="width:100%; padding:10px; border-radius:8px; border:1px solid var(--line);">
    </div>
    <button type="submit" class="btn btn-primary" style="border:none; cursor:pointer;">Changer le mot de passe</button>
  </form>
</div>
@endsection
