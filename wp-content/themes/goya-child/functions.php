<?php

///////////////////////////////////////
//                                   //
//    Overriding the parent Theme    //
//                                   //
///////////////////////////////////////

// Constant: Theme version
define( 'CHILD_THEME_VERSION', '0.1' );



//    Define Paths    //
define('CHILD_DIR', get_theme_file_path() . '/inc' );
define( 'CHILD_THEME_URI', get_template_directory_uri() );
define('CHILD_ASSET',  get_theme_file_uri() . '/assets' );
define('CHILD_ASSET_PACKAGES',  get_theme_file_uri() . '/assets/packages' );
define( 'CHILD_ASSET_CSS',  get_theme_file_uri() . '/assets/css' );
define( 'CHILD_ASSET_JS', CHILD_THEME_URI . '/assets/js' );
define( 'CHILD_ASSET_ICON', CHILD_THEME_URI . '/assets/icons' );

//    Load Scripts    //
require CHILD_DIR .'/script-calls.php';


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

add_filter('woocommerce_product_data_tabs', function ($tabs) {

    $tabs['additional_info'] = [

        'label' => __('Additional info', 'txtdomain'),

        'target' => 'additional_product_data',

        'class' => ['hide_if_external'],

        'priority' => 25

    ];

    return $tabs;

});



add_action('woocommerce_product_data_panels', function () {

?>

    <div id="additional_product_data" class="panel woocommerce_options_panel hidden">

        <?php

        woocommerce_wp_text_input([

            'id' => '_latin_name',

            'label' => __('Latin Name', 'txtdomain'),

        ]);



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

                update_post_meta($post_id, '_single_size_field', esc_attr($single_size_field));



                $copyright_checkbox = isset($_POST['_custom_product_copyright_checkbox']) ? 'yes' : '';

                update_post_meta($post_id, '_custom_product_copyright_checkbox', $copyright_checkbox);



                $latin_name = $_POST['_latin_name'];

                update_post_meta($post_id, '_latin_name', esc_attr($latin_name));



                $artist_credit = $_POST['_artist_credit'];

                update_post_meta($post_id, '_artist_credit', esc_attr($artist_credit));



                $artist_credit_link = $_POST['_artist_credit_link'];

                update_post_meta($post_id, '_artist_credit_link', esc_attr($artist_credit_link));



                $creation_date = $_POST['_creation_date'];

                update_post_meta($post_id, '_creation_date', esc_attr($creation_date));

            }

            add_action('woocommerce_process_product_meta', 'woocommerce_product_custom_fields_save');





            //Display custom fields on Woocommerce Product Details Page

            // visual guide of locations : https://www.businessbloomer.com/woocommerce-visual-hook-guide-single-product-page/



            add_action('woocommerce_single_product_summary', '_size_el', 5);

            function _size_el()

            {

                global $product;

                $editionText = get_post_meta($product->get_id(), '_single_size_field', true);

                if ($editionText) :

                    echo '<div>';

                    echo '<span><strong> Edition:</strong> ' . $editionText . '</span>';

                    echo '</div>';

                endif;

            }



            add_action('woocommerce_single_product_summary', 'latinName', 5);

            function latinName()

            {

                global $product;

                $latin = get_post_meta($product->get_id(), '_latin_name', true);

                if ($latin) :

                    echo '<h2 class="latin">' . $latin . '</h2>';

                endif;

            }



            add_action('woocommerce_single_product_summary', '_credit', 26);

            function _credit()

            {

                global $product;

                $artistCredit = get_post_meta($product->get_id(), '_artist_credit', true);

                $artistCreditLink = get_post_meta($product->get_id(), '_artist_credit_link', true);

                $creationDate = get_post_meta($product->get_id(), '_creation_date', true);





                if ($artistCredit) :

                    echo '<p class="artist-credit">Credit : ';

                    if ($artistCreditLink) :

                        echo  '<a href="' . $artistCreditLink . '" title="Link to ' . $artistCredit . '" >' . $artistCredit . '</a>';

                    else :

                        echo  $artistCredit;

                    endif;

                    if ($creationDate) :

                        echo ' (' . $creationDate . ')';

                    endif;

                    echo '</p>';

                endif;

            }





            add_action('woocommerce_single_product_summary', 'paperType', 27);

            function paperType()

            {

                global $product;

                $papers = get_the_terms($product->get_id(), 'pa_paper');

                write_log('$papers');



                write_log($papers);



                // if ($papers) {

                //     echo '<div class="paper-types">';



                //     foreach ($papers as $term) {

                //         echo '<p>Printed on ' . $term->name . '</p>';

                //         if ($term->description) {

                //             echo  '<p>' . $term->description . '</p>';

                //         }

                //         // write_log($term->name);

                //         // write_log($term->description);

                //     }

                //     echo '</div>';

                // }





                if ($papers) {

                    echo '<div class="paper-types">';



                    foreach ($papers as $term) {

                        //  echo '<p>Printed on ' . $term->name . '</p>';

                        if ($term->description) {

                            echo  '<p>Printed on ' . $term->description . '</p>';

                        }

                        // write_log($term->name);

                        // write_log($term->description);

                    }

                    echo '</div>';

                }

            }





            add_action('woocommerce_share', '_copyright_el', 11);

            function _copyright_el()

            {

                global $product;

                $copyValue = get_post_meta($product->get_id(), '_custom_product_copyright_checkbox', true);

                if ($copyValue === 'yes') :

                    echo '<div class="cc-graphic">

            <a rel="license" target="_blank" href="http://creativecommons.org/licenses/by-nc-nd/4.0/">

            <img src="' . get_site_url() . '/wp-content/assets/images/cc-by-nc-nd-80x15.png" alt="Creative Commons License BY-NC-DD" title="This work is licensed under a Creative Commons Attribution-NonCommercial-NoDerivatives 4.0 International License"/></div></a>';

                else :

                    echo '<div class="cc-graphic">

            <a rel="license" target="_blank" href="https://creativecommons.org/publicdomain/mark/1.0/">

            <img src="' . get_site_url() . '/wp-content/assets/images/cc-pd-80x15.png" alt="Create Commons Public Domain License" title="This work has been identified as being free of known restrictions under copyright law, including all related and neighboring rights."/></div></a>';



                endif;

            }



            /** Remove product data tabs */



            add_filter('woocommerce_product_tabs', 'my_remove_product_tabs', 98);

            function my_remove_product_tabs($tabs)

            {

                unset($tabs['description']);

                unset($tabs['additional_information']);

                // remove reviews from admin config

                // unset($tabs['reviews']);

                return $tabs;

            }





            // Add shortcode for Tag descriptions 

            add_filter('term_description', 'do_shortcode');





            // custom logs to main log file -> https://sarathlal.com/write-custom-data-wordpress-debug-log-file/

            if (!function_exists('write_log')) {

                function write_log($log)

                {

                    if (true === WP_DEBUG) {

                        if (is_array($log) || is_object($log)) {

                            error_log(print_r($log, true));

                        } else {

                            error_log($log);

                        }

                    }

                }

            }



        // If need to adjust related products could restrict to just tags or category



        // add_filter('woocommerce_product_related_posts_relate_by_category', '__return_false');

        // add_filter('woocommerce_product_related_posts_relate_by_tag', '__return_false');



