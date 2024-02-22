<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
// defining variables
$header = new \Mediabit\Templates\Sections\Header();

?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class();?>>
<?php 
wp_body_open();
echo $header->render(); 

?>
