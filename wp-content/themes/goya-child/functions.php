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