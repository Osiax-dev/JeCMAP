@props(['prefixe', 'selection' => null, 'legende' => 'Tu es…', 'afficherErreur' => true])
@php
    $options = [
        'collegien' => 'Collégien',
        'lyceen' => 'Lycéen',
        'etudiant' => 'Étudiant',
        'sans_emploi' => 'Sans emploi',
        'autre' => 'Autre',
    ];
@endphp
{{-- Statut scolaire en pastilles : de vrais boutons radio (clavier, lecteur d'écran), plus faciles à toucher qu'une liste. --}}
<fieldset class="d-choices">
    <legend class="d-label">{{ $legende }}</legend>
    <div class="d-choices__list">
        @foreach ($options as $valeur => $libelle)
            <label class="d-choice" for="{{ $prefixe }}-{{ $valeur }}">
                <input type="radio" id="{{ $prefixe }}-{{ $valeur }}" name="statut_scolaire" value="{{ $valeur }}" @checked($selection === $valeur) @if ($loop->first) required @endif>
                <span>{{ $libelle }}</span>
            </label>
        @endforeach
    </div>
    @if ($afficherErreur)
        @error('statut_scolaire')
            <p class="d-error" style="margin-top: 8px;">{{ $message }}</p>
        @enderror
    @endif
</fieldset>
