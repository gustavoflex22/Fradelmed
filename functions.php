<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Theme setup.
function fradelmed_theme_setup() {
    add_theme_support( 'title-tag' );
}
add_action( 'after_setup_theme', 'fradelmed_theme_setup' );

function fradelmed_woocommerce_setup() {
    add_theme_support( 'woocommerce' );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );
}
add_action( 'after_setup_theme', 'fradelmed_woocommerce_setup' );

/**
 * Detect checkout-related requests early (before WooCommerce conditionals are reliable).
 */
function fradelmed_is_checkout_request() {
    $request_uri = isset( $_SERVER['REQUEST_URI'] ) ? (string) $_SERVER['REQUEST_URI'] : '';

    if ( isset( $_REQUEST['wc-ajax'] ) || isset( $_GET['wc-ajax'] ) ) {
        return true;
    }

    if ( '' !== $request_uri && false !== strpos( $request_uri, 'finalizar-compra' ) ) {
        return true;
    }

    if ( '' !== $request_uri && false !== strpos( $request_uri, 'wc-ajax=' ) ) {
        return true;
    }

    return false;
}

/**
 * Temporarily disable known-problematic checkout plugins only on checkout requests.
 * This is a surgical rollback to restore the native WooCommerce checkout flow.
 */
function fradelmed_filter_active_plugins_for_checkout( $plugins ) {
    if ( ! fradelmed_is_checkout_request() || ! is_array( $plugins ) ) {
        return $plugins;
    }

    $blocked = array(
        'fraud-and-scam-detection-for-woocommerce/fraud-scam-detection-woocommerce.php',
        'woo-checkout-field-editor-pro/checkout-form-designer.php',
    );

    return array_values( array_diff( $plugins, $blocked ) );
}
// Disabled for testing: allow plugins to load normally on checkout.
// add_filter( 'option_active_plugins', 'fradelmed_filter_active_plugins_for_checkout', 1 );

function fradelmed_filter_sitewide_plugins_for_checkout( $plugins ) {
    if ( ! fradelmed_is_checkout_request() || ! is_array( $plugins ) ) {
        return $plugins;
    }

    $blocked = array(
        'fraud-and-scam-detection-for-woocommerce/fraud-scam-detection-woocommerce.php',
        'woo-checkout-field-editor-pro/checkout-form-designer.php',
    );

    foreach ( $blocked as $plugin_file ) {
        if ( isset( $plugins[ $plugin_file ] ) ) {
            unset( $plugins[ $plugin_file ] );
        }
    }

    return $plugins;
}
// Disabled for testing: allow sitewide plugins to load normally on checkout.
// add_filter( 'site_option_active_sitewide_plugins', 'fradelmed_filter_sitewide_plugins_for_checkout', 1 );

// Assets.
function fradelmed_enqueue_assets() {
    wp_enqueue_style(
        'fradelmed-style',
        get_stylesheet_uri(),
        array(),
        '1.0'
    );

    $nav_path = get_template_directory() . '/assets/js/nav.js';
    if ( file_exists( $nav_path ) ) {
        wp_enqueue_script(
            'fradelmed-nav',
            get_template_directory_uri() . '/assets/js/nav.js',
            array(),
            filemtime( $nav_path ),
            true
        );
    }

    $cart_ui_path = get_template_directory() . '/assets/js/cart-ui.js';
    $skip_cart_ui = function_exists( 'is_checkout' ) && is_checkout();
    if ( function_exists( 'is_order_received_page' ) && is_order_received_page() ) {
        $skip_cart_ui = false;
    }

    if ( ! $skip_cart_ui && file_exists( $cart_ui_path ) ) {
        wp_enqueue_script(
            'fradelmed-cart-ui',
            get_template_directory_uri() . '/assets/js/cart-ui.js',
            array(),
            filemtime( $cart_ui_path ),
            true
        );
    }

    $footer_ui_path = get_template_directory() . '/assets/js/footer-ui.js';
    $skip_footer_ui = function_exists( 'is_checkout' ) && is_checkout();
    if ( function_exists( 'is_order_received_page' ) && is_order_received_page() ) {
        $skip_footer_ui = false;
    }

    if ( ! $skip_footer_ui && file_exists( $footer_ui_path ) ) {
        wp_enqueue_script(
            'fradelmed-footer-ui',
            get_template_directory_uri() . '/assets/js/footer-ui.js',
            array(),
            filemtime( $footer_ui_path ),
            true
        );
    }

    $checkout_fallback_path = get_template_directory() . '/assets/js/checkout-fallback.js';
    $load_checkout_fallback = function_exists( 'is_checkout' ) && is_checkout();
    if ( function_exists( 'is_order_received_page' ) && is_order_received_page() ) {
        $load_checkout_fallback = false;
    }

    if ( $load_checkout_fallback && file_exists( $checkout_fallback_path ) ) {
        wp_enqueue_script(
            'fradelmed-checkout-fallback',
            get_template_directory_uri() . '/assets/js/checkout-fallback.js',
            array( 'jquery' ),
            filemtime( $checkout_fallback_path ),
            true
        );
    }

    $hero_autoplay_path = get_template_directory() . '/assets/js/hero-autoplay.js';
    if ( file_exists( $hero_autoplay_path ) ) {
        wp_enqueue_script(
            'fradelmed-hero-autoplay',
            get_template_directory_uri() . '/assets/js/hero-autoplay.js',
            array(),
            filemtime( $hero_autoplay_path ),
            true
        );
    }
}
add_action( 'wp_enqueue_scripts', 'fradelmed_enqueue_assets' );

function fradelmed_cart_badge_fragment( $fragments ) {
    if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
        return $fragments;
    }

    ob_start();
    ?>
    <span id="cartBadge" class="cart-badge"><?php echo esc_html( WC()->cart->get_cart_contents_count() ); ?></span>
    <?php
    $fragments['#cartBadge'] = ob_get_clean();

    return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'fradelmed_cart_badge_fragment' );

function fradelmed_force_ship_to_different_address_unchecked( $checked ) {
    return false;
}
add_filter( 'woocommerce_ship_to_different_address_checked', 'fradelmed_force_ship_to_different_address_unchecked', 1000 );

function fradelmed_force_ship_to_different_address_value( $value, $input ) {
    if ( 'ship_to_different_address' === $input ) {
        return 0;
    }

    return $value;
}
add_filter( 'woocommerce_checkout_get_value', 'fradelmed_force_ship_to_different_address_value', 10, 2 );

function fradelmed_remove_order_actions_on_thankyou( $actions, $order ) {
    if ( function_exists( 'is_order_received_page' ) && is_order_received_page() ) {
        return array();
    }

    return $actions;
}
add_filter( 'woocommerce_my_account_my_orders_actions', 'fradelmed_remove_order_actions_on_thankyou', 20, 2 );

// WooCommerce tweaks.
function fradelmed_return_to_shop_redirect() {
    return home_url( '/' );
}
add_filter( 'woocommerce_return_to_shop_redirect', 'fradelmed_return_to_shop_redirect' );

function fradelmed_return_to_shop_text( $text ) {
    return __( 'Voltar para a página inicial', 'fradelmed-theme' );
}
add_filter( 'woocommerce_return_to_shop_text', 'fradelmed_return_to_shop_text' );

function fradelmed_remove_single_product_meta() {
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
}
add_action( 'init', 'fradelmed_remove_single_product_meta' );

add_filter( 'woocommerce_get_stock_html', '__return_empty_string' );

function fradelmed_single_product_zoom_options( $options ) {
    $options['magnify'] = 1.2;
    return $options;
}
add_filter( 'woocommerce_single_product_zoom_options', 'fradelmed_single_product_zoom_options' );

function fradelmed_open_cart_shell() {
    if ( function_exists( 'is_cart' ) && is_cart() ) {
        echo '<div class="fm-cart-shell">';
    }
}
add_action( 'woocommerce_before_cart', 'fradelmed_open_cart_shell', 20 );

function fradelmed_close_cart_shell() {
    if ( function_exists( 'is_cart' ) && is_cart() ) {
        echo '</div>';
    }
}
add_action( 'woocommerce_after_cart', 'fradelmed_close_cart_shell', 999 );

function fradelmed_get_mini_cart_items_html() {
    if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
        return '<p class="mini-cart__meta">Seu carrinho esta vazio.</p>';
    }

    $items = WC()->cart->get_cart();
    if ( empty( $items ) ) {
        return '<p class="mini-cart__meta">Seu carrinho esta vazio.</p>';
    }

    ob_start();
    foreach ( $items as $cart_item_key => $cart_item ) {
        $product = $cart_item['data'];
        if ( ! $product ) {
            continue;
        }

        $name       = $product->get_name();
        $qty        = (int) $cart_item['quantity'];
        $subtotal   = WC()->cart->get_product_subtotal( $product, $qty );
        $remove_url = wc_get_cart_remove_url( $cart_item_key );
        ?>
        <div class="mini-cart__item">
            <div>
                <div class="font-medium"><?php echo esc_html( $name ); ?></div>
                <div class="mini-cart__meta"><?php echo esc_html( 'Qtd: ' . $qty ); ?></div>
                <div class="mini-cart__meta"><?php echo wp_kses_post( $subtotal ); ?></div>
            </div>
            <a class="mini-cart__remove" href="<?php echo esc_url( $remove_url ); ?>" aria-label="<?php echo esc_attr( sprintf( 'Remover %s', $name ) ); ?>">×</a>
        </div>
        <?php
    }

    return ob_get_clean();
}

function fradelmed_get_mini_cart_items_fragment() {
    return '<div id="miniCartItems" class="mini-cart__items">' . fradelmed_get_mini_cart_items_html() . '</div>';
}

function fradelmed_get_mini_cart_total_fragment() {
    $total = 'R$ 0,00';
    if ( function_exists( 'WC' ) && WC()->cart ) {
        $total = WC()->cart->get_cart_total();
    }

    return '<span id="miniCartTotal">' . wp_kses_post( $total ) . '</span>';
}

function fradelmed_get_product_card_html( $product_id ) {
    if ( ! function_exists( 'wc_get_product' ) ) {
        return '';
    }

    $product = wc_get_product( $product_id );
    if ( ! $product ) {
        return '';
    }

    $sku        = $product->get_sku();
    $price      = $product->get_price_html();
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

    ob_start();
    ?>
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
    <?php

    return ob_get_clean();
}

function fradelmed_add_to_cart_fragments( $fragments ) {
    if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
        return $fragments;
    }

    $fragments['span#cartBadge'] = '<span id="cartBadge" class="cart-badge">' . (int) WC()->cart->get_cart_contents_count() . '</span>';
    $fragments['div#miniCartItems'] = fradelmed_get_mini_cart_items_fragment();
    $fragments['span#miniCartTotal'] = fradelmed_get_mini_cart_total_fragment();

    return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'fradelmed_add_to_cart_fragments' );

function fradelmed_cart_popup_message( $message, $products ) {
    if ( empty( $products ) || ! is_array( $products ) ) {
        return $message;
    }

    $product_id = array_key_first( $products );
    $product    = $product_id ? wc_get_product( $product_id ) : null;
    $name       = $product ? $product->get_name() : __( 'Item adicionado ao carrinho', 'fradelmed-theme' );
    $image      = $product ? $product->get_image( 'woocommerce_thumbnail', array( 'class' => 'fm-cart-popup__image', 'loading' => 'lazy', 'decoding' => 'async' ) ) : '';

    $cart_url     = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : '#';
    $checkout_url = function_exists( 'wc_get_checkout_url' ) ? wc_get_checkout_url() : '#';
    $shop_url     = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
    $cart_count   = ( function_exists( 'WC' ) && WC()->cart ) ? WC()->cart->get_cart_contents_count() : 0;
    $cart_label   = $cart_count ? sprintf( __( 'Ver carrinho (%d)', 'fradelmed-theme' ), $cart_count ) : __( 'Ver carrinho', 'fradelmed-theme' );

    $message  = '<div class="fm-cart-popup__overlay" data-cart-popup-close></div>';
    $message .= '<div class="fm-cart-popup" role="dialog" aria-live="polite" aria-label="' . esc_attr__( 'Item adicionado ao carrinho', 'fradelmed-theme' ) . '">';
    $message .= '<button type="button" class="fm-cart-popup__close" data-cart-popup-close aria-label="' . esc_attr__( 'Fechar', 'fradelmed-theme' ) . '">×</button>';
    $message .= '<div class="fm-cart-popup__head"><span class="fm-cart-popup__check">✓</span><span class="fm-cart-popup__label">' . esc_html__( 'Item adicionado ao carrinho', 'fradelmed-theme' ) . '</span></div>';
    $message .= '<div class="fm-cart-popup__body">';
    if ( $image ) {
        $message .= '<div class="fm-cart-popup__media">' . $image . '</div>';
    }
    $message .= '<div class="fm-cart-popup__info"><div class="fm-cart-popup__name">' . esc_html( $name ) . '</div></div>';
    $message .= '</div>';
    $message .= '<div class="fm-cart-popup__actions">';
    $message .= '<a class="fm-cart-popup__btn fm-cart-popup__btn--ghost" href="' . esc_url( $cart_url ) . '">' . esc_html( $cart_label ) . '</a>';
    $message .= '<a class="fm-cart-popup__btn fm-cart-popup__btn--primary" href="' . esc_url( $checkout_url ) . '">' . esc_html__( 'Finalizar a compra', 'fradelmed-theme' ) . '</a>';
    $message .= '<a class="fm-cart-popup__link" href="' . esc_url( $shop_url ) . '">' . esc_html__( 'Voltar à loja', 'fradelmed-theme' ) . '</a>';
    $message .= '</div>';
    $message .= '</div>';

    return $message;
}

