<?php
/* GET THE GLOBAL SETTINGS */
$lc_settings = get_option('lc_settings');


/* =================== LIVECANVAS FOOTER LOADING =================== */
if (lc_plugin_option_is_set('footer')): //IF FLAG IS ENABLED IN PLUGIN SETTINGS
	
	//DISABLE UNDERSTRAP STANDARD FOOTER
	if (!function_exists('understrap_site_info')){
		function understrap_site_info(){}
	}
	
	//ADD STUFF TO FOOTER
	add_action('wp_footer', 'lc_add_footer_stuff');
	function lc_add_footer_stuff(){
		if ('lc_block' === get_post_type() OR 'lc_section' === get_post_type()) return;
		if (is_front_page()){
			//HOMEPAGE CASE
			if (!isset($_GET['lc_page_editing_mode']) ): //AND WE ARE NOT EDITING
				?>
				<script> 
				jQuery( document ).ready(function() {
					var footer_code = document.querySelector("main#lc-main #global-footer").outerHTML.replace("section","footer");
					document.querySelector("#wrapper-footer").outerHTML=footer_code;
					document.querySelector("main#lc-main #global-footer").remove();
				});
				</script>
				<?php
			endif;
			//JUST A RECOMMENDATION FOR ADMINS
			if (current_user_can("administrator")){
				?>
				<script>
					<!-- //JUST A RECOMMENDATION FOR ADMINS// -->
					jQuery( document ).ready(function() {
						 if(!jQuery("#global-footer").length) alert("LiveCanvas tip: Assign the 'global-footer' ID to the section you want to become the site footer"); 
					});
				</script>
				<?php 
			} //end if
			//DO NOTHING, EXIT FUNCTION
		} else {
			//NOT IN HOMEPAGE CASE: LOAD THE FOOTER FROM HOMEPAGE
			?>
			<script> 
			jQuery( document ).ready(function() {
				jQuery.ajax({
					url: "<?php echo get_home_url() ?>",
					success: function( result ) {
					  var footer_code = jQuery(result).find("#global-footer")[0].outerHTML.replace("section","footer");
					  document.querySelector("#wrapper-footer").outerHTML=footer_code; 
					}
				});
			});
			</script>
			<?php
			
		} // end else	

	} //end function

endif;



 
/* =================== LIVECANVAS ANIMATIONS =================== */
if (lc_plugin_option_is_set('aos')): //IF FLAG IS ENABLED IN PLUGIN SETTINGS
	
	function lc_add_aos_libs() { 
		 
		if (lc_plugin_option_is_set('aos-priority')) wp_enqueue_style( 'lc-aos',  plugin_dir_url( __FILE__ )."optin-extra-assets/aos.css", array(), 1, 'all');
		wp_enqueue_script( 'lc-aos',  plugin_dir_url( __FILE__ )."optin-extra-assets/aos-with-init.js", array(), false, true );
	}
	add_action( 'wp_enqueue_scripts', 'lc_add_aos_libs' );
	
	//enqueue style in footer
	add_action( 'get_footer', function(){ 
		if (!lc_plugin_option_is_set('aos-priority')) wp_enqueue_style( 'lc-aos',  plugin_dir_url( __FILE__ )."optin-extra-assets/aos.css", array(), false, 'all');
	} );



endif;

