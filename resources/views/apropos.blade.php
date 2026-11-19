@extends('layouts.app')

@section('title', 'À propos')
@section('description', 'JeCMAP est une ONG de terrain née à Natitingou : orientation, formation, coaching et insertion, gratuits pour les jeunes de l\'Atacora.')

@php
    $valeurs = [
        ['titre' => 'Gratuité', 'texte' => 'Tous nos services sont accessibles sans frais pour les jeunes accompagnés.', 'icone' => '<path d="M20 6 9 17l-5-5"/>'],
        ['titre' => 'Proximité', 'texte' => 'Un accompagnement de terrain, ancré dans les réalités locales de l’Atacora.', 'icone' => '<path d="M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.9M16 3.1a4 4 0 0 1 0 7.8"/>'],
        ['titre' => 'Engagement', 'texte' => 'Un suivi dans la durée, du premier contact jusqu’à l’insertion professionnelle.', 'icone' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>'],
    ];
@endphp

@section('content')

<section class="d-hero d-hero--page" aria-labelledby="titre-apropos">
  <div class="d-wrap d-hero__grid">
    <div class="d-hero__text">
      <p class="d-eyebrow"><span class="d-eyebrow__dot" aria-hidden="true"></span>Notre histoire</p>
      <h1 class="d-hero__title" id="titre-apropos">
        <span class="d-line"><span class="d-line__inner">Une ONG de terrain,</span></span>
        <span class="d-line d-line--accent"><span class="d-line__inner">née à Natitingou.</span></span>
      </h1>
      <p class="d-hero__lead">JeCMAP est née d’un constat simple : trop de jeunes béninois choisissent un métier par défaut, faute d’information ou d’accompagnement.</p>
      <div class="d-hero__actions">
        <a href="{{ route('orientation.create') }}" class="d-btn d-btn--primary">
          Demander une orientation
          <svg class="d-btn__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </a>
      </div>
    </div>
    <div class="d-hero__visual">
      <svg class="d-hero__ring" viewBox="0 0 200 200" aria-hidden="true"><circle cx="100" cy="100" r="88" pathLength="1"/></svg>
      <div class="d-hero__capsule" aria-hidden="true"></div>
      <figure class="d-hero__arch">
        <img src="{{ asset('images/apropos.jpg') }}" alt="Des jeunes réunis lors d’une activité JeCMAP" width="735" height="490" fetchpriority="high">
      </figure>
    </div>
  </div>
</section>

<section class="d-section d-section--surface" aria-labelledby="titre-mission-apropos">
  <div class="d-wrap d-story">
    <div class="d-story__card" data-reveal>
      <p class="d-eyebrow" style="color: var(--d-accent-ink);"><span class="d-eyebrow__dot" style="background: var(--d-shape);" aria-hidden="true"></span>Depuis Natitingou</p>
      <h2 class="d-story__card-title">Une ONG de terrain</h2>
      <p>Née dans le nord du Bénin, JeCMAP construit un accompagnement pensé pour les réalités locales de la jeunesse, avant de le rendre accessible à l’échelle nationale.</p>
    </div>
    <div class="d-story__copy">
      <h2 class="d-section__title" id="titre-mission-apropos" data-reveal>Accompagner chaque jeune vers son avenir.</h2>
      <p data-reveal style="--i: 1">JeCMAP existe pour inverser cette logique : donner à chaque jeune les moyens de choisir son avenir, puis de le construire étape par étape.</p>
      <p data-reveal style="--i: 2">Orientation, formation, coaching et insertion : quatre leviers entièrement gratuits pour transformer un potentiel en véritable trajectoire professionnelle.</p>
    </div>
  </div>
</section>

<section class="d-section" aria-labelledby="titre-valeurs">
  <div class="d-wrap">
    <div class="d-section__head">
      <h2 class="d-section__title" id="titre-valeurs" data-reveal>Ce qui guide notre action</h2>
      <p class="d-section__lead" data-reveal style="--i: 1">Trois valeurs, appliquées à chaque jeune que nous accompagnons.</p>
    </div>
    <ul class="d-values">
      @foreach ($valeurs as $i => $valeur)
        <li class="d-value" data-reveal style="--i: {{ $i }}">
          <span class="d-value__icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $valeur['icone'] !!}</svg></span>
          <h3>{{ $valeur['titre'] }}</h3>
          <p>{{ $valeur['texte'] }}</p>
        </li>
      @endforeach
    </ul>
  </div>
</section>

<section class="d-section" style="padding-top: 0;">
  <div class="d-wrap">
    <div class="d-cta" data-reveal="scale">
      <span class="d-cta__ring" aria-hidden="true"></span>
      <div>
        <h2 class="d-cta__title">Tu hésites sur ta filière ou ton métier&nbsp;?</h2>
        <p class="d-cta__text">Parle-nous de ta situation : c’est gratuit.</p>
      </div>
      <div class="d-cta__actions">
        <a href="{{ route('orientation.create') }}" class="d-btn d-btn--primary">Demander une orientation</a>
        <a href="https://wa.me/2290196888191" class="d-btn d-btn--outline" target="_blank" rel="noopener">Écrire sur WhatsApp</a>
      </div>
    </div>
  </div>
</section>

@endsection
