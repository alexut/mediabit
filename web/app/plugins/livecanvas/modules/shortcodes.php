<?php

// EXIT IF ACCESSED DIRECTLY.
defined( 'ABSPATH' ) || exit;


///////////////////////// DEMO DUMMY SHORTCODE //////////////////////
add_shortcode( 'lc_dummyshortcode', 'lc_demoshortcode_func' );
function lc_demoshortcode_func(){
	return '
	<div style="width:100%;padding:20px;background:#eee;color:#333;text-align:center">
		<h2>I am a dummy shortcode</h2>
	</div>
	';
}

///////////////////////// SITE URL SHORTCODE //////////////////////
add_shortcode( 'lc_home_url', 'lc_home_url_func' );
function lc_home_url_func(){	return esc_url( home_url( '/' ) ); }

///SITE LOGO SHORTCODE///////////
add_shortcode( 'lc_custom_logo', function(){return get_custom_logo(); } );

///////////////////////// HEADER MENU SHORTCODE //////////////////////
add_shortcode( 'lc_nav_menu', 'lc_nav_menu_func' );
function lc_nav_menu_func($attributes){
	//FIND OUT IF THERE'S AN ACTIVE BOOTSTRAP NAVWALKER IN THE THEME, AND PLUG TO IT
	$the_walker="";
	if (class_exists( "Understrap_WP_Bootstrap_Navwalker") )  $the_walker= new Understrap_WP_Bootstrap_Navwalker();  //understrap
	if (class_exists( "WP_Bootstrap_Navwalker" ) )  $the_walker= new WP_Bootstrap_Navwalker();  //p4
	if (class_exists( "wp_bootstrap_navwalker" ) )  $the_walker= new wp_bootstrap_navwalker(); //bootscore
	if (class_exists("bootstrap_5_wp_nav_menu_walker")) $the_walker = new bootstrap_5_wp_nav_menu_walker(); //p5

	//EXTRACT VALUES FROM SHORTCODE CALL - default, or BS4
	$the_shortcode_atts=shortcode_atts( array(
						'theme_location'  => 'primary',
						'container_class' => '',
						'container_id'    => '',
						'menu_class'      => '', //TO THE UL
						'fallback_cb'     => '',
						'menu_id'         => '',
						'depth'           => 2,
						'echo'			 => FALSE,	
						'walker'          => $the_walker,
					), $attributes );
	
	//EXTRACT VALUES FROM SHORTCODE CALL - BS5
	if (class_exists("bootstrap_5_wp_nav_menu_walker")) $the_shortcode_atts=shortcode_atts( array(
						'theme_location' => 'primary',
						'container' => false,
						'menu_class' => '',
						'fallback_cb' => '__return_false',
						'items_wrap' => '<ul id="%1$s" class="navbar-nav me-auto mb-2 mb-md-0 %2$s">%3$s</ul>',
						'menu_id'         => '',
						//'depth'           => 2,
						'echo'			 => FALSE,	
						'walker'          => $the_walker,
					), $attributes );
					
	extract($the_shortcode_atts);
	
	return  " <!--  lc_nav_menu --> ".wp_nav_menu($the_shortcode_atts)." <!-- /lc_nav_menu --> ";
}



///////////////////////////// 'POSTLIST' SHORTCODE TO GET POSTS - a simple wrap for the get_posts function /////////////
add_shortcode( 'lc_get_posts', 'lc_get_posts_func' );
function lc_get_posts_func( $atts ){
	//EXTRACT VALUES FROM SHORTCODE CALL
	$get_posts_shortcode_atts=shortcode_atts( array(
			///INPUT
			'posts_per_page'   => 10,
			'offset'           => 0,
			'category'         => '',
			'category_name'    => '',
			'orderby'          => 'date',
			'order'            => 'DESC',
			'include'          => '',
			'exclude'          => '',
			'meta_key'         => '',
			'meta_value'       => '',
			'post_type'        => 'post',
			'post_mime_type'   => '',
			'post_parent'      => '',
			'author'	   		 => '',
			'post_status'      => 'publish',
			'suppress_filters' => true,
			'tax_query' => '', //custom: taxonomy=term_id
			///OUTPUT ////////////
			'output_view' => 'lc_get_posts_default_view',
			'output_wrapper_class' => '',
			'output_link_class' => '',
			'output_number_of_columns' => 3,
			'output_article_class' => '',
			'output_heading_tag' => 'h2',
			'output_hide_elements'  => '',
			'output_excerpt_length' =>45,
			'output_excerpt_text' => '&hellip;',
			'output_featured_image_before' =>'',
			'output_featured_image_format' =>'large',
			'output_featured_image_class' => 'attachment-thumbnail img-responsive alignleft'
     ), $atts, 'lc_get_posts' );
	 
	extract($get_posts_shortcode_atts);
	
	//CUSTOM TAX QUERY CASE
	if ($tax_query!=""):
		//custom tax case
		$array_tax_query_par=explode("=",$tax_query);
		$get_posts_shortcode_atts= array_merge($get_posts_shortcode_atts,
											  array( 'tax_query' => array(
													array(
													  'taxonomy' => $array_tax_query_par[0], //taxonomy name
													  'field' => 'id',
													  'terms' => $array_tax_query_par[1], //term_id  
													  'include_children' => false
													)
													  )));
	endif; //end custom tax case
	
	//print_r($get_posts_shortcode_atts);return; //for debug
	
	//NOW GET THE POSTS
	$the_posts = get_posts( $get_posts_shortcode_atts );
	
	//LAUNCH OUTPUT CALLBACK FUNCTION
	return call_user_func(  $output_view ,$the_posts, $get_posts_shortcode_atts);
}


 



//TEMPLATING: PLAIN DEFAULT ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function lc_get_posts_default_view($the_posts,$get_posts_shortcode_atts) {
	
	extract($get_posts_shortcode_atts);
	$out='';// INIT
	$output_hide_elements=strtolower($output_hide_elements);
	
	//default is 3 rows
	if($output_number_of_columns==1) $column_classes='col-12'; 
	if($output_number_of_columns==2) $column_classes='col-md-6 col-lg-6';
	if($output_number_of_columns==3) $column_classes='col-md-6 col-lg-4';
	if($output_number_of_columns==4) $column_classes='col-md-6 col-lg-3';
	
	//Special case for no results
	if(!$the_posts) return '<h2 id="error-no-results">' . _x('No results found','shortcodes', 'livecanvas') . '</h2>'; 

	foreach ( $the_posts as $the_post ):    //lc_get_posts_default_view($the_post);
		//print_r($the_post);
		$out.="<div class='".$column_classes." mb-3 mb-md-4'>";
		$out.='<article role="article" id="post-'.$the_post->ID.'" class="'.$output_article_class.'">';
		$out.='<header>';
		  
		if ($output_featured_image_before=="1" && strpos( $output_hide_elements,'featuredimage')  === false   ) 
			$out.='<a href="'.get_the_permalink($the_post).'">'.get_the_post_thumbnail($the_post,$output_featured_image_format,array( 'class'	=> $output_featured_image_class )).'</a>';
			   
		if (strpos( $output_hide_elements,'title')  === false   )
			$out.='<'. $output_heading_tag.'> <a href="'.get_the_permalink($the_post).'">'.get_the_title($the_post).'</a>	</'.$output_heading_tag.'>'; 
	   
		if (strpos( $output_hide_elements,'author')  === false  OR strpos( $output_hide_elements,'datetime')  === false  ): 
			$out.='<em>';
			
			if (strpos( $output_hide_elements,'author')  === false   ) $out.=' <span class="text-muted author">'.translate("by", "livecanvas" )." ". get_the_author_meta('display_name',$the_post->post_author).',</span>';
				  
			if (strpos( $output_hide_elements,'date')  === false ) $out.=' <time class="text-muted">'.get_the_date('',$the_post).'</time>';
			
			$out.="</em>";
			endif;
			
		$out.='  </header>';
		
		if ($output_featured_image_before!="1" && strpos( $output_hide_elements,'featuredimage')  === false)
			$out.='<a href="'.get_the_permalink($the_post).'">'.get_the_post_thumbnail($the_post,$output_featured_image_format,array( 'class'	=> $output_featured_image_class )).'</a>';
		
		if (strpos( $output_hide_elements,'excerpt')  === false  && $output_excerpt_length !=0  )
			$out.="<p>".   apply_filters( 'NOOO_the_content',  wp_trim_words ( wp_strip_all_tags( ($the_post->post_content)), $output_excerpt_length, $output_excerpt_text ))."</p>"; 
		
		if (strpos( $output_hide_elements,'category')  === false  OR strpos( $output_hide_elements,'comments')  === false    ):
			$out.='  <footer class="text-muted">';
			if (strpos( $output_hide_elements,'category')  === false )
				$out.='<div class="category"><i class="fa fa-folder-open"></i>&nbsp;'.translate('Category', 'livecanvas').': '. get_the_category_list(', ','',$the_post->ID).'</div>';
			if (strpos( $output_hide_elements,'comments')  === false )
				$out.='<div class="comments"><i class="fa fa-comment"></i>&nbsp;'.translate('Comments', 'livecanvas').': '. get_comments(array('post_id' => ($the_post->ID),'count' => true )).'</div>';
			$out.='</footer>';
		endif;
		
		if (strpos( $output_hide_elements,'clearfix')  === false ) $out.='<div class="clearfix"></div>';
		
		$out.='</article>';
		$out.='</div>';
	
   endforeach;
	
   return  "<div class='row ".$output_wrapper_class."'> ".$out."</div>";
}


// TEMPLATING: ELEMENTARY LINKS LISTING VIEW ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function lc_get_posts_listing_view($the_posts,$get_posts_shortcode_atts){
	extract($get_posts_shortcode_atts);
	$out = "<ul class='".$output_wrapper_class."'>";

	//Special case for no results
	if(!$the_posts) return '<h2 id="error-no-results">' . _x('No results found','shortcodes', 'livecanvas') . '</h2>'; 

	foreach ( $the_posts as $post ) {
		$out.= " <li>";
		$out.= "<a class='" . $output_link_class . "' href='" . get_permalink( $post->ID ) . "'>". $post->post_title. "</a>";
		$out.=" </li>";
	}
	$out .= "</ul>";
	return $out;
}




//TEMPLATING: CARDS ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function lc_get_posts_card_view($the_posts,$get_posts_shortcode_atts) {
	
	extract($get_posts_shortcode_atts);
	$out='';// INIT
	$output_hide_elements=strtolower($output_hide_elements);
	
	//default is 3 rows
	if($output_number_of_columns==1) $column_classes='col-12'; 
	if($output_number_of_columns==2) $column_classes='col-md-6 col-lg-6';
	if($output_number_of_columns==3) $column_classes='col-md-6 col-lg-4';
	if($output_number_of_columns==4) $column_classes='col-md-6 col-lg-3';
	
	//Special case for no results
	if(!$the_posts) return '<h2 id="error-no-results">' . _x('No results found','shortcodes', 'livecanvas') . '</h2>'; 

	foreach ( $the_posts as $the_post ):    
		//print_r($the_post);
		$out.="<div class='".$column_classes." mb-3 mb-md-4'>";
		$out.='<article role="article" id="post-'.$the_post->ID.'" class="card '.$output_article_class.'">';
		
		if ( strpos( $output_hide_elements,'featuredimage')  === false) $out.='<a href="'.get_the_permalink($the_post).'">'.
			'<img alt="" class="'.$output_featured_image_class.'" src="'.get_the_post_thumbnail_url($the_post,$output_featured_image_format).'" />'.
			'</a>';
		
		$out.="<div class='card-body'>";
		$out.='<header>';
		
		
		if ( strpos( $output_hide_elements,'title') === false) 
			$out.='<'. $output_heading_tag.'> <a href="'.get_the_permalink($the_post).'">'.get_the_title($the_post).'</a> </'.$output_heading_tag.'>'; 


	   
		if (strpos( $output_hide_elements,'author')  === false  OR strpos( $output_hide_elements,'datetime')  === false  ): 
			$out.='<em>';
			
			if (strpos( $output_hide_elements,'author')  === false   ) $out.=' <span class="text-muted author">'.translate("by", "livecanvas" )." ". get_the_author_meta('display_name',$the_post->post_author).',</span>';
				  
			if (strpos( $output_hide_elements,'date')  === false ) $out.=' <time class="text-muted">'.get_the_date('',$the_post).'</time>';
			
			$out.="</em>";
			endif;
			
		$out.='  </header>';
	 
		if (strpos( $output_hide_elements,'excerpt')  === false  && $output_excerpt_length !=0  )
			$out.="<p>".   apply_filters( 'NOOO_the_content',  wp_trim_words ( wp_strip_all_tags( ($the_post->post_content)), $output_excerpt_length, $output_excerpt_text ))."</p>"; 
		$out.='  </div>'; //close card body
		
 
		if (strpos( $output_hide_elements,'category')  === false  OR strpos( $output_hide_elements,'comments')  === false    ):
			$out.='<div class="card-footer">  <footer class="text-muted">';
			if (strpos( $output_hide_elements,'category')  === false )
				$out.='<div class="category"><i class="fa fa-folder-open"></i>&nbsp;'.translate('Category', 'livecanvas').': '. get_the_category_list(', ','',$the_post->ID).'</div>';
			if (strpos( $output_hide_elements,'comments')  === false )
				$out.='<div class="comments"><i class="fa fa-comment"></i>&nbsp;'.translate('Comments', 'livecanvas').': '. get_comments(array('post_id' => ($the_post->ID),'count' => true )).'</div>';
			$out.='</footer></div>';
		endif;
 
 
		$out.='</article>';
		$out.='</div>';
	
   endforeach;
	
   return  "<div class='row ".$output_wrapper_class."'> ".$out."</div>";
}


function lc_get_posts_collapsible_view_bs5($the_posts,$get_posts_shortcode_atts) {
	
	extract($get_posts_shortcode_atts); 
	$out='<div class="accordion mb-5 shadow" id="accordionFAQ">'; // INIT

	//Special case for no results
	if(!$the_posts) return '<h2 id="error-no-results">' . _x('No results found','shortcodes', 'livecanvas') . '</h2>'; 

	foreach ( $the_posts as $the_post ):   
		//print_r($the_post);
		$out.='<div class="accordion-item">';
		$out.='<h2 class="accordion-header" id="heading-'.$the_post->ID.'">';
		$out.='<button class="accordion-button collapsed rfs-8 fw-bold py-4 text-secondary"   type="button" data-bs-toggle="collapse" data-bs-target="#collapse-'.$the_post->ID.'" aria-expanded="true" aria-controls="collapse-'.$the_post->ID.'">';
		$out.= get_the_title($the_post);
		$out.='</button>';
		$out.='</h2>';
		$out.='<div id="collapse-'.$the_post->ID.'" class="accordion-collapse collapse" aria-labelledby="heading-'.$the_post->ID.'" data-bs-parent="#accordionFAQ">';
		$out.='<div class="accordion-body">';
		$out.= nl2br( get_post_field('post_content', $the_post->ID));
		$out.='  </div>';
		$out.='</div>';

   endforeach;
   
   $out.='</div>'; // END
	
   return  $out;
}


 
///////////////////////// CUSTOM SIDEBARS SHORTCODE /////////////////////
add_shortcode( 'lc_widgetsarea', 'lc_sidebar_func' );
function lc_sidebar_func( $atts ){
 
    $attributes = shortcode_atts( array(
        'id' => 'main-sidebar',
    ), $atts );
	
	extract($attributes); 
	ob_start();
	
	dynamic_sidebar($id);
	 
	$sidebar_html = ob_get_contents();
	if ($sidebar_html=="") {
		$sidebar_html="<section class='text-center' style='width:100%;padding:20px;background:#efefef';text-align:center>
							<h2>Populate this Widget Area!</h2>
							<blockquote>
								<p>Use the <a class='lc-open-cutomizer-toeditwidgetarea' onFocus=\"javascript:jQuery(this).attr('href',jQuery('#wp-admin-bar-customize a').attr('href'));\" href='#'>theme customizer</a>  
									and go to <code>Widgets</code> >    <code>  ".$id." </code>
								</p>				
							<blockquote>
						</section>";
	}
	ob_end_clean();
	
    return $sidebar_html;
}



 
		
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////// 'CATLIST' SHORTCODE TO LIST ELEMENTS FROM CATEGORY - A simple wrap for wp_list_categories /////////////
add_shortcode( 'lc_get_cats', 'lc_get_cats_func' );
function lc_get_cats_func( $atts ){
	//EXTRACT VALUES FROM SHORTCODE CALL
	$get_cats_shortcode_atts=shortcode_atts( array(
			'child_of'  => '0',
			'current_category' => '0',
			'depth'  => '0',
			'echo'  => false, //so we return the output in a string instead of printing it
			 // INPUT-RELATED PARAMETERS
			'exclude' => false,
			'exclude_tree' => false, //ADD feed,feed_image,feed_type ?
			'hide_empty' => '1',
			'hide_title_if_empty' => false,
			'hierarchical' => true,
			'order' => 'ASC',
			'orderby' => 'ID',
			'separator' => '<br>',
			'show_count' => 0,
			'show_option_all' =>  false,
			'show_option_none' => 'No categories',
			'style'   => 'list',
			'taxonomy'   => 'category',
			'title_li' => 'Categories',
			'use_desc_for_title'     => 1,
			// OUTPUT-RELATED PARAMETERS
			'output_view' => 'lc_get_cats_default_view',
     ), $atts );
	
	extract($get_cats_shortcode_atts);
	
	//GET THE THING
 
	$the_cats = wp_list_categories( $get_cats_shortcode_atts );
	 
	//LAUNCH OUTPUT CALLBACK FUNCTION
	return call_user_func(  $output_view ,$the_cats, $get_cats_shortcode_atts);
} 



//////////////// CATLIST: OUTPUT CALLBACKS /////////////////////////
////////////////////////////////////////////////////////////////////

/////////////////CATSLIST OUTPUT DEFAULT CALLBACK //////////////////////
function lc_get_cats_default_view($the_cats,$get_cats_shortcode_atts){
	extract($get_cats_shortcode_atts);
	return "<ul>". $the_cats. "</ul>";
}
/////////////////ADD TO BLOG //////////////////////
function lc_get_cats_custom_view($the_cats,$get_cats_shortcode_atts){
	extract($get_cats_shortcode_atts);
	//PROCESS THINGS ....
	return "<ul>". $the_cats. "</ul>";
}



/// SUPPORT FUNCTION ///
function lc_get_post_by_slug($slug, $post_type){
    $posts = get_posts(array('name' => $slug, 'posts_per_page' => 1, 'post_type' => $post_type, 'post_status' => 'publish' ));
    if($posts) return $posts[0]; else return FALSE;
}


/////////////////////////////   GET POST CONTENT: GENERAL FOR BLOCKS / SECTIONS /  PARTIALS   /////////////
add_shortcode( 'lc_get_post', function  ( $atts ){
	//grab the post object by id or slug
	if (isset($atts['id'])) $the_post = get_post( $atts['id'] );
	if (isset($atts['slug'])) {
		if (!isset($atts['post_type'])) $atts['post_type'] = get_post_types();
		$the_post = lc_get_post_by_slug($atts['slug'], $atts['post_type']);
	}
	
	if (!$the_post) return ("<i>The lc_get_post shortcode could not retrieve any matching post.</i>");
	$out = do_shortcode(lc_strip_lc_attributes($the_post->post_content));
	return $out;	
});

 
/////////////////////////////   GET GT BLOCKS   /////////////
add_shortcode( 'lc_get_gt_block', function  ( $atts ){
	// https://awhitepixel.com/blog/wordpress-gutenberg-access-parse-blocks-with-php/
		
	if (isset($atts['id'])) $the_post = get_post( $atts['id'] );
	if (isset($atts['slug'])) $the_post = lc_get_post_by_slug($atts['slug'], 'lc_gt_block');

	if (!$the_post) return ("<i>The lc_get_gt_block shortcode could not retrieve any matching post.</i>");
	$blocks = parse_blocks(lc_strip_lc_attributes($the_post->post_content));
	$out = "";
	global $wp_embed;
	foreach ($blocks as $block) { 
		//https://github.com/WordPress/gutenberg/issues/19114#issuecomment-850637818
		$out.=   $wp_embed->autoembed(render_block($block));
	}
	return do_shortcode($out);	
});





///////////////////////// GET CUSTOM FIELD SHORTCODE //////////////////////  
add_shortcode( 'lc_get_cf', function($atts){
	global $post;
	$value =  get_post_meta($post->ID, $atts['field'],TRUE);
	if ($value=="" & current_user_can("administrator")) $value = 'Custom Field <b>'.esc_attr($atts['field']).'</b> is empty';
	return $value;

});




///////////////////////// DISPLAY IMAGE SHORTCODE  //////////////////////  
add_shortcode( 'lc_get_image', function($atts){
  return wp_get_attachment_image(99, 'medium');

});



///////////////////////// AJAX FORM ACTIVATOR SHORTCODE FOR FRONTEND //////////////////// 
//Usage: Add [lc_form action="lc_test_action"] before </form>  

add_shortcode( 'lc_form', function($atts){
	$shortcode_options = shortcode_atts( array( 'action' => 'lc_test_action',  ), $atts );
	global $post;
    return '
	<script>
	//LC form submit event handler
	document.currentScript.closest("form").addEventListener("submit", function (event) {
		event.preventDefault(); 
		const theForm = this.closest("form");
		theForm.querySelector("[type=submit]").setAttribute("disabled","disabled");
		if(!theForm.querySelector(".lc_form_feedback")) theForm.insertAdjacentHTML("beforeend", "<div class=lc_form_feedback></div>");
        theForm.querySelector(".lc_form_feedback").innerHTML="";
		const formdata = new FormData(event.currentTarget);
		formdata.append( "nonce", "'.wp_create_nonce( $shortcode_options['action'] ).'" );
		formdata.append( "action", "'.esc_attr($shortcode_options['action']).'" );
		if (typeof (lc_forms_callback) == "function") { 
			the_result = lc_forms_callback(formdata); 
			if (!the_result) {
				//abort execution
				theForm.querySelector("[type=submit]").removeAttribute("disabled" );
				return false;
			}
		}
		fetch("'.admin_url( 'admin-ajax.php' ).'", {
			method: "POST",
			credentials: "same-origin",
			headers: {
				"Cache-Control": "no-cache",
			},
			body: formdata
		}).then(response => response.text())
		.then(response => {
			//console.log(response); 
			theForm.querySelector(".lc_form_feedback").innerHTML=response;
			theForm.querySelector(".lc_form_feedback").scrollIntoView({block: "end", inline: "nearest"});
			theForm.querySelector("[type=submit]").removeAttribute("disabled" );	
		})
		.catch(err => {
			alert("Form submit error. Details: "+err);
			theForm.querySelector("[type=submit]").removeAttribute("disabled" );	
		});
	});
	</script>
	';
});


///////// SHORTCODE to  GET BLOCKS / SECTIONS / PARTIALS FROM CHILD THEME //

add_shortcode( 'lc_get_file', 'lc_get_file_func' );
function lc_get_file_func( $atts ){
 
    $attributes = shortcode_atts( array(
        'type' => 'partial',
		'name' => 'My test Partial',
    ), $atts );
	
	extract($attributes); 

	return file_get_contents(get_stylesheet_directory() . '/template-livecanvas-' . $type . 's/' . $name . '.html');

}