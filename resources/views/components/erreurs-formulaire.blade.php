{{-- Affiche les erreurs de validation et les refus (anti-robot, limite d'envoi, doublon, séance complète). --}}
@if ($errors->any())
    <div class="d-alert d-alert--error" role="alert">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 8v5M12 16.5h.01"/></svg>
        <div>
            @if ($errors->count() > 1)
                <p style="margin: 0 0 6px; font-weight: 700;">{{ $errors->count() }} points à corriger :</p>
            @endif
            <ul style="{{ $errors->count() > 1 ? '' : 'list-style: none; padding: 0;' }}">
                @foreach ($errors->all() as $erreur)
                    <li>{{ $erreur }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
