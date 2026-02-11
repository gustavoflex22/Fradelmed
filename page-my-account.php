<?php
/**
 * Template: Minha Conta (layout dedicado)
 */
?>
<!doctype html>
<html <?php language_attributes(); ?> class="transition-colors duration-500">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Fradel-Med | Minha conta</title>
  <meta name="description" content="Acesse sua conta Fradel-Med ou crie um cadastro novo.">

  <link rel="icon" type="image/svg+xml" href="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/img/favicon.svg">
  <?php
    $base_css_path = get_template_directory() . '/assets/css/base.css';
    $base_css_ver  = file_exists( $base_css_path ) ? filemtime( $base_css_path ) : '1.0';
  ?>
  <link rel="stylesheet" href="<?php echo esc_url( get_template_directory_uri() . '/assets/css/base.css?v=' . $base_css_ver ); ?>">

  <?php wp_head(); ?>
</head>
<body <?php body_class( 'account-standalone' ); ?>>
  <?php wp_body_open(); ?>

  <main id="conteudo" class="account-page">
    <div class="account-page__inner">
      <a class="account-page__brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php esc_attr_e( 'Voltar para a página inicial', 'fradelmed-theme' ); ?>">
        <img
          class="account-page__brand-img"
          src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/img/logotipo-fradel-med-icone.png"
          alt="Logotipo Fradel-Med"
          decoding="async"
        >
      </a>
      <a class="account-page__back" href="<?php echo esc_url( home_url( '/' ) ); ?>">
        <?php esc_html_e( 'Voltar ao site', 'fradelmed-theme' ); ?>
      </a>

      <div class="account-page__panel">
        <div class="account-page__intro">
          <p class="account-page__eyebrow">Bem-vindo(a)</p>
          <h1 class="account-page__title">Minha conta</h1>
          <p class="account-page__subtitle">Acesse sua conta ou crie um cadastro novo para acompanhar pedidos.</p>
        </div>

        <div class="account-page__content">
          <?php echo do_shortcode( '[woocommerce_my_account]' ); ?>
        </div>
      </div>
    </div>
  </main>

  <?php wp_footer(); ?>
</body>
</html>
