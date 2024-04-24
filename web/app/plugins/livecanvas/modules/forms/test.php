<?php
defined('ABSPATH') || exit; // EXIT IF ACCESSED DIRECTLY.

//////////////////////////////////////////////////////////////////////////////// 
///DEMO FORM ACTION /////////////////////////////////////////////////////////
/////copy the code below including the function, and rename lc_test_action to whatever
// Use it in a custom plugin or in your child theme functions.php file

add_action('wp_ajax_lc_test_action', 'lc_test_action_func'); //allow for logged people
add_action('wp_ajax_nopriv_lc_test_action', 'lc_test_action_func'); //allow for unlogged people

function lc_test_action_func() {
	
	//INPUT VALIDATION
	check_ajax_referer( 'lc_test_action', 'nonce' );

	//sample validation of a honeypot, customize as you want
	if (!empty($_POST['mouseglue'])) die();

	//OPTIONALLY do your homework and add validation for your fields

	//PRINT SOME FEEDBACK FOR TESTING
    ?>
    	<div class="alert alert-warning my-2 my-3 lead" role="alert">   <h2>Submitted data:</h2>  <?php if (current_user_can("install_plugins")) print_r($_POST); ?></div>
    <?php 
	//
	//OPTIONALLY DO WHAT YOU NEED TO IN PHP TO PROCESS THE DATA
	//

	//THEN FINISH THE AJAX ACTION
	wp_die(); // this is required to return a proper result
}

