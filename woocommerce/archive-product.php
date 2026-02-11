<?php
/**
 * Product archive template (shop, category, tag, search).
 */

defined( 'ABSPATH' ) || exit;

get_header();

global $wp_query;
$total = isset( $wp_query->found_posts ) ? (int) $wp_query->found_posts : 0;
$results_label = ( 1 === $total ) ? 'resultado' : 'resultados';
$lead_text = '';
$eyebrow = 'Portfólio cirúrgico';

if ( is_search() ) {
  $eyebrow = 'Resultados da pesquisa';
  $lead_text = sprintf( 'Mostrando %d %s', $total, $results_label );
} elseif ( is_product_category() || is_product_tag() ) {
  $term = get_queried_object();
  if ( $term && ! is_wp_error( $term ) ) {
    $lead_text = term_description( $term, $term->taxonomy );
  }
}
?>

<main id="conteudo" class="page-content">
  <section class="section section--light">
    <div class="container">
      <header class="section__header--compact">
        <span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
        <?php if ( is_search() ) : ?>
          <h1><?php echo esc_html( sprintf( 'Resultados da pesquisa por: "%s"', get_search_query() ) ); ?></h1>
        <?php else : ?>
          <h1><?php echo esc_html( woocommerce_page_title( false ) ); ?></h1>
        <?php endif; ?>
        <?php if ( $lead_text ) : ?>
          <div class="section__lead"><?php echo wp_kses_post( $lead_text ); ?></div>
        <?php endif; ?>
      </header>

      <?php if ( woocommerce_product_loop() ) : ?>
        <ul id="prodGrid" class="product-grid">
          <?php while ( have_posts() ) : ?>
            <?php the_post(); ?>
            <li>
              <?php echo fradelmed_get_product_card_html( get_the_ID() ); ?>
            </li>
          <?php endwhile; ?>
        </ul>

        <?php do_action( 'woocommerce_after_shop_loop' ); ?>
      <?php else : ?>
        <?php do_action( 'woocommerce_no_products_found' ); ?>
      <?php endif; ?>
    </div>
  </section>
</main>

<?php
get_footer();
