@props([
    'id',
    'name',
    'label',
    'type' => 'text',
    'valeur' => null,
    'optionnel' => false,
    'aide' => null,
    'zoneTexte' => false,
    'lignes' => 5,
    'afficherErreur' => true,
])
@php
    $enErreur = $afficherErreur && $errors->has($name);
    $decrit = trim(($aide ? $id.'-aide ' : '').($enErreur ? $id.'-erreur' : ''));
@endphp
{{-- Champ de formulaire : label visible et relié, aide et erreur annoncées aux lecteurs d'écran. --}}
<div class="d-field {{ $enErreur ? 'd-field--erreur' : '' }}">
    <label class="d-label" for="{{ $id }}">{{ $label }} @if ($optionnel)<span class="d-label__opt">(facultatif)</span>@endif</label>
    @if ($aide)
        <p class="d-hint" id="{{ $id }}-aide">{{ $aide }}</p>
    @endif
    @if ($zoneTexte)
        <textarea id="{{ $id }}" name="{{ $name }}" rows="{{ $lignes }}" @if ($enErreur) aria-invalid="true" @endif @if ($decrit) aria-describedby="{{ $decrit }}" @endif {{ $attributes->merge(['class' => 'd-textarea']) }}>{{ $valeur }}</textarea>
    @else
        <input id="{{ $id }}" type="{{ $type }}" name="{{ $name }}" value="{{ $valeur }}" @if ($enErreur) aria-invalid="true" @endif @if ($decrit) aria-describedby="{{ $decrit }}" @endif {{ $attributes->merge(['class' => 'd-input']) }}>
    @endif
    @if ($enErreur)
        <p class="d-error" id="{{ $id }}-erreur">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 8v5M12 16.5h.01"/></svg>
            {{ $errors->first($name) }}
        </p>
    @endif
</div>
