<?php
/**
 * Search results template
 */
get_header();

$post_type = get_query_var( 'post_type' );
$is_product_search = false;
if ( is_array( $post_type ) ) {
  $is_product_search = in_array( 'product', $post_type, true );
} else {
  $is_product_search = ( 'product' === $post_type );
}
?>

<main id="conteudo" class="page-content">
  <?php if ( $is_product_search && function_exists( 'wc_get_product' ) ) : ?>
    <?php
    global $wp_query;
    $total = isset( $wp_query->found_posts ) ? (int) $wp_query->found_posts : 0;
    $results_label = ( 1 === $total ) ? 'resultado' : 'resultados';
    ?>
    <section class="section section--light">
      <div class="container">
        <header class="section__header--compact">
          <span class="eyebrow">Resultados da pesquisa</span>
          <h1><?php echo esc_html( sprintf( 'Resultados da pesquisa por: "%s"', get_search_query() ) ); ?></h1>
          <div class="section__lead"><?php echo esc_html( sprintf( 'Mostrando %d %s', $total, $results_label ) ); ?></div>
        </header>

        <?php if ( have_posts() ) : ?>
          <ul id="prodGrid" class="product-grid">
            <?php while ( have_posts() ) : ?>
              <?php the_post(); ?>
              <li>
                <?php echo fradelmed_get_product_card_html( get_the_ID() ); ?>
              </li>
            <?php endwhile; ?>
          </ul>
        <?php else : ?>
          <p>Nenhum produto encontrado para esta pesquisa.</p>
        <?php endif; ?>
      </div>
    </section>
  <?php else : ?>
    <section class="section section--light">
      <div class="container">
        <header class="section__header--compact">
          <span class="eyebrow">Busca</span>
          <h1><?php echo esc_html( sprintf( 'Resultados da pesquisa por: "%s"', get_search_query() ) ); ?></h1>
        </header>

        <?php if ( have_posts() ) : ?>
          <ul class="product-grid">
            <?php
            while ( have_posts() ) :
              the_post();
              ?>
              <li class="product-card">
                <h2 class="product-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                <?php if ( has_excerpt() ) : ?>
                  <p class="product-card__meta"><?php echo esc_html( get_the_excerpt() ); ?></p>
                <?php endif; ?>
              </li>
            <?php endwhile; ?>
          </ul>
        <?php else : ?>
          <p>Nenhum resultado encontrado.</p>
        <?php endif; ?>
      </div>
    </section>
  <?php endif; ?>
</main>

<?php
get_footer();
