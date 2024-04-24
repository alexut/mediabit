<?php

// // redirect all users to home page after login
// function redirect_all_users_to_specific_page() {
//     wp_redirect( home_url('/dashboard') );
//     exit();
// }
// add_action( 'wp_login', 'redirect_all_users_to_specific_page' );

// function redirect_retailer_to_specific_page() {
//     $user = wp_get_current_user();
//     if ( in_array( 'retailer', (array) $user->roles ) ) {
//         wp_redirect( home_url() );
//         exit();
//     }
// }
// add_action( 'wp_login', 'redirect_retailer_to_specific_page' );

