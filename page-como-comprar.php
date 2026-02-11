<?php
/**
 * Template: Como comprar
 */
get_header();
?>

<main id="conteudo" class="page-content page-content--guide">
  <section class="section section--light">
    <div class="container">
      <?php if ( have_posts() ) : ?>
        <?php while ( have_posts() ) : the_post(); ?>
          <article class="guide-card">
            <header class="section__header--compact">
              <span class="eyebrow">Guia</span>
              <h1><?php the_title(); ?></h1>
            </header>
            <div class="guide-content">
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
