<?php
/*
Plugin Name: LiveCanvas
Description: Build better Web pages. An awesome live HTML editor focused on speed and code quality.
Version: 3.5.1
Author: The LiveCanvas Team
Author URI: https://www.livecanvas.com
*/

// EXIT IF ACCESSED DIRECTLY.
defined( 'ABSPATH' ) || exit;

//DEFINE SCRIPTS VERSION
if (strpos($_SERVER['REQUEST_URI'], '/livecanvas-wp/') !== false)	define("LC_SCRIPTS_VERSION", rand(0, 1000)); else define("LC_SCRIPTS_VERSION", "3.5.0");

if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}

//DEFINE MUST USE CONSTANTS
define('LC_MU_PLUGIN_NAME', 'livecanvas-must-use-plugin.php');
define('LC_MU_PLUGIN_TEMPLATE', 'livecanvas-must-use-plugin.template');
define('LC_MU_PLUGIN_DIR', dirname(__FILE__) . DS . 'mu' . DS);

if (!defined('LC_MU_PLUGIN')) {
	define('LC_MU_PLUGIN', WPMU_PLUGIN_DIR . DS . LC_MU_PLUGIN_NAME);
}

//EXTRAS
require("modules/admin-page-switch.php");
require("modules/plugin-settings-pages.php");
require("modules/plugin-apikey-settings.php");
require("modules/plugin-apikey-autoissue.php");
require("modules/optin-extras.php");
require("modules/shortcodes.php");
require("modules/media-selector.php");
if (lc_plugin_option_is_set("enable-dynamic-templating")):
	require("modules/templating.php");
	require("modules/templating-admin.php");
	if( !isset($_GET['lc_dynamic_template']) &&  (!is_admin() OR wp_doing_ajax())  && (!isset($_GET['post_type']) OR $_GET['post_type']!='lc_dynamic_template')) {
		require("modules/templating-shortcodes.php");
		require("modules/templating-shortcodes-woocommerce.php");
		require("modules/templating-shortcodes-woocommerce-extras.php");
	}
endif;
require("modules/starter-content.php");
require("modules/thirdparty-compatibility-extras.php");
require("modules/form-handlers.php");
require("modules/content-filtering.php");

//MUST USE STUFF ///

//must use plugin tearup
function lc_must_use_writer() {
	if(!file_exists(WPMU_PLUGIN_DIR)) {
		mkdir(WPMU_PLUGIN_DIR, 0755, true);
	}
	$fileContent = file_get_contents(LC_MU_PLUGIN_DIR .  LC_MU_PLUGIN_TEMPLATE);
	file_put_contents(LC_MU_PLUGIN, $fileContent);
}

//must use plugin teardown
function lc_must_use_deleter() {
	unlink(LC_MU_PLUGIN);
}

// on plugin activation / deactivation, copy or delete must use plugin
register_activation_hook( __FILE__, 'lc_must_use_writer');
register_deactivation_hook( __FILE__, 'lc_must_use_deleter');

//take care of must use option changes / update check
add_action('init', 'lc_check_mu_actions');
function lc_check_mu_actions() {

	//check if we have to add the file
	if (!lc_plugin_option_is_set('disable-mu-plugin') && !file_exists(LC_MU_PLUGIN)) {
		lc_must_use_writer();
	}

	//check if we have to remove the file
	if (lc_plugin_option_is_set('disable-mu-plugin') && file_exists(LC_MU_PLUGIN)) {
		lc_must_use_deleter();
	}   

	//check if we need to update mu plugin, but only while editing pages
	if (isset($_GET["lc_action_launch_editing"]) && file_exists(LC_MU_PLUGIN) && file_exists(LC_MU_PLUGIN_DIR . LC_MU_PLUGIN_TEMPLATE)) {
		include_once ABSPATH . 'wp-admin' . DS . 'includes' . DS . 'plugin.php';
		$templateData = get_plugin_data(LC_MU_PLUGIN_DIR . LC_MU_PLUGIN_TEMPLATE);
		$muPluginData = get_plugin_data(LC_MU_PLUGIN);
		if(version_compare($muPluginData['Version'], $templateData['Version'], '<')) {
			lc_must_use_writer();
		}
		unset($templateData);
		unset($muPluginData);
	}
}


//GENERAL MICRO UTILITIES
function lc_print_editor_url() { echo esc_url(plugin_dir_url( __FILE__ ).'editor/'); }

function lc_plugin_option_is_set($option_name){
	$lc_settings = get_option('lc_settings');
	$lc_settings = apply_filters('lc_settings', $lc_settings);
	return (isset($lc_settings[$option_name]));
}

function lc_get_license_code(){
	$lc_settings = get_option('lc_settings');
	if(!$lc_settings OR !isset($lc_settings['license-code']) OR strlen($lc_settings['license-code'])<4 )  return FALSE; else return $lc_settings['license-code'];
}

function lc_get_main_bs_version(){
	$bs_version = "4"; //as default
	if (function_exists('lc_theme_bootstrap_version')) $bs_version = intval(lc_theme_bootstrap_version()); //get from theme custom function
	if (lc_plugin_option_is_set('enable-bs-5') ) { $bs_version = "5"; } // or force via plugin option
	return $bs_version;
}

//IF PARENT THEME IS NOT a COMPATIBLE THEME, SHOW A SUGGESTION RECOMMENDING PICOSTRAP
$style_parent_theme = wp_get_theme(get_template());
if ($style_parent_theme->get('Name') != "UnderStrap" && !function_exists("lcta_plugin_is_enabled")) {
	add_action('admin_notices', 'lc_admin_theme_recommend_notice'); 
}
function lc_admin_theme_recommend_notice() {
	$screen = get_current_screen();
	if ($screen->base == "theme-install" OR 
		function_exists('lc_theme_is_livecanvas_friendly') OR 
		lc_plugin_option_is_set("force-embedded-template-for-lc-pages") OR 
		lc_get_theme_info( 'Name' ) ==  "Understrap") return;
?>
	<div class="notice error is-dismissible" style="padding:10px">
		<img src="<?php echo esc_url(plugin_dir_url( __FILE__ ).'images/lc-logo.svg'); ?>" style="width:250px;height: auto";>
			<p style=font-size:1rem>LiveCanvas strongly recommends you to use the <b>picostrap</b> Theme.  Get it <a target="_blank" href="https://picostrap.com/#downloads"> here </a></p>
			<p> It's a fast and versatile foundation for your site, [allowing you to customize your Bootstrap CSS directly from the Customizer interface] </p>
			 
			<p>Otherwise you can use any other Bootstrap 4/5 based theme. Learn more <a target="_blank" href="https://livecanvas.com/faq/which-themes-with-livecanvas/">here</a></p>
			<!-- <a  class="button button-primary button-hero " href="<?php	echo esc_attr(admin_url("/theme-install.php?search=understrap%20_s")); ?>">Let's install and activate the Theme</a> -->
</div> <?php
}

