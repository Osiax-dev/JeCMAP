@props(['texte'])
{{-- Bouton d'envoi des formulaires publics : affiche « Envoi en cours… » (voir public/js/jecmap.js). --}}
<button type="submit" {{ $attributes->merge(['class' => 'd-btn d-btn--primary d-submit']) }}>
    <span class="d-submit__spinner" aria-hidden="true"></span>
    <span data-texte="{{ $texte }}">{{ $texte }}</span>
</button>
