<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'Accueil') — JeCMAP, Je Choisis et Construis Mon Avenir Professionnel</title>
<meta name="description" content="@yield('description', 'JeCMAP, ONG basée à Natitingou, accompagne gratuitement les jeunes de l\'Atacora : orientation scolaire et professionnelle, formations, coaching, mentorat et insertion.')">
<meta name="theme-color" content="#EAF2FB">
{{-- Active les états « animables » seulement si JavaScript tourne : sans JS, tout reste visible. --}}
<script>document.documentElement.classList.add('js')</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/jecmap.css') }}">
<link rel="stylesheet" href="{{ asset('css/jecmap-v2.css') }}">
<link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
@stack('head')
</head>
<body>

<a class="skip-link" href="#contenu">Aller au contenu</a>

@include('partials.header')

<main id="contenu" tabindex="-1">
    @yield('content')
</main>

@include('partials.footer')

<script src="{{ asset('js/jecmap.js') }}" defer></script>
</body>
</html>
