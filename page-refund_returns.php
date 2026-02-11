<?php
/**
 * Template: Refund Returns
 */
get_header();
?>

<main id="conteudo" class="page-content page-content--policy">
  <section class="section section--light">
    <div class="container">
      <?php if ( have_posts() ) : ?>
        <?php while ( have_posts() ) : the_post(); ?>
          <article class="policy-card">
            <header class="section__header--compact">
              <span class="eyebrow">Politica</span>
              <h1><?php the_title(); ?></h1>
            </header>
            <div class="policy-content">
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
