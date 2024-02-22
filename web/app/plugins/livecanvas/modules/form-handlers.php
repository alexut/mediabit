<?php
defined('ABSPATH') || exit; // EXIT IF ACCESSED DIRECTLY.

//////////////////////////////////////////////////////////////////////////////// 
///DEMO FORM ACTION /////////////////////////////////////////////////////////
/////copy the code below including the function, and rename lc_test_action to whatever
// Use it in a custom plugin or in your child theme functions.php file

add_action('wp_ajax_lc_test_action', 'lc_test_action_func'); //allow for logged people
add_action('wp_ajax_nopriv_lc_test_action', 'lc_test_action_func'); //allow for unlogged people

function lc_test_action_func()
{
	
	//INPUT VALIDATION
	check_ajax_referer( 'lc_test_action', 'nonce' );

	//sample validation of a honeypot, customize as you want
	if (!empty($_POST['mouseglue'])) die();

	//do your homework and add validation for your fields

	//PRINT SOME FEEDBACK FOR TESTING
    ?>
    	<div class="alert alert-warning my-2 my-3 lead" role="alert">   <h2>Submitted data:</h2>  <?php if (current_user_can("install_plugins")) print_r($_POST); ?></div>
    <?php 
	//
	//DO WHAT YOU NEED TO IN PHP TO PROCESS THE DATA
	//

	//THEN FINISH THE AJAX ACTION
	wp_die(); // this is required to return a proper result
}

////END DEMO FORM ACTION //////////////////////////////////////////////////////////////////////////// 
 

///////////////////////////////////////////////////////////////////////////////
///CONTACT FORM CALLBACK //////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

///DEFINE FORM HANDLING 
add_action('wp_ajax_lc_submit_contact_form', 'lc_submit_contact_form_func'); //allow for logged people
add_action('wp_ajax_nopriv_lc_submit_contact_form', 'lc_submit_contact_form_func'); //allow for unlogged people

function lc_submit_contact_form_func()
{
    //SECURITY CHECK
    check_ajax_referer('lc_submit_contact_form', 'nonce');

	//DEBUG
	//if (current_user_can("install_plugins")) print_r($_POST);

	//FORM DATA VALIDATION: VALIDATE YOUR FIELDS
	$form_errors = null; //init

	//validate mouseglue: a simple Honeypot Anti-spam trap
	if (!empty($_POST['mouseglue'])) $form_errors .= 'Sorry, no s p * m allowed<br>'; 

	//get the form data
	$name = trim(sanitize_text_field($_POST['name']));
	$message = trim(sanitize_text_field($_POST['message']));
	$email = sanitize_email($_POST['email']);
	$subject = trim(sanitize_text_field($_POST['subject']));
	$urlparts = parse_url(home_url());
	$domain = $urlparts['host'];
	$blogName = get_option('blogname');
	
	//validate name
	if (/* empty($name) or */ strlen($name) > 100) $form_errors .= 'Invalid Name<br>';

	//validate email 
	if(!$email or strlen($email) > 120 or strlen($_POST['email']) < 5) $form_errors .= 'Invalid Email<br>'; 
	
	//validate message 
	if(strlen($message) < 1) $form_errors .= 'Message Field is Empty<br>'; 
    if(strlen($message) > 1000) $form_errors .= 'Invalid Message Field<br>'; 

	//IF FORM ERRORS, ABORT EXECUTION
	if ($form_errors) die('<div class="alert alert-danger my-2 my-3 lead" role="alert">' . $form_errors . '</div>');

	///BUILD EMAIL 
	$to = get_option('admin_email');
	$subject = $blogName . ' > Contact form submitted ';
	$headers = [
		'Content-Type: text/html',
		'charset=UTF-8', 
		'Reply-To: ' . $name . '<' . $email . '>',
		'From: ' . $blogName . ' <noreply@' . $domain . '>',
	];

	$body = 'Hello Admin, <br> a form has been submitted on your site.<br><br>';
	$body .= 'Name: ' . $name . '<br>';
	$body .= 'Email: ' . $email . '<br>';
	$body .= 'Subject: ' . $subject . '<br>';
	$body .= 'Message: ' . $message . '<br>';

	$send_email = wp_mail($to, $subject, $body, $headers);
	
	if ($send_email) echo '<div class="alert alert-success my-2 lead" role="alert"><h2>Thank you!</h2>Your message was sent. We will reply as soon as possible. </div>';
	else echo '<div class="alert alert-danger my-2 lead" role="alert"><h2>Send error</h2> Apologies, your message could not be sent. Please check back soon and retry. </div>';

	wp_die(); // this is required to return a proper result
}

//ON WP MAIL FAIL, HELP DEBUG 
add_action( 'wp_mail_failed', 'lc_onMailError', 10, 1 );
function lc_onMailError( $wp_error ) 
{
	if (!current_user_can("install_plugins")) return;
	echo '<div class="alert alert-warning my-2 small" role="alert"><h2>Email could not be sent</h2> ';
	echo '<pre>' . print_r($wp_error, true) . '</pre>';
	echo '</div>';
}
 