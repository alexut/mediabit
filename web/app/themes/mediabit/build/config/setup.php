<?php

add_theme_support('title-tag');
add_theme_support('post-thumbnails');
add_theme_support( 'custom-logo' );
register_nav_menu('main-menu', 'Main menu');
register_nav_menu('services-menu', 'Services menu');
register_nav_menu('solutions-menu', 'Solutions menu');
add_action('after_setup_theme', 'custom_image_sizes');

function custom_image_sizes()
{
  add_image_size('image-1920px-wide', 1920); // used as alternative for maximum size of images instead of 'full'
  // 'medium_large' for 768 pixels wide is automatically added by WordPress
}
