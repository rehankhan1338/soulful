/* Soulful Beginnings — testimonials carousel.
   Recreates the reference page's slider (arrows + dots + scroll-snap) for the
   native Elementor card row. Runs only on the front end. */
(function () {
  function init() {
    var track = document.querySelector('.sb-track');
    var prev = document.querySelector('.sb-cnav--prev');
    var next = document.querySelector('.sb-cnav--next');
    var dotsWrap = document.querySelector('.sb-cdots');
    if (!track || !prev || !next || !dotsWrap) return;

    var cards = Array.prototype.slice.call(track.querySelectorAll('.sb-quote'));
    if (!cards.length) return;

    var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var behavior = reduce ? 'auto' : 'smooth';

    // one dot per card
    var dots = cards.map(function (_, i) {
      var dot = document.createElement('button');
      dot.type = 'button';
      dot.setAttribute('role', 'tab');
      dot.setAttribute('aria-label', 'Go to review ' + (i + 1));
      dot.addEventListener('click', function () { centerCard(i); });
      dotsWrap.appendChild(dot);
      return dot;
    });

    var gap = parseFloat(getComputedStyle(track).columnGap) || 24;
    function step() { return cards[0].offsetWidth + gap; }

    function contentLeft(card) {
      return track.scrollLeft + card.getBoundingClientRect().left - track.getBoundingClientRect().left;
    }
    function centerCard(i) {
      var card = cards[i];
      var left = contentLeft(card) - (track.clientWidth - card.offsetWidth) / 2;
      track.scrollTo({ left: left, behavior: behavior });
    }
    function activeIndex() {
      var trackLeft = track.getBoundingClientRect().left;
      var mid = track.clientWidth / 2;
      var best = 0, bestDist = Infinity;
      cards.forEach(function (card, i) {
        var r = card.getBoundingClientRect();
        var center = r.left - trackLeft + r.width / 2;
        var dist = Math.abs(center - mid);
        if (dist < bestDist) { bestDist = dist; best = i; }
      });
      return best;
    }
    function update() {
      var i = activeIndex();
      dots.forEach(function (dot, d) { dot.setAttribute('aria-selected', String(d === i)); });
      prev.disabled = track.scrollLeft <= 1;
      next.disabled = track.scrollLeft >= track.scrollWidth - track.clientWidth - 1;
    }

    prev.addEventListener('click', function () { track.scrollBy({ left: -step(), behavior: behavior }); });
    next.addEventListener('click', function () { track.scrollBy({ left: step(), behavior: behavior }); });

    var ticking = false;
    track.addEventListener('scroll', function () {
      if (ticking) return;
      ticking = true;
      requestAnimationFrame(function () { update(); ticking = false; });
    });
    window.addEventListener('resize', update);
    update();
  }

  // Mobile header hamburger: toggle the dropdown menu
  function initNav() {
    var burger = document.querySelector('.sb-burger');
    var header = document.querySelector('.sb-header');
    if (!burger || !header) return;
    burger.addEventListener('click', function () {
      var open = header.classList.toggle('sb-open');
      burger.setAttribute('aria-expanded', String(open));
    });
    // close when a menu link is tapped
    header.addEventListener('click', function (e) {
      if (e.target.closest('.sb-nav a')) {
        header.classList.remove('sb-open');
        burger.setAttribute('aria-expanded', 'false');
      }
    });
  }

  function boot() { init(); initNav(); }
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();
