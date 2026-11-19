@extends('layouts.app')

@section('title', 'Séances d\'orientation')
@section('description', 'Les prochaines séances collectives d\'orientation de JeCMAP : explore les métiers et pose tes questions à l\'équipe. Inscription gratuite.')

@section('content')

<section class="d-hero d-hero--page" aria-labelledby="titre-seances">
  <div class="d-wrap d-hero__grid">
    <div class="d-hero__text">
      <p class="d-eyebrow"><span class="d-eyebrow__dot" aria-hidden="true"></span>Séances collectives</p>
      <h1 class="d-hero__title" id="titre-seances">
        <span class="d-line"><span class="d-line__inner">Nos séances</span></span>
        <span class="d-line d-line--accent"><span class="d-line__inner">d’orientation.</span></span>
      </h1>
      <p class="d-hero__lead">Des rencontres collectives pour explorer les métiers et poser tes questions à l’équipe JeCMAP. L’inscription est gratuite.</p>
      <ul class="d-chips" aria-label="En bref">
        @if ($seances->isNotEmpty())
          <li class="d-chip">{{ trans_choice(':n séance à venir|:n séances à venir', $seances->count(), ['n' => $seances->count()]) }}</li>
        @endif
        <li class="d-chip"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>Gratuit</li>
      </ul>
    </div>
    <div class="d-hero__visual">
      <svg class="d-hero__ring" viewBox="0 0 200 200" aria-hidden="true"><circle cx="100" cy="100" r="88" pathLength="1"/></svg>
      <div class="d-hero__capsule" aria-hidden="true"></div>
      <figure class="d-hero__arch">
        <img src="{{ asset('images/seance.jpg') }}" alt="" width="736" height="587" fetchpriority="high">
      </figure>
    </div>
  </div>
</section>

<section class="d-section d-section--surface" aria-labelledby="titre-liste-seances">
  <div class="d-wrap">
    <h2 class="d-section__title" id="titre-liste-seances" data-reveal style="margin-bottom: 28px;">Prochaines séances</h2>

    <div id="messages-formulaire" class="d-anchor"></div>
    @if (session('success'))
      <div class="d-alert d-alert--success" role="status">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="m8 12 3 3 5-6"/></svg>
        <p style="margin: 0;">{{ session('success') }}</p>
      </div>
    @endif
    <x-erreurs-formulaire />

    @if ($seances->isEmpty())
      <div class="d-empty" data-reveal>
        <p class="d-empty__title">Aucune séance programmée pour le moment</p>
        <p class="d-empty__text">Reviens bientôt, ou envoie une demande d’orientation : l’équipe te recontacte.</p>
        <a href="{{ route('orientation.create') }}" class="d-btn d-btn--primary">Demander une orientation</a>
      </div>
    @else
      <ul class="d-seances">
        @foreach ($seances as $seance)
          @php
            $formulaireActif = (string) old('seance_formulaire') === (string) $seance->id;
            $date = $seance->date_seance->locale('fr');
            $idForm = 'seance-'.$seance->id;
          @endphp
          <li class="d-seance" data-reveal style="--i: {{ min($loop->index, 3) }}">
            <p class="d-seance__date" aria-hidden="true">
              <span class="d-seance__jour">{{ $date->isoFormat('D') }}</span>
              <span class="d-seance__mois">{{ $date->isoFormat('MMM YYYY') }}</span>
            </p>

            <div class="d-seance__body">
              <h3 class="d-seance__title">{{ $seance->titre }}</h3>
              <ul class="d-chips">
                <li class="d-chip"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="17" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>{{ ucfirst($date->isoFormat('dddd D MMMM [à] HH[h]mm')) }}</li>
                <li class="d-chip">{{ $seance->format === 'en_ligne' ? 'En ligne' : 'En présentiel' }}</li>
                @if ($seance->lieu)
                  <li class="d-chip"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s-7-6.2-7-11.5A7 7 0 0 1 19 9.5C19 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/></svg>{{ $seance->lieu }}</li>
                @endif
                @if ($seance->places !== null && ! $seance->estComplete())
                  <li class="d-chip d-chip--accent">{{ trans_choice(':n place restante|:n places restantes', $seance->placesRestantes(), ['n' => $seance->placesRestantes()]) }}</li>
                @endif
              </ul>
              @if ($seance->description)
                <p class="d-seance__text">{{ \Illuminate\Support\Str::limit($seance->description, 180) }}</p>
              @endif

              @if ($seance->estComplete())
                <p class="d-seance__complet">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M5.6 5.6l12.8 12.8"/></svg>
                  Séance complète : inscriptions closes.
                </p>
              @else
                {{-- <details> natif : fonctionne sans JavaScript ; rouvert automatiquement après une erreur sur ce formulaire --}}
                <details class="d-inscription" data-seance="{{ $seance->id }}" @if ($formulaireActif) open @endif>
                  <summary class="d-btn d-btn--outline">
                    S’inscrire à cette séance
                    <svg class="d-btn__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                  </summary>
                  <form method="POST" action="{{ route('seances.inscrire', $seance) }}" class="d-form d-inscription__form" data-envoi novalidate>
                    @csrf
                    <x-anti-robot />
                    <input type="hidden" name="seance_formulaire" value="{{ $seance->id }}">
                    <div class="d-form__row">
                      <x-champ :id="$idForm.'-nom'" name="nom" label="Nom et prénoms" :valeur="$formulaireActif ? old('nom') : ''" autocomplete="name" :afficher-erreur="$formulaireActif" required />
                      <x-champ :id="$idForm.'-tel'" name="telephone" label="Téléphone ou WhatsApp" type="tel" :valeur="$formulaireActif ? old('telephone') : ''" autocomplete="tel" inputmode="tel" :afficher-erreur="$formulaireActif" required />
                    </div>
                    <x-choix-statut :prefixe="$idForm.'-statut'" :selection="$formulaireActif ? old('statut_scolaire') : null" :afficher-erreur="$formulaireActif" />
                    <x-bouton-envoi texte="Confirmer mon inscription" />
                  </form>
                </details>
              @endif
            </div>

            @if ($seance->photo)
              <figure class="d-seance__media">
                <img src="{{ asset('storage/'.$seance->photo) }}" alt="" loading="lazy" width="360" height="270">
              </figure>
            @endif
          </li>
        @endforeach
      </ul>
    @endif
  </div>
</section>

@endsection
