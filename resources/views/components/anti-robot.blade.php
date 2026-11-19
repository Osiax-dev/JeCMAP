{{-- Protection anti-robot (voir App\Http\Middleware\ProtectionAntiRobot) : à placer dans chaque formulaire public. --}}
@php($idPotDeMiel = 'site_internet_'.\Illuminate\Support\Str::random(8))
<div aria-hidden="true" style="position:absolute; left:-10000px; top:auto; width:1px; height:1px; overflow:hidden;">
    <label for="{{ $idPotDeMiel }}">Ne pas remplir ce champ</label>
    <input type="text" name="site_internet" id="{{ $idPotDeMiel }}" value="" tabindex="-1" autocomplete="off">
</div>
<input type="hidden" name="_debut_formulaire" value="{{ \Illuminate\Support\Facades\Crypt::encryptString((string) now()->timestamp) }}">
