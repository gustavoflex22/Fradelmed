(() => {
  const body = document.body;
  const navToggle = document.getElementById('navToggle');
  const nav = document.getElementById('siteNav');
  const navOverlay = document.querySelector('.nav-overlay');
  const closeButtons = nav ? nav.querySelectorAll('[data-nav-close]') : [];
  const focusableSelector = 'a, button, input, select, textarea, [tabindex]:not([tabindex="-1"])';
  let lastFocused = null;

  if (nav && navToggle) {
    const syncNavAria = () => {
      const isDesktop = window.innerWidth >= 1024;
      if (isDesktop) {
        nav.removeAttribute('aria-hidden');
        navToggle.setAttribute('aria-expanded', 'false');
        body.classList.remove('nav-open');
        return;
      }

      if (!body.classList.contains('nav-open')) {
        nav.setAttribute('aria-hidden', 'true');
        navToggle.setAttribute('aria-expanded', 'false');
      }
    };

    const openNav = () => {
      lastFocused = document.activeElement;
      body.classList.add('nav-open');
      navToggle.setAttribute('aria-expanded', 'true');
      nav.removeAttribute('aria-hidden');
      const focusTarget = nav.querySelector(focusableSelector);
      if (focusTarget) {
        focusTarget.focus();
      }
    };

    const closeNav = () => {
      body.classList.remove('nav-open');
      navToggle.setAttribute('aria-expanded', 'false');
      nav.setAttribute('aria-hidden', 'true');
      if (lastFocused && typeof lastFocused.focus === 'function') {
        lastFocused.focus();
      }
    };

    navToggle.addEventListener('click', () => {
      if (body.classList.contains('nav-open')) {
        closeNav();
      } else {
        openNav();
      }
    });

    if (navOverlay) {
      navOverlay.addEventListener('click', closeNav);
    }

    closeButtons.forEach((button) => {
      button.addEventListener('click', closeNav);
    });

    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape' && body.classList.contains('nav-open')) {
        closeNav();
      }
    });

    window.addEventListener('resize', syncNavAria);
    window.addEventListener('load', syncNavAria);
    syncNavAria();

    const navLinks = nav.querySelectorAll('a');
    navLinks.forEach((link) => {
      link.addEventListener('click', () => {
        if (window.innerWidth < 1024) {
          closeNav();
        }
      });
    });
  }

  const accountNav = document.querySelector('.woocommerce-MyAccount-navigation');
  if (accountNav) {
    const list = accountNav.querySelector('ul');
    if (list && !accountNav.querySelector('.account-nav__toggle')) {
      const activeLink = list.querySelector('.is-active a') || list.querySelector('a');
      const toggle = document.createElement('button');
      const listId = list.id || 'accountNavList';

      list.id = listId;
      toggle.type = 'button';
      toggle.className = 'account-nav__toggle';
      toggle.setAttribute('aria-expanded', 'false');
      toggle.setAttribute('aria-controls', listId);
      toggle.textContent = activeLink ? activeLink.textContent.trim() : 'Minha conta';

      accountNav.insertBefore(toggle, list);

      toggle.addEventListener('click', () => {
        const isExpanded = toggle.getAttribute('aria-expanded') === 'true';
        toggle.setAttribute('aria-expanded', String(!isExpanded));
        accountNav.classList.toggle('is-open', !isExpanded);
      });

      document.addEventListener('click', (event) => {
        if (!accountNav.contains(event.target)) {
          toggle.setAttribute('aria-expanded', 'false');
          accountNav.classList.remove('is-open');
        }
      });
    }
  }
})();
