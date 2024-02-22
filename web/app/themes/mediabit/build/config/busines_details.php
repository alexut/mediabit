<?php
if (function_exists('acf_add_options_page')) {
    acf_add_options_page(array(
        'page_title' => 'Business Details',
        'menu_title' => 'Business Details',
        'menu_slug'  => 'business-details',
        'capability' => 'edit_posts',
        'redirect'   => false,
        'icon_url'   => 'dashicons-store', // Use a Dashicons class or a custom image URL
        'position'   => 32 // Optional, set the position of the menu item
    ));
}