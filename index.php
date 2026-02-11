<?php
/**
 * Template básico para páginas e posts
 */
get_header();
?>

<main id="conteudo" class="page-content">
  <?php
  if ( have_posts() ) :
    while ( have_posts() ) :
      the_post();
      $term = null;
      if ( is_page() && function_exists( 'wc_get_product' ) && taxonomy_exists( 'product_cat' ) ) {
        $slug = get_post_field( 'post_name', get_the_ID() );
        $term = get_term_by( 'slug', $slug, 'product_cat' );
      }

      if ( $term && ! is_wp_error( $term ) ) :
        $page_title = get_the_title();
        $lead_text  = has_excerpt() ? get_the_excerpt() : '';
        $term_desc  = term_description( $term, 'product_cat' );
        if ( '' === $lead_text ) {
          $lead_text = $term_desc;
        }
        ?>
        <section class="section section--light">
          <div class="container">
            <header class="section__header--compact">
              <span class="eyebrow">Portfólio cirúrgico</span>
              <h1><?php echo esc_html( $page_title ); ?></h1>
              <?php if ( $lead_text ) : ?>
                <div class="section__lead"><?php echo wp_kses_post( $lead_text ); ?></div>
              <?php endif; ?>
            </header>

            <?php
            $products = new WP_Query(
              array(
                'post_type'      => 'product',
                'posts_per_page' => -1,
                'post_status'    => 'publish',
                'tax_query'      => array(
                  array(
                    'taxonomy' => 'product_cat',
                    'field'    => 'term_id',
                    'terms'    => $term->term_id,
                  ),
                ),
              )
            );

            if ( $products->have_posts() ) :
              ?>
              <ul id="prodGrid" class="product-grid">
                <?php
                while ( $products->have_posts() ) :
                  $products->the_post();
                  ?>
                  <li>
                    <?php echo fradelmed_get_product_card_html( get_the_ID() ); ?>
                  </li>
                  <?php
                endwhile;
                ?>
              </ul>
              <?php
            else :
              ?>
              <p>Nenhum produto encontrado nesta categoria.</p>
              <?php
            endif;
            wp_reset_postdata();
            ?>
          </div>
        </section>
        <?php
      else :
        the_content();
      endif;
    endwhile;
  else :
    echo '<p>Conteúdo não encontrado.</p>';
  endif;
  ?>
</main>

<?php
get_footer();
