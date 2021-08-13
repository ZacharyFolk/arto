<?php

/**
 * Child theme functions and definitions.
 */
function goya_child_enqueue_styles()
{
    wp_enqueue_style('goya-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style(
        'goya-child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array('goya-style'),
        1.0
    );
}

add_action('wp_enqueue_scripts', 'goya_child_enqueue_styles', 99);

add_filter('goya_main_font_choices', 'goya_main_font_custom');
function goya_main_font_custom()
{
    return array(
        'fonts' => array(
            'google'  => array('popularity', 700),
            'families' => array(
                'custom' => array(
                    'text'   => 'Goya Custom Fonts',
                    'children' => array(
                        array('id' => 'Linden Hill', 'text' => 'Linden Hill'),
                    ),
                ),
            ),
            'variants' => array(
                'Linden Hill' => array('regular', 'italic', 'bold', 'bolditalic', '400', '400italic', '600', '600italic', '800', '800italic'),
            ),
        ),
    );
}


// Add size attribute if no variations
// todo:  For some reason all General tab being hidden if you select Variable Product, works in my favor now because I don't want this field available for anything but single type, but kind of broken solution
add_action('woocommerce_product_options_general_product_data', 'woocommerce_product_custom_fields');
function woocommerce_product_custom_fields()
{
    global $woocommerce, $post;
    echo '<div class="product_custom_field">';
    woocommerce_wp_text_input(
        array(
            'id' => '_single_size_field',
            'placeholder' => 'Size if single option',
            'label' => __('Size:', 'woocommerce'),
            'desc_tip' => 'true',
            'description' => __('Size to list if not variable.', 'woocommerce')
        )
    );
    echo '</div>';
}

// Add additional tab with extra attributes 
add_filter('woocommerce_product_data_tabs', function($tabs) {
	$tabs['additional_info'] = [
		'label' => __('Additional info', 'txtdomain'),
		'target' => 'additional_product_data',
		'class' => ['hide_if_external'],
		'priority' => 25
	];
	return $tabs;
});

add_action('woocommerce_product_data_panels', function() {
    ?>
    <div id="additional_product_data" class="panel woocommerce_options_panel hidden">
    <?php
	woocommerce_wp_text_input([
		'id' => '_artist_credit',
		'label' => __('Artist Credit', 'txtdomain'),
	]);

    woocommerce_wp_text_input([
		'id' => '_artist_credit_link',
		'label' => __('Artist Credit Link', 'txtdomain'),
	]);

    woocommerce_wp_text_input([
		'id' => '_creation_date',
		'label' => __('Date: ', 'txtdomain'),
	]);

    woocommerce_wp_checkbox(
        array(
            'id' => '_custom_product_copyright_checkbox',
            'label' => __('Does this have a copyright license?', 'woocommerce'),
            'desc_tip' => 'true',
            'description' => __('Check for copyright restrictions.  Unchecked defaults to Public Domain license', 'woocommerce')
        )
    );

 
	?></div><?php
});


// Save Fields
function woocommerce_product_custom_fields_save($post_id)
 {
    $single_size_field = $_POST['_single_size_field'];
    if (!empty($single_size_field)) update_post_meta($post_id, '_single_size_field', esc_attr($single_size_field));

    $copyright_checkbox = isset($_POST['_custom_product_copyright_checkbox']) ? 'yes' : '';
    update_post_meta($post_id, '_custom_product_copyright_checkbox', $copyright_checkbox);

    $artist_credit = $_POST['_artist_credit'];
    if (!empty($artist_credit)) update_post_meta($post_id, '_artist_credit', esc_attr($artist_credit));    

    $artist_credit_link = $_POST['_artist_credit_link'];
    if (!empty($artist_credit_link)) update_post_meta($post_id, '_artist_credit_link', esc_attr($artist_credit_link));    

    $creation_date = $_POST['_creation_date'];
    if (!empty($creation_date)) update_post_meta($post_id, '_creation_date', esc_attr($creation_date));   
}
add_action('woocommerce_process_product_meta', 'woocommerce_product_custom_fields_save');


//Display custom fields on Woocommerce Product Details Page
// visual guide of locations : https://www.businessbloomer.com/woocommerce-visual-hook-guide-single-product-page/

add_action('woocommerce_single_product_summary', '_size_el', 5);
function _size_el()
{
    global $product;
    $editionText = get_post_meta($product->id, '_single_size_field', true);
    if ($editionText) :
        echo '<div>';
        echo '<span><strong> Edition:</strong> ' . $editionText . '</span>';
        echo '</div>';
    endif;
}

add_action('woocommerce_share', '_copyright_el', 11);
function _copyright_el()
{
    global $product;
    $copyValue = get_post_meta($product->id, '_custom_product_copyright_checkbox', true);
    if ($copyValue === 'yes') :
        echo '<div class="cc-graphic">
            <a rel="license" target="_blank" href="http://creativecommons.org/licenses/by-nc-nd/4.0/">
            <img src="' . get_site_url() . '/wp-content/assets/images/cc-by-nc-nd-80x15.png" alt="Creative Commons License BY-NC-DD" title="This work is licensed under a Creative Commons Attribution-NonCommercial-NoDerivatives 4.0 International License"/></div></a>';
    else :
        echo '<div class="cc-graphic">
            <a rel="license" target="_blank" href="https://creativecommons.org/publicdomain/mark/1.0/">
            <img src="' . get_site_url() . '/wp-content/assets/images/cc-pd-80x15.png" alt="Create Commons Public Domain License" title="This work has been identified as being free of known restrictions under copyright law, including all related and neighboring rights."/></div></a>';

    endif;
    $creationDate = get_post_meta($product->id, '_creation_date', true);
    if ($creationDate) :
        echo '<div>';
        echo '<span><strong> Date:</strong> ' . $creationDate . '</span>';
        echo '</div>';
    endif;
}

add_action('woocommerce_share', '_credit', 20);
function _credit()
{
    global $product;
    $artistCredit = get_post_meta($product->id, '_artist_credit', true);
    $artistCreditLink = get_post_meta($product->id, '_artist_credit_link', true);
    if ($artistCredit):
        echo '<div class="artist-credit">Credit : '; 
    if ($artistCreditLink) :
        echo  '<a href="' . $artistCreditLink . '" title="Link to '. $artistCredit . '" target="_blank">' . $artistCredit . '</a>';
        else : 
            echo  '<p>' . $artistCredit . '</p>';
        endif;
        echo '</div>';
    endif;
}
