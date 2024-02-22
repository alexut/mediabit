<?php
// EXIT IF ACCESSED DIRECTLY.
defined( 'ABSPATH' ) || exit;

//ALLOW unfiltered HTML in XML IMPORT 
add_filter( 'force_filtered_html_on_import', '__return_false', 999 );

/// ALTER CONTENT FILTERING ON PAGES WHERE LIVECANVAS IS ENABLED
add_action('wp', 'lc_alter_content_filters', PHP_INT_MAX);
function lc_alter_content_filters() {

	//IF PAGE IS NOT USING LIVECANVAS, and isnt a lc cpt,  EXIT FUNCTION
	$page_id = get_queried_object_id();  
	if (!lc_post_is_using_livecanvas($page_id))	return;
	
	//CHECK IF WE ARE IN EDITING MODE
	if (isset($_GET['lc_page_editing_mode']) && current_user_can("edit_pages") ) {
		
		//WE ARE IN EDITING MODE
		remove_all_filters('the_content');
		add_filter('the_content', 'lc_get_main_content_raw');
 
	} else {
		
		//WE ARE NOT IN EDITING MODE, A LC PAGE IS BEING SERVED
		
		//Got this list from core wp /wp-includes/default-filters.php - might be useful to update it in the future. Wp is now 572
		remove_filter( 'the_content', 'do_blocks', 9 );
		remove_filter( 'the_content', 'wptexturize' );
		remove_filter( 'the_content', 'convert_smilies', 20 );
		remove_filter( 'the_content', 'wpautop' );
		remove_filter( 'the_content', 'shortcode_unautop' );
		remove_filter( 'the_content', 'prepend_attachment' );
		remove_filter( 'the_content', 'wp_filter_content_tags' );
		remove_filter( 'the_content', 'wp_replace_insecure_home_url' );

		//more to remove, by inspection
		remove_filter( 'the_content', 'capital_P_dangit', 11 ); 
		
		//embedz, thank you rap1s
		remove_filter('the_content', array($GLOBALS['wp_embed'], 'run_shortcode'), 8);
		remove_filter('the_content', array($GLOBALS['wp_embed'], 'autoembed'), 8);

		//add filter to remove useless lc attributes, necessary only when editing
		add_filter('the_content','lc_strip_lc_attributes');
		
		//optionally, if using UnderStrap theme, wrap in a <main> - this COULD be eliminated
		$style_parent_theme = wp_get_theme(get_template());
		if ($style_parent_theme->get('Name') == "UnderStrap" or function_exists("livecanvas_print_main") ) add_filter('the_content','lc_wrap_in_main',999);


	} //end else

	// FOR DEBUG, prints all active filters, eg: http://localhost:8888/livecanvas-wp/?lc_show_filters 
	// or to see the page being edited: http://localhost:8888/livecanvas-wp/?lc_page_editing_mode=1&lc_show_filters
	if (current_user_can('administrator') && isset($_GET['lc_show_filters'])) { lc_print_filters_for('the_content'); die(); } 

}


//USED ONLY WHEN EDITING, TO GET RAW CONTENT
function lc_get_main_content_raw($input) { 
	
	$html_out = get_post_field('post_content', get_queried_object_id(), 'raw');
	
	
	if (!lc_plugin_option_is_set('disable-ob-handling')):
		
		//disable output buffering for nasty plugins like ewww that would screw stuff 
		//still breaks on some hosts, editing page is broken
		//see gail debug case and video ob

		if (is_int(ob_get_length())) ob_end_flush(); 
	
	endif;
	 
	
	return "<main id='lc-main'>" . $html_out . "</main>";
	
}

//FUNCTION used to wrap in <main> - nice for UnderStrap
function lc_wrap_in_main($input){
	return "<main id='lc-main'>" . $input . "</main>";
}


//useful FOR DEBUGGING
function lc_print_filters_for( $hook = '' ) {
    global $wp_filter;
    if( empty( $hook ) || !isset( $wp_filter[$hook] ) ) return;
	print '<h1> Listing filters applied to: '.$hook.' </h1>';
    print '<pre>';
    print_r( $wp_filter[$hook] );
    print '</pre>';
}

//ALLOW SAVING UNFILTERED HTML FOR EDITORS.
//USEFUL ON MULTISITE, OR   <SCRIPTS> and some tags are stripped away 
function lc_kses_init() {
	if (is_multisite() &&  current_user_can( 'edit_pages' ) )
		kses_remove_filters();
}

add_action( 'init', 'lc_kses_init', 11 );

