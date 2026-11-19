@extends('layouts.app')

@section('title', 'Accueil')

@push('head')
<link rel="preload" as="image" href="{{ asset('images/hero-bg.jpg') }}" fetchpriority="high">
@endpush

@php
    $citation = 'Chaque jeune a un potentiel. Notre mission est de l’aider à le révéler.';
    $mots = explode(' ', $citation);

    $etapes = [
        ['titre' => 'Orientation', 'texte' => 'Identifier la filière et le métier qui te correspondent.'],
        ['titre' => 'Formation', 'texte' => 'Des ateliers pratiques pour des compétences concrètes.'],
        ['titre' => 'Accompagnement', 'texte' => 'Coaching individuel et mentorat personnalisé.'],
        ['titre' => 'Insertion', 'texte' => 'Accéder à l’emploi ou créer son activité.'],
    ];
@endphp

@section('content')

{{-- ============ HERO ============ --}}
<section class="d-hero" aria-labelledby="titre-accueil">
  <div class="d-wrap d-hero__grid">
    <div class="d-hero__text">
      <p class="d-eyebrow"><span class="d-eyebrow__dot" aria-hidden="true"></span>ONG d’orientation, Natitingou</p>
      <h1 class="d-hero__title" id="titre-accueil">
        <span class="d-line"><span class="d-line__inner">Choisir sa voie.</span></span>
        <span class="d-line d-line--accent"><span class="d-line__inner">Construire son avenir.</span></span>
      </h1>
      <p class="d-hero__lead">JeCMAP accompagne gratuitement les jeunes de l’Atacora, de l’orientation scolaire jusqu’au premier emploi.</p>
      <div class="d-hero__actions">
        <a href="{{ route('orientation.create') }}" class="d-btn d-btn--primary">
          Demander une orientation
          <svg class="d-btn__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </a>
        <a href="#parcours" class="d-btn d-btn--outline">Découvrir nos actions</a>
      </div>
      <p class="d-hero__note">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
        Tous nos services sont gratuits, pour les collégiens, lycéens, étudiants et jeunes sans emploi.
      </p>
    </div>

    <div class="d-hero__visual">
      <svg class="d-hero__ring" viewBox="0 0 200 200" aria-hidden="true"><circle cx="100" cy="100" r="88" pathLength="1"/></svg>
      <div class="d-hero__capsule" aria-hidden="true"></div>
      <figure class="d-hero__arch">
        <img src="{{ asset('images/hero-bg.jpg') }}" alt="Des jeunes accompagnés par JeCMAP échangent autour d’une table" width="736" height="736" fetchpriority="high">
      </figure>
      <p class="d-hero__badge">
        <span aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg></span>
        100 % gratuit
      </p>
    </div>
  </div>
</section>

{{-- ============ PARCOURS ============ --}}
<section class="d-section d-section--surface" id="parcours" aria-labelledby="titre-parcours">
  <div class="d-wrap">
    <div class="d-section__head">
      <h2 class="d-section__title" id="titre-parcours" data-reveal>Un accompagnement complet, en quatre étapes</h2>
      <p class="d-section__lead" data-reveal style="--i: 1">Chaque jeune avance à son rythme et peut entrer à n’importe quelle étape.</p>
    </div>

    <div class="d-steps__track" aria-hidden="true" data-track><span></span></div>
    <ol class="d-steps">
      @foreach ($etapes as $i => $etape)
        <li class="d-step @if ($loop->last) d-step--final @endif" data-reveal style="--i: {{ $i }}">
          <span class="d-step__num" aria-hidden="true">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
          <h3 class="d-step__title"><span class="sr-only">Étape {{ $i + 1 }} : </span>{{ $etape['titre'] }}</h3>
          <p class="d-step__text">{{ $etape['texte'] }}</p>
        </li>
      @endforeach
    </ol>
  </div>
</section>

{{-- ============ MISSION + PROCHAINE SÉANCE ============ --}}
<section class="d-section" id="mission" aria-labelledby="titre-mission">
  <div class="d-wrap d-mission">
    <figure class="d-mission__media" data-reveal>
      <img src="{{ asset('images/mission.jpg') }}" alt="Une animatrice JeCMAP échange avec des élèves" width="626" height="417" loading="lazy">
    </figure>

    <div class="d-mission__body">
      <h2 class="sr-only" id="titre-mission">Notre mission</h2>
      <blockquote class="d-quote">
        {{-- Chaque mot dans un span : sa variable --w sert à l'éclairer au scroll. Guillemets collés au premier et au dernier mot. --}}
        <p data-words style="--n: {{ count($mots) }}">@foreach ($mots as $i => $mot)<span class="d-word" style="--w: {{ $i }}">@if ($loop->first)«&nbsp;@endif{{ $mot }}@if ($loop->last)&nbsp;»@endif</span>{{ $loop->last ? '' : ' ' }}@endforeach</p>
        <footer>— L’équipe JeCMAP</footer>
      </blockquote>

      @if ($prochaineSeance)
        @php($restantes = $prochaineSeance->placesRestantes())
        <div class="d-session" data-reveal>
          <div>
            <p class="d-session__label">Prochaine séance collective</p>
            <p class="d-session__title">{{ $prochaineSeance->titre }}</p>
            <p class="d-session__meta">
              {{ ucfirst($prochaineSeance->date_seance->locale('fr')->isoFormat('dddd D MMMM [à] HH[h]mm')) }}
              @if ($prochaineSeance->lieu) — {{ $prochaineSeance->lieu }} @endif
              @if ($restantes !== null) · {{ $restantes > 0 ? trans_choice(':n place restante|:n places restantes', $restantes, ['n' => $restantes]) : 'Complet' }} @endif
            </p>
          </div>
          @if ($restantes === null || $restantes > 0)
            <a href="{{ route('seances.index') }}" class="d-btn d-btn--primary">Je m’inscris</a>
          @else
            <a href="{{ route('seances.index') }}" class="d-btn d-btn--soft">Voir les autres séances</a>
          @endif
        </div>
      @else
        <div class="d-session" data-reveal>
          <div>
            <p class="d-session__label">Séances collectives</p>
            <p class="d-session__title">Aucune séance programmée pour le moment</p>
            <p class="d-session__meta">Envoie une demande d’orientation : l’équipe te recontacte.</p>
          </div>
          <a href="{{ route('orientation.create') }}" class="d-btn d-btn--primary">Demander une orientation</a>
        </div>
      @endif
    </div>
  </div>
</section>

@endsection
