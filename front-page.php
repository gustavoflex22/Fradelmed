<?php
// Template da página inicial usando o layout estático
get_header();
$theme_img = get_template_directory_uri() . '/assets/img/';
$theme_dir = get_template_directory() . '/assets/img/';
?>
<main id="conteudo">
    <section class="hero hero--video">
      <div class="hero__media" aria-hidden="true">
        <video class="hero__video is-active" autoplay muted playsinline webkit-playsinline loop preload="auto" controlslist="nodownload noplaybackrate nofullscreen" disablepictureinpicture>
          <source src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/12686081_3840_2160_30fps.mp4' ); ?>" type="video/mp4">
        </video>
      </div>
      <div class="container hero__content hero__content--solo">
        <div class="hero__text">
          <p class="eyebrow">Desde 1999 apoiando a saúde</p>
          <h1>Compromisso com a qualidade e melhoria contínua.</h1>
          <p>A FRADEL-MED por seu comportamento empresarial, preza por qualidade para proporcionar o máximo de conforto e bem estar ao seu cliente, e aos seus Fornecedores e Parceiros o profissionalismo impera como uma máxima.</p>
          <div class="hero__actions">
            <a class="button" href="#especialidades">Ver portfólio</a>
            <a class="button button--outline" href="#destaques">Mais vendidos</a>
          </div>
        </div>
      </div>
    </section>

    <section id="prova" class="section section--light">
      <div class="container">
        <header class="section__header section__header--center">
          <h2>Confiança que vira resultado no centro cirúrgico</h2>
          <p class="section__lead">Atendimento consultivo, estoque pronto e padrão técnico constante em cada entrega.</p>
        </header>
        <div class="stats-grid">
          <div class="stat-card">
            <span class="stat-number">57 anos</span>
            <span class="stat-label">atuando na área</span>
          </div>
          <div class="stat-card">
            <span class="stat-number">24h</span>
            <span class="stat-label">resposta em cotações</span>
          </div>
          <div class="stat-card">
            <span class="stat-number">100%</span>
            <span class="stat-label">rastreabilidade</span>
          </div>
          <div class="stat-card">
            <span class="stat-number">Brasil</span>
            <span class="stat-label">entregas em todo o país</span>
          </div>
        </div>
      </div>
    </section>

    <section id="diferenciais" class="section">
      <div class="container">
        <header class="section__header section__header--center">
          <h2>Mais previsibilidade, menos atrito na compra</h2>
        </header>
        <div class="grid grid--three feature-grid">
          <div class="feature-card">
            <div class="feature-card__icon">
              <?php
              $icon_file = 'confirmacao.svg';
              if ( file_exists( $theme_dir . $icon_file ) ) :
                ?>
                <img src="<?php echo esc_url( $theme_img . $icon_file ); ?>" alt="" aria-hidden="true" loading="lazy" decoding="async">
                <?php
              else :
                ?>
                <i class="ph ph-shield-check" aria-hidden="true"></i>
                <?php
              endif;
              ?>
            </div>
            <h3>Garantia estendida</h3>
            <p>Produtos com procedência e controle de qualidade em lote.</p>
          </div>
          <div class="feature-card">
            <div class="feature-card__icon">
              <?php
              $icon_file = 'estoque.svg';
              if ( file_exists( $theme_dir . $icon_file ) ) :
                ?>
                <img src="<?php echo esc_url( $theme_img . $icon_file ); ?>" alt="" aria-hidden="true" loading="lazy" decoding="async">
                <?php
              else :
                ?>
                <i class="ph ph-truck" aria-hidden="true"></i>
                <?php
              endif;
              ?>
            </div>
            <h3>Estoque estrategico</h3>
            <p>Reposição programada e disponibilidade para urgências.</p>
          </div>
          <div class="feature-card">
            <div class="feature-card__icon">
              <?php
              $icon_file = 'comunicacao.svg';
              if ( file_exists( $theme_dir . $icon_file ) ) :
                ?>
                <img src="<?php echo esc_url( $theme_img . $icon_file ); ?>" alt="" aria-hidden="true" loading="lazy" decoding="async">
                <?php
              else :
                ?>
                <i class="ph ph-stethoscope" aria-hidden="true"></i>
                <?php
              endif;
              ?>
            </div>
            <h3>Suporte tecnico</h3>
            <p>Ajuda especialista na escolha de instrumentos e kits.</p>
          </div>
        </div>
      </div>
    </section>

    <section id="destaques" class="section section--light">
      <div class="container">
        <header class="section__header section__header--center">
          <p class="eyebrow">Mais vendidos</p>
          <h2>Linhas campeãs: cânulas, conjuntos, válvulas </h2>
          <div class="tag-list">
            <span class="tag">Cânulas</span>
            <span class="tag">Conjuntos</span>
            <span class="tag">Válvulas</span>
            <span class="tag">Kits</span>
          </div>
        </header>

        <?php
        $best_terms    = array( 'canulas', 'conjuntos', 'estiletes', 'kits' );
        $priority_ids  = array( 459, 776 );
        $exclude_words = array( 'valvula', 'válvula', 'adaptador' );
        $display_ids   = array();

        if ( function_exists( 'wc_get_product' ) ) {
          foreach ( $priority_ids as $priority_id ) {
            $product = wc_get_product( $priority_id );
            if ( $product && 'publish' === get_post_status( $priority_id ) ) {
              $display_ids[] = $priority_id;
            }
          }

          $fill_needed = 4 - count( $display_ids );
          if ( $fill_needed > 0 ) {
            $fill_query = new WP_Query(
              array(
                'post_type'      => 'product',
                'posts_per_page' => 8,
                'post_status'    => 'publish',
                'post__not_in'   => $display_ids,
                'tax_query'      => array(
                  array(
                    'taxonomy' => 'product_cat',
                    'field'    => 'slug',
                    'terms'    => $best_terms,
                  ),
                ),
              )
            );

            if ( $fill_query->have_posts() ) {
              while ( $fill_query->have_posts() && count( $display_ids ) < 4 ) {
                $fill_query->the_post();
                $title = get_the_title();
                $skip  = false;
                foreach ( $exclude_words as $word ) {
                  if ( false !== stripos( $title, $word ) ) {
                    $skip = true;
                    break;
                  }
                }
                if ( $skip ) {
                  continue;
                }
                $display_ids[] = get_the_ID();
              }
              wp_reset_postdata();
            }
          }

          $fill_needed = 4 - count( $display_ids );
          if ( $fill_needed > 0 ) {
            $fallback_query = new WP_Query(
              array(
                'post_type'      => 'product',
                'posts_per_page' => 8,
                'post_status'    => 'publish',
                'post__not_in'   => $display_ids,
              )
            );

            if ( $fallback_query->have_posts() ) {
              while ( $fallback_query->have_posts() && count( $display_ids ) < 4 ) {
                $fallback_query->the_post();
                $title = get_the_title();
                $skip  = false;
                foreach ( $exclude_words as $word ) {
                  if ( false !== stripos( $title, $word ) ) {
                    $skip = true;
                    break;
                  }
                }
                if ( $skip ) {
                  continue;
                }
                $display_ids[] = get_the_ID();
              }
              wp_reset_postdata();
            }
          }
        }
        ?>

        <?php if ( ! empty( $display_ids ) ) : ?>
          <ul class="product-grid best-sellers-grid">
            <?php
            foreach ( $display_ids as $product_id ) :
              $product    = wc_get_product( $product_id );
              if ( ! $product || 'product' !== get_post_type( $product_id ) || 'publish' !== get_post_status( $product_id ) ) {
                continue;
              }
              $sku        = $product ? $product->get_sku() : '';
              $price      = $product ? $product->get_price_html() : '';
              $image      = get_the_post_thumbnail( $product_id, 'woocommerce_thumbnail', array( 'loading' => 'lazy', 'decoding' => 'async' ) );
              $permalink  = get_permalink( $product_id );
              $card_class = 'product-card--grid';
              $image_id   = get_post_thumbnail_id( $product_id );
              if ( $image_id ) {
                $meta = wp_get_attachment_metadata( $image_id );
                if ( ! empty( $meta['width'] ) && ! empty( $meta['height'] ) ) {
                  $ratio = (float) $meta['width'] / max( 1, (float) $meta['height'] );
                  if ( $ratio >= 1.6 ) {
                    $card_class .= ' product-card--wide';
                  }
                }
              }
              if ( ! $image && function_exists( 'wc_placeholder_img' ) ) {
                $image = wc_placeholder_img( 'woocommerce_thumbnail' );
              }
              ?>
              <li>
                <a class="<?php echo esc_attr( $card_class ); ?>" href="<?php echo esc_url( $permalink ); ?>" aria-label="<?php echo esc_attr( get_the_title( $product_id ) ); ?>">
                  <div class="product-card__image"><?php echo $image; ?></div>
                  <div class="product-card__body">
                    <div class="product-card__title"><?php echo esc_html( get_the_title( $product_id ) ); ?></div>
                    <?php if ( $sku ) : ?>
                      <div class="product-card__meta"><?php echo esc_html( 'Código ' . $sku ); ?></div>
                    <?php endif; ?>
                    <div class="product-card__footer">
                      <div class="product-card__price"><?php echo wp_kses_post( $price ); ?></div>
                      <span class="button button--sm">Ver produto</span>
                    </div>
                  </div>
                </a>
              </li>
              <?php
            endforeach;
            ?>
          </ul>
        <?php else : ?>
          <p>Nenhum produto encontrado no momento.</p>
        <?php endif; ?>
      </div>
    </section>


    <section id="especialidades" class="section">
      <div class="container">
        <header class="section__header">
          <p class="eyebrow">Especialidades atendidas</p>
        </header>
        <div class="grid grid--four">
          <a class="card card--link" href="<?php echo esc_url( site_url( '/cirurgia-geral/' ) ); ?>">
            <div class="card__media" aria-hidden="true" style="--count:3;">
              <span style="--img:url('<?php echo esc_url( $theme_img . rawurlencode( 'aparelhor cir1.webp' ) ); ?>'); --i:0;"></span>
              <span style="--img:url('<?php echo esc_url( $theme_img . rawurlencode( 'aparelho cir2.jpg' ) ); ?>'); --i:1;"></span>
              <span style="--img:url('<?php echo esc_url( $theme_img . rawurlencode( 'aparelho cir3.webp' ) ); ?>'); --i:2;"></span>
            </div>
            <h3>Cirurgia Geral</h3>
            <p>Pinças, afastadores, caixas de instrumental e acessórios para procedimentos de média e alta complexidade.</p>
            <span class="card__link">Ver catálogo</span>
          </a>
          <a class="card card--link" href="<?php echo esc_url( site_url( '/traqueostomia/' ) ); ?>">
            <div class="card__media" aria-hidden="true" style="--count:4;">
              <span style="--img:url('<?php echo esc_url( $theme_img . rawurlencode( 'traqueostomia1.jpg' ) ); ?>'); --i:0;"></span>
              <span style="--img:url('<?php echo esc_url( $theme_img . rawurlencode( 'traqueostomia2.jpg' ) ); ?>'); --i:1;"></span>
              <span style="--img:url('<?php echo esc_url( $theme_img . rawurlencode( 'traqueostomia3.jpg' ) ); ?>'); --i:2;"></span>
              <span style="--img:url('<?php echo esc_url( $theme_img . rawurlencode( 'traqueostomia4.jpg' ) ); ?>'); --i:3;"></span>
            </div>
            <h3>Traqueostomia</h3>
            <p>Cânulas metálicas.</p>
            <span class="card__link">Ver catálogo</span>
          </a>
          <a class="card card--link" href="<?php echo esc_url( site_url( '/proctologia/' ) ); ?>">
            <div class="card__media" aria-hidden="true" style="--count:2;">
              <span style="--img:url('<?php echo esc_url( $theme_img . rawurlencode( 'proctologia1.webp' ) ); ?>'); --i:0;"></span>
              <span style="--img:url('<?php echo esc_url( $theme_img . rawurlencode( 'proctologia2.jpg' ) ); ?>'); --i:1;"></span>
            </div>
            <h3>Proctologia</h3>
            <p>Conjuntos para anuscopia, ligaduras elásticas e soluções especializadas.</p>
            <span class="card__link">Ver catálogo</span>
          </a>
        </div>
      </div>
    </section>

    <section id="contato" class="section section--highlight">
      <div class="container contact">
        <div class="contact__text">
          <p class="eyebrow">Fale conosco</p>
          <h2>Entre em contato com a Fradel-Med</h2>
          <p>Estamos à disposição para esclarecer dúvidas, enviar catálogos completos, apoiar na montagem de enxoval e oferecer propostas comerciais personalizadas.</p>
          <ul class="contact__topics">
            <li>Suporte técnico</li>
            <li>Orçamentos</li>
            <li>Informações de produtos</li>
            <li>Parcerias comerciais</li>
          </ul>
          <div class="contact__hours">
            <strong>Atendimento de segunda a sexta</strong>
            <span>Horário: das 8h às 17h</span>
          </div>
          <div class="contact__map" aria-label="Mapa da Fradel-Med">
            <iframe
              src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3954.5385789755305!2d-46.659696624251104!3d-23.649619364841403!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x94ce5a9668d44d61%3A0x8ea0d000f63c2409!2sFradel%20Med%20Ind%20Com%20Ap%20M%C3%A9dicos%20Ltda%20ME!5e1!3m2!1sen!2sbr!4v1769110724843!5m2!1sen!2sbr"
              allowfullscreen
              loading="lazy"
              referrerpolicy="no-referrer-when-downgrade"
              title="Mapa da Fradel-Med"
            ></iframe>
          </div>
          <ul class="contact__channels"></ul>
        </div>
        <form class="contact__form" action="#" method="post">
          <div class="form-group">
            <label for="contact-name">Nome completo</label>
            <input id="contact-name" name="nome" type="text" autocomplete="name">
          </div>
          <div class="form-group">
            <label for="contact-company">Instituição</label>
            <input id="contact-company" name="instituicao" type="text" autocomplete="organization">
          </div>
          <div class="form-group">
            <label for="contact-email">E-mail corporativo</label>
            <input id="contact-email" name="email" type="email" autocomplete="email">
          </div>
          <div class="form-group">
            <label for="contact-message">Necessidade</label>
            <textarea id="contact-message" name="mensagem" rows="4"></textarea>
          </div>
          <button class="button" type="submit">Quero falar com a Fradel-Med</button>
        </form>
      </div>
      <nav class="contact-marquee" aria-label="Linhas e soluções Fradel-Med">
        <div class="contact-marquee__track">
          <a href="<?php echo esc_url( site_url( '/cirurgia-geral/' ) ); ?>" class="contact-marquee__item">Cirurgia Geral</a>
          <a href="<?php echo esc_url( site_url( '/ginecologia/' ) ); ?>" class="contact-marquee__item">Ginecologia</a>
          <a href="<?php echo esc_url( site_url( '/proctologia/' ) ); ?>" class="contact-marquee__item">Proctologia</a>
          <a href="<?php echo esc_url( site_url( '/traqueostomia/' ) ); ?>" class="contact-marquee__item">Traqueostomia</a>
          <span class="contact-marquee__item">Instrumentais em aço inox</span>
          <span class="contact-marquee__item">Kits cirúrgicos sob medida</span>
          <span class="contact-marquee__item">Reposição programada de estoque</span>
          <span class="contact-marquee__item">Atendimento em todo o Brasil</span>
          <!-- duplicação para loop contínuo -->
          <a href="<?php echo esc_url( site_url( '/cirurgia-geral/' ) ); ?>" class="contact-marquee__item">Cirurgia Geral</a>
          <a href="<?php echo esc_url( site_url( '/ginecologia/' ) ); ?>" class="contact-marquee__item">Ginecologia</a>
          <a href="<?php echo esc_url( site_url( '/proctologia/' ) ); ?>" class="contact-marquee__item">Proctologia</a>
          <a href="<?php echo esc_url( site_url( '/traqueostomia/' ) ); ?>" class="contact-marquee__item">Traqueostomia</a>
          <span class="contact-marquee__item">Instrumentais em aço inox</span>
          <span class="contact-marquee__item">Kits cirúrgicos sob medida</span>
          <span class="contact-marquee__item">Reposição programada de estoque</span>
          <span class="contact-marquee__item">Atendimento em todo o Brasil</span>
        </div>
      </nav>
    </section>
  </main>

  

<?php
get_footer();
