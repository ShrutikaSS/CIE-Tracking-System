/* ============================================================
   Hero BlurText Reveal Animation — JavaScript
   Inspired by React Bits BlurText component
   Pure Vanilla JS, zero dependencies
   ============================================================ */
(function () {
  'use strict';

  /* ---- Bail out if the user prefers reduced motion ---- */
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

  const hero = document.querySelector('.hero');
  if (!hero) return;

  /* ---- 1. Inject the slow-zoom background layer ---- */
  const bgLayer = document.createElement('div');
  bgLayer.classList.add('hero-bg');
  hero.prepend(bgLayer);           // insert before ::before pseudo
  hero.classList.add('hero-animated');

  /* ---- 2. Grab the three target elements ---- */
  const heroContent = hero.querySelector('.hero-content');
  const accentLine  = heroContent.querySelector('.colorful-hr');
  const heading     = heroContent.querySelector('h1');
  const subtitle    = heroContent.querySelector('p');
  const ctaButton   = heroContent.querySelector('button, .btn-login');

  /* ---- 3. Disable old CSS animation ---- */
  heroContent.classList.add('hero-anim-ready');

  /* ---- 4. Tag the accent line ---- */
  if (accentLine) accentLine.classList.add('hero-reveal-line');

  /* ---- 5. Split heading into word spans ---- */
  if (heading) {
    const words = heading.textContent.trim().split(/\s+/);
    heading.innerHTML = '';
    words.forEach(function (word, i) {
      const span = document.createElement('span');
      span.classList.add('hero-word');
      span.textContent = word;
      span.style.transitionDelay = (i * 150) + 'ms';
      heading.appendChild(span);

      // whitespace between words
      if (i < words.length - 1) {
        heading.appendChild(document.createTextNode(' '));
      }
    });
  }

  /* ---- 6. Tag subtitle ---- */
  if (subtitle) subtitle.classList.add('hero-subtitle-reveal');

  /* ---- 7. Tag CTA button ---- */
  if (ctaButton) ctaButton.classList.add('hero-cta-reveal');

  /* ---- 8. Intersection Observer — fire once ---- */
  var played = false;

  function revealSequence() {
    if (played) return;
    played = true;

    var wordEls   = heading ? heading.querySelectorAll('.hero-word') : [];
    var wordCount = wordEls.length;

    // total time (ms) the heading words occupy
    var headingDuration = wordCount * 150 + 600; // last word delay + anim duration

    // Step 1 — accent line
    requestAnimationFrame(function () {
      if (accentLine) accentLine.classList.add('revealed');
    });

    // Step 2 — heading words (stagger already in CSS transitionDelay)
    // Start 350ms after the line begins drawing
    setTimeout(function () {
      wordEls.forEach(function (el) {
        el.classList.add('revealed');
      });
    }, 350);

    // Step 3 — subtitle appears after heading finishes
    setTimeout(function () {
      if (subtitle) subtitle.classList.add('revealed');
    }, 350 + headingDuration);

    // Step 4 — CTA appears 400ms after subtitle starts
    setTimeout(function () {
      if (ctaButton) ctaButton.classList.add('revealed');
    }, 350 + headingDuration + 400);
  }

  var heroObserver = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (entry.isIntersecting && !played) {
        revealSequence();
        heroObserver.unobserve(hero);
      }
    });
  }, { threshold: 0.15 });

  heroObserver.observe(hero);
})();
