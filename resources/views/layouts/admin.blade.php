<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'Admin') — JeCMAP</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
<link rel="stylesheet" href="{{ asset('css/jecmap.css') }}">
<style>
  body{ font-family:'Inter', sans-serif; }

  .admin-shell{ display:flex; min-height:100vh; }

  .admin-sidebar{
    width:240px;
    background: linear-gradient(180deg, var(--navy) 0%, #0A1E3D 100%);
    color:#fff;
    padding:0 0 24px;
    flex-shrink:0;
    position:fixed;
    top:0; left:0;
    height:100vh;
    overflow-y:auto;
    display:flex;
    flex-direction:column;
  }
  .admin-brand{
    display:flex; align-items:center; gap:12px;
    padding:24px 24px 20px;
    border-bottom:1px solid rgba(255,255,255,0.08);
    margin-bottom:12px;
  }
  .admin-brand img{ width:38px; height:38px; object-fit:contain; }
  .admin-brand span{
    font-family:'Space Grotesk', sans-serif;
    font-weight:700;
    font-size:1.05rem;
  }
  .admin-brand small{
    display:block;
    font-weight:400;
    font-size:0.7rem;
    color:var(--gold);
    letter-spacing:0.04em;
  }

  .admin-nav{ flex:1; }
  .admin-nav a{
    display:flex; align-items:center; gap:12px;
    padding:12px 24px;
    color:#B8C4D6;
    font-size:0.92rem;
    font-weight:500;
    transition: background .2s ease, color .2s ease, border-color .2s ease;
    border-left:3px solid transparent;
  }
  .admin-nav a svg{ width:18px; height:18px; flex-shrink:0; }
  .admin-nav a:hover{ color:#fff; background:rgba(255,255,255,0.05); }
  .admin-nav a.active{
    color:#fff;
    background:rgba(255,255,255,0.07);
    border-left-color:var(--gold);
  }

  .admin-logout{ padding:16px 24px 0; border-top:1px solid rgba(255,255,255,0.08); margin-top:12px; padding-top:16px; }
  .admin-logout button{
    display:flex; align-items:center; gap:10px;
    background:none; border:none; color:#B8C4D6;
    cursor:pointer; font-size:0.9rem; font-family:inherit;
    transition: color .2s ease;
  }
  .admin-logout button:hover{ color:#fff; }
  .admin-logout svg{ width:18px; height:18px; }

  .admin-main{ flex:1; padding:36px 40px; background:var(--bg); margin-left:240px; }
  .admin-topbar{
    display:flex; justify-content:space-between; align-items:center;
    margin-bottom:28px;
  }
  .admin-topbar .who{
    font-size:0.88rem; color:var(--ink-soft);
  }
  .admin-topbar .who strong{ color:var(--navy); }

  .admin-card{
    background:#fff; border-radius:14px; padding:26px;
    border:1px solid var(--line);
    box-shadow:0 4px 18px -10px rgba(11,37,69,0.08);
  }

  .kpi-card{
    display:flex; align-items:center; gap:16px;
  }
  .kpi-icon{
    width:48px; height:48px; border-radius:12px;
    display:flex; align-items:center; justify-content:center;
    flex-shrink:0;
  }
  .kpi-icon svg{ width:22px; height:22px; }
  .kpi-icon.kpi-gold{ background:var(--gold-soft); color:#7A5200; }
  .kpi-icon.kpi-green{ background:var(--green-light); color:var(--green); }
  .kpi-icon.kpi-navy{ background:#EAF0F7; color:var(--navy); }

  table{ width:100%; border-collapse:collapse; }
  th, td{ text-align:left; padding:12px 14px; font-size:0.9rem; }
  th{ color:var(--ink-soft); font-weight:600; font-size:0.78rem; text-transform:uppercase; letter-spacing:0.04em; border-bottom:2px solid var(--line); }
  tbody tr{ border-bottom:1px solid var(--line); transition:background .15s ease; }
  tbody tr:hover{ background:var(--bg); }

  .badge{ padding:4px 12px; border-radius:999px; font-size:0.76rem; font-weight:700; letter-spacing:0.02em; }
  .badge-nouvelle{ background:var(--gold-soft); color:#7A5200; }
  .badge-active{ background:var(--green-light); color:var(--green); }
  .badge-attente{ background:#EEF1F0; color:#666; }
</style>
</head>
<body>
<div class="admin-shell">
  <aside class="admin-sidebar">
    <div class="admin-brand">
      <img src="{{ asset('images/logo.png') }}" alt="JeCMAP">
      <span>JeCMAP<small>ESPACE ADMIN</small></span>
    </div>

    <nav class="admin-nav">
      <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/></svg>
        Tableau de bord
      </a>
      <a href="{{ route('admin.demandes.index') }}" class="{{ request()->routeIs('admin.demandes.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        Demandes d'orientation
      </a>
      <a href="{{ route('admin.formations.index') }}" class="{{ request()->routeIs('admin.formations.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 10 12 5 2 10l10 5 10-5Z"/><path d="M6 12v5c0 1.7 2.7 3 6 3s6-1.3 6-3v-5"/></svg>
        Formations
      </a>
      <a href="{{ route('admin.seances.index') }}" class="{{ request()->routeIs('admin.seances.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="17" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
        Séances
      </a>
      <a href="{{ route('admin.beneficiaires.index') }}" class="{{ request()->routeIs('admin.beneficiaires.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        Bénéficiaires
      </a>
      <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="4"/><path d="M4 21v-1a6 6 0 0 1 6-6h4a6 6 0 0 1 6 6v1"/></svg>
        Mon profil
      </a>
    </nav>

    <div class="admin-logout">
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/></svg>
          Déconnexion
        </button>
      </form>
    </div>
  </aside>

  <main class="admin-main">
    <div class="admin-topbar">
      <div></div>
      <div class="who">Connecté en tant que <strong>{{ auth()->user()->name }}</strong></div>
    </div>
    @yield('content')
  </main>
</div>
</body>
</html>