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
