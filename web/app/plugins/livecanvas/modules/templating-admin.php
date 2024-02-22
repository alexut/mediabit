<?php
//////////////ADD CHECKBOXES TO SINGLE TEMPLATE PARTIAL POST EDITING SCREEN /////////

//ADD A META BOX ON THE DYNAMIC TEMPLATE POST TYPE EDITING SCREEN
add_action( 'add_meta_boxes', function ( $post ){
	 
	//determine post type
	if(isset($_GET['post_type'])) $the_post_type=$_GET['post_type']; // for new screen cpt
	if(isset($_GET['post'])) $the_post_type=get_post_type($_GET['post']); // for edit cpt screen
	
	if (isset($the_post_type) && $the_post_type== 'lc_dynamic_template' ){
		add_meta_box( 'lca_meta_boxes_template_assign', __( 'Assign to Template', 'livecanvas' ), 'lc_build_meta_boxes_template_assign', $the_post_type, 'side', 'high' );
	}
	
},9  );

//SUPPORT FUNCTION: BUILD THE CHECKBOX
function lc_print_template_assign_checkbox($key, $label){
	global $post;
	$checked = get_post_meta( $post->ID, $key, true ) == 1;
	?>
	<label>    
		<input title="<?php echo $key ?>" name="<?php echo $key ?>" type="checkbox" value="1" <?php if ($checked) echo "checked"; ?> >
		<span class="lc-pro-mode-label"> <?php echo $label; ?> </span> 
	</label> 
	<?php
}

