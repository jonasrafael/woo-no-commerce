<?php

/*
  Plugin Name: Woo-no-commmerce.
  Description: this plugin will be available for wordpress soon.
  Author: Jonas Rafael Rossatto
  Version: 0.1
  Author URI: http://PRFVR.com/
  License: GPLv2 or later


  /**
 * WooCommerce - Product attributs HTML list
 */

function custom_woocommerce_attribute($html, $attribute, $values) {
    $html = '<ul>';
    foreach ($values as $value) {
        $html .= '<li>' . $value . '</li>';
    }

    $html .= '</ul>';

    return $html;
}

add_filter('woocommerce_attribute', 'custom_woocommerce_attribute', 10, 3);

/*
 * WooCommerce - Remove add to cart button in product page
 */

remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);

/**
 * Register new Tab MoreDetails on WooCommerce Front-end product tabs.
 * If you want, change the name.
 * @param  array $tabs Default WooCommerce tabs.
 * @return array  MoreDetails  New tabs.
 */

function cs_register_woocommerce_product_tab($tabs) {
    $tabs['MoreDetails'] = array(
        'title' => __('More Details', 'textdomain'),
        'priority' => 60,
        'callback' => 'cs_woocommerce_custom_tab_view'
    );

    return $tabs;
}

add_filter('woocommerce_product_tabs', 'cs_register_woocommerce_product_tab');

/**
 * Creates a view to More Details on the custom tab.
 * thanks, claudiosanches!
 * @return string
 */
function cs_woocommerce_custom_tab_view() {
    global $product;

    echo '<h2>Product</h2><li>Weight: <strong>', $product->get_weight() . ' ' . esc_attr(get_option('woocommerce_weight_unit')), '</strong></li>';
    echo '<li>Dimensions: <strong>', $product->get_dimensions(), '</strong></li>';
 //   echo '<li>Use here to add More Details of your product</li>';
}

/*
 * WooCommerce -  Renaming Tabs
 if you want another name. Use!
 */

add_filter('woocommerce_product_tabs', 'woo_rename_tabs', 98);
add_filter('wc_product_enable_dimensions_display', '__return_false');

function woo_rename_tabs($tabs) {

    $tabs['description']['title'] = __('Descrição');  // Rename the description tab
    $tabs['reviews']['title'] = __('Avaliações');    // Rename the reviews tab
    $tabs['additional_information']['title'] = __('Tabela Nutricional'); // Rename the additional information tab

    return $tabs;
}

function callback($buffer) {

    /* Remove the Div Class from the product page */
    $contents = $buffer;
    $pattern = '#\<div class="quantity"\>\s*(.+?)\s*\<\/div\>#s';
    $contents = preg_replace_callback(
            $pattern, create_function(
                    '$matches', 'return "<!--<div class=quantity>$matches[1]</div>-->";'
            ), $contents
    );

    /* Remove the TH from the Cart page */
    $pattern = '#\<th class="product-quantity"\>\s*(.+?)\s*\<\/th\>#s';
    $contents = preg_replace_callback(
            $pattern, create_function(
                    '$matches', 'return "<!--<th class=product-quantity>$matches[1]</th>-->";'
            ), $contents
    );

    $pattern = '#\<td class="product-quantity"\>\s*<!--(.+?)-->\s*<\/td\>#s';
    $contents = preg_replace_callback(
            $pattern, create_function(
                    '$matches', 'return "<!--<td class=product-quantity>$matches[1]</td>-->";'
            ), $contents
    );

    $buffer = $contents;

    return $buffer;
}

function buffer_start() {
    ob_start("callback");
}

function buffer_end() {
    ob_end_flush();
}

add_action('wp_head', 'buffer_start');
add_action('wp_footer', 'buffer_end');


/*
 * Hide Prices and checkout
 */

remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart');
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10);


/**
 * Returns max price for variably priced products
 * */
add_filter('woocommerce_variable_price_html', 'custom_variation_price', 10, 2);

function custom_variation_price($price, $product) {
    $price = '';

    $price .= woocommerce_price($product->max_variation_price);

    return $price;
}

/*
 * Block selectabs metaboxs to shop_manager in custom.js
 */ if (current_user_can('shop_manager')) {

    function load_custom_js($page_hook) {
        global $post;

        if (!in_array($page_hook, array('post.php', 'post-new.php'))) {
            return;
        }
        if ($post->post_type !== 'product') {
            return;
        }

        $script_url = get_stylesheet_directory_uri() . '/js/custom.js';
        wp_enqueue_script('custom-js', $script_url, array('jquery'), false, true);
    }

    add_action('admin_enqueue_scripts', 'load_custom_js');
} // if current_user_can('some_role)
//
// Display Fields
add_action('woocommerce_product_options_general_product_data', 'woo_add_custom_general_fields');

// Save Fields
add_action('woocommerce_process_product_meta', 'woo_add_custom_general_fields_save');

// Enjoy you'r Custom Text Fields
function woo_add_custom_general_fields() {

    global $woocommerce, $post;

    woocommerce_wp_textarea_input(
    //use here to define what u want on admin...
    );
}

function woo_add_custom_general_fields_save($post_id) {
    // Textarea
    //$woocommerce_textarea = $_POST['_sampleOgg'];
//    if (!empty($woocommerce_textarea))
    //      update_post_meta($post_id, '_sampleOgg', esc_html($woocommerce_textarea));
}
?>

