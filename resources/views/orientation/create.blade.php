@extends('layouts.app')

@section('title', 'Demande d\'orientation')
@section('description', 'Demande une orientation gratuite à JeCMAP : décris ta situation, un membre de l\'équipe te recontacte pour t\'aider à choisir ta filière ou ton métier.')

@section('content')

<section class="d-hero d-hero--page" aria-labelledby="titre-orientation">
  <div class="d-wrap d-hero__grid">
    <div class="d-hero__text">
      <p class="d-eyebrow"><span class="d-eyebrow__dot" aria-hidden="true"></span>Orientation gratuite</p>
      <h1 class="d-hero__title" id="titre-orientation">
        <span class="d-line"><span class="d-line__inner">Tu ne sais pas encore</span></span>
        <span class="d-line d-line--accent"><span class="d-line__inner">quoi choisir&nbsp;?</span></span>
      </h1>
      <p class="d-hero__lead">Décris-nous ta situation : un membre de l’équipe JeCMAP te recontacte pour t’aider à y voir plus clair.</p>
      <div class="d-hero__actions">
        <a href="#formulaire-orientation" class="d-btn d-btn--primary">
          Remplir le formulaire
          <svg class="d-btn__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 5v14M6 13l6 6 6-6"/></svg>
        </a>
        <a href="https://wa.me/2290196888191" class="d-btn d-btn--outline" target="_blank" rel="noopener">Écrire sur WhatsApp</a>
      </div>
    </div>
    <div class="d-hero__visual">
      <svg class="d-hero__ring" viewBox="0 0 200 200" aria-hidden="true"><circle cx="100" cy="100" r="88" pathLength="1"/></svg>
      <div class="d-hero__capsule" aria-hidden="true"></div>
      <figure class="d-hero__arch">
        <img src="{{ asset('images/orientation.jpg') }}" alt="" width="736" height="980" fetchpriority="high">
      </figure>
    </div>
  </div>
</section>

<section class="d-section d-section--surface" id="formulaire-orientation" aria-labelledby="titre-demande" style="scroll-margin-top: 80px;">
  <div class="d-wrap d-split d-split--form-first">

    <div class="d-prose">
      <div class="d-block" data-reveal>
        <h2 class="d-block__title">Comment ça se passe</h2>
        <ol class="d-howto">
          <li><span class="d-howto__num" aria-hidden="true">1</span><div><h3>Tu remplis le formulaire</h3><p>Quelques informations sur toi et ta situation suffisent.</p></div></li>
          <li><span class="d-howto__num" aria-hidden="true">2</span><div><h3>L’équipe te recontacte</h3><p>Par téléphone, WhatsApp ou e-mail, selon ce que tu indiques.</p></div></li>
          <li><span class="d-howto__num" aria-hidden="true">3</span><div><h3>Vous faites le point ensemble</h3><p>Filières, métiers, formations : tu repars avec des pistes concrètes.</p></div></li>
        </ol>
      </div>
      <div class="d-block" data-reveal style="--i: 1">
        <h2 class="d-block__title">C’est pour toi si…</h2>
        <ul class="d-chips">
          <li class="d-chip">Tu es au collège</li>
          <li class="d-chip">Tu prépares le BAC</li>
          <li class="d-chip">Tu es étudiant</li>
          <li class="d-chip">Tu cherches un emploi</li>
        </ul>
      </div>
    </div>

    <div class="d-split__aside">
      <div class="d-panel" data-reveal="scale">
        <h2 class="d-panel__title" id="titre-demande">Je demande une orientation</h2>
        <p class="d-panel__lead">Gratuit. Indique au moins un téléphone ou un e-mail pour qu’on puisse te répondre.</p>

        <div id="messages-formulaire" class="d-anchor"></div>
        @if (session('success'))
          <div class="d-alert d-alert--success" role="status">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="m8 12 3 3 5-6"/></svg>
            <p style="margin: 0;">{{ session('success') }}</p>
          </div>
        @endif
        <x-erreurs-formulaire />

        <form method="POST" action="{{ route('orientation.store') }}" class="d-form" data-envoi novalidate>
          @csrf
          <x-anti-robot />
          <x-champ id="o-nom" name="nom" label="Nom et prénoms" :valeur="old('nom')" autocomplete="name" required />
          <div class="d-form__row">
            <x-champ id="o-tel" name="telephone" label="Téléphone ou WhatsApp" type="tel" :valeur="old('telephone')" autocomplete="tel" inputmode="tel" />
            <x-champ id="o-email" name="email" label="E-mail" type="email" :valeur="old('email')" autocomplete="email" :optionnel="true" />
          </div>
          <x-choix-statut prefixe="o-statut" :selection="old('statut_scolaire')" />
          <x-champ id="o-niveau" name="niveau" label="Niveau ou classe" :valeur="old('niveau')" :optionnel="true" placeholder="Ex. : Terminale D, Licence 2" />
          <x-champ id="o-besoin" name="besoin" label="Décris ta situation" :zone-texte="true" :lignes="6" :valeur="old('besoin')" aide="Ta classe, tes matières préférées, les métiers qui t’intéressent, tes questions." required />
          <x-bouton-envoi texte="Envoyer ma demande" />
          <p class="d-form__aside">Tu préfères parler directement ? <a href="https://wa.me/2290196888191" target="_blank" rel="noopener">WhatsApp : 01 96 88 81 91</a></p>
        </form>
      </div>
    </div>

  </div>
</section>

@endsection
