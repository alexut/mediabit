<?php

// EXIT IF ACCESSED DIRECTLY.
defined( 'ABSPATH' ) || exit;


function lc_add_meta_boxes( $post ){
	//ADD ON PAGES
	add_meta_box( 'lca_meta_boxes_switch', __( 'LiveCanvas', 'livecanvas' ), 'lc_build_meta_box_lc_switch', 'page', 'side', 'high' );
	
	//ADD ON POSTS
	if (lc_plugin_option_is_set('enable-on-post-type-post')) add_meta_box( 'lca_meta_boxes_switch', __( 'LiveCanvas', 'livecanvas' ), 'lc_build_meta_box_lc_switch', 'post', 'side', 'high' );
	
	//determine post type
	if(isset($_GET['post_type'])) $the_post_type=$_GET['post_type']; // for new screen cpt
	if(isset($_GET['post'])) $the_post_type=get_post_type($_GET['post']); // for edit cpt screen
	
	//OTHER opt-in CPTs 
	if (isset($the_post_type) &&  lc_plugin_option_is_set('enable-on-post-type-'.$the_post_type) )  
		add_meta_box( 'lca_meta_boxes_switch', __( 'LiveCanvas', 'livecanvas' ), 'lc_build_meta_box_lc_switch', $the_post_type, 'side', 'high' );

	//add the meta box for gt blocks
	if (isset($the_post_type) && $the_post_type== 'lc_gt_block' )
		add_meta_box( 'lca_meta_boxes_gtb', __( 'LiveCanvas', 'livecanvas' ), 'lc_build_meta_box_lc_gt_block', $the_post_type, 'side', 'high' );
	
	//add the meta box for 'lc_block','lc_section','lc_partial' for shortcode get_post and php example to pull content
	if (isset($the_post_type) && in_array($the_post_type, array('lc_block','lc_section','lc_partial')) )
		add_meta_box( 'lca_meta_boxes_sam', __( 'Use as Partial', 'livecanvas' ), 'lc_build_meta_box_lc_get_post', $the_post_type, 'side', 'high' );
		
}
add_action( 'add_meta_boxes', 'lc_add_meta_boxes' );

 
function lc_build_meta_box_lc_switch( $post ){
	// make sure the form request comes from WordPress
	wp_nonce_field( basename( __FILE__ ), 'lc_meta_box_nonce' );

	// retrieve the _lc_livecanvas_enabled current value
	$current_livecanvas_enabled = get_post_meta( $post->ID, '_lc_livecanvas_enabled', true );
 
	?>
	<div class='inside'>
		<h4><?php _e( 'Enable the LiveCanvas Editor', 'livecanvas' ); ?></h4>
		<p>
			<input type="radio" name="livecanvas_enabled" value="1" <?php checked( $current_livecanvas_enabled, '1' ); ?> /> Yes<br />
			<input type="radio" name="livecanvas_enabled" value="" <?php checked( $current_livecanvas_enabled, '' ); ?> /> No
		</p>
	</div>
	<?php
}

