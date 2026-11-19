<h2>Nouvelle demande d'orientation</h2>
<p><strong>Nom :</strong> {{ $demande->beneficiaire->nom }}</p>
<p><strong>Contact :</strong> {{ $demande->beneficiaire->telephone ?? $demande->beneficiaire->email ?? 'Non renseigné' }}</p>
<p><strong>Statut :</strong> {{ $demande->beneficiaire->statut_scolaire }} — {{ $demande->beneficiaire->niveau }}</p>
<p><strong>Besoin exprimé :</strong></p>
<p>{{ $demande->besoin }}</p>