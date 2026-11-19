<footer class="d-footer" id="contact">
  <div class="d-wrap">
    <div class="d-footer__grid">

      <div class="d-footer__brand">
        <a href="{{ route('home') }}" class="d-brand">
          <img src="{{ asset('images/logo.png') }}" alt="" width="46" height="46" loading="lazy">
          <span class="d-brand__text">
            <span class="d-brand__name">JeCMAP</span>
            <span class="d-brand__tag">Je Choisis et Construis Mon Avenir Professionnel</span>
          </span>
        </a>
        <p style="margin: 0; line-height: 1.6;">ONG basée à Natitingou, JeCMAP accompagne gratuitement les jeunes dans leur orientation scolaire, universitaire et professionnelle.</p>
      </div>

      <nav class="d-footer__nav" aria-label="Liens du pied de page">
        <h2 class="d-footer__title">Navigation</h2>
        <ul class="d-footer__list">
          <li><a href="{{ route('home') }}#mission">Notre mission</a></li>
          <li><a href="{{ route('formations.index') }}">Formations</a></li>
          <li><a href="{{ route('orientation.create') }}">Orientation</a></li>
          <li><a href="{{ route('seances.index') }}">Séances</a></li>
          <li><a href="{{ route('apropos') }}">À propos</a></li>
        </ul>
      </nav>

      <div>
        <h2 class="d-footer__title">Nous contacter</h2>
        <ul class="d-footer__list">
          <li>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s-7-6.2-7-11.5A7 7 0 0 1 19 9.5C19 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/></svg>
            <span>Natitingou, Atacora — Bénin</span>
          </li>
          <li>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.4 1.8.7 2.7a2 2 0 0 1-.5 2.1L8 9.8a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.5c.9.3 1.8.6 2.7.7a2 2 0 0 1 1.7 2z"/></svg>
            <span><a href="tel:+2290196888191">+229 01 96 88 81 91</a><br><a href="tel:+2290195916091">+229 01 95 91 60 91</a></span>
          </li>
          <li>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
            <a href="mailto:ongjecmap2026@gmail.com">ongjecmap2026@gmail.com</a>
          </li>
        </ul>
      </div>

      <div>
        <h2 class="d-footer__title">Suivez-nous</h2>
        <div class="d-footer__social">
          <a href="https://www.facebook.com/profile.php?id=61589951895118&amp;mibextid=rS40aB7S9Ucbxw6v" target="_blank" rel="noopener" aria-label="JeCMAP sur Facebook (nouvel onglet)">
            <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M13.5 21v-7.5h2.5l.5-3H13.5V8.5c0-.9.3-1.5 1.6-1.5H16.5V4.2C16.2 4.1 15.1 4 13.9 4c-2.5 0-4.2 1.5-4.2 4.3V10.5H7v3h2.7V21h3.8Z"/></svg>
          </a>
          <a href="https://www.linkedin.com/company/ong-jecmap/" target="_blank" rel="noopener" aria-label="JeCMAP sur LinkedIn (nouvel onglet)">
            <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M6.94 8.5H4V20h2.94V8.5ZM5.47 4a1.7 1.7 0 1 0 0 3.4 1.7 1.7 0 0 0 0-3.4ZM20 20h-2.94v-6.1c0-1.46-.03-3.33-2.03-3.33-2.04 0-2.35 1.6-2.35 3.22V20H9.74V8.5h2.82v1.57h.04c.39-.74 1.36-1.53 2.8-1.53 3 0 3.6 1.98 3.6 4.56V20Z"/></svg>
          </a>
          <a href="https://wa.me/2290196888191" target="_blank" rel="noopener" aria-label="Écrire à JeCMAP sur WhatsApp (nouvel onglet)">
            <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38a9.9 9.9 0 0 0 4.74 1.2h.01c5.46 0 9.9-4.45 9.9-9.91C21.96 6.45 17.5 2 12.04 2z"/></svg>
          </a>
        </div>
      </div>
    </div>

    <div class="d-footer__bottom">
      <p style="margin: 0;">© {{ date('Y') }} ONG JeCMAP — Tous droits réservés.</p>
      <p style="margin: 0;">Natitingou, Bénin</p>
    </div>
  </div>
</footer>
