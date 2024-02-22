<?php

//function to get active parent theme information
function lc_get_theme_info( $parameter = "Name" ){

	if (get_template_directory() === get_stylesheet_directory())  { $my_theme = wp_get_theme(); } else { $my_theme = wp_get_theme()->parent(); }
	if(!empty($my_theme)) return  $my_theme->get( $parameter );
}

//enable only for testing purpose
if(0) add_action("wp_head", function(){
	?>
	<script>
		alert("<?php echo lc_get_theme_info("Name"). " ". lc_get_theme_info("Version"); ?>"); 
	</script>
	<?php 
});

//Theme-specific LC option forcing
add_filter('lc_settings', function ($lc_settings) {
	if(is_array($lc_settings) && in_array(lc_get_theme_info( 'Name' ), array("bootScore", "b5st")))   {
		$lc_settings['force-embedded-template-for-lc-pages'] = TRUE;
		$lc_settings['enable-bs-5'] = TRUE;
	}
	return $lc_settings;
});

//Theme-specific fixes for page being edited
add_action("wp_head", function(){
	
	if ( !isset($_GET["lc_page_editing_mode"]) OR !current_user_can("edit_pages") ) return; //only when page is being edited by LC

 	if(lc_get_theme_info( 'Name' ) ==  "bootScore" ) { 
		//allow picking the first section on page on Bootscore
		?>
		<style>
			header#masthead {pointer-events:none}
		</style>
		<?php
	 }

});

//POLYLANG COMPATIBILITY
add_filter( 'pll_get_post_types', 'lc_add_cpt_to_pll', 10, 2 );
function lc_add_cpt_to_pll( $post_types, $is_settings ) {
    
    // enables language and translation management for 'lc_partial'
    $post_types['lc_partial'] = 'lc_partial';
	
	// we could add here also lc_dynamic_template but most of the times it does not make sense
    
    return $post_types;
}

//FOR WOOCOMMERCE TEMPLATING: ADD BODY CLASS WHEN EDITING WC TEMPLATES
add_filter( 'body_class','lc_wc_templating_body_classes' );
function lc_wc_templating_body_classes( $classes ) {
	global $_GET;
	if (isset($_GET['lc_dynamic_template']) && strpos($_GET['lc_dynamic_template'], 'product') !== false) {
    	$classes[] = 'woocommerce-page';
	}
    return $classes;
}