add_filter( 'wc_add_to_cart_message_html', 'fradelmed_cart_popup_message', 10, 2 );

function fradelmed_checkout_cart_item_name( $name, $cart_item, $cart_item_key ) {
    if ( ! function_exists( 'is_checkout' ) || ! is_checkout() ) {
        return $name;
    }

    if ( function_exists( 'is_wc_endpoint_url' ) && is_wc_endpoint_url( 'order-received' ) ) {
        return $name;
    }

    $product = isset( $cart_item['data'] ) ? $cart_item['data'] : null;
    if ( ! $product ) {
        return $name;
    }

    $qty = isset( $cart_item['quantity'] ) ? (int) $cart_item['quantity'] : 1;
    $thumb = $product->get_image(
        'woocommerce_thumbnail',
        array(
            'class'    => 'checkout-product__thumb',
            'loading'  => 'lazy',
            'decoding' => 'async',
        )
    );

    $qty_badge = '<span class="checkout-product__qty" aria-hidden="true">' . $qty . '</span>';

    return '<span class="checkout-product"><span class="checkout-product__thumb-wrap">' . $thumb . $qty_badge . '</span><span class="checkout-product__name">' . $name . '</span></span>';
}
// Temporarily disable checkout-specific cart item markup to restore default checkout flow.
// add_filter( 'woocommerce_cart_item_name', 'fradelmed_checkout_cart_item_name', 10, 3 );

function fradelmed_checkout_coupon_row() {
    if ( ! function_exists( 'wc_coupons_enabled' ) || ! wc_coupons_enabled() ) {
        return;
    }
    ?>
    <tr class="checkout-coupon-row">
        <td colspan="2">
            <div class="checkout-coupon">
                <label class="screen-reader-text" for="checkout_coupon_code"><?php esc_html_e( 'Código de desconto', 'fradelmed-theme' ); ?></label>
                <input type="text" id="checkout_coupon_code" class="checkout-coupon__input" placeholder="<?php esc_attr_e( 'Código de desconto ou cartão-presente', 'fradelmed-theme' ); ?>" autocomplete="off">
                <button type="button" class="checkout-coupon__apply button"><?php esc_html_e( 'Aplicar', 'fradelmed-theme' ); ?></button>
            </div>
        </td>
    </tr>
    <?php
}
// Temporarily disable custom coupon row in checkout review table.
// add_action( 'woocommerce_review_order_after_cart_contents', 'fradelmed_checkout_coupon_row', 15 );

function fradelmed_quantity_minus_button() {
    $allow_product  = function_exists( 'is_product' ) && is_product();
    $allow_cart     = function_exists( 'is_cart' ) && is_cart();
    $allow_checkout = false;

    if ( function_exists( 'is_wc_endpoint_url' ) && is_wc_endpoint_url( 'order-received' ) ) {
        $allow_checkout = false;
    }

    if ( ! $allow_product && ! $allow_cart && ! $allow_checkout ) {
        return;
    }

    echo '<button type="button" class="qty-button qty-button--minus" aria-label="' . esc_attr__( 'Diminuir quantidade', 'fradelmed-theme' ) . '">-</button>';
}
add_action( 'woocommerce_before_quantity_input_field', 'fradelmed_quantity_minus_button' );

function fradelmed_quantity_plus_button() {
    $allow_product  = function_exists( 'is_product' ) && is_product();
    $allow_cart     = function_exists( 'is_cart' ) && is_cart();
    $allow_checkout = false;

    if ( function_exists( 'is_wc_endpoint_url' ) && is_wc_endpoint_url( 'order-received' ) ) {
        $allow_checkout = false;
    }

    if ( ! $allow_product && ! $allow_cart && ! $allow_checkout ) {
        return;
    }

    echo '<button type="button" class="qty-button qty-button--plus" aria-label="' . esc_attr__( 'Aumentar quantidade', 'fradelmed-theme' ) . '">+</button>';
}
add_action( 'woocommerce_after_quantity_input_field', 'fradelmed_quantity_plus_button' );

