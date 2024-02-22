<?php
// EXIT IF ACCESSED DIRECTLY.
defined( 'ABSPATH' ) || exit;

//REMOVE CONTENT AUTOP FOR CPTs // not necessary anymore with new content filtering, could be eliminated
add_filter('the_content', 'lc_remove_autop_for_posttype', 0);
function lc_remove_autop_for_posttype($content) { //wal?
	('lc_block' === get_post_type() OR 'lc_section' === get_post_type() OR 'lc_partial' === get_post_type() OR 'lc_dynamic_template' === get_post_type()) && remove_filter('the_content', 'wpautop');
	return $content;
}

/// REMOVE FILTERS IF IN EDITING MODE, eg shortcode solving
add_action('wp', 'lc_remove_plugin_filters', PHP_INT_MAX);
function lc_remove_plugin_filters() {
	if (current_user_can("edit_pages") && isset($_GET['lc_page_editing_mode'])) {
		remove_all_filters('the_content');
		add_filter('the_content', 'lc_get_main_content');
	}
}

/* CRITICAL PLUS: MAIN CONTENT FILTER  */
add_filter('the_content', 'lc_get_main_content');
function lc_get_main_content($input) {
	 
	//SET A FLAG
	if (current_user_can("edit_pages") && isset($_GET['lc_page_editing_mode']))	$lc_editing_mode = TRUE;	else	$lc_editing_mode = FALSE;
	
	//GET PURE RAW CONTENT
	$page_id = get_queried_object_id();
	if (!lc_post_is_using_livecanvas($page_id))	return $input;
	$html_out = get_post_field('post_content', $page_id, 'raw');
	
	//PASSWORD PROTECTED PAGES
	if ($lc_editing_mode == FALSE && post_password_required()) $html_out = '<div class="lc-container-wrap-passwordform"><div class="container"><div class="row"><div class="col-xs-12 text-center">' . get_the_password_form() . "</div></div></div></div>";
		
	//STRIP OUT USELESS ATTRIBUTES IF NOT EDITING
	if ($lc_editing_mode == FALSE) $html_out=lc_strip_lc_attributes($html_out);
	
	$style_parent_theme = wp_get_theme(get_template());
	if ($style_parent_theme->get('Name') == "UnderStrap" or function_exists("livecanvas_print_main") ) {
		//parent theme is understrap, wrap in main
		return "<main id='lc-main'>" . $html_out . "</main>";
	} else {
		//all themes
		if ($lc_editing_mode) return "<main id='lc-main'>" . $html_out . "</main>"; 
			else return $html_out;
	}
}