function lc_save_meta_box_data( $post_id ){
	// verify meta box nonce
	if ( !isset( $_POST['lc_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['lc_meta_box_nonce'], basename( __FILE__ ) ) ){		return;	}

	// return if autosave
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){ return; }

	// Check the user's permissions.
	if ( ! current_user_can( 'edit_post', $post_id ) ){	return;	}
	
	// If we have some data handle the situation
	if ( isset( $_REQUEST['livecanvas_enabled'] ) ):
		
		
		if ( get_post_meta($_REQUEST['post_ID'], '_lc_livecanvas_enabled', true) != '1' && $_REQUEST['livecanvas_enabled']==1) { ///// CASE LC RADIO SWITCHED FROM OFF TO ON /////////
			
			//set the right template for LC
			update_post_meta( $post_id, '_wp_page_template', "page-templates/empty.php" );
		}
		
		
		if ( get_post_meta($_REQUEST['post_ID'], '_lc_livecanvas_enabled', true) == '1' && $_REQUEST['livecanvas_enabled']!=1) { ///// CASE LC RADIO SWITCHED FROM ON TO OFF /////////
					
			//reset page template
			delete_post_meta( $post_id, '_wp_page_template'  );
		}
		
		if ( get_post_meta($_REQUEST['post_ID'], '_lc_livecanvas_enabled', true) == '1' && $_REQUEST['livecanvas_enabled']==1 &&  get_post_type( $post_id ) == 'post'  ) { ///// CASE LC RADIO FROM ON TO ON /////////
			
			//this case takes care of wp resetting the page template for single posts upon updating the post from the backend. Thanks Bea
			//set the right template for LC
			update_post_meta( $post_id, '_wp_page_template', "page-templates/empty.php" );
		}
		
		//SAVE THE CUSTOM FIELD VALUE
		update_post_meta( $post_id, '_lc_livecanvas_enabled', sanitize_text_field( $_REQUEST['livecanvas_enabled'] ) );
			
		
	endif;
	

}
add_action( 'save_post', 'lc_save_meta_box_data' );


//// GT BLOCKS SIDE META BOX
function lc_build_meta_box_lc_gt_block( $post ){
	?>
	<div class='inside'>
		<h4><?php 
		if($post->post_status!='publish') _e('After publishing this post, ');
		_e( 'You can embed this content using the shortcode:', 'livecanvas' ); ?></h4>
		<h2>[lc_get_gt_block slug="<?php echo esc_attr($post->post_name); ?>"]</h2>

		<h4><?php _e( 'Alternatively, you can use this PHP code in your templates:', 'livecanvas' ); ?></h4>
		<h2>echo do_shortcode("[lc_get_gt_block slug='<?php echo esc_attr($post->post_name); ?>']");</h2>
		 
	</div>
	<?php
}


//// LC SIDE META BOX for LC's own post types
function lc_build_meta_box_lc_get_post( $post ){ 
	$shortcode_string = '[lc_get_post post_type="'.   esc_attr($post->post_type).'" slug="'. esc_attr($post->post_name).'"]';
	?>
	<div class='inside'>
		<h4><?php 
		if($post->post_status!='publish') _e('After publishing this post, ');
		_e( 'You can embed this content using the shortcode:', 'livecanvas' ); ?></h4>
		<h2><?php echo $shortcode_string ?></h2>
		
		<h4><?php _e( 'Alternatively, you can use this PHP code in your templates:', 'livecanvas' ); ?></h4>
		<h2>echo do_shortcode('<?php echo $shortcode_string ?>');</h2>

	</div>
	<?php
}



//////////////////////////////////  SUGGESTION /////////////////////////////////
 
/////SUGGEST IN WP ADMIN TO ENABLE LC TEMPLATES FOR PAGES, DISABLE WYSIWYG WHEN LC TEMPLATES ARE ENABLED
add_action( 'current_screen', 'lc_tweak_wp_interface_page' ); 
function lc_tweak_wp_interface_page() { 
	
	global $pagenow;
	
	//exit if we're not in the editing page of wp-admin
	if (!in_array($pagenow,array('post.php','post-new.php')) ) return;
	
	if(isset( $_GET['post']))
		$already_using_lc_template=lc_post_is_using_livecanvas($_GET['post']);
			else
		$already_using_lc_template=FALSE;  
	
	if ($already_using_lc_template) { 
		  //remove_post_type_support('page', 'editor'); //commented these 3 lines to restore the editor for YOAST SEO compatibility
		  //remove_post_type_support('post', 'editor');
		  //if(isset( $_GET['post'])) remove_post_type_support(get_post_type($_GET['post']), 'editor'); //for saved cpt posts
		  add_action('admin_notices', 'lc_template_admin_notice_using_lc');
    } else {
	 //not using lc template (yet)
	add_action('admin_notices', 'lc_template_admin_notice_not_using_lc_yet');
	}
}



 
function lc_template_admin_notice_not_using_lc_yet(){

	global $post; 
	?>
	 
	<style>
		#wpbody-content .wrap .lc-add-editing-icon {  margin: 10px 0 0 45px;} /*no gut */
		.edit-post-header-toolbar .lc-add-editing-icon {  margin: 0 0 0 45px;} /* gut */
	</style> 
	
	<script>
 
		function isGutenbergActive() {    return typeof wp !== 'undefined' && typeof wp.blocks !== 'undefined';}
		
		jQuery(document).ready(function($) {
			
			if(isGutenbergActive()) { ///////ONLY FOR GUTENBERG - future useful link: https://github.com/WordPress/gutenberg/issues/17632
				wp.data.subscribe(function () { 
					if (wp.data.select('core/editor') && wp.data.select('core/editor').didPostSaveRequestSucceed() && !wp.data.select('core/editor').isAutosavingPost()    ) {  
						//console.log("Guten is doing something");
						postsaving = wp.data.select('core/editor').isSavingPost();
						autosaving = wp.data.select('core/editor').isAutosavingPost();
						success = wp.data.select('core/editor').didPostSaveRequestSucceed();

						if (!(postsaving && !autosaving && success)) return;
						
						console.log('Saving: '+postsaving+' - Autosaving: '+autosaving+' - Success: '+success);
						
						//check if radio button of LC is enabled
						if ($('input[name=livecanvas_enabled][value=1]').prop("checked") == true ) {
							//LC is enabled!
							if ($("#lc-guten-trigger-editing").length==0) { 
								//button is not there, but its needed: let's append it 
								var lc_button_url="<?php echo get_site_url() ?>?lc_redirect_to_edit_post_id="+wp.data.select('core/editor').getCurrentPostId();  
								var lc_button_html="<a id='lc-guten-trigger-editing' class='lc-add-editing-icon button button-primary button-large' href='"+lc_button_url+"' >Edit with LiveCanvas</a>";
								
								$(".edit-post-header-toolbar").append(lc_button_html);
									
							}
						}	else {
							//LC is not enabled, button is not necessary
							$("#lc-guten-trigger-editing").remove();
						}				 
						
					} //end if
				
			  	});  //end subscribe
			} //end if Gutenberg
			else {
				//no gutenberg
			}
			
		});//end document ready
	
	
	</script>
			
		    
	<?php 
 
}
 
function lc_template_admin_notice_using_lc(){
	 ///ADDS THE BUTTON TO LAUNCH LIVECANVAS EDITOR
	 global $post;	 
    ?>
	<script>
		jQuery( document ).ready(function() {
			 
			//no guten 
			var lc_button_url="<?php echo esc_url( add_query_arg( array('lc_action_launch_editing'=> '1','from_page_edit' =>'1'), get_permalink($post->ID))) ?>"
			jQuery("#titlediv").append("<br><a class='lc-add-editing-icon button button-primary button-hero' href='"+lc_button_url+"' >Edit with LiveCanvas</a>");
		});
		 
	</script>
	<?php 
		//hide the editor on posts and pages, not on LC's CPTs
		if ( !(in_array($post->post_type, array('lc_block', 'lc_section', 'lc_partial', 'lc_dynamic_template')))) {
			?>
			<style>
				#postdivrich {display: none} /* hide the editor */
			</style>
		
			<?php } else {
			?>
			<style>
				#postdivrich {margin-top:20px;} /* add some spacing from the button */
			</style>
		
			<?php }

 
}

