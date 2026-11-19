@php
    $liensNavigation = [
        ['libelle' => 'Accueil', 'url' => route('home'), 'actif' => request()->routeIs('home')],
        ['libelle' => 'À propos', 'url' => route('apropos'), 'actif' => request()->routeIs('apropos')],
        ['libelle' => 'Formations', 'url' => route('formations.index'), 'actif' => request()->routeIs('formations.*')],
        ['libelle' => 'Orientation', 'url' => route('orientation.create'), 'actif' => request()->routeIs('orientation.*')],
        ['libelle' => 'Séances', 'url' => route('seances.index'), 'actif' => request()->routeIs('seances.*')],
    ];
@endphp

<header class="site-header" data-header>
  <div class="d-wrap site-header__inner">
    <a href="{{ route('home') }}" class="d-brand">
      <img src="{{ asset('images/logo.png') }}" alt="" width="46" height="46">
      <span class="d-brand__text">
        <span class="d-brand__name">JeCMAP</span>
        <span class="d-brand__tag">Choisir · Construire</span>
      </span>
      <span class="sr-only">, retour à l'accueil</span>
    </a>

    <nav class="d-nav" aria-label="Navigation principale">
      <ul class="d-nav__list">
        @foreach ($liensNavigation as $lien)
          <li><a href="{{ $lien['url'] }}" class="d-nav__link" @if ($lien['actif']) aria-current="page" @endif>{{ $lien['libelle'] }}</a></li>
        @endforeach
      </ul>
    </nav>

    <a href="#contact" class="d-btn d-btn--soft site-header__cta">Nous contacter</a>

    <button type="button" class="d-menu-btn" aria-expanded="false" aria-controls="menu-mobile" data-menu-toggle>
      <span class="sr-only">Ouvrir le menu</span>
      <span class="d-menu-btn__bars" aria-hidden="true"><span></span><span></span></span>
    </button>
  </div>
  <span class="site-header__progress" aria-hidden="true"></span>
</header>

{{-- Hors du header : un parent avec backdrop-filter casserait le position: fixed --}}
<div class="d-menu" id="menu-mobile" hidden inert>
  <nav aria-label="Navigation mobile">
    <ul class="d-menu__list">
      @foreach ($liensNavigation as $i => $lien)
        <li class="d-menu__item" style="--i: {{ $i }}">
          <a href="{{ $lien['url'] }}" class="d-menu__link" @if ($lien['actif']) aria-current="page" @endif>
            {{ $lien['libelle'] }}
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
          </a>
        </li>
      @endforeach
    </ul>
  </nav>
  <div class="d-menu__actions" style="--i: {{ count($liensNavigation) }}">
    <a href="{{ route('orientation.create') }}" class="d-btn d-btn--primary">Demander une orientation</a>
    <a href="https://wa.me/2290196888191" class="d-btn d-btn--outline" target="_blank" rel="noopener">Écrire sur WhatsApp</a>
  </div>
</div>
