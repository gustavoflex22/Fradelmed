// Footer and global UI behaviors that do not depend on WooCommerce.
(function () {
  function setCurrentYear() {
    const yearElement = document.getElementById('year');
    if (yearElement) {
      yearElement.textContent = String(new Date().getFullYear());
    }
  }

  function initThemeToggle() {
    const themeToggleBtn = document.getElementById('theme-toggle');
    if (!themeToggleBtn) return;

    const htmlElement = document.documentElement;
    const sunIcon = themeToggleBtn.querySelector('.ph-sun-fill');
    const moonIcon = themeToggleBtn.querySelector('.ph-moon-fill');

    const applyTheme = (theme) => {
      const isDark = theme === 'dark';
      htmlElement.classList.toggle('dark', isDark);
      if (sunIcon) sunIcon.style.display = isDark ? 'inline-block' : 'none';
      if (moonIcon) moonIcon.style.display = isDark ? 'none' : 'inline-block';
      try {
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
      } catch (e) {
        // Ignore storage errors (e.g., private mode).
      }
    };

    const savedTheme = (function () {
      try {
        return localStorage.getItem('theme');
      } catch (e) {
        return null;
      }
    })();

    if (savedTheme) {
      applyTheme(savedTheme);
    } else {
      const prefersDark =
        window.matchMedia &&
        window.matchMedia('(prefers-color-scheme: dark)').matches;
      applyTheme(prefersDark ? 'dark' : 'light');
    }

    themeToggleBtn.addEventListener('click', () => {
      const nextTheme = htmlElement.classList.contains('dark') ? 'light' : 'dark';
      applyTheme(nextTheme);
    });
  }

  function initCarousel() {
    const slides = Array.from(document.querySelectorAll('.carousel-slide'));
    const indicators = Array.from(
      document.querySelectorAll('.carousel-indicators button')
    );
    if (!slides.length || !indicators.length) return;

    let currentIndex = 0;
    const intervalTime = 5000;
    let slideInterval = null;

    const goToSlide = (index) => {
      slides.forEach((slide, i) => {
        const isActive = i === index;
        slide.classList.toggle('active', isActive);
        slide.setAttribute('aria-hidden', isActive ? 'false' : 'true');
      });

      indicators.forEach((indicator, i) => {
        const isActive = i === index;
        indicator.classList.toggle('active', isActive);
        indicator.setAttribute('aria-selected', isActive ? 'true' : 'false');
        indicator.setAttribute('tabindex', isActive ? '0' : '-1');
      });

      currentIndex = index;
    };

    const nextSlide = () => {
      const nextIndex = (currentIndex + 1) % slides.length;
      goToSlide(nextIndex);
    };

    const resetInterval = () => {
      if (slideInterval) window.clearInterval(slideInterval);
      slideInterval = window.setInterval(nextSlide, intervalTime);
    };

    indicators.forEach((indicator, index) => {
      indicator.addEventListener('click', () => {
        goToSlide(index);
        resetInterval();
      });

      indicator.addEventListener('keydown', (event) => {
        if (event.key !== 'Enter' && event.key !== ' ') return;
        event.preventDefault();
        goToSlide(index);
        resetInterval();
      });
    });

    resetInterval();

    const carouselEl = document.querySelector('.carousel');
    if (!carouselEl) return;
    carouselEl.addEventListener('mouseenter', () => window.clearInterval(slideInterval));
    carouselEl.addEventListener('mouseleave', resetInterval);
    carouselEl.addEventListener('focusin', () => window.clearInterval(slideInterval));
    carouselEl.addEventListener('focusout', resetInterval);
  }

  function initMagneticMenu() {
    if (window.innerWidth < 1024) return;
    const magneticList = document.querySelector('.main-nav__list[data-magnetic]');
    if (!magneticList) return;

    const links = Array.from(magneticList.querySelectorAll('a'));
    const maxDistance = 140;

    magneticList.addEventListener('mousemove', (event) => {
      const rect = magneticList.getBoundingClientRect();
      const x = event.clientX - rect.left;
      const y = event.clientY - rect.top;

      links.forEach((link) => {
        const linkRect = link.getBoundingClientRect();
        const centerX = linkRect.left + linkRect.width / 2 - rect.left;
        const centerY = linkRect.top + linkRect.height / 2 - rect.top;
        const dx = x - centerX;
        const dy = y - centerY;
        const distance = Math.hypot(dx, dy);

        if (distance >= maxDistance) {
          link.style.transform = '';
          return;
        }

        const scale = Math.max(1, 1.12 - distance / maxDistance);
        link.style.transform = `translateY(-1px) scale(${scale.toFixed(3)})`;
      });
    });

    magneticList.addEventListener('mouseleave', () => {
      links.forEach((link) => {
        link.style.transform = '';
      });
    });
  }

  function initBrandVideos() {
    const videos = document.querySelectorAll('.brand-video');
    if (!videos.length) return;

    videos.forEach((video) => {
      video.addEventListener(
        'loadeddata',
        () => {
          const mark = video.closest('.site-brand__mark');
          if (mark) mark.classList.add('has-video');
        },
        { once: true }
      );

      try {
        const playPromise = video.play && video.play();
        if (playPromise && typeof playPromise.catch === 'function') {
          playPromise.catch(() => {});
        }
      } catch (e) {
        // Ignore autoplay failures.
      }
    });
  }

  function initHeroVideoPlayback() {
    const heroVideo = document.querySelector('.hero--video .hero__video');
    if (!heroVideo) return;

    const heroSection = heroVideo.closest('.hero--video');
    let fallbackTimer = null;
    const hasBufferedData = () => {
      const buffered = heroVideo.buffered;
      if (!buffered.length) {
        return false;
      }
      return buffered.end(buffered.length - 1) > heroVideo.currentTime + 0.25;
    };
    const fallbackToPoster = () => {
      if (!heroSection || heroSection.classList.contains('hero--video--fallback')) {
        return;
      }
      if (fallbackTimer) {
        window.clearTimeout(fallbackTimer);
        fallbackTimer = null;
      }
      heroSection.classList.add('hero--video--fallback');
      heroVideo.pause();
      heroVideo.removeAttribute('src');
      heroVideo.hidden = true;
      heroVideo.style.display = 'none';
      heroVideo.classList.remove('is-active');
      heroVideo.load();
    };

    const attemptPlay = () => {
      try {
        const playPromise = heroVideo.play && heroVideo.play();
        if (playPromise && typeof playPromise.catch === 'function') {
          playPromise.catch(() => {});
        }
      } catch (error) {
        // Silence autoplay errors.
      }
    };

    const scheduleFallbackCheck = () => {
      if (!heroSection || heroSection.classList.contains('hero--video--fallback')) {
        return;
      }
      if (fallbackTimer) {
        window.clearTimeout(fallbackTimer);
      }
      fallbackTimer = window.setTimeout(() => {
        if (
          (heroVideo.readyState < 3 && !heroVideo.paused && !hasBufferedData()) ||
          heroVideo.paused
        ) {
          fallbackToPoster();
        }
      }, 5000);
    };

    const refreshPlayback = () => {
      attemptPlay();
      if (heroVideo.readyState < 3) {
        heroVideo.load();
      }
    };

    const activateVideo = () => {
      heroVideo.classList.add('is-active');
      if (fallbackTimer) {
        window.clearTimeout(fallbackTimer);
        fallbackTimer = null;
      }
    };

    heroVideo.addEventListener('canplay', activateVideo);
    heroVideo.addEventListener('waiting', () => {
      refreshPlayback();
      scheduleFallbackCheck();
    });
    heroVideo.addEventListener('stalled', () => {
      refreshPlayback();
      scheduleFallbackCheck();
    });
    heroVideo.addEventListener('error', () => {
      refreshPlayback();
      fallbackToPoster();
    });
    heroVideo.addEventListener('playing', activateVideo);

    attemptPlay();
    scheduleFallbackCheck();
  }

  function init() {
    setCurrentYear();
    initThemeToggle();
    initCarousel();
    initMagneticMenu();
    initBrandVideos();
    initHeroVideoPlayback();
  }

  document.addEventListener('DOMContentLoaded', init);
})();
