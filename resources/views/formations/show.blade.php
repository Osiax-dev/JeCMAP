@extends('layouts.app')

@section('title', $formation->titre)
@section('description', \Illuminate\Support\Str::limit($formation->description ?: 'Formation gratuite proposée par JeCMAP à Natitingou.', 155))

@section('content')

<section class="d-hero d-hero--page" aria-labelledby="titre-formation">
  <div class="d-wrap d-hero__grid">
    <div class="d-hero__text">
      <p class="d-breadcrumb">
        <a href="{{ route('formations.index') }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 12H5M11 18l-6-6 6-6"/></svg>
          Toutes les formations
        </a>
      </p>
      <h1 class="d-hero__title" id="titre-formation">
        <span class="d-line"><span class="d-line__inner">{{ $formation->titre }}</span></span>
      </h1>
      @if ($formation->description)
        <p class="d-hero__lead">{{ \Illuminate\Support\Str::limit($formation->description, 180) }}</p>
      @endif
      @if ($formation->statut === 'active')
        <div class="d-hero__actions">
          <a href="#candidature" class="d-btn d-btn--primary">
            Candidater
            <svg class="d-btn__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 5v14M6 13l6 6 6-6"/></svg>
          </a>
        </div>
      @endif
      <ul class="d-chips" aria-label="Informations pratiques">
        <li class="d-chip {{ $formation->statut === 'active' ? 'd-chip--accent' : 'd-chip--muted' }}">{{ $formation->statut === 'active' ? 'Candidatures ouvertes' : 'Candidatures closes' }}</li>
        @if ($formation->duree_jours)
          <li class="d-chip"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>{{ trans_choice(':n jour|:n jours', $formation->duree_jours, ['n' => $formation->duree_jours]) }}</li>
        @endif
        @if ($formation->places)
          <li class="d-chip">{{ trans_choice(':n place|:n places', $formation->places, ['n' => $formation->places]) }}</li>
        @endif
        <li class="d-chip"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>Gratuite</li>
      </ul>
    </div>
    <div class="d-hero__visual">
      <svg class="d-hero__ring" viewBox="0 0 200 200" aria-hidden="true"><circle cx="100" cy="100" r="88" pathLength="1"/></svg>
      <div class="d-hero__capsule" aria-hidden="true"></div>
      <figure class="d-hero__arch">
        <img src="{{ $formation->photo ? asset('storage/'.$formation->photo) : asset('images/formation.jpg') }}" alt="" width="640" height="640" fetchpriority="high">
      </figure>
    </div>
  </div>
</section>

<section class="d-section d-section--surface" style="padding-top: clamp(40px, 6vw, 72px);">
  <div class="d-wrap d-split">

    <div class="d-prose">
      <div class="d-block" data-reveal>
        <h2 class="d-block__title">À propos de la formation</h2>
        <p>{{ $formation->description ?: 'Le programme détaillé est présenté par l’équipe JeCMAP après ta candidature.' }}</p>
      </div>

      @if ($formation->prerequis)
        <div class="d-block" data-reveal style="--i: 1">
          <h2 class="d-block__title">Prérequis</h2>
          <p>{{ $formation->prerequis }}</p>
        </div>
      @endif

      <div class="d-block" data-reveal style="--i: 2">
        <h2 class="d-block__title">Comment ça se passe</h2>
        <ol class="d-howto">
          <li><span class="d-howto__num" aria-hidden="true">1</span><div><h3>Tu envoies ta candidature</h3><p>Avec tes coordonnées et quelques mots sur ta motivation.</p></div></li>
          <li><span class="d-howto__num" aria-hidden="true">2</span><div><h3>L’équipe te recontacte</h3><p>Par téléphone ou WhatsApp, pour faire le point avec toi.</p></div></li>
          <li><span class="d-howto__num" aria-hidden="true">3</span><div><h3>Tu rejoins la formation</h3><p>Gratuitement, aux dates communiquées par l’équipe.</p></div></li>
        </ol>
      </div>
    </div>

    <aside class="d-split__aside" id="candidature" aria-labelledby="titre-candidature">
      <div class="d-panel" data-reveal="scale">
        <h2 class="d-panel__title" id="titre-candidature">Rejoindre la formation</h2>
        <p class="d-panel__lead">Gratuit. L’équipe JeCMAP te recontacte après ta candidature.</p>

        <div id="messages-formulaire" class="d-anchor"></div>
        @if (session('success'))
          <div class="d-alert d-alert--success" role="status">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="m8 12 3 3 5-6"/></svg>
            <p style="margin: 0;">{{ session('success') }}</p>
          </div>
        @endif
        <x-erreurs-formulaire />

        @if ($formation->statut === 'active')
          <form action="{{ route('formations.candidater', $formation) }}" method="POST" class="d-form" data-envoi novalidate>
            @csrf
            <x-anti-robot />
            <x-champ id="f-nom" name="nom" label="Nom et prénoms" :valeur="old('nom')" autocomplete="name" required />
            <x-champ id="f-tel" name="telephone" label="Téléphone ou WhatsApp" type="tel" :valeur="old('telephone')" autocomplete="tel" inputmode="tel" required />
            <x-champ id="f-email" name="email" label="E-mail" type="email" :valeur="old('email')" autocomplete="email" :optionnel="true" />
            <x-champ id="f-motivation" name="motivation" label="Ta motivation" :zone-texte="true" :lignes="4" :valeur="old('motivation')" :optionnel="true" aide="Pourquoi cette formation t’intéresse, ce que tu veux en faire." />
            <x-bouton-envoi texte="Envoyer ma candidature" />
            <p class="d-form__aside">Une question avant de candidater ? <a href="https://wa.me/2290196888191" target="_blank" rel="noopener">Écris-nous sur WhatsApp</a></p>
          </form>
        @else
          <div class="d-empty" style="padding: 24px;">
            <p class="d-empty__title" style="font-size: 19px;">Les candidatures sont closes</p>
            <p class="d-empty__text">Cette formation n’accepte plus de candidatures. Consulte les autres formations ou demande une orientation.</p>
            <a href="{{ route('formations.index') }}" class="d-btn d-btn--soft">Voir les formations ouvertes</a>
          </div>
        @endif
      </div>
    </aside>

  </div>
</section>

@endsection
