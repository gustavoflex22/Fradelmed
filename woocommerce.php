<?php
/**
 * Template base para páginas do WooCommerce
 * (shop, single product, cart, checkout, etc.)
 */

get_header(); ?>

<main id="conteudo" class="site-main">
  <div class="container">
    <?php
      // Deixa o WooCommerce decidir se é produto, cart, checkout, etc.
      woocommerce_content();
    ?>
  </div>
</main>

<?php get_footer();