//IF NO APIKEY IS PRESENT, SHOW NAG
add_action('admin_notices', function(){

	if (lc_get_apikey()) return; //exit if not necessary
	lc_try_autoissue_apikey_from_license_func();
	if (lc_get_apikey()) return; //exit if just activated

	$screen = get_current_screen();
	if ($screen->base=='livecanvas_page_livecanvas_license'  ) return;
	?>
	<div class="notice error is-dismissible" style="padding:15px">
		<img src="<?php echo esc_url(plugin_dir_url( __FILE__ ).'images/lc-logo.svg'); ?>" style="width:250px;height: auto";>
		<p style=font-size:1rem>Please activate your product</a></p>
		<p>To enable automatic plugin updates and access to the Library, a valid license code is needed.<br><br></p>
		<?php if (is_main_site()) { ?>
		<a  class="button button-primary button-hero " href="<?php	echo esc_attr(admin_url("/admin.php?page=livecanvas_license")); ?>">Activate LiveCanvas</a> 
		<?php } else { ?>
			<p><b>Please perform activation from the main site of your Multisite install.</b></p>
		<?php 
		}
		?>	
	</div> <?php
}); 
 
 
//FUNCTION TO DETERMINE IF POST IS USING LIVECANVAS
function lc_post_is_using_livecanvas($post_id) {
	//old take, keep for compat test here
	//return (get_post_meta($post_id, '_lc_livecanvas_enabled', true) == '1' OR 'lc_block' === get_post_type() OR 'lc_section' === get_post_type() OR 'lcr_section' === get_post_type() OR 'lc_partial'  === get_post_type()  );
	return (
		in_array(get_post_type($post_id), array('lc_block', 'lc_section', 'lc_partial', 'lc_dynamic_template')) OR 
		get_post_meta($post_id, '_lc_livecanvas_enabled', true) == '1'
	);

}

// UTILITY: ALLOW SVG  (4 admins) AND WEBP IMAGE UPLOADS  /////////
add_filter('upload_mimes',function ($mimes){
	 if (current_user_can('administrator')) $mimes['svg'] = 'image/svg+xml';
	 $mimes['webp'] = 'image/webp';
	 return $mimes;
});

// IF FILTERS NOT DEFINED, ADD THEM, SO THEYRE NO MORE NECESSARY IN THEME
add_action('template_redirect', function(){
	//  ADD CUSTOM SECTIONS FROM THEME
	if (!has_filter('lc_load_cpt_lc_section')) 
	add_filter('lc_load_cpt_lc_section', function (array $sections) {
		
		foreach (glob(get_stylesheet_directory() . '/template-livecanvas-sections/*.html') as $section) {
			$pathInfo = pathinfo($section);
			$name = ucwords(str_replace('-', ' ', $pathInfo['filename']));
			$sections[] = [
				'id' => 'section-' . rand(),
				'name' => $name,
				'description' => $name,
				'template' => file_get_contents($section)
			];
		}
		return $sections;
	}, PHP_INT_MAX-1);

	// ADD CUSTOM BLOCKS FROM THE THEME
	if (!has_filter('lc_load_cpt_lc_block')) 
	add_filter('lc_load_cpt_lc_block', function (array $blocks) {
		foreach (glob(get_stylesheet_directory() . '/template-livecanvas-blocks/*.html') as $block) {
			$pathInfo = pathinfo($block);
			$name = ucwords(str_replace('-', ' ', $pathInfo['filename']));
			$blocks[] = [
				'id' => 'block-' . rand(),
				'name' => $name,
				'description' => $name,
				'template' => file_get_contents($block)
			];
		}
		return $blocks;
	}, PHP_INT_MAX-1);
});

/////// CHECK URL ACTIONS IN FRONTEND ////////////////////////////////
add_action('template_redirect', 'lc_check_url_actions');
function lc_check_url_actions() {
	
	//IF THE USER TRIES TO EDIT BUT ITS NOT LOGGED IN, LET HIM LOG IN
	if (!is_user_logged_in() && isset($_GET['lc_action_launch_editing'])) {
		wp_redirect(wp_login_url(add_query_arg(array('lc_action_launch_editing' => '1'), get_permalink())));
		exit;
	}
	
	//DEMO MODE ONLY
	if (function_exists('lc_demo_enabled') && isset($_GET['lc_action_launch_demo']) && $_GET['lc_action_launch_demo'] == "1") {	include("editor/editor.php");	die;	}

	//FOLLOWING STUFF IS ONLY FOR SUPER ADMINS AND WHEN EDITING IS ENABLED  
	if (!current_user_can("edit_pages")) return;
	
	//EDITOR
	if (isset($_GET['lc_action_launch_editing']) && $_GET['lc_action_launch_editing'] == "1" && lc_get_apikey()) {	include("editor/editor.php");	die;	}
	
	//EDITOR REDIRECT
	
	if (isset($_GET['lc_redirect_to_edit_post_id']) ) {	wp_redirect( ( add_query_arg( array('lc_action_launch_editing'=> '1','from_page_edit' =>'1'), get_permalink($_GET['lc_redirect_to_edit_post_id']))));	die;	}
	
	//FA4
	if (isset($_GET['lc_action']) && $_GET['lc_action'] == "load_fa4_icons") {		include("editor/icons/icons-fontawesome-4.html");		die;	}
	//BOOTSTRAP ICONS
	if (isset($_GET['lc_action']) && $_GET['lc_action'] == "load_bs_icons") {		include("editor/icons/icons-bootstrap.html");		die;	}
	
	/// FA 5
	if (isset($_GET['lc_action']) && $_GET['lc_action'] == "load_fa5-regular_icons") {		include("editor/icons/icons-fa5-regular.html");		die;	}
	if (isset($_GET['lc_action']) && $_GET['lc_action'] == "load_fa5-brands_icons") {		include("editor/icons/icons-fa5-brands.html");		die;	}
	if (isset($_GET['lc_action']) && $_GET['lc_action'] == "load_fa5-solid_icons") {		include("editor/icons/icons-fa5-solid.html");		die;	}
	
	//MDI
	if (isset($_GET['lc_action']) && $_GET['lc_action'] == "load_mdi_icons") {		include("editor/icons/icons-mdi.html");		die;	}

	// LOAD BLOCKS / SECTIONS  via CPTS / FILTERS - ticket 117
	if (isset($_GET['lc_action']) && $_GET['lc_action'] == "load_cpt") { //load your components case
		
		global $post;
		$args    = array(
			'posts_per_page' => 115,
			'post_type' => $_GET['cpt_post_type']
		);
		$myposts = get_posts($args);
		
		$templates = [];
		foreach ($myposts as $post) {
			$templates[] = [
				'id' => get_the_ID(),
				'name' => get_post_field('post_title', get_the_ID(), 'raw'),
				'description' => get_post_field('post_excerpt', get_the_ID(), 'raw'),
				'template' => get_post_field('post_content', get_the_ID(), 'raw')
			];
		}
		$templates = apply_filters('lc_load_cpt_'. $_GET['cpt_post_type'], $templates);
		
		if (!$templates) { ?>
			<p class="none-yet">None yet</p>
			<?php
		} else
			foreach ($templates as $template):
			?>
					<block data-id="<?php echo $template['id']; ?>">
						<h5 class="block-name"><?php echo $template['name'];?></h5>
						 <i class="block-description"><?php	echo $template['description'];?></i> 
						<template><?php	echo $template['template']; ?></template>
					</block>
			<?php
			endforeach;
			?><a class="open-cpt-archive lc-button" target="_blank" href="<?php	echo admin_url('edit.php?post_type=' . $_GET['cpt_post_type']); ?>">Open <?php echo ucfirst(substr($_GET['cpt_post_type'], 3));?>s Archive</a><?php
			die;
	} // end if
	
	
} //end function

/////// CHECK URL ACTIONS IN BACKEND ////////////////////////////////
add_action("admin_init", "lc_check_url_actions_backend");
function lc_check_url_actions_backend() {
	
	if (!current_user_can("edit_pages")) return; //EDITORS ONLY allowed here below

	if ( isset($_GET['lc_action_new_page']) OR isset($_GET['lc_action_clone_page']) ) {

		//we have to create  a new lc page, or to clone a post: let's prepare the data
		
		if (isset($_GET['lc_action_clone_page']) ){
			//clone case
			$post_title = get_post_field('post_title', $_GET['lc_original_page_id'], 'raw') . " - Copy";
			$post_content = get_post_field('post_content', $_GET['lc_original_page_id'], 'raw');
			$post_type = get_post_field('post_type', $_GET['lc_original_page_id'], 'raw');
			$livecanvas_enabled = (get_post_meta($_GET['lc_original_page_id'], '_lc_livecanvas_enabled', true) == '1');
			$template = get_post_meta($_GET['lc_original_page_id'], '_wp_page_template', true);

		} else {
			//new lc page case
			if (isset($_GET['lc_page_name'])) $post_title = $_GET['lc_page_name']; else	$post_title = 'Untitled LiveCanvas Page';
			$post_content = '';
			$post_type = 'page';
			$livecanvas_enabled = TRUE;
			$template = 'page-templates/empty.php';
		} 
		
		//insert the new post
		$post_id = wp_insert_post(array('post_title' => $post_title, 'post_status' => 'draft', 'post_type' => $post_type, 'post_content' => $post_content));
		
		if ($livecanvas_enabled) update_post_meta($post_id, '_lc_livecanvas_enabled', 1);
		update_post_meta($post_id, '_wp_page_template', $template);
		
		//now redirect to edit the newly created post
		if ($livecanvas_enabled) wp_redirect(add_query_arg(array('lc_action_launch_editing' => '1'), get_permalink($post_id))); else 
			wp_redirect( admin_url('post.php?post=' . $post_id . '&action=edit') );

		exit;
		
	} //end if

 


} //end func


////////HIDE TOOLBAR IF EDITING ////////////////////////////
add_action('wp_loaded', 'lc_check_early_actions');
function lc_check_early_actions() {
	if (isset($_GET['lc_page_editing_mode'])) {
		add_filter('show_admin_bar', '__return_false');
		add_filter('edit_post_link', '__return_false');
	}
	
}

////////////PAGE HTML & CSS SAVING /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function lc_tweak_css($css){$css= (stripslashes($css));$css=trim(preg_replace('/\t+/', '', $css));return $css; }


add_action('wp_ajax_lc_save_page', 'lc_ajax_save_page_func');

function lc_ajax_save_page_func() {
	
	if (!current_user_can("edit_pages")) return; //Only for those who can
	
	$current_template = get_post_meta($_POST['post_id'], '_wp_page_template', true);

	$update_post = array(
		'ID' => $_POST['post_id'],
		'post_content' => $_POST['html_to_save']
	);
	
	//UPDATE THE PAGE CONTENT INTO THE DATABASE
	remove_all_shortcodes(); //https://stackoverflow.com/questions/49238257/preventing-shortcode-execution-with-wp-update-post
	$the_update = wp_update_post($update_post);
		
	//UPDATE GLOBAL CSS, if it's different from current one
	if (wp_get_custom_css_post() != lc_tweak_css($_POST['css_to_save'])) wp_update_custom_css_post(lc_tweak_css($_POST['css_to_save']));
		
	//FOR COMPATIBILITY WITH LC ON POSTS: SINCE THE UPDATE WE JUST DONE BEFORE F*CKS UP THE  CHOSEN TEMPLATE FIELD, RESTORE IT 
	if ( get_post_type( $_POST['post_id'] ) != 'page' ) update_post_meta($_POST['post_id'], '_wp_page_template', $current_template); 

	if ($the_update == true) echo "Save"; else echo "Error!";
	wp_die();
	
}

///////// SAVE BLOCK / SECTION TO LOCAL LIBRARY /////////////////

add_action('wp_ajax_lc_save_element', 'lc_ajax_save_element_func');

function lc_ajax_save_element_func() {
	
	if (!current_user_can("edit_pages")) return; //Only for those who can
	
	remove_all_shortcodes(); //https://stackoverflow.com/questions/49238257/preventing-shortcode-execution-with-wp-update-post

	$the_insert=wp_insert_post(array(
		'post_type' => $_POST['post_type'],
		'post_title' => $_POST['post_title'],
		'post_content' => $_POST['post_content'],
		'post_status' => 'publish'));

	
	if ($the_insert == true) echo "Save"; else echo "Error!";
	wp_die();
	
}


// EDITING TRIGGER LINKS: Place in admin menu bar a link to trigger page editing
if (lc_get_apikey()) add_action('admin_bar_menu', 'lc_add_toolbar_items', 100);
function lc_add_toolbar_items($admin_bar) {
	//check if user has rights to edit,   and that  we are not in editing mode 
	if (!current_user_can("edit_pages") or isset($_GET['lc_action_launch_editing'])) return;
	
	
	//ADD LINK: NEW LIVECANVAS PAGE LINK
	global $wp_admin_bar;
	if (current_user_can("administrator") OR !lc_plugin_option_is_set("simplified-client-ui"))  $wp_admin_bar->add_node(array(
		'parent' => 'new-content',
		'id' => 'lc-add-new-page',
		'class' => 'ab-item',
		'title' =>  __('LiveCanvas Page Draft', 'livecanvas'),
		'href' => add_query_arg(array(
			'lc_action_new_page' => '1'
		), get_admin_url()),
		'meta' => array(
			'onclick' => 'var page_name = prompt("'.__('New page name', 'livecanvas').'", "Untitled LC Page");if (page_name!=null)  window.location = this.getAttribute("href") +"&lc_page_name="+encodeURIComponent(page_name);   return false;'
		)
	));
	
	// ADD LINK: LAUNCH LC EDITING of the page
	if (is_admin())	return; //ONLY IN FRONTEND
	if (!is_single() && !is_page())	return; //ONLY SINGLE POSTS OR PAGES OR CPTs
	if (!lc_post_is_using_livecanvas(get_the_ID()))	return; // the page is not using a LC template
	
	global $wp_admin_bar;
	if(!lc_plugin_option_is_set("whitelabel"))
		$wp_admin_bar->add_node(array(
			'id' => 'lc-launch-editing',
			'title' => '<span id="icon-lc-launch-editing"></span>' . __('Edit with', 'livecanvas'),
			'href' => add_query_arg(array(
				'lc_action_launch_editing' => '1'
			))
		));
		else
		$wp_admin_bar->add_node(array(
			'id' => 'lc-launch-editing',
			'title' => __('Edit in Frontend', 'livecanvas'),
			'href' => add_query_arg(array(
				'lc_action_launch_editing' => '1'
			))
		));
	//OPTIONALLY...
	//$wp_admin_bar->remove_menu('edit');
	
	//ADD CHILDS
	//repeat current page
	$wp_admin_bar->add_node(array(
			'id' => 'lc-launch-current-editing',
			'parent' => 'lc-launch-editing',
			'title' =>  __('Edit Current Page', 'livecanvas'),
			'href' => add_query_arg(array(
				'lc_action_launch_editing' => '1'
			))
		));
	//header
	if(lc_plugin_option_is_set("header"))  $wp_admin_bar->add_node(array(
			'id' => 'lc-launch-header-editing',
			'parent' => 'lc-launch-editing',
			'title' =>  __('Edit Header', 'livecanvas'),
			'href' => add_query_arg(array(
				'lc_action_launch_editing' => '1'), get_permalink(    lc_get_partial_postid('is_header', "1") 
			))
		));
	//footer
	if(lc_plugin_option_is_set("footerV2"))  $wp_admin_bar->add_node(array(
			'id' => 'lc-launch-footer-editing',
			'parent' => 'lc-launch-editing',
			'title' =>  __('Edit Footer', 'livecanvas'),
			'href' => add_query_arg(array(
				'lc_action_launch_editing' => '1'), get_permalink(    lc_get_partial_postid('is_footer', "1") 
			))
		));
} //end func

// EDITING TRIGGER LINKS FOR DYN TEMPLATES: Place in admin menu bar a link to trigger page editing
if (lc_get_apikey()) add_action('admin_bar_menu', 'lc_add_toolbar_items_dyn_templates', 100);
function lc_add_toolbar_items_dyn_templates($admin_bar) {
	//check if user has rights to edit,   and that  we are not in editing mode 
	if (!current_user_can("administrator") or isset($_GET['lc_action_launch_editing'])) return;	
	global $wp_admin_bar;
	
	// ADD LINK: TO DYNAMIC TEMPLATE EDITING  
	global $lc_rendered_dynamic_template_id;	
	if(isset($lc_rendered_dynamic_template_id))  $wp_admin_bar->add_node(array(
			'id' => 'lc-launch-header-editing', 
			'title' =>  __('Edit Dynamic '.ucwords(get_the_title($lc_rendered_dynamic_template_id)), 'livecanvas'),
			'href' => esc_url( add_query_arg( array('lc_action_launch_editing'=> '1','from_page_edit' =>'1','demo_id' => get_the_ID(),), get_permalink($lc_rendered_dynamic_template_id))),
			//'href' => get_edit_post_link($lc_rendered_dynamic_template_id), //alternative to edit the post without LC
			'meta' => array('target' => '_blank'),
			
		));

} //end func

///ADD NEW ELEMENT TO WP-ADMIN LEFT MENU
function lc_add_admin_menu_item() {
	if (current_user_can("administrator") OR !lc_plugin_option_is_set("simplified-client-ui")) add_pages_page(__('Add LiveCanvas Page'), __('Add LiveCanvas Page'), 'edit_pages', '#lc_click_action_new_page');
}
if (lc_get_apikey()) add_action('admin_menu', 'lc_add_admin_menu_item');

//////////////////ADD JS TO MAKE THAT LINK ACTUALLY WORK /////////////
function lc_add_admin_js() {
	?> 
	<script>
		document.addEventListener("DOMContentLoaded", function() { 
			var lc_link_el = document.querySelector("a[href='#lc_click_action_new_page']");
			if(lc_link_el) lc_link_el.addEventListener("click", function(event){
				event.preventDefault();
				document.querySelector("#wp-admin-bar-lc-add-new-page a").click();
			}); //end event click
		});	//end DOMContentLoaded
	</script>
	<?php
} //end func

add_action('admin_head', 'lc_add_admin_js');


 
//Add a post display state for Livecanvas posts and pages in the list table.
 
add_filter(
	'display_post_states',
	function ( $post_states, $post ) { 

		if ( get_post_field( '_lc_livecanvas_enabled', $post ) == 1 ) {
			$post_states['lc-content'] = __( '[LiveCanvas]', 'livecanvas' );
		}

		return $post_states;
	},
	10,
	2
);


/////// ICON IN TOOLBAR STYLING ///////////////////////////////////////////////////
add_action('admin_head', 'lc_print_launch_icon_styles'); // on backend area
add_action('wp_head', 'lc_print_launch_icon_styles'); // on frontend area
function lc_print_launch_icon_styles() {
	if (!is_user_logged_in())
		return;
?>
	<style> 
		#icon-lc-launch-editing:after {
			position: relative;    float: right;    content: ' ';    min-width: 86px;    height: 13px;    margin-right: 6px;
			margin-top: 9px;    margin-left: 4px;    background-size: contain;    background-repeat: no-repeat;
			background-image: url('<?php echo esc_url(plugin_dir_url( __FILE__ )) ?>images/lc-logo.svg'); 
		}	
	</style>
	<?php
}


/// HIDE WP ADMIN BAR WHILE EDITING WITH LC
add_action('wp_loaded', 'lc_handle_actions');
function lc_handle_actions() {
	if (!current_user_can("edit_pages") or is_admin()) return;
	global $wp_admin_bar;
	if (isset($_GET['lc_action_launch_editing'])) add_filter('show_admin_bar', '__return_false');
}



// WHEN EDITING PARTIALS, BLOCKS, SECTION, HIDE HEADER AND FOOTER - Legacy, for Customstrap
add_action("template_redirect",function(){
	if ( current_user_can("edit_pages") && isset($_GET['lc_page_editing_mode']) && in_array(get_post_type(), array('lc_block', 'lc_section', 'lc_partial', 'lc_dynamic_template')) 	 ):
		if ( !function_exists('customstrap_custom_header')){function customstrap_custom_header($variant=1){} function lc_custom_header($variant=1){} }
		if ( !function_exists('customstrap_custom_footer')){function customstrap_custom_footer($variant=1){} function lc_custom_footer($variant=1){} }
		?> <!-- <style>#wrapper-navbar {display: none} </style> --><?php //old hack - needed to be hooked to wp_head
	endif;
});

// function to strip useless tags, should be used by critical when not editing
function lc_strip_lc_attributes($html){
	if ($_SERVER['SERVER_NAME'] ==  'library.livecanvas.com')  return $html;
	$html = str_replace(' editable="inline"', "", $html);
	$html = str_replace(' editable="rich"', "", $html);
	$html = str_replace(' lc-helper="svg-icon"', "", $html);
	//
	$html = str_replace(' lc-helper="background"', " ", $html);
	$html = str_replace(' lc-helper="video-bg"', " ", $html);
	$html = str_replace(' lc-helper="gmap-embed"', " ", $html);
	$html = str_replace(' lc-helper="video-embed"', " ", $html);
	$html = str_replace(' lc-helper="shortcode"', " ", $html);
	$html = str_replace(' lc-helper="image"', " ", $html);
	$html = str_replace(' lc-helper="icon"', " ", $html);
	
	return $html;
}
/* END of the BLOCK. Shorter than you have thought! Optionally, get also the header & footer stuff below.  */

//GET HEADER HTML
function  lc_get_header($variant=1){
	if (in_array(get_post_type(), array('lc_block', 'lc_section', 'lc_partial', 'lc_dynamic_template_NO')) ) return '';
	$header_html = get_post_field( 'post_content', lc_get_partial_postid('is_header', $variant), 'raw' );
	return  "\n\n\n<header id='lc-header'>".do_shortcode(lc_neutralize_section_tags(lc_strip_lc_attributes($header_html)))."</header>\n\n\n";
}

//GET FOOTER HTML
function  lc_get_footer($variant=1){
	if (in_array(get_post_type(), array('lc_block', 'lc_section', 'lc_partial', 'lc_dynamic_template_NO')) ) return '';
	$footer_html = get_post_field( 'post_content', lc_get_partial_postid('is_footer', $variant), 'raw' );
	return  "\n\n\n<footer id='lc-footer'>".do_shortcode(lc_neutralize_section_tags(lc_strip_lc_attributes($footer_html)))."</footer>\n\n\n";
}

function lc_neutralize_section_tags($html){
	$html = str_replace('<section', '<div', $html);
	$html = str_replace('</section>', '</div>', $html);
	return $html;
}

//DECLARE THE FUNCTION TO INTERFACE HEADER WITH CUSTOMSTRAP / OTHER THEMES 
if (lc_plugin_option_is_set("header") && !function_exists('customstrap_custom_header')):
	function customstrap_custom_header($variant=1){echo lc_get_header($variant); }
	function lc_custom_header($variant=1){echo lc_get_header($variant); }
endif;

//DECLARE THE FUNCTION TO INTERFACE FOOTER WITH CUSTOMSTRAP / OTHER THEMES 
if (lc_plugin_option_is_set("footerV2") && !function_exists('customstrap_custom_footer')):
	function customstrap_custom_footer($variant=1){echo lc_get_footer($variant); }
	function lc_custom_footer($variant=1){echo lc_get_footer($variant); }
endif;

///ADD LC EDITING LINKS TO PAGE LISTING IN THE WP ADMIN////
add_filter('page_row_actions', 'lc_add_action_links', 10, 2);
add_filter('post_row_actions', 'lc_add_action_links', 10, 2);
function lc_add_action_links($actions, $page_object) {
	if ( lc_post_is_using_livecanvas($page_object->ID))	$actions['lc_edit_page'] = "<a class='lc_edit_page' href='" . esc_url(add_query_arg(array('lc_action_launch_editing' =>'1','from_page_edit' => 1), get_permalink($page_object->ID))) . "'>" . __('Edit with LiveCanvas', 'livecanvas') . "</a>";
	if (current_user_can("edit_pages") && !function_exists('lc_hide_clone_page_links')) $actions['lc_clone_page'] = "<a class='lc_clone_page' href='" . esc_url( add_query_arg(array('lc_action_clone_page' => '1','lc_original_page_id' => $page_object->ID, 'from_page_edit' => 1	), get_admin_url()) ) . "'>" . __('Clone', 'livecanvas') . "</a>";
	return $actions;
}


/////////

//GET PARTIAL CPT ID: check if post exists; if not, create it. Useful for Header and Footer and dynamic templates
function lc_get_partial_postid($field_name, $field_value=1) { 

	$my_posts = get_posts(array('post_type'=> 'lc_partial', 'orderby' => 'menu_order', 'meta_key' => $field_name, 'meta_value' => $field_value,'numberposts' => 1, 'post_status'    => 'publish'));
	if( $my_posts ){
		//there are results, return the first one
		$partial_ID= $my_posts[0]->ID;
	} else {
		//no results: if the user is logged, let's create a new dummy
		if (!is_user_logged_in() OR !is_admin()) return FALSE;
		$partial_ID = wp_insert_post(array('post_type' => 'lc_partial','post_title' => ucwords(str_replace('_',' ',substr($field_name . (is_numeric($field_value)? '': ': '.$field_value ),3))   ), 'post_content' => lc_get_starter_content($field_name, $field_value), 'post_status' => 'publish'));
		update_post_meta($partial_ID, $field_name, $field_value);
	}
	return $partial_ID;
}


//////////// AJAX FETCH OEMBED CODE /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_action('wp_ajax_lc_process_oembed', 'lc_process_oembed_func');
function lc_process_oembed_func() {
	if (!current_user_can("edit_pages")) return; //Only for editors
	
	$content = "[embed]" . $_POST['src_url'] . "[/embed]";
	global $post;

	//still useful? //trick to allow content filtering in ajax calls - to say, I love you
	if (version_compare(PHP_VERSION, '8.0.0', '<')) $post->ID = PHP_INT_MAX; 
	
	remove_filter('the_content', 'wptexturize'); //remve #38
	$embed_code = apply_filters('the_content', $content);
	
	//get the url only
	$embed_code_exploded = explode(' src="', $embed_code);
	$embed_code_exploded = explode('"', $embed_code_exploded[1]);
	echo $embed_code_exploded[0];
	wp_die();
}

//////////// AJAX FETCH SHORTCODE /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_action('wp_ajax_lc_process_shortcode', 'lc_process_shortcode_func');
function lc_process_shortcode_func() {
	
	if (!current_user_can("edit_pages")) return; //Only for editors				
	
	global $post;
	
	if (isset($_POST['post_id']) && is_numeric($_POST['post_id'])) {
		$post = get_post( $_POST['post_id']); 
	}
	
	//$post->ID = PHP_INT_MAX; // no more useful trick to allow content filtering in ajax calls - to say, I love you
	
	$input = stripslashes($_POST['shortcode']);
	$output   = do_shortcode($input);
	
	if ($input == $output) $output = "<b>Unrecognized Shortcode</b>";
	
	echo $output;
	wp_die();
}


///////// AJAX GET IMAGE SRCSET AJAX ACTION, gets the full img tag /////////////////
add_action('wp_ajax_lc_get_img_tag', 'lc_ajax_get_img_tag_func');

function lc_ajax_get_img_tag_func() {
	$id = $_POST['post_id'];
	$format = $_POST['format'] ?? 'full';
	if (!$id) {
		wp_die();
	}
	
	$img = wp_get_attachment_image_src($id, $format);
	if (!$img) {
		wp_die();
	}

	list( $src, $width, $height ) = $img;
	$metas = wp_get_attachment_metadata($id);
	$metasHeight = $metas['sizes'][$format]['height'] ?? $height;
	$metasWidth = $metas['sizes'][$format]['width'] ?? $width;
	$srcset = wp_calculate_image_srcset([$width, $height], $src, $metas, $id);
	$sizes = $srcset ? wp_calculate_image_sizes($format, $src, $metas, $id) : false;
	$alt = get_post_meta($id, '_wp_attachment_image_alt', TRUE);
	
	$filetype = wp_check_filetype($metas['file']);
	$ext = $filetype['ext'];

	/**
	 * remove width and height if extension isn't readable 
	 */
	switch($ext) {
		case 'svg':
			$metasWidth = $metasWidth = $width = $height = null;
		 break;
		case false:
			$metasHeight = $metasWidth = $width = $height = null;
		 break;
	}

	$result = array_filter([
		'src' => $src,
		'width' => $metasWidth,
		'height' => $metasHeight,
		'sizes' => $sizes,
		'srcset' => $srcset,
		'alt' => $alt,
	]);

	echo json_encode($result);

	wp_die(); 
}

add_action('wp_ajax_lc_ajax_unsplash_srcset', 'lc_ajax_unsplash_srcset');

function lc_ajax_unsplash_srcset() {
	
	$str = $_POST['image_url'];
	$query = parse_url($str, PHP_URL_QUERY);
	$url = str_replace($query, null, $str); //url without parameters
	parse_str($query, $parse);

	$wpDefinedSizes = get_intermediate_image_sizes();
	$wpAdditionalSizes = wp_get_additional_image_sizes();	
	$res = [$str . ' ' . $parse['w'] . 'w'];
	$widths = [$parse['w']];
	
	foreach($wpDefinedSizes as $v) {
		if (array_key_exists($v, $wpAdditionalSizes)) {
			continue; //skip custom formats - this row could be commented
		}
		//check format size setting
		$w = $wpAdditionalSizes[$v]['width'] ?? get_option($v . '_size_w');
		$h = $wpAdditionalSizes[$v]['width'] ?? get_option($v . '_size_h');
		
		$scheme = $parse;
		$scheme['w'] = $w;
		unset($scheme['h']); //non abbiamo bisogno dell'h per mantenere l'aspect ratio
		
		$res[] = $url . '?' . http_build_query($scheme) . ' ' . $w . 'w';
		$widths[] = $w;
	}

	$max = max($widths);
	$sizes = count($res) ? '(max-width: ' . $max . 'px) 100vw, ' . $max . 'px' : null;
	$results = array_filter([
		'srcset' => implode(', ', $res),
		'sizes' => $sizes,
		'width' => $parse['w'],
		'height' => $parse['h'],
	]);
	
	echo json_encode($results);
	wp_die();
}

///////////////////// CUSTOM BLOCKS & SECTION CUSTOM POST TYPE REGISTRATION ////////////////////////////////////////////////////////////////////////

function lc_cpts() {
	
	$labels = array(
		'name' => _x('Blocks', 'Post Type General Name', 'livecanvas'),
		'singular_name' => _x('Block', 'Post Type Singular Name', 'livecanvas'),
		'menu_name' => __('Custom Blocks', 'livecanvas'),
		'name_admin_bar' => __('Block', 'livecanvas'),
		'archives' => __('Item Archives', 'livecanvas'),
		'attributes' => __('Item Attributes', 'livecanvas'),
		'parent_item_colon' => __('Parent Item:', 'livecanvas'),
		'all_items' => __('All Items', 'livecanvas'),
		'add_new_item' => __('Add New Item', 'livecanvas'),
		'add_new' => __('Add New', 'livecanvas'),
		'new_item' => __('New Item', 'livecanvas'),
		'edit_item' => __('Edit Item', 'livecanvas'),
		'update_item' => __('Update Item', 'livecanvas'),
		'view_item' => __('View Item', 'livecanvas'),
		'view_items' => __('View Items', 'livecanvas'),
		'search_items' => __('Search Item', 'livecanvas'),
		'not_found' => __('Not found', 'livecanvas'),
		'not_found_in_trash' => __('Not found in Trash', 'livecanvas'),
		'featured_image' => __('Featured Image', 'livecanvas'),
		'set_featured_image' => __('Set featured image', 'livecanvas'),
		'remove_featured_image' => __('Remove featured image', 'livecanvas'),
		'use_featured_image' => __('Use as featured image', 'livecanvas'),
		'insert_into_item' => __('Insert into item', 'livecanvas'),
		'uploaded_to_this_item' => __('Uploaded to this item', 'livecanvas'),
		'items_list' => __('Items list', 'livecanvas'),
		'items_list_navigation' => __('Items list navigation', 'livecanvas'),
		'filter_items_list' => __('Filter items list', 'livecanvas')
	);
	$args   = array(
		'label' => __('Block', 'livecanvas'),
		'description' => __('Your own HTML snippets intended  as small, atomic "starters" you can use as blueprints and  re-edit in the LiveCanvas editor.
		<br>You can save a new Block directly from within the LiveCanvas editor interface, using "Save to Library..." in the Block contextual menu.', 'livecanvas'),
		'labels' => $labels,
		'supports' => array('title', 'editor', 'revisions'),
		'hierarchical' => false,
		'public' => false,
		'show_ui' => true,
		'show_in_menu' => false,
		'menu_position' => 100,
		'menu_icon' => 'dashicons-welcome-write-blog',
		'show_in_admin_bar' => 0,
		'show_in_nav_menus' => false,
		'can_export' => true,
		'has_archive' => false,
		'exclude_from_search' => true,
		'publicly_queryable' => (current_user_can('administrator')),
		'rewrite' => false,
		'capability_type' => 'page',
		'show_in_rest' => false
	);
	register_post_type('lc_block', $args);
	
 
	
	$labels = array(
		'name' => _x('Sections', 'Post Type General Name', 'livecanvas'),
		'singular_name' => _x('Section', 'Post Type Singular Name', 'livecanvas'),
		'menu_name' => __('Custom Sections', 'livecanvas'),
		'name_admin_bar' => __('Section', 'livecanvas'),
		'archives' => __('Item Archives', 'livecanvas'),
		'attributes' => __('Item Attributes', 'livecanvas'),
		'parent_item_colon' => __('Parent Item:', 'livecanvas'),
		'all_items' => __('All Items', 'livecanvas'),
		'add_new_item' => __('Add New Item', 'livecanvas'),
		'add_new' => __('Add New', 'livecanvas'),
		'new_item' => __('New Item', 'livecanvas'),
		'edit_item' => __('Edit Item', 'livecanvas'),
		'update_item' => __('Update Item', 'livecanvas'),
		'view_item' => __('View Item', 'livecanvas'),
		'view_items' => __('View Items', 'livecanvas'),
		'search_items' => __('Search Item', 'livecanvas'),
		'not_found' => __('Not found', 'livecanvas'),
		'not_found_in_trash' => __('Not found in Trash', 'livecanvas'),
		'featured_image' => __('Featured Image', 'livecanvas'),
		'set_featured_image' => __('Set featured image', 'livecanvas'),
		'remove_featured_image' => __('Remove featured image', 'livecanvas'),
		'use_featured_image' => __('Use as featured image', 'livecanvas'),
		'insert_into_item' => __('Insert into item', 'livecanvas'),
		'uploaded_to_this_item' => __('Uploaded to this item', 'livecanvas'),
		'items_list' => __('Items list', 'livecanvas'),
		'items_list_navigation' => __('Items list navigation', 'livecanvas'),
		'filter_items_list' => __('Filter items list', 'livecanvas')
	);
	$args   = array(
		'label' => __('Section', 'livecanvas'),
		'description' => __('Your own HTML snippets intended as larger "starters" you can use as blueprints and re-edit in the LiveCanvas editor. 
		<br>You can save a new Section directly from within the LiveCanvas editor interface, using "Save to Library..." in the Section contextual menu.', 'livecanvas'),
		'labels' => $labels,
		'supports' => array('title','editor','revisions' ),
		'hierarchical' => false,
		'public' => false,
		'show_ui' => true,
		'show_in_menu' => false,
		'menu_position' => 100,
		'menu_icon' => 'dashicons-welcome-write-blog',
		'show_in_admin_bar' => 0,
		'show_in_nav_menus' => false,
		'can_export' => true,
		'has_archive' => false,
		'exclude_from_search' => true,
		'publicly_queryable' => (current_user_can('administrator')),
		'rewrite' => false,
		'capability_type' => 'page',
		'show_in_rest' => false
	);
	register_post_type('lc_section', $args);
 


    register_post_type( 'lc_gt_block',
        array(
            'labels' => array(
                'name' => __( 'Gutenberg Blocks' ),
                'singular_name' => __( 'Gutenberg Block' ),
				'add_new_item' => __('Add New Gutenberg Block', 'livecanvas'),
				'edit_item' => __('Edit Gutenberg Block', 'livecanvas'),
            ),
			'description' => __('Gutenberg Blocks are elements you can craft with the Gutenberg editor and recall throughout the site via Shortcodes.', 'livecanvas'),
            'has_archive' => false,
			'hierarchical' => false,
            'public' => false,
			'show_ui' => true,
            'show_in_rest' => true,
            'supports' => array('title','editor','revisions'),
			'show_in_menu' => false,
			'menu_position' => 100,
			'menu_icon' => 'dashicons-welcome-write-blog',
			'show_in_admin_bar' => 0,
			'show_in_nav_menus' => false,
			'can_export' => true,
			'has_archive' => false,
			'exclude_from_search' => true,
			'publicly_queryable' => (current_user_can('administrator')),
			'rewrite' => false,
			'capability_type' => 'post',
		
        )
    );
 

    register_post_type( 'lc_partial',
        array(
            'labels' => array(
                'name' => __( 'Template Partials' ),
                'singular_name' => __( 'Template Partial' ),
				'add_new_item' => __('Add New Partial', 'livecanvas'),
				'edit_item' => __('Edit Partial', 'livecanvas'),
            ),
			'description' => __('Template Partials are HTML chunks you can recall to display multiple times the very same element. 
			<br>Differently from what happens with Sections and Blocks, Template Partials cannot be consumed as HTML to re-edit in the LiveCanvas editor in your design, they can be just recalled as a whole using shortcodes.', 'livecanvas'),
            'has_archive' => false,
			'hierarchical' => false,
            'public' => false,
			'show_ui' => true,
            'show_in_rest' => false,
            'supports' => array('title','editor', 'revisions', 'page-attributes' ),
			'show_in_menu' => false,
			'menu_position' => 100,
			'menu_icon' => 'dashicons-welcome-write-blog',
			'show_in_admin_bar' => 0,
			'show_in_nav_menus' => false,
			'can_export' => true,
			'has_archive' => false,
			'exclude_from_search' => true,
			'publicly_queryable' => (current_user_can('administrator')),
			'rewrite' => false,
			'capability_type' => 'page',
        )
    );	

	register_post_type( 'lc_dynamic_template',
        array(
            'labels' => array(
                'name' => __( 'Dynamic Templates' ),
                'singular_name' => __( 'Dynamic Template' ),
				'add_new_item' => __('Add New Dynamic Template', 'livecanvas'),
				'edit_item' => __('Edit Dynamic Template', 'livecanvas'),
            ),
			'description' => __('Dynamic Templates are useful for handling the WordPress single templates, category archives, 404s, WooCommerce products, etc...<br>Learn more about <a target="_blank" href="https://docs.livecanvas.com/dynamic-templating/">dynamic templating</a> and <a target="_blank" href="https://docs.livecanvas.com/woocommerce/">WooCommerce templating</a>.', 'livecanvas'),
            'has_archive' => false,
			'hierarchical' => false,
            'public' => false,
			'show_ui' => true,
            'show_in_rest' => false,
		'supports' => array('title','editor', 'revisions', 'page-attributes', /* 'custom-fields' */ ), //useful for more flexibility
			'show_in_menu' => false,
			'menu_position' => 100,
			'menu_icon' => 'dashicons-welcome-write-blog',
			'show_in_admin_bar' => 0,
			'show_in_nav_menus' => true,
			'can_export' => true,
			'has_archive' => false,
			'exclude_from_search' => true,
			'publicly_queryable' => (current_user_can('administrator')),
			'rewrite' => false,
			'capability_type' => 'page',
        )
    );	

}
add_action('init', 'lc_cpts', 0);

//PRINT CPT DESCRIPTIONS
add_filter("views_edit-lc_block", 'lc_show_post_type_description'); 
add_filter("views_edit-lc_section", 'lc_show_post_type_description');
 
add_filter("views_edit-lc_gt_block", 'lc_show_post_type_description'); 
add_filter("views_edit-lc_partial", 'lc_show_post_type_description');
add_filter("views_edit-lc_dynamic_template", 'lc_show_post_type_description');

function  lc_show_post_type_description( $views ){
    $screen = get_current_screen();
    $post_type = get_post_type_object($screen->post_type);
    if ($post_type->description) {
      printf('<p style="font-size: 18px;">%s</p>',  ($post_type->description)); // echo 
    }
    return $views; // return original input unchanged
}


// KEEP WP SIDE MENU ITEM OPEN ON SINGLE POST EDIT SCREEN OF LC CPTS
add_action('admin_head', function () {
	global $pagenow;
	if (!in_array( $pagenow, array( 'post.php', 'post-new.php' ) )) return; //exit if not in post edit or post new screen
	$current_screen = get_current_screen();
	// Post types for which the media buttons should be removed.
	$post_types = array('lc_block',	'lc_section', 'lc_partial', 'lc_dynamic_template' );
	// Bail out if media buttons should not be removed for the current post type.
	if (!$current_screen || !in_array($current_screen->post_type, $post_types, true)) return;
	?> 
	<script>
		document.addEventListener("DOMContentLoaded", function() { 
			document.querySelector("#toplevel_page_livecanvas").classList.remove("wp-not-current-submenu");
			document.querySelector("#toplevel_page_livecanvas").classList.add("wp-has-current-submenu");
		});	
	</script>
	<?php
}); //end func


//FORCE TEMPLATE FOR CPTs BLOCK & SECTION & PARTIALS
add_filter( 'template_include', 'lc_force_template' );
function lc_force_template($template){
    
	if ( (in_array(get_post_type(), array("lc_block", "lc_section", "lc_partial", "lc_dynamic_template")))) {	
		$template = get_stylesheet_directory().'/page-templates/empty.php'; //of current theme
		if (!file_exists($template)) $template = get_template_directory().'/page-templates/empty.php';//if not found, fallback to parent
		if (!file_exists($template)) $template = dirname( __FILE__ ) . '/modules/templates/single-lc-template.php'; //fallback to built-in
    }
    // Always return, even if we didn't change anything
    return $template;
}

//DISABLE WYSIWYG EDITING ON LC-POWERED POSTS LEAVING SIMPLE A CODE EDITOR SO YOAST CAN PICK IT 
add_filter('user_can_richedit', 'lc_page_can_richedit');
function lc_page_can_richedit($can) {
	if (!is_admin()) return $can; //don't mess around the frontend
	global $post,$lc_ww_count;
	$lc_ww_count++;//let's count how many editors are printed
	
	//echo "VVV".$lc_ww_count;// for debug
	
	if($lc_ww_count > 2 && lc_plugin_option_is_set("allow_multiple_editors"))   return $can; //this is done so that additional WW editors are not deleted
	if ( empty($post)) return $can;
	if ( isset($post->ID) && is_numeric ($post->ID) && lc_post_is_using_livecanvas($post->ID)  ) 	return false;
	
	return $can;
}

// DISABLE GUTENBERG IN LC PAGES
add_filter('use_block_editor_for_post_type', 'lc_use_block_editor_for_post_type' , 10);
function lc_use_block_editor_for_post_type($in) {
	if (!is_admin()) return $in; 
	if ( isset($_GET['post']) && is_numeric($_GET['post']) &&   lc_post_is_using_livecanvas($_GET['post'])  )	return false;
	return $in;
}

// REMOVES MEDIA BUTTONS FROM POST TYPES
add_filter('wp_editor_settings', function($settings) {
	if (!is_admin()) return $settings; 
	$current_screen = get_current_screen();
	// Post types for which the media buttons should be removed.
	$post_types = array('lc_block',	'lc_section', 'lc_partial', 'lc_dynamic_template' );
	// Bail out if media buttons should not be removed for the current post type.
	if (!$current_screen || !in_array($current_screen->post_type, $post_types, true)) {	return $settings; }
	$settings['media_buttons'] = false;
	return $settings;
});


// CODEMIRROR FOR CPTs
add_action('admin_enqueue_scripts', function() {
	if (!in_array( get_current_screen()->id, array('lc_block', 'lc_section', 'lc_partial', 'lc_dynamic_template'))) return;
	// Enqueue code editor and settings for manipulating HTML.
	$settings = wp_enqueue_code_editor(array(	'type' => 'text/html'	));
	// Bail if user disabled CodeMirror.
	if (false === $settings) {	return;	}
	wp_add_inline_script('code-editor', sprintf('jQuery( function() { 
				var lc_editor=wp.codeEditor.initialize( "content", %s );
				window.lc_editor = lc_editor;
				lc_editor.codemirror.setSize(null, 700);
			} );', wp_json_encode($settings)));
}); //end add action



//GET ACTIVE PLUGINS LIST
function lc_get_active_plugins_list() {
	$the_list  = "";
	$the_plugs = get_option('active_plugins');
	
	if ($the_plugs)
		foreach ($the_plugs as $key => $value) {
			$string = explode('/', $value); // Folder name will be displayed
			$the_list .= $string[0] . ',';
		}
	
	$the_network_plugs = get_site_option('active_sitewide_plugins');
	
	if ($the_network_plugs)
		foreach ($the_network_plugs as $key => $value) {
			$string = explode('/', $key); // Folder name will be displayed
			$the_list .= $string[0] . ',';
		}
	return $the_list;
}

///////////// HOOK THE CUSTOM INLINE CSS FOR EDITING WITH LC so it's never empty ////////
add_filter("wp_get_custom_css", 'lc_alter_custom_css',100);
function lc_alter_custom_css($css) {
	if (current_user_can("edit_pages") && isset($_GET['lc_page_editing_mode']))		$lc_editing_mode = TRUE;	else		$lc_editing_mode = FALSE;
	if ($lc_editing_mode && $css == "")
		$css .= " "; //ALWAYS NECESSARY WHEN EDITING
	return $css;
}

//PLUGIN UPDATER
if(lc_get_apikey()):
	require 'modules/plugin-update-checker/plugin-update-checker.php';
	$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(	'https://updater.livecanvas.com/lc-plugin-updater-meta/?apikey='.lc_get_apikey(),	__FILE__,	'livecanvas' );
endif;