////  DEFINE THE SIDE META BOX FOR THE TEMPLATE ASSIGMNENT
function lc_build_meta_boxes_template_assign( $post ){

	?>
	<div class='inside' id='lc-template-assignment'>

		<?php wp_nonce_field( basename( __FILE__ ), 'lc_tp_meta_box_nonce' ); ?>

		<label class='lc-dt-label'>    
			<input type="checkbox" id="lc-pro-mode" value="1" >
			<span class="lc-pro-mode-label"> PRO mode</span> 
		</label> 

		<h3><?php _e( 'Singular', 'livecanvas' ); ?></h3> 
		<?php 
			$post_types =  get_post_types(  array('public'   => true  ), 'objects', 'and' );
			foreach($post_types as $post_type):   
				if($post_type->labels->name=='Media') continue;

				lc_print_template_assign_checkbox('is_single_'.$post_type -> name, 'Single ' . $post_type->labels-> name); 

				//now get taxonomies associated to that post type
				$taxonomies = get_object_taxonomies( $post_type -> name, 'object' );
				
				if ($taxonomies) foreach($taxonomies as $taxonomy):  
					$terms = get_terms( array('taxonomy' => $taxonomy->name , 'hide_empty' => false,) );
					//print_r($terms);
					
					if ($terms):
						?><div class="lc-subset lc-hidden"><h4>Singular <?php echo $post_type -> name . 's in '. $taxonomy->name ?>:</h4>	<?php
						foreach ($terms as $term) {
							lc_print_template_assign_checkbox('is_single_'.$post_type -> name.'__in_'. $taxonomy->name . '_' . $term->slug,   $term->name);
						}
						?></div><?php 
					endif;	   
					
				endforeach;
			endforeach; 
		?>
	 	
		<h3><?php _e( 'Archive', 'livecanvas' ); ?></h3>
		 
		<h4><?php _e( 'Taxonomy Archives', 'livecanvas' ); ?></h4>
		<?php

			$taxonomies = get_taxonomies( array('public'   => true, ), 'objects', 'and' ); 
			foreach($taxonomies as $taxonomy): 
				if($taxonomy->labels->name=='Formats') continue;
				lc_print_template_assign_checkbox('is_archive_for_tax_'.$taxonomy -> name,   $taxonomy->labels-> name . ' Archive');
				$terms = get_terms( array('taxonomy' => $taxonomy -> name,'hide_empty' => false,) );
				//print_r($terms);
				?><div class="lc-subset lc-hidden"><?php
				if ($terms) foreach ($terms as $term) {
					lc_print_template_assign_checkbox('is_archive_for_tax_'.$taxonomy -> name.'__'.$term->slug,   $term->name);
				}   
				?></div><?php 
			endforeach; ?>	
			
		
			
		<h4><?php _e( 'Post Type Archives', 'livecanvas' ); ?></h4>
		<?php 
			$post_types =  get_post_types(  array('public'   => true , 'hierarchical' => false ), 'objects', 'and' );
			foreach($post_types as $post_type):    
				if($post_type->labels->name=='Media') continue;
				lc_print_template_assign_checkbox('is_archive_for_post_type_'.$post_type -> name,   $post_type->labels-> name . ' Archives');
			endforeach; ?>

		<h4><?php _e( 'Misc', 'livecanvas' ); ?></h4>
		
		<?php lc_print_template_assign_checkbox('is_archive_author', 'Author Archives'); ?> 
		<?php lc_print_template_assign_checkbox('is_archive_date', 'Date Archives'); ?> 

		<h3><?php _e( 'Specialty', 'livecanvas' ); ?></h3>

		<?php lc_print_template_assign_checkbox('is_front_page', 'Front Page'); ?> 
		<?php lc_print_template_assign_checkbox('is_blog_posts_index', 'Blog Posts Index'); ?> 
		<?php lc_print_template_assign_checkbox('is_search', 'Search Results'); ?> 
		<?php lc_print_template_assign_checkbox('is_404', '404 Not found'); ?> 
		
		<?php if ( class_exists( 'woocommerce' ) ): ?>

		<h3><?php _e( 'WooCommerce Specialty', 'livecanvas' ); ?></h3>
		<?php lc_print_template_assign_checkbox('is_shop_page', 'Shop page'); ?> 
		<?php lc_print_template_assign_checkbox('is_cart_page', 'Cart page'); ?> 
		<?php lc_print_template_assign_checkbox('is_checkout_page', 'Checkout page'); ?> 
		<?php lc_print_template_assign_checkbox('is_account_page', 'Account page'); ?> 
		
		<?php endif ?>	 
			
	</div>

	<style>
		#lc-template-assignment label {display:block;margin-bottom: 10px;  }
		.lc-option-key {color:#ccc;font-size:1em;display:block}
		.lc-subset {margin-left:24px;font-size:0.8em;color: #aaa;}
		.lc-hidden {display:none}
		.lc-dt-label {position:absolute;top:0px;right:0px; transform:scale(0.7);opacity:0.4}
	</style>
	<?php
}

//FOR SAVING META BOX DATA
add_action( 'save_post_lc_dynamic_template',  function  ( $post_id ){
	// verify meta box nonce
	if ( !isset( $_POST['lc_tp_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['lc_tp_meta_box_nonce'], basename( __FILE__ ) ) ){		return;	}

	// return if autosave
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){ return; }

	// Check the user's permissions.
	if ( ! current_user_can( 'edit_post', $post_id ) ){	return;	}

	//remove all is_ values, that do not contain __
	foreach(get_post_meta($post_id) as $meta_key=>$meta_value) { 
		if (substr($meta_key,0,3)=='is_') delete_post_meta( $post_id, $meta_key );
	}

	//update post meta where is valid
	foreach ($_POST as $key => $value){
		if (substr($key,0,3)=='is_' && $value==1)  update_post_meta( $post_id, $key, 1 );
	}
 
});


//ADD COLUMNS TO WP ADMIN LISTING
// Add the custom columns to the lc_dynamic_template post type:
add_filter( 'manage_lc_dynamic_template_posts_columns', 'set_custom_edit_lc_dynamic_template_columns' );
function set_custom_edit_lc_dynamic_template_columns($columns) {
 
    $columns['assigned_to'] = __( 'Assigned To', 'livecanvas' );

    return $columns;
}


// Add the data to the custom columns for the lc_dynamic_template post type:
add_action( 'manage_lc_dynamic_template_posts_custom_column' , 'custom_lc_dynamic_template_column', 10, 2 );
function custom_lc_dynamic_template_column( $column, $post_id ) {
    switch ( $column ) {

        case 'assigned_to' :
            
			foreach(get_post_meta($post_id) as $meta_key=>$meta_value) {
				if (substr($meta_key,0,3)=='is_') echo ucwords(str_replace('is ','',str_replace('_',' ', $meta_key ))).'<br>';
			}
            break;

    }
}


//Message for new posts, in Add New Dynamic Template admin screen
add_action( 'current_screen', function  () { 
	
	global $pagenow, $current_screen;
	
	//exit if we're not in the editing page of wp-admin
	if (!in_array($pagenow,array( 'post-new.php')) ) return;
 
	//only on lc_dynamic_template post types
	if( $current_screen->post_type != 'lc_dynamic_template') return;

	add_action('admin_notices',  function(){
		?>
			<div class="notice notice-success "> 
				<h3><strong>Please choose a Template from the list on the right column</strong> </h3>
				<p>A default template will be loaded. </p>
				<p>Upon saving, you'll be able to launch the LiveCanvas editor.</p>
			</div>

			<style>
				div#lca_meta_boxes_template_assign {
					border-left: 4px solid #00a32b;
					animation: lc_shake 0.5s;
					animation-iteration-count: 1;
				}

				@keyframes lc_shake {
					0% { transform: translate(1px, 1px) rotate(0deg); }
					10% { transform: translate(-1px, -2px) rotate(-1deg); }
					20% { transform: translate(-3px, 0px) rotate(1deg); }
					30% { transform: translate(3px, 2px) rotate(0deg); }
					40% { transform: translate(1px, -1px) rotate(1deg); }
					50% { transform: translate(-1px, 2px) rotate(-1deg); }
					60% { transform: translate(-3px, 1px) rotate(0deg); }
					70% { transform: translate(3px, 1px) rotate(-1deg); }
					80% { transform: translate(-1px, -1px) rotate(1deg); }
					90% { transform: translate(1px, 2px) rotate(0deg); }
					100% { transform: translate(1px, -2px) rotate(-1deg); }
				}

			</style>
		<?php
	}); 

});  


//WHEN USER CLICKS CHECKBOXES, LOAD A DEFAULT TEMPLATE
add_action( 'current_screen', function  () { 
	
	global $pagenow, $current_screen;
	
	//exit if we're not in the editing page of wp-admin
	if (!in_array ($pagenow,array('post.php','post-new.php')) ) return;
 
	//only on lc_dynamic_template post types
	if( $current_screen->post_type != 'lc_dynamic_template') return;

	add_action('admin_footer', function(){ ?>

		<script>
			
			jQuery(document).ready(function($) {

				//check codemirror content
				lc_editor_initial_content = window.lc_editor.codemirror.getValue();

				//change label for template priority
				$("label[for=menu_order]").text("Priority");
				$("div#pageparentdiv > div > h2").text("Template Attributes");

				//on  clicking of "PRO mode" checkbox, reveal hidden options
				$("body").on("click", "#lc-pro-mode", function(e) { 
					if ($(this).is(":checked"))	$('.lc-subset').removeClass("lc-hidden"); else $('.lc-subset').addClass("lc-hidden");
				});
				
				//if an advanced option is used, reveal hidden options 
				if ($(".lc-subset input[type=checkbox]:checked").length>0) $('#lc-pro-mode').click();

				//on clicking of template checkboxes, update title / load readymade
				$("body").on("click", "#lca_meta_boxes_template_assign input[type=checkbox]", function(e) {
					
					//if there was something in the editor initially, and editor is not currently empty, exit
					if (lc_editor_initial_content != '' && window.lc_editor.codemirror.getValue() != '')  return;

					//if we just unchecked the option, exit
					if (!$(this).is(':checked')) return;

					//if there's another checked one, exit
					if ($("#lca_meta_boxes_template_assign input[type=checkbox]:checked").length > 1) return;

					var theTemplateCase = $(this).attr('name').replace('is_','');
					
					//update post title input field
					$('#title-prompt-text').addClass('screen-reader-text');
					$('input[name=post_title]').val("Template for "+theTemplateCase.replaceAll('_',' ')).change();

					console.log("theTemplateCase is now: "+  theTemplateCase ); //for debug

					if (theTemplateCase.includes('product')) {
						
						console.log('product');
						
						//CASE WC PRODUCTS

						//all singles
						if (theTemplateCase.includes('single') && !theTemplateCase.includes('single_page')) theTemplateCase='single_product';

						//all archives
						if (theTemplateCase.includes('archive') || theTemplateCase=='blog_posts_index') theTemplateCase='archive_product'; 

					} else {
					
						//CASE NOT WC PRODUCTS

						//all singles
						if (theTemplateCase.includes('single') && !theTemplateCase.includes('single_page')) theTemplateCase='single_post';

						//all archives
						if (theTemplateCase.includes('archive') || theTemplateCase=='blog_posts_index') theTemplateCase='archive'; 
					}

					//WC "NORMAL" PAGES
					if (theTemplateCase=='cart_page'  || theTemplateCase=='checkout_page' || theTemplateCase=='account_page') theTemplateCase='single_page'; 

					console.log("the final TemplateCase is now: "+  theTemplateCase ); //for debug

					jQuery.ajax({
						url: ajaxurl, // this will point to admin-ajax.php
						type: 'POST',
						data: {
							'action': 'lc_grab_dyn_template',  
							'template': theTemplateCase  
						}, 
						success: function (response) {
							if (response != '') window.lc_editor.codemirror.setValue(response);
						}
					});			 
					
				});

			});

		</script>
	<?php
	}); //end add action admin_footer

}); //end add action current_screen




// AJAX ACTION TO READ THE STARTER TEMPLATE
add_action("wp_ajax_lc_grab_dyn_template" , "lc_grab_dyn_template");
function lc_grab_dyn_template(){
    //echo 'hello'.json_encode($_POST);
	//echo $_POST['template'];

	global $wp_filesystem;
	if (empty($wp_filesystem)) {
		require_once (ABSPATH . '/wp-admin/includes/file.php');
		WP_Filesystem();
	}
	
	//READ THE FILE
	$file = $wp_filesystem->get_contents( plugin_dir_path( __FILE__ ).'/templates/'.$_POST['template'].'.html'); 	
    wp_die($file);
}
