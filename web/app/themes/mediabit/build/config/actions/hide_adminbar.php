<?php

// // Hide admin bar for everyone
// add_filter('show_admin_bar', '__return_false');


// hide admin bar for all users except admin


function remove_admin_bar() {
    if (!current_user_can('administrator') && !is_admin()) {
      show_admin_bar(false);
    }
}
add_action('after_setup_theme', 'remove_admin_bar');

// function hide_admin_bar_for_retailer() {
//     if ( current_user_can( 'retailer' ) && ! is_admin() ) {
//         show_admin_bar( false );
//     }
// }
// add_action( 'after_setup_theme', 'hide_admin_bar_for_retailer' );
