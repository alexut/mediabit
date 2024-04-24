<?php

defined('ABSPATH') || exit; // EXIT IF ACCESSED DIRECTLY.

// LOGIN FORM AJAX ACTION HANDLER ///////////////////////// /////////////////////////  
add_action( 'wp_ajax_lc_submit_user_login', 'lc_login_action_callback' ); //allow for logged people
add_action( 'wp_ajax_nopriv_lc_submit_user_login', 'lc_login_action_callback' ); //allow for unlogged people


function  lc_login_action_callback() {
    
    //SECURITY CHECK
    check_ajax_referer('lc_submit_user_login', 'nonce');

    //BUILD THE PROCEED BUTTON HTML
    $next_page_url = $_POST['next_page_slug'] === "" || $_POST['next_page_slug'] === "/" ? esc_url( home_url() ) : esc_url( get_permalink( get_page_by_path( sanitize_text_field( $_POST['next_page_slug'] ) ) ) ?: esc_url( home_url() ) );
    $proceed_button = "<a class='btn btn-warning btn-lg mt-3 d-block' href='" . add_query_arg( 'just_logged', '1', $next_page_url )  . "'>Click here to proceed &raquo;</a>";

    //OPTIONAL: IF LOGGED IN, STOP HERE
    /*
    if(is_user_logged_in()) { ?>        <div class="alert alert-danger" role="alert">            <h4 class="alert-heading">You are already logged in!</h4>            <p>No need to fill this form.</p>            <hr><?php echo $proceed_button ?>  </div> <?php wp_die(); }
    */

    //ANTI SPAM PREFLIGHT CHECK
	//sample validation of a honeypot, customize as you want
	if (!empty($_POST['mouseglue'])) die();

    // PROCESS THE DATA: GO ON AND LOGIN THE USER  

    // Assume $username and $password are collected securely
    $creds = array(
        'user_login'    => $_POST['log'],
        'user_password' => $_POST['pwd'],
        'remember'      => $_POST['rememberme']
    );

    $user = wp_signon( $creds, false );

    if ( is_wp_error( $user ) ) {
        // LOGIN FAILED
        ?>
        <div class="alert alert-danger" role="alert">
            <h4 class="alert-heading">Login Failed!</h4>
            <p><?php echo  ( $user->get_error_message() ); ?></p>
        </div>
        <?php
    } else {
        // LOGIN SUCCESS 
        ?>
        <style>
            #lc-login-form {display: none}
        </style>
        <div class="alert alert-success" role="alert">
            <h4 class="alert-heading">Welcome, <?php echo esc_html( $user->display_name ); ?>!</h4>
            <p>You have successfully logged in.</p>
            <hr>
            <?php echo $proceed_button ?> 
        </div>
        <?php
        // Optionally, redirect or perform additional actions as needed
    }

    wp_die();

} // END FUNCTION

