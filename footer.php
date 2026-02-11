<?php $is_cart_page = function_exists( 'is_cart' ) && is_cart(); ?>

<?php if ( ! $is_cart_page ) : ?>
  <footer class="site-footer">
    <div class="container site-footer__grid">
      <div>
        <div class="site-brand">
          <a class="site-brand__mark" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php esc_attr_e( 'Voltar para a página inicial', 'fradelmed-theme' ); ?>">
            <img class="brand-image" src="<?php echo get_template_directory_uri(); ?>/assets/img/logotipo-fradel-med-icone.png" alt="Logotipo Fradel‑Med" decoding="async">
          </a>
        </div>
        <p class="site-brand__meta">CNPJ: 61.089.270/0001-05<br>Inscrição Estadual: 154.278.230.116</p>
      </div>
      <div>
        <ul>
          <li><a href="<?php echo esc_url( home_url( '/#especialidades' ) ); ?>">Especialidades</a></li>
          <li><a href="<?php echo site_url('/institucional/'); ?>">Quem somos</a></li>
          <li><a href="<?php echo site_url('/como-comprar/'); ?>">Como comprar</a></li>
          <li><a href="<?php echo site_url('/contato/'); ?>">Fale conosco</a></li>
        </ul>
      </div>
      <div>
        <h3>Contato</h3>
        <ul>
          <li class="contact-item">
            <i class="ph ph-whatsapp-logo"></i>
            <span class="contact-text">
              <span class="contact-label">WhatsApp</span>
              <span class="contact-detail">(11) 98263-4766</span>
            </span>
          </li>
          <li class="contact-item">
            <i class="ph ph-phone"></i>
            <span class="contact-text">
              <span class="contact-label">Telefone fixo</span>
              <span class="contact-detail">(11) 5562-3541</span>
            </span>
          </li>
          <li class="contact-item">
            <i class="ph ph-envelope-simple"></i>
            <span class="contact-text">
              <span class="contact-label">Email</span>
              <span class="contact-detail contact-detail--nowrap">sac@fradel-med.com.br</span>
            </span>
          </li>
          <li class="contact-item">
            <i class="ph ph-map-pin"></i>
            <span class="contact-text">
              <span class="contact-label">Matriz</span>
              <span class="contact-detail">Rua Tenente Américo Moretti, 579 – Vila Santa Catarina, São Paulo/SP – CEP 04372-062</span>
            </span>
          </li>
          <li class="contact-item">
            <i class="ph ph-map-pin"></i>
            <span class="contact-text">
              <span class="contact-label">Vendas</span>
              <span class="contact-detail">Av. José Estevão de Magalhães, 50 - Jabaquara, São Paulo - SP, 04332-050</span>
            </span>
          </li>
        </ul>
      </div>
      <div>
        <h3>Documentos</h3>
        <ul>
          <li><a href="<?php echo site_url('/rastrear-pedidos/'); ?>">Rastrear pedidos</a></li>
          <li><a href="<?php echo site_url('/refund_returns/'); ?>">Política de Trocas</a></li>
          <li><a href="<?php echo site_url('/termos-de-uso/'); ?>">Termos de Uso</a></li>
        </ul>
      </div>
    </div>
    <div class="site-footer__bottom">
      <div class="container">
        <p>© <span id="year"></span> Fradel-Med. Todos os direitos reservados.</p>
      </div>
    </div>
  </footer>

  <?php if ( function_exists( 'WC' ) ) : ?>
    <div id="miniCart" class="mini-cart">
      <div class="mini-cart__overlay" data-close></div>
      <aside class="mini-cart__panel">
        <header class="mini-cart__header">
          <div class="mini-cart__title"><i class="ph ph-shopping-cart"></i> Seu carrinho</div>
          <button class="mini-cart__close" data-close aria-label="Fechar">×</button>
        </header>
        <?php
        if ( function_exists( 'fradelmed_get_mini_cart_items_fragment' ) ) {
          echo fradelmed_get_mini_cart_items_fragment();
        } else {
          echo '<div id="miniCartItems" class="mini-cart__items"><p class="mini-cart__meta">Seu carrinho esta vazio.</p></div>';
        }
        ?>
        <footer class="mini-cart__footer">
          <div class="mini-cart__total">
            <span>Total</span>
            <?php
            if ( function_exists( 'fradelmed_get_mini_cart_total_fragment' ) ) {
              echo fradelmed_get_mini_cart_total_fragment();
            } else {
              echo '<span id="miniCartTotal">R$ 0,00</span>';
            }
            ?>
          </div>
          <a class="mini-cart__checkout" href="<?php echo esc_url( wc_get_checkout_url() ); ?>"><i class="ph ph-credit-card"></i> Finalizar compra</a>
        </footer>
      </aside>
    </div>
  <?php endif; ?>
<?php endif; ?>
  <a href="https://wa.me/5511982634766" class="whatsapp-float" target="_blank" rel="noopener" aria-label="Falar no WhatsApp">
    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/whatsapp-logo.png" alt="WhatsApp">
  </a>


<?php wp_footer(); ?>
</body>
</html>
