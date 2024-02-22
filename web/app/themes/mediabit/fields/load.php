<?php 
define('PATH', get_stylesheet_directory( ));
define('URL', get_stylesheet_directory_uri(  ));

define('INCLUDES_PATH', PATH . '/fields');
define('INCLUDES_URI', URL . '/fields');

// Define path and URL to the ACF plugin.
define( 'ACF_PATH', INCLUDES_PATH . '/acf/' );
define( 'ACF_URL', INCLUDES_URI . '/acf/' );
define( 'ACFE_PATH', INCLUDES_PATH . '/acf-extended/');
define( 'ACFE_URL', INCLUDES_URI . '/acf-extended/');

include_once( INCLUDES_PATH . '/acf/acf.php' );
include_once( INCLUDES_PATH . '/acf-extended/acf-extended.php' );
include_once( INCLUDES_PATH . '/acf-theme-code-pro/acf_theme_code_pro.php');

?>