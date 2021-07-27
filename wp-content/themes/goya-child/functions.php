<?php
/**
 * Child theme functions and definitions.
 */
function goya_child_enqueue_styles() {
wp_enqueue_style( 'goya-style' , get_template_directory_uri() . '/style.css' );    
    wp_enqueue_style( 'goya-child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( 'goya-style' ),
        1.0
    );
}

add_action(  'wp_enqueue_scripts', 'goya_child_enqueue_styles', 99 );

add_filter( 'goya_main_font_choices', 'goya_main_font_custom' );
function goya_main_font_custom() {
 return array(
  'fonts' => array(
   'google'  => array( 'popularity', 700 ),
   'families' => array(
    'custom' => array(
     'text'   => 'Goya Custom Fonts',
     'children' => array(
      array( 'id' => 'Linden Hill', 'text' => 'Linden Hill' ),
     ),
    ),
   ),
   'variants' => array(
    'Linden Hill' => array( 'regular','italic','bold','bolditalic','400','400italic','600','600italic','800','800italic' ),
   ),
  ),
 );
}

// Display Fields
add_action('woocommerce_product_options_general_product_data', 'woocommerce_product_custom_fields');
function woocommerce_product_custom_fields()
{
    global $woocommerce, $post;
    echo '<div class="product_custom_field">';
    // Custom Product Text Field
    woocommerce_wp_text_input(
        array(
            'id' => '_custom_product_size_field',
            'placeholder' => 'Size if single option',
            'label' => __('Size:', 'woocommerce'),
            'desc_tip' => 'true',
            'description' => __('Size to list if not variable.', 'woocommerce')
        )
    );

    //Custom Product  Textarea
    woocommerce_wp_textarea_input(
        array(
            'id' => '_custom_product_textarea',
            'placeholder' => 'Custom Product Textarea',
            'label' => __('Custom Product Textarea', 'woocommerce')
        )
    );

        //Custom Product  Checkbox
        woocommerce_wp_checkbox(
            array(
                'id' => '_custom_product_copyright_checkbox',
                'label' => __('Public Domain?', 'woocommerce'),
                'description' =>__('Check for public domain', 'woocommerce')
            )
        );

    echo '</div>';
}

// Save the custom fileds to the database
function woocommerce_product_custom_fields_save($post_id)
{
    // Custom Product Text Field
    $woocommerce_custom_product_size_field = $_POST['_custom_product_size_field'];
    if (!empty($woocommerce_custom_product_size_field))
        update_post_meta($post_id, '_custom_product_size_field', esc_attr($woocommerce_custom_product_size_field));

// Custom Product Textarea Field
    $woocommerce_custom_procut_textarea = $_POST['_custom_product_textarea'];
    if (!empty($woocommerce_custom_procut_textarea))
        update_post_meta($post_id, '_custom_product_textarea', esc_html($woocommerce_custom_procut_textarea));

        // Custom Checkbox
        $woocommerce_checkbox = isset( $_POST['_custom_product_copyright_checkbox'] ) ? 'yes' : 'no';
        update_post_meta( $post_id, '_custom_product_copyright_checkbox', $woocommerce_checkbox );
}

// Save Fields
add_action('woocommerce_process_product_meta', 'woocommerce_product_custom_fields_save');


//Display custom fields on Woocommerce Product Details Page
add_action( 'woocommerce_single_product_summary', '_size_el', 5 );
function _size_el() {
    global $product;
    $editionText = get_post_meta( $product->id, '_custom_product_size_field', true );
		if ($editionText):
		echo '<div>';
	    echo '<span><strong> Edition:</strong> ' . $editionText . '</span>';
		echo '</div>';
        endif;
	}
    add_action( 'woocommerce_share', '_copyright_el', 11 );
    function _copyright_el() {
        global $product;
        $copyValue = get_post_meta( $product->id, '_custom_product_copyright_checkbox', true );
        if ( $copyValue === 'yes') :
            echo '<div class="cc-graphic">
            <a rel="license" target="_blank" href="http://creativecommons.org/licenses/by-nc-nd/4.0/">
            <img src="'.get_site_url() .'/wp-content/assets/images/cc-by-nc-nd-80x15.png" alt="Creative Commons License BY-NC-DD" title="This work is licensed under a Creative Commons Attribution-NonCommercial-NoDerivatives 4.0 International License"/></div></a>';
        else: 
            echo '<div class="cc-graphic">
            <a rel="license" target="_blank" href="https://creativecommons.org/publicdomain/mark/1.0/">
            <img src="'.get_site_url() .'/wp-content/assets/images/cc-pd-80x15.png" alt="Create Commons Public Domain License" title="This work has been identified as being free of known restrictions under copyright law, including all related and neighboring rights."/></div></a>';

            endif;
        
            
        }