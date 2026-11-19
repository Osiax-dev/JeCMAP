@extends('layouts.app')

@section('title', 'Formations')
@section('description', 'Les formations gratuites de JeCMAP à Natitingou : des ateliers pratiques pour construire des savoir-faire concrets.')

@section('content')

<section class="d-hero d-hero--page" aria-labelledby="titre-formations">
  <div class="d-wrap d-hero__grid">
    <div class="d-hero__text">
      <p class="d-eyebrow"><span class="d-eyebrow__dot" aria-hidden="true"></span>Nos formations</p>
      <h1 class="d-hero__title" id="titre-formations">
        <span class="d-line"><span class="d-line__inner">Renforcer</span></span>
        <span class="d-line d-line--accent"><span class="d-line__inner">tes compétences.</span></span>
      </h1>
      <p class="d-hero__lead">Des formations gratuites, adaptées et innovantes pour construire des savoir-faire concrets.</p>
      <ul class="d-chips" aria-label="En bref">
        <li class="d-chip"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>Gratuites</li>
        <li class="d-chip"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s-7-6.2-7-11.5A7 7 0 0 1 19 9.5C19 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/></svg>Natitingou</li>
        @if ($formations->isNotEmpty())
          <li class="d-chip">{{ trans_choice(':n formation ouverte|:n formations ouvertes', $formations->count(), ['n' => $formations->count()]) }}</li>
        @endif
      </ul>
    </div>
    <div class="d-hero__visual">
      <svg class="d-hero__ring" viewBox="0 0 200 200" aria-hidden="true"><circle cx="100" cy="100" r="88" pathLength="1"/></svg>
      <div class="d-hero__capsule" aria-hidden="true"></div>
      <figure class="d-hero__arch">
        <img src="{{ asset('images/formation.jpg') }}" alt="Jeunes en atelier de formation" width="626" height="408" fetchpriority="high">
      </figure>
    </div>
  </div>
</section>

<section class="d-section d-section--surface" aria-labelledby="titre-liste-formations">
  <div class="d-wrap">
    <div class="d-section__head">
      <h2 class="d-section__title" id="titre-liste-formations" data-reveal>Les formations ouvertes</h2>
      <p class="d-section__lead" data-reveal style="--i: 1">Choisis une formation pour voir son contenu et envoyer ta candidature.</p>
    </div>

    @if ($formations->isEmpty())
      <div class="d-empty" data-reveal>
        <p class="d-empty__title">Aucune formation ouverte pour le moment</p>
        <p class="d-empty__text">De nouvelles sessions sont annoncées régulièrement. En attendant, l’équipe peut t’orienter vers la bonne formation.</p>
        <a href="{{ route('orientation.create') }}" class="d-btn d-btn--primary">Demander une orientation</a>
      </div>
    @else
      <ul class="d-grid">
        @foreach ($formations as $formation)
          <li class="d-card" data-reveal style="--i: {{ $loop->index % 3 }}">
            @if ($formation->photo)
              <figure class="d-card__media">
                <img src="{{ asset('storage/'.$formation->photo) }}" alt="" loading="lazy" width="640" height="400">
              </figure>
            @else
              <div class="d-card__media d-card__media--vide" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M3 8l9-4 9 4-9 4z"/><path d="M7 10v5c0 1.5 2.2 3 5 3s5-1.5 5-3v-5"/></svg>
              </div>
            @endif
            <div class="d-card__body">
              @if ($formation->duree_jours || $formation->places)
                <ul class="d-chips">
                  @if ($formation->duree_jours)
                    <li class="d-chip"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>{{ trans_choice(':n jour|:n jours', $formation->duree_jours, ['n' => $formation->duree_jours]) }}</li>
                  @endif
                  @if ($formation->places)
                    <li class="d-chip">{{ trans_choice(':n place|:n places', $formation->places, ['n' => $formation->places]) }}</li>
                  @endif
                </ul>
              @endif
              <h3 class="d-card__title"><a href="{{ route('formations.show', $formation) }}">{{ $formation->titre }}</a></h3>
              @if ($formation->description)
                <p class="d-card__text">{{ \Illuminate\Support\Str::limit($formation->description, 130) }}</p>
              @endif
              <span class="d-card__more" aria-hidden="true">
                Voir la formation
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
              </span>
            </div>
          </li>
        @endforeach
      </ul>
    @endif
  </div>
</section>

@endsection
