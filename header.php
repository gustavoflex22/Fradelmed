<!doctype html>
<html <?php language_attributes(); ?> class="transition-colors duration-500">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Fradel-Med | Distribuidora de produtos médicos</title>
  <meta name="description" content="Fradel-Med fornece instrumentais, equipamentos e materiais médicos com suporte consultivo em todo o Brasil.">

  <link rel="icon" type="image/svg+xml" href="<?php echo get_template_directory_uri(); ?>/assets/img/favicon.svg">
  <link rel="preload" as="image" href="<?php echo get_template_directory_uri(); ?>/assets/img/FD001 CANULA TRAQ.S BL METAL N.05.jpg">
  <link rel="preload" as="video" href="<?php echo get_template_directory_uri(); ?>/assets/img/logo.webm" type="video/webm">
  <link rel="preload" as="video" href="<?php echo get_template_directory_uri(); ?>/assets/img/12686081_3840_2160_30fps.mp4" type="video/mp4">

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Phosphor Icons -->
  <link rel="stylesheet" href="https://unpkg.com/@phosphor-icons/web@2.1.1/assets/phosphor.css">

  <!-- CSS principal do tema -->
  <?php
    $base_css_path = get_template_directory() . '/assets/css/base.css';
    $base_css_ver  = file_exists( $base_css_path ) ? filemtime( $base_css_path ) : '1.0';
  ?>
  <link rel="stylesheet" href="<?php echo esc_url( get_template_directory_uri() . '/assets/css/base.css?v=' . $base_css_ver ); ?>">

  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
  <a href="#conteudo" class="skip-link">Pular para o conteúdo</a>

  <?php $is_checkout_page = function_exists( 'is_checkout' ) && is_checkout(); ?>

  <?php
    $brand_logo_path = get_template_directory() . '/assets/img/logotipo-fradel-med-icone.png';
    $brand_logo_url  = get_template_directory_uri() . '/assets/img/logotipo-fradel-med-icone.png';
    $brand_logo_ver  = file_exists( $brand_logo_path ) ? filemtime( $brand_logo_path ) : '1.0';
  ?>

  <header class="site-header<?php echo $is_checkout_page ? ' site-header--checkout' : ''; ?>">
    <?php if ( $is_checkout_page ) : ?>
      <div class="site-header__main">
        <div class="container site-header__grid site-header__grid--checkout">
          <a class="checkout-back" href="<?php echo home_url( '/' ); ?>" aria-label="<?php esc_attr_e( 'Voltar ao menu principal', 'fradelmed-theme' ); ?>">
            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
              <path d="M15 6l-6 6 6 6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </a>
          <a class="checkout-home" href="<?php echo home_url( '/' ); ?>" aria-label="<?php esc_attr_e( 'Voltar para a página inicial', 'fradelmed-theme' ); ?>">
            <img
              class="checkout-home__icon"
              src="<?php echo get_template_directory_uri(); ?>/assets/img/favicon.svg"
              alt="<?php esc_attr_e( 'Fradel-Med', 'fradelmed-theme' ); ?>"
              decoding="async"
            >
          </a>
          <span class="checkout-header__spacer" aria-hidden="true"></span>
        </div>
      </div>
    <?php else : ?>
      <div class="site-header__top"></div>

      <div class="site-header__main">
        <div class="container site-header__grid">
          <button
            class="nav-toggle"
            id="navToggle"
            type="button"
            aria-expanded="false"
            aria-controls="siteNav"
            aria-label="<?php esc_attr_e( 'Abrir menu', 'fradelmed-theme' ); ?>"
          >
            <span class="nav-toggle__bars" aria-hidden="true">
              <span class="nav-toggle__bar"></span>
              <span class="nav-toggle__bar"></span>
              <span class="nav-toggle__bar"></span>
            </span>
          </button>

          <a class="site-brand" href="<?php echo home_url('/'); ?>">
            <span class="site-brand__mark">
              <img
                class="brand-image"
                src="<?php echo esc_url( $brand_logo_url . '?v=' . $brand_logo_ver ); ?>"
                alt="Logotipo Fradel-Med"
                decoding="async"
              >
            </span>
          </a>

          <nav id="siteNav" class="main-nav" aria-label="Principal">
            <div class="main-nav__inner">
              <div class="main-nav__header">
                <span class="main-nav__title"><?php esc_html_e( 'Menu', 'fradelmed-theme' ); ?></span>
                <button class="nav-close" type="button" data-nav-close aria-label="<?php esc_attr_e( 'Fechar menu', 'fradelmed-theme' ); ?>">×</button>
              </div>
              <ul class="main-nav__list" data-magnetic>
                <li><a href="<?php echo site_url('/cirurgia-geral/'); ?>">Cirurgia Geral</a></li>
                <li><a href="<?php echo site_url('/ginecologia/'); ?>">Ginecologia</a></li>
                <li><a href="<?php echo site_url('/proctologia/'); ?>">Proctologia</a></li>
                <li><a href="<?php echo site_url('/traqueostomia/'); ?>">Traqueostomia</a></li>
                <li><a href="<?php echo site_url('/institucional/'); ?>">Institucional</a></li>
                <li><a href="<?php echo site_url('/contato/'); ?>">Contato</a></li>
              </ul>
              <div class="main-nav__footer">
                <a class="main-nav__link" href="<?php echo esc_url( site_url( '/my-account/' ) ); ?>"><?php esc_html_e( 'Minha conta', 'fradelmed-theme' ); ?></a>
                <a class="main-nav__link" href="<?php echo function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : '#'; ?>"><?php esc_html_e( 'Carrinho', 'fradelmed-theme' ); ?></a>
              </div>
            </div>
          </nav>

          <div class="site-header__actions">
            <div class="header-search header-search--desktop">
              <form
                role="search"
                method="get"
                class="header-search__form"
                action="<?php echo esc_url( home_url( '/' ) ); ?>"
              >
                <label class="screen-reader-text" for="headerSearchInputDesktop"><?php esc_html_e( 'Buscar produtos', 'fradelmed-theme' ); ?></label>
                <input
                  type="search"
                  id="headerSearchInputDesktop"
                  class="header-search__input"
                  name="s"
                  placeholder="Buscar produtos"
                  value="<?php echo get_search_query(); ?>"
                >
                <input type="hidden" name="post_type" value="product">
                <button class="header-search__button" type="submit" aria-label="<?php esc_attr_e( 'Buscar produtos', 'fradelmed-theme' ); ?>">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" aria-hidden="true" focusable="false">
                    <rect width="256" height="256" fill="none"/>
                    <circle cx="112" cy="112" r="80" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/>
                    <line x1="168.57" y1="168.57" x2="224" y2="224" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/>
                  </svg>
                </button>
              </form>
            </div>

            <a
              href="<?php echo esc_url( site_url( '/my-account/' ) ); ?>"
              class="header-account"
              aria-label="<?php esc_attr_e( 'Minha conta', 'fradelmed-theme' ); ?>"
            >
              <svg class="header-account__icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                <circle cx="12" cy="8" r="4" fill="none" stroke="currentColor" stroke-width="1.8" />
                <path d="M4 20c0-3.3 3.6-6 8-6s8 2.7 8 6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
              </svg>
            </a>

            <a
              href="<?php echo function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : '#'; ?>"
              id="headerCartBtn"
              class="header-cart"
              aria-label="<?php esc_attr_e( 'Ver carrinho', 'fradelmed-theme' ); ?>"
            >
              <img
                src="<?php echo get_template_directory_uri(); ?>/assets/img/shopping-cart-simple.svg"
                alt="Cart"
              >

              <span id="cartBadge" class="cart-badge">
                <?php
                if ( function_exists( 'WC' ) && WC()->cart ) {
                  echo WC()->cart->get_cart_contents_count();
                } else {
                  echo '0';
                }
                ?>
              </span>
            </a>
          </div>

          <button id="theme-toggle" class="theme-toggle" aria-label="Alternar tema">
            <i class="ph-sun-fill" style="display:none;"></i>
            <i class="ph-moon-fill"></i>
          </button>
        </div>

        <div class="site-header__search-row">
          <div class="container">
            <form
              role="search"
              method="get"
              class="header-search__form header-search__form--row"
              action="<?php echo esc_url( home_url( '/' ) ); ?>"
            >
              <label class="screen-reader-text" for="headerSearchInputMobile"><?php esc_html_e( 'Buscar produtos', 'fradelmed-theme' ); ?></label>
              <input
                type="search"
                id="headerSearchInputMobile"
                class="header-search__input"
                name="s"
                placeholder="Buscar produtos"
                value="<?php echo get_search_query(); ?>"
              >
              <input type="hidden" name="post_type" value="product">
              <button class="header-search__button" type="submit" aria-label="<?php esc_attr_e( 'Buscar produtos', 'fradelmed-theme' ); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" aria-hidden="true" focusable="false">
                  <rect width="256" height="256" fill="none"/>
                  <circle cx="112" cy="112" r="80" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/>
                  <line x1="168.57" y1="168.57" x2="224" y2="224" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/>
                </svg>
              </button>
            </form>
          </div>
        </div>

        <div class="nav-overlay" data-nav-close aria-hidden="true"></div>
      </div>
    <?php endif; ?>
  </header>
