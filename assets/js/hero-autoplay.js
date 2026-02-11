(function () {
  'use strict';

  if ( typeof window === 'undefined' || typeof document === 'undefined' ) {
    return;
  }

  var mobileQuery = window.matchMedia('(max-width: 768px)');
  var heroMedia = document.querySelector('.hero--video .hero__media');
  if ( mobileQuery.matches && heroMedia ) {
    heroMedia.classList.add('hero__media--static');
    var heroVideo = heroMedia.querySelector('.hero__video');
    if ( heroVideo && heroVideo.parentNode ) {
      heroVideo.parentNode.removeChild( heroVideo );
    }
    var heroIframe = heroMedia.querySelector('iframe');
    if ( heroIframe && heroIframe.parentNode ) {
      heroIframe.parentNode.removeChild( heroIframe );
    }
    return;
  }

  if ( ! heroMedia ) {
    return;
  }

  var heroVideo = heroMedia.querySelector('.hero__video');
  if ( ! heroVideo ) {
    return;
  }

  heroVideo.muted = true;
  heroVideo.playsInline = true;
  heroVideo.setAttribute('playsinline', '');
  heroVideo.setAttribute('muted', '');

  var attemptPlay = function () {
    if ( typeof heroVideo.play !== 'function' ) {
      return;
    }
    heroVideo.play().catch(function () {
      // swallow errors; autoplay might still be blocked
    });
  };

  window.addEventListener('load', attemptPlay);

  var touchHandler = function () {
    attemptPlay();
    document.removeEventListener('touchstart', touchHandler);
  };

  document.addEventListener('touchstart', touchHandler, { passive: true });
})();
