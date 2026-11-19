/*
 * JeCMAP — interactions et mouvement (JavaScript natif, aucune dépendance).
 * Chargé une seule fois par le layout public, avec `defer`.
 * Chaque bloc vérifie que ses éléments existent : aucune erreur si une page ne les contient pas.
 */
(function () {
  'use strict';

  var reduit = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ---------- En-tête compact au scroll ---------- */
  var header = document.querySelector('[data-header]');
  if (header) {
    var majHeader = function () { header.classList.toggle('is-scrolled', window.scrollY > 24); };
    majHeader();
    window.addEventListener('scroll', majHeader, { passive: true });
  }

  /* ---------- Menu mobile accessible ---------- */
  var bouton = document.querySelector('[data-menu-toggle]');
  var menu = document.getElementById('menu-mobile');
  if (bouton && menu) {
    var libelle = bouton.querySelector('.sr-only');
    var focusables = function () { return menu.querySelectorAll('a[href], button:not([disabled])'); };

    var ouvrir = function () {
      menu.hidden = false;
      menu.removeAttribute('inert');
      // laisse le navigateur appliquer hidden=false avant d'animer
      requestAnimationFrame(function () { menu.classList.add('is-open'); });
      bouton.setAttribute('aria-expanded', 'true');
      if (libelle) libelle.textContent = 'Fermer le menu';
      document.body.classList.add('d-menu-open');
      var premier = focusables()[0];
      if (premier) setTimeout(function () { premier.focus(); }, reduit ? 0 : 250);
    };

    var fermer = function (rendreFocus) {
      menu.classList.remove('is-open');
      menu.setAttribute('inert', '');
      bouton.setAttribute('aria-expanded', 'false');
      if (libelle) libelle.textContent = 'Ouvrir le menu';
      document.body.classList.remove('d-menu-open');
      setTimeout(function () { if (!menu.classList.contains('is-open')) menu.hidden = true; }, reduit ? 0 : 560);
      if (rendreFocus) bouton.focus();
    };

    bouton.addEventListener('click', function () {
      bouton.getAttribute('aria-expanded') === 'true' ? fermer(false) : ouvrir();
    });

    menu.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') { fermer(true); return; }
      if (e.key !== 'Tab') return;
      // garde le focus dans le menu (et sur le bouton de fermeture)
      var liste = Array.prototype.slice.call(focusables()).concat([bouton]);
      var i = liste.indexOf(document.activeElement);
      if (e.shiftKey && i <= 0) { e.preventDefault(); liste[liste.length - 1].focus(); }
      else if (!e.shiftKey && i === liste.length - 1) { e.preventDefault(); liste[0].focus(); }
    });
    bouton.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && bouton.getAttribute('aria-expanded') === 'true') fermer(true);
    });

    // un lien d'ancre (#contact…) ferme le menu
    menu.addEventListener('click', function (e) { if (e.target.closest('a')) fermer(false); });

    // repasser en grand écran ferme le menu
    window.matchMedia('(min-width: 961px)').addEventListener('change', function (m) { if (m.matches) fermer(false); });
  }

  /* ---------- Révélations au scroll ----------
   * [data-reveal] : nouvelle identité. .reveal, .path-card, .mission-slide-* : pages pas encore refaites
   * (même comportement qu'avant, mais chaque bloc est observé individuellement). */
  var aReveler = document.querySelectorAll('[data-reveal], .reveal, .path-card, .mission-slide-left, .mission-slide-right');
  var montrer = function (el) { el.classList.add('is-visible', 'in'); };
  if (reduit || !('IntersectionObserver' in window)) {
    aReveler.forEach(montrer);
  } else if (aReveler.length) {
    var io = new IntersectionObserver(function (entrees) {
      entrees.forEach(function (e) {
        if (e.isIntersecting) { montrer(e.target); io.unobserve(e.target); }
      });
    }, { threshold: 0.15, rootMargin: '0px 0px -8% 0px' });
    aReveler.forEach(function (el) { io.observe(el); });
  }

  /* ---------- Formulaires : état « envoi en cours » ----------
   * Évite les doubles envois (réseau lent) et montre que la demande part. */
  document.querySelectorAll('form[data-envoi]').forEach(function (form) {
    form.addEventListener('submit', function () {
      var btn = form.querySelector('[type="submit"]');
      if (!btn || btn.getAttribute('aria-busy') === 'true') return;
      btn.setAttribute('aria-busy', 'true');
      var texte = btn.querySelector('[data-texte]');
      if (texte) texte.textContent = 'Envoi en cours…';
      // désactivé juste après l'envoi (le navigateur a déjà pris en compte le clic)
      setTimeout(function () { btn.disabled = true; }, 0);
    });
  });
  // retour arrière (page restaurée depuis le cache) : réactive les boutons
  window.addEventListener('pageshow', function (e) {
    if (!e.persisted) return;
    document.querySelectorAll('form[data-envoi] [type="submit"]').forEach(function (btn) {
      btn.disabled = false; btn.removeAttribute('aria-busy');
      var texte = btn.querySelector('[data-texte]');
      if (texte && texte.dataset.texte) texte.textContent = texte.dataset.texte;
    });
  });

  /* ---------- Progression liée au scroll ----------
   * Écrit une variable CSS --p (0 → 1) sur l'élément, uniquement quand il est visible.
   * [data-words] : citation révélée mot à mot. [data-track] : ligne du parcours qui se dessine. */
  var suivis = document.querySelectorAll('[data-words], [data-track]');
  if (suivis.length) {
    if (reduit || !('IntersectionObserver' in window)) {
      suivis.forEach(function (el) { el.style.setProperty('--p', 1); el.style.setProperty('--d-progress', 1); });
    } else {
      var visibles = new Set();
      var enAttente = false;
      var calculer = function () {
        enAttente = false;
        var h = window.innerHeight;
        visibles.forEach(function (el) {
          var r = el.getBoundingClientRect();
          // 0 quand le haut de l'élément arrive à 85 % de l'écran, 1 quand son bas atteint 45 %
          var debut = h * 0.85, fin = h * 0.45;
          var p = (debut - r.top) / ((debut - fin) + r.height);
          p = Math.max(0, Math.min(1, p));
          el.style.setProperty('--p', p.toFixed(3));
          el.style.setProperty('--d-progress', p.toFixed(3));
        });
      };
      var demander = function () { if (!enAttente) { enAttente = true; requestAnimationFrame(calculer); } };
      var ioSuivi = new IntersectionObserver(function (entrees) {
        entrees.forEach(function (e) { e.isIntersecting ? visibles.add(e.target) : visibles.delete(e.target); });
        demander();
      });
      suivis.forEach(function (el) { ioSuivi.observe(el); });
      window.addEventListener('scroll', demander, { passive: true });
      window.addEventListener('resize', demander, { passive: true });
    }
  }
})();
