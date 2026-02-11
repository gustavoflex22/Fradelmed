<?php
/**
 * Template: Rastrear pedidos
 */
get_header();
?>

<main id="conteudo" class="page-content page-content--tracker">
  <section class="section section--light">
    <div class="container">
      <?php if ( have_posts() ) : ?>
        <?php while ( have_posts() ) : the_post(); ?>
          <article class="tracker-card">
            <header class="section__header--compact">
              <span class="eyebrow">Rastreamento</span>
              <h1><?php the_title(); ?></h1>
            </header>
            <div class="tracker-content">
              <?php if ( function_exists( 'woocommerce_order_tracking' ) ) : ?>
                <?php echo do_shortcode( '[woocommerce_order_tracking]' ); ?>
              <?php endif; ?>
              <?php the_content(); ?>
            </div>
          </article>
        <?php endwhile; ?>
      <?php endif; ?>
    </div>
  </section>
</main>

<?php
get_footer();
