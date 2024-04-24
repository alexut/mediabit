<?php

defined('ABSPATH') || exit; // EXIT IF ACCESSED DIRECTLY.

//QUICK HELPER TO DISPLAY FORM ERRORS AND DIE
function lc_form_die($error) {
    ?>
    <div class="alert alert-danger" role="alert">
        <span class="fs-4"><?php echo $error ?></span>
    </div>
    <?php
    wp_die();
}

// AJAX ACTION HANDLER ///////////////////////// /////////////////////////  
add_action( 'wp_ajax_lc_submit_user_registration', 'lc_user_registration_action_callback' ); //allow for logged people
add_action( 'wp_ajax_nopriv_lc_submit_user_registration', 'lc_user_registration_action_callback' ); //allow for unlogged people

function  lc_user_registration_action_callback() {
    
    //SECURITY CHECK
    check_ajax_referer('lc_submit_user_registration', 'nonce');

    //print_r($_POST); ///FOR DEBUG

    //BUILD THE PROCEED BUTTON HTML
    $next_page_url = $_POST['next_page_slug'] === "" || $_POST['next_page_slug'] === "/" ? esc_url( home_url() ) : esc_url( get_permalink( get_page_by_path( sanitize_text_field( $_POST['next_page_slug'] ) ) ) ?: esc_url( home_url() ) );
    $proceed_button = "<a class='btn btn-warning btn-lg mt-3 d-block' href='" . add_query_arg( 'just_registered', '1', $next_page_url ) . "'>Click here to proceed &raquo;</a>";
    $login_link = " <a class='text-dark fw-bold' href='" . wp_login_url() . "'> Log in </a>";

    //OPTIONAL: IF LOGGED IN, STOP HERE
    /*
    if( is_user_logged_in()) {
        lc_form_die("You are already logged in.  No need to fill this form $proceed_button  "); 
    }
    */

    //USER INPUT VALIDATION ////////////////////////////////////////

    //ANTI SPAM
    if (!empty($_POST['mouzetrp'])) lc_form_die("no s p * m here");
    if ( ($_POST['checkit']!='yo')) lc_form_die("no s p * m2 here");

    //CHECK EMAIL
    if (empty($_POST['efield'])   )	lc_form_die("<b>Email address</b> is empty. ");
    if (!is_email($_POST['efield']) )	lc_form_die("Incorrect <b>email address</b>.");
    if (email_exists($_POST['efield']))	lc_form_die("This <b>email address</b> is already used. <br /> If you have an account, please   $login_link  ");

    //CHECK USERNAME 
    if (empty($_POST['ufield'])  ) lc_form_die("Username</b> is empty. ");
    if (strlen ($_POST['ufield']) < 5 OR strlen ($_POST['ufield'])>30) lc_form_die("Invalid <b>Username</b>. Minimum 5 chars. Maximum 30 chars. Thank you!");
    if (!validate_username($_POST['ufield'])) lc_form_die("Invalid <b>Username</b>. Lowercase chars / numbers only, please.  ");
    if (username_exists( $_POST['ufield'] )) lc_form_die("This <b>Username</b> already exists. <br />If you are already registered, please $login_link <br> If you are a new user, please choose another username. Thank you!");

    //CHECK PASSWORD
    if (empty($_POST['pfield'])  ) lc_form_die("<b>Password</b> field is empty. ");
    if ( strlen($_POST['pfield']) < 8  or strlen($_POST['pfield'])>40  ) lc_form_die("Invalid <b>Password</b> Field. You need to insert at least eight chars and up to fourty.  ");

    // PROCESS THE DATA: GO ON AND CREATE THE USER /////////////////////////////////////////////////////////////////////////////////

    $user_id = wp_create_user( $_POST['ufield'], $_POST['pfield'], $_POST['efield'] );

    if ( is_wp_error( $user_id ) ) lc_form_die("User creation error: " . $user_id->get_error_message());
        
    // Log the user in
    $creds = array(
        'user_login'    => $_POST['ufield'],
        'user_password' => $_POST['pfield'],
        'remember'      => true
    );

    $user = wp_signon( $creds, false );

    if ( is_wp_error( $user ) ) lc_form_die("Login error: " . $user->get_error_message());

    //ALL OK, NEW USER HAS BEEN CREATED SUCCESSFULLY: USER REGISTERED AND LOGGED IN ////////////////////// 

    //SEND MAIL TO USER 
    $password_hint=substr($_POST['pfield'], 0, -5).'***** (last 5 chars are hidden for security reasons)';
    $subject="Your " . get_bloginfo('name') . " account credentials | username: " . esc_attr($_POST['ufield']);
    $mailcontent="Hello " . esc_attr($_POST['ufield']).
        ",\nyour account at " . get_bloginfo('name') . "  has been created.\nPlease keep this email in a safe place for further access.\n". 
        "\nUsername: ".esc_attr($_POST['ufield'])."\nPassword: ".esc_attr($password_hint)."\n\nThe " . get_bloginfo('name') . " Team\n". get_bloginfo('url') . "\n";
    
    //echo "Send email to:".$_POST['efield']."<br><b>".$subject.'</b><br>'.$mailcontent; //for testing
        
    wp_mail( $_POST['efield'], $subject, $mailcontent );

    //SHOW POSITIVE FEEDBACK TO USER 
    ?>
    <style>
        /* Hide the registration form */
        #lc-registration-form{display:none !important}
    </style>

    <div class="alert alert-success" role="alert">
        <h3>Your account has been created</h3>
        <p>An email has been sent to <?php esc_attr($_POST['efield']) ?>  with your login credentials.</p>
        <?php echo $proceed_button ?>

    </div> <!-- close ALERT -->

    <?php
    //END CASE NEW USER HAS BEEN CREATED SUCCESSFULLY ///////////////////////////////////////////// 

    wp_die();
}