function fradelmed_update_cart_in_checkout( $post_data ) {
    if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
        return;
    }

    parse_str( $post_data, $data );
    if ( empty( $data['cart'] ) || ! is_array( $data['cart'] ) ) {
        return;
    }

    $cart_updated = false;

    foreach ( $data['cart'] as $cart_item_key => $values ) {
        if ( ! isset( $values['qty'] ) ) {
            continue;
        }

        $qty = wc_stock_amount( $values['qty'] );
        if ( $qty < 0 ) {
            $qty = 0;
        }

        if ( ! isset( WC()->cart->cart_contents[ $cart_item_key ] ) ) {
            continue;
        }

        $current_qty = WC()->cart->cart_contents[ $cart_item_key ]['quantity'];

        if ( 0 === $qty ) {
            WC()->cart->remove_cart_item( $cart_item_key );
            $cart_updated = true;
            continue;
        }

        if ( $qty !== $current_qty ) {
            WC()->cart->set_quantity( $cart_item_key, $qty, false );
            $cart_updated = true;
        }
    }

    if ( $cart_updated ) {
        WC()->cart->calculate_totals();
    }
}
// Temporarily disable custom cart updates during checkout refreshes.
// add_action( 'woocommerce_checkout_update_order_review', 'fradelmed_update_cart_in_checkout' );

function fradelmed_create_test_product() {
    if ( ! is_admin() || ! function_exists( 'wc_get_product_id_by_sku' ) ) {
        return;
    }

    if ( get_option( 'fradelmed_test_product_created' ) ) {
        return;
    }

    $sku   = 'TEST-ESTILETE-FISTULA';
    $title = 'ESTILETE MALEÁVEL PARA FÍSTULA';

    $existing_id = wc_get_product_id_by_sku( $sku );
    if ( ! $existing_id ) {
        $existing_post = get_page_by_title( $title, OBJECT, 'product' );
        if ( $existing_post ) {
            $existing_id = $existing_post->ID;
        }
    }

    if ( $existing_id ) {
        update_option( 'fradelmed_test_product_created', 1 );
        return;
    }

    $product_id = wp_insert_post(
        array(
            'post_title'  => $title,
            'post_status' => 'publish',
            'post_type'   => 'product',
        )
    );

    if ( is_wp_error( $product_id ) || ! $product_id ) {
        return;
    }

    update_post_meta( $product_id, '_sku', $sku );
    update_post_meta( $product_id, '_regular_price', '300' );
    update_post_meta( $product_id, '_price', '300' );
    update_post_meta( $product_id, '_weight', wc_get_weight( 300, 'g' ) );
    update_post_meta( $product_id, '_manage_stock', 'no' );
    update_post_meta( $product_id, '_stock_status', 'instock' );

    update_option( 'fradelmed_test_product_created', 1 );
}

add_action( 'init', 'fradelmed_create_test_product' );

function fradelmed_use_custom_product_archive_template( $template ) {
    if ( is_admin() ) {
        return $template;
    }

    if ( ! function_exists( 'is_shop' ) ) {
        return $template;
    }

    $custom = get_stylesheet_directory() . '/woocommerce/archive-product.php';
    if ( ! file_exists( $custom ) ) {
        $custom = get_template_directory() . '/woocommerce/archive-product.php';
    }
    if ( ! file_exists( $custom ) ) {
        return $template;
    }

    if ( is_shop() || is_product_category() || is_product_tag() ) {
        return $custom;
    }

    if ( is_search() ) {
        $post_type = get_query_var( 'post_type' );
        $is_product_search = false;
        if ( is_array( $post_type ) ) {
            $is_product_search = in_array( 'product', $post_type, true );
        } else {
            $is_product_search = ( 'product' === $post_type );
        }

        if ( $is_product_search ) {
            return $custom;
        }
    }

    return $template;
}
add_filter( 'template_include', 'fradelmed_use_custom_product_archive_template', 99 );
// Desliga Heartbeat no FRONT-END (para parar requests em loop quando logado)
add_action('init', function () {
  if (!is_admin()) {
    wp_deregister_script('heartbeat');
    wp_register_script('heartbeat', false);
  }
}, 1);
add_action('wp_enqueue_scripts', function () {
    if (!is_front_page() && !is_home()) {
        return;
    }

    wp_enqueue_style(
        'fradel-ios-hero-fix',
        get_stylesheet_directory_uri() . '/assets/css/ios-hero-fix.css',
        [],
        '1.0.4'
    );

    wp_enqueue_script(
        'fradel-ios-hero-fix',
        get_stylesheet_directory_uri() . '/assets/js/ios-hero-fix.js',
        [],
        '1.0.4',
        true
    );
}, 99);
