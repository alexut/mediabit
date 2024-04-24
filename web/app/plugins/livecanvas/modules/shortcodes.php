<?php
// EXIT IF ACCESSED DIRECTLY.
defined( 'ABSPATH' ) || exit;

// DEMO DUMMY SHORTCODE //////////////////////
add_shortcode( 'lc_dummyshortcode', 'lc_demoshortcode_func' );
function lc_demoshortcode_func(){
	return '
	<div style="width:100%;padding:20px;background:#eee;color:#333;text-align:center">
		<h2>I am a dummy shortcode</h2>
	</div>
	';
}

//  SITE URL SHORTCODE //////////////////////
add_shortcode( 'lc_home_url', 'lc_home_url_func' );
function lc_home_url_func(){	return esc_url( home_url( '/' ) ); }

///SITE LOGO SHORTCODE///////////
add_shortcode( 'lc_custom_logo', function(){return get_custom_logo(); } );

//  HEADER MENU SHORTCODE //////////////////////
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
	if (class_exists("bootstrap_5_wp_nav_menu_walker")) {
        
        $the_shortcode_atts=shortcode_atts( array(
						'theme_location' => 'primary',
						'container' => false,
						'menu_class' => 'navbar-nav', //TO THE UL
						'fallback_cb' => '__return_false', 
						'menu_id'         => '',
						//'depth'           => 2,
						'echo'			 => FALSE,	
						'walker'          => $the_walker,
					), $attributes );
    }

	return  " <!--  lc_nav_menu --> " . wp_nav_menu($the_shortcode_atts) . " <!-- /lc_nav_menu --> ";
}

// GET TEMPLATE PART SHORTCODE
add_shortcode( 'lc_get_template_part',  function ( $atts ) {
	$attributes = shortcode_atts( array(
		'slug' => '' ,
		'name' => NULL 
	), $atts );

    if (  $attributes['slug'] == '') {
        return "<div class='alert alert-danger'><h3>Partial file name (slug parameter) is empty</h3> </div>";
    }

	ob_start();
    get_template_part($attributes['slug'], $attributes['name'], $attributes);
    $html = ob_get_contents();
    ob_end_clean();

    if ($html == '') {
        return "<div class='alert alert-danger'><h3>Partial file not found or empty</h3> </div>";
    }
    return $html;
} );

// LC_GET_POSTS SHORTCODE TO GET POSTS - a simple wrap for the get_posts function /////////////
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
            'lang'           => apply_filters( 'wpml_current_language', NULL ),
			///OUTPUT ////////////
			'output_view' => 'lc_get_posts_default_view',
			'output_dynamic_view_id' => FALSE,
            'output_theme_loop_name' => 'cards',
			'output_wrapper_class' => '',
			'output_link_class' => '',
			'output_number_of_columns' => 3,
            'output_article_wrapper_class' => 'mb-3 mb-md-4',
			'output_article_class' => '',
			'output_heading_tag' => 'h2',
			'output_hide_elements'  => '',
            'output_date_format' => '',
			'output_excerpt_length' =>45,
			'output_excerpt_text' => '&hellip;',
			'output_featured_image_before' =>'',
			'output_featured_image_format' =>'large',
			'output_featured_image_class' => 'attachment-thumbnail img-responsive alignleft',
            'output_no_results_message' =>'No results found',
     ), $atts, 'lc_get_posts' );
	 
	extract($get_posts_shortcode_atts);
	
	//CUSTOM TAX QUERY CASE
	if ($tax_query != ""):

		$array_tax_query_par = explode("=", $tax_query);

        //RELATED POSTS subcase
        if($array_tax_query_par[1] == 'related') {

            global $post;
            
            $terms = wp_get_post_terms($post->ID, $array_tax_query_par[0]);
            
            if (!empty($terms)) { 
                $the_main_term = $terms[0]; 
                //return $the_main_term->term_id;
                $array_tax_query_par[1]  =  $the_main_term->term_id;  // main category/term ID of the current post
                $get_posts_shortcode_atts = array_merge($get_posts_shortcode_atts, array('exclude'=> $post->ID,  )); //exclude the current post
            } 
        } //end related
		
        //ADD THE TAX QUERY
        $get_posts_shortcode_atts = array_merge($get_posts_shortcode_atts,
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
	if (!function_exists ( $output_view )) return "<div class='alert alert-danger'><h3>The PHP function: <i>'". $output_view."'</i> is not defined</h3><p>Find a starter example here: https://livecanvas.com/download/18044/</p></div>";
	return call_user_func ( $output_view, $the_posts, $get_posts_shortcode_atts);
}
 
// TEMPLATING: THEME LOOPS VIEW TO RECALL THEME /LOOPS/ PARTIAL FILE //////////
function lc_get_posts_theme_loop_view($the_posts, $get_posts_shortcode_atts) {

	extract($get_posts_shortcode_atts);

	$out = "";
    global $post;
	
    foreach ( $the_posts as $post ):    
        setup_postdata($post);
        ob_start();
        get_template_part('loops/'.$output_theme_loop_name); 
        $out .= ob_get_clean();
    endforeach;

    if ($out == '') {
        return "<div class='alert alert-danger'><h3>Partial file not found or empty</h3> </div>";
    }
	
   return  "<div class='row'> " . $out .  " </div> ";
}

 
// TEMPLATING: PLAIN DEFAULT ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

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
	if(!$the_posts) return '<div class="lc-get-posts-no-results"><p>' . _x($output_no_results_message,'shortcodes', 'livecanvas') . '</p></div>'; 

	foreach ( $the_posts as $the_post ):    //lc_get_posts_default_view($the_post);
		//print_r($the_post);
		$out.="<div class='".$column_classes." ".$output_article_wrapper_class."'>";
		$out.='<article role="article" id="post-'.$the_post->ID.'" class="'.$output_article_class.'">';
		$out.='<header>';
		  
		if ($output_featured_image_before=="1" && strpos( $output_hide_elements,'featuredimage')  === false   ) 
			$out.='<a href="'.get_the_permalink($the_post).'">'.get_the_post_thumbnail($the_post,$output_featured_image_format,array( 'class'	=> $output_featured_image_class )).'</a>';
			   
		if (strpos( $output_hide_elements,'title')  === false   )
			$out.='<'. $output_heading_tag.'> <a href="'.get_the_permalink($the_post).'">'.get_the_title($the_post).'</a>	</'.$output_heading_tag.'>'; 
	   
		if (strpos( $output_hide_elements,'author')  === false  OR strpos( $output_hide_elements,'datetime')  === false  ): 
			$out.='<em>';
			
			if (strpos( $output_hide_elements,'author')  === false   ) $out.=' <span class="text-muted author">'.translate("by", "livecanvas" )." ". get_the_author_meta('display_name',$the_post->post_author).',</span>';
				  
			if (strpos( $output_hide_elements,'date')  === false ) $out.=' <time class="text-muted">' . get_the_date($output_date_format, $the_post).'</time>';
			
			$out.="</em>";
			endif;
			
		$out.='  </header>';
		
		if ($output_featured_image_before!="1" && strpos( $output_hide_elements,'featuredimage')  === false)
			$out.='<a href="'.get_the_permalink($the_post).'">'.get_the_post_thumbnail($the_post,$output_featured_image_format,array( 'class'	=> $output_featured_image_class )).'</a>';
		
		if (strpos( $output_hide_elements,'excerpt')  === false  && $output_excerpt_length !=0  )
			$out.="<p>". wp_trim_words ( wp_strip_all_tags( ($the_post->post_content)), $output_excerpt_length, (
				($output_excerpt_length) ? ' <a href="'.get_the_permalink($the_post).'">'.$output_excerpt_text.'</a>' : ''
				) )."</p>"; 
		
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
	if(!$the_posts) return '<div class="lc-get-posts-no-results"><p>' . _x($output_no_results_message,'shortcodes', 'livecanvas') . '</p></div>'; 

	foreach ( $the_posts as $post ) {
		$out.= " <li>";
		$out.= "<a class='" . $output_link_class . "' href='" . get_permalink( $post->ID ) . "'>". $post->post_title. "</a>";
		$out.=" </li>";
	}
	$out .= "</ul>";
	return $out;
}

//TEMPLATING: MEDIA OBJECT ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function lc_get_posts_media_object_view($the_posts,$get_posts_shortcode_atts) {
	
	extract($get_posts_shortcode_atts);
	$out='';// INIT
	$output_hide_elements=strtolower($output_hide_elements);
	
	//default is 3 rows
	if($output_number_of_columns==1) $column_classes='col-12'; 
	if($output_number_of_columns==2) $column_classes='col-xl-6';
	if($output_number_of_columns==3) $column_classes='col-xl-4';
	if($output_number_of_columns==4) $column_classes='col-xl-3';
	
	//Special case for no results
	if(!$the_posts) return '<div class="lc-get-posts-no-results"><p>' . _x($output_no_results_message,'shortcodes', 'livecanvas') . '</p></div>'; 

	foreach ( $the_posts as $the_post ):    
		//print_r($the_post);
		$out.="<div class='".$column_classes." ".$output_article_wrapper_class."'>";

		// START ARTICLE 
        $out.='<article role="article" id="post-'.$the_post->ID.'" class="d-flex '.$output_article_class.'">';
		
		if ( strpos($output_hide_elements,'featuredimage')  === false && has_post_thumbnail($the_post) ) 
            $out.='
                <div class="flex-sm-shrink-0 col-4 col-sm-auto">
                    <a href="'.get_the_permalink($the_post).'">'.
                        '<img alt="" class="'.str_replace('attachment-thumbnail img-responsive alignleft', '', $output_featured_image_class).'" src="' . get_the_post_thumbnail_url($the_post, $output_featured_image_format).'" />'.
                    '</a>
                </div>';
		
		$out.="<div class='flex-grow-1 ms-3'>";
		$out.='<header>';
		
		if ( strpos( $output_hide_elements,'title') === false) 
			$out.='<'. $output_heading_tag.'> <a class="'.$output_link_class.'" href="'.get_the_permalink($the_post).'">'.get_the_title($the_post).'</a> </'.$output_heading_tag.'>'; 


		if (strpos( $output_hide_elements,'author')  === false  OR strpos( $output_hide_elements,'datetime')  === false  ): 
			$out.='<em>';
			
			if (strpos( $output_hide_elements,'author')  === false   ) $out.=' <span class="text-muted author">'.translate("by", "livecanvas" )." ". get_the_author_meta('display_name',$the_post->post_author).',</span>';
				  
			if (strpos( $output_hide_elements,'date')  === false ) $out.=' <time class="text-muted">'.get_the_date($output_date_format,$the_post).'</time>';
			
			$out.="</em>";
		endif;
			
		$out.='  </header>';
	 
		if (strpos( $output_hide_elements,'excerpt')  === false  && $output_excerpt_length !=0  )
			$out.="<p>". wp_trim_words ( wp_strip_all_tags( ($the_post->post_content)), $output_excerpt_length, (
				($output_excerpt_length) ? ' <a href="'.get_the_permalink($the_post).'">'.$output_excerpt_text.'</a>' : ''
				) )."</p>"; 

		if (strpos( $output_hide_elements,'category')  === false  OR strpos( $output_hide_elements,'comments')  === false    ):
			$out.='<footer class="text-muted">';
			if (strpos( $output_hide_elements,'category')  === false )
				$out.='<div class="category"><i class="fa fa-folder-open"></i>&nbsp;'.translate('Category', 'livecanvas').': '. get_the_category_list(', ','',$the_post->ID).'</div>';
			if (strpos( $output_hide_elements,'comments')  === false )
				$out.='<div class="comments"><i class="fa fa-comment"></i>&nbsp;'.translate('Comments', 'livecanvas').': '. get_comments(array('post_id' => ($the_post->ID),'count' => true )).'</div>';
			$out.='</footer>';
		endif;


		$out.='  </div>'; //close div

		$out.='</article>';
        // END ARTICLE 

		$out.='</div>';
	
   endforeach;
	
   return  "<div class='row ".$output_wrapper_class."'> ".$out."</div>";
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
	if(!$the_posts) return '<div class="lc-get-posts-no-results"><p>' . _x($output_no_results_message,'shortcodes', 'livecanvas') . '</p></div>'; 

	foreach ( $the_posts as $the_post ):    
		//print_r($the_post);
		$out.="<div class='".$column_classes." ".$output_article_wrapper_class."'>";

		$out.='<article role="article" id="post-'.$the_post->ID.'" class="card '.$output_article_class.'">';
		
		if ( strpos( $output_hide_elements,'featuredimage')  === false) $out.='<a href="'.get_the_permalink($the_post).'">'.
			'<img alt="" class="'.$output_featured_image_class.'" src="'.get_the_post_thumbnail_url($the_post,$output_featured_image_format).'" />'.
			'</a>';
		
		$out.="<div class='card-body'>";
		$out.='<header>';
		
		
		if ( strpos( $output_hide_elements,'title') === false) 
			$out.='<'. $output_heading_tag.'> <a class="' . $output_link_class . '" href="'.get_the_permalink($the_post).'">'.get_the_title($the_post).'</a> </'.$output_heading_tag.'>'; 


	   
		if (strpos( $output_hide_elements,'author')  === false  OR strpos( $output_hide_elements,'datetime')  === false  ): 
			$out.='<em>';
			
			if (strpos( $output_hide_elements,'author')  === false   ) $out.=' <span class="text-muted author">'.translate("by", "livecanvas" )." ". get_the_author_meta('display_name',$the_post->post_author).',</span>';
				  
			if (strpos( $output_hide_elements,'date')  === false ) $out.=' <time class="text-muted">'.get_the_date($output_date_format,$the_post).'</time>';
			
			$out.="</em>";
			endif;
			
		$out.='  </header>';
	 
		if (strpos( $output_hide_elements,'excerpt')  === false  && $output_excerpt_length !=0  )
			$out.="<p>". wp_trim_words ( wp_strip_all_tags( ($the_post->post_content)), $output_excerpt_length, (
				($output_excerpt_length) ? ' <a href="'.get_the_permalink($the_post).'">'.$output_excerpt_text.'</a>' : ''
				) )."</p>"; 


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

/////////////TEMPLATING: COLLAPSIBLE
function lc_get_posts_collapsible_view_bs5($the_posts,$get_posts_shortcode_atts) {
	
	extract($get_posts_shortcode_atts); 
	$out='<div class="accordion mb-5 shadow" id="accordionFAQ">'; // INIT

	//Special case for no results
	if(!$the_posts) return '<div class="lc-get-posts-no-results"><p>' . _x($output_no_results_message,'shortcodes', 'livecanvas') . '</p></div>'; 

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
		$out.='</div>';

   endforeach;
   
   $out.='</div>'; // END
	
   return  $out;
}


////TEMPLATING: DYNAMIC LOOP VIEW 
function lc_get_posts_dynamic_view( $the_posts, $get_posts_shortcode_atts) {
	
	extract($get_posts_shortcode_atts);
	$out=''; //init output

	if (!function_exists('lc_get_template_id')) return "
		<div class='alert alert-info'> 
			<h1>LiveCanvas Dynamic Templating is not active</h1>
			<h4>Please enable the LiveCanvas dynamic templating feature.<br>
				 Tick the box <i>'Handle WordPress Templates'</i> in the LiveCanvas backend settings
			</h4> 
		</div>";

	$dynamic_template_id = ( $output_dynamic_view_id && is_numeric($output_dynamic_view_id) && get_post_status($output_dynamic_view_id) == 'publish' && get_post_type($output_dynamic_view_id)=='lc_dynamic_template' ) ? $output_dynamic_view_id : lc_get_template_id('is_post_loop');

	if (!$dynamic_template_id)  $dynamic_template_id = lc_create_dynamic_template('is_post_loop','Template for post loop (default)');

	global $post;

	foreach ( $the_posts as $the_post ):   
		$post = $the_post;
		//print_r($the_post);
		
		$out .= lc_render_dynamic_template ($dynamic_template_id);

	endforeach;
	
   if ($out != '') return  $out; else return "--No output from lc_get_posts_dynamic_view --";
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


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////// 'lc_get_terms' SHORTCODE TO LIST TERMS AS ELEMENTS - A simple wrap for get_categories /////////////
add_shortcode( 'lc_get_terms', 'lc_get_terms_func' );
function lc_get_terms_func( $atts ){
	//EXTRACT VALUES FROM SHORTCODE CALL
	$attributes=shortcode_atts( array(
			'taxonomy'   => 'post_tag',
			'orderby' => 'name',
			'order' =>'ASC',
    		'hide_empty' => FALSE,
			'include' => '',
			'exclude' => '',
			'exclude_tree' =>'',
			'count'=> FALSE,

			'link_class' =>'',
			'class' => 'badge rounded-pill bg-warning',
			'parent'=> FALSE,
			'link' => 1,
    ), $atts );
	
	extract($attributes);
	
	$output="";

	//GET THE DATA
    $the_terms = get_terms( $attributes );
     
    if ($the_terms):
		foreach($the_terms as $tag):
			if (!is_object($tag)) continue; //for safety

			if ( $attributes['parent'] != '' && $tag->parent != $attributes['parent'] ) continue;

			if ($attributes['link']==1) {
				$output .= '<a class="lc-term-'.esc_attr($attributes['taxonomy']).'-'.esc_attr($tag->slug).' '.esc_attr($attributes['link_class']).'" href="' . esc_attr( get_tag_link( $tag->term_id ) )
				.'"><span class="'.$attributes['class'].'">' . __( $tag->name )	. 
				( $count ? ' ('.$tag ->count.')':'') 
				. '</span></a> ';
			}
			else {
				$output.= '<span class="lc-term-'.$attributes['taxonomy'].'-'.esc_attr($tag->slug).' '.$attributes['class'].'">'. __( $tag->name ).'</span> '; 	
			}
		endforeach;
	endif;

    return $output;

}
//DERIVED ONES
add_shortcode( 'lc_get_categories', function( $atts ) {	
	$attributes = shortcode_atts( array(	  
		'taxonomy'   => 'category',
		'orderby' => 'name',
		'order' =>'ASC',
		'hide_empty' => FALSE,
		'include' => '',
		'exclude' => '',
		'exclude_tree' =>'',
		'count'=>FALSE,
		'link_class' =>'',
		'class' => 'badge rounded-pill bg-warning',
		'parent'=> FALSE,
		'link' => 1, 
	), $atts );

	return lc_get_terms_func($attributes);
});

add_shortcode( 'lc_get_tags', function( $atts ) {	
	$attributes = shortcode_atts( array(	  
		'taxonomy'   => 'post_tag',
		'orderby' => 'name',
		'order' =>'ASC',
		'hide_empty' => FALSE,
		'include' => '',
		'exclude' => '',
		'exclude_tree' =>'',
		'count'=>FALSE,
		'link_class' =>'',
		'class' => 'badge rounded-pill bg-info',
		'parent'=> FALSE,
		'link' => 1, 
	), $atts );  

	return lc_get_terms_func($attributes);
});


/// SUPPORT FUNCTION ///
function lc_get_post_by_slug($slug, $post_type){
    $posts = get_posts(array('name' => $slug, 'posts_per_page' => 1, 'post_type' => $post_type, 'post_status' => 'publish' ));
    if($posts) return $posts[0]; else return FALSE;
}


// GET POST CONTENT: GENERAL FOR BLOCKS / SECTIONS /  PARTIALS   /////////////
add_shortcode( 'lc_get_post', function  ( $atts ){
	//grab the post object by id or slug
	if (isset($atts['id'])) $the_post = get_post( $atts['id'] );
	if (isset($atts['slug'])) {
		if (!isset($atts['post_type'])) $atts['post_type'] = get_post_types();
		$the_post = lc_get_post_by_slug($atts['slug'], $atts['post_type']);
	}
	
	if (!$the_post) return ("<i>The lc_get_post shortcode could not retrieve any matching post.</i>");
	$out = do_shortcode(lc_strip_lc_attributes($the_post->post_content));

	//if class attribute is provided, filter output
	if (isset($atts['class'])) $out = lc_filter_html_by_class($atts['class'], $out);

	return $out;	
});

 
//   GET GT BLOCKS   /////////////
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


// GET CUSTOM FIELD SHORTCODE //////////////////////  
add_shortcode( 'lc_get_cf', function($atts){
	global $post;
	$value =  get_post_meta($post->ID, $atts['field'],TRUE);
	if ($value=="" & current_user_can("administrator")) $value = 'Custom Field <b>'.esc_attr($atts['field']).'</b> is empty';
	return $value;

});

//  DISPLAY IMAGE SHORTCODE  //////////////////////  
add_shortcode( 'lc_get_image', function($atts){
  return wp_get_attachment_image(99, 'medium');

});

//  SHORTCODE to  GET BLOCKS / SECTIONS / PARTIALS FROM CHILD THEME //
add_shortcode( 'lc_get_file', 'lc_get_file_func' );
function lc_get_file_func( $atts ){
 
    $attributes = shortcode_atts( array(
        'type' => 'partial',
		'name' => 'My test Partial',
    ), $atts );
	
	extract($attributes); 

	return file_get_contents(get_stylesheet_directory() . '/template-livecanvas-' . $type . 's/' . $name . '.html');

}

//  MULTILANGUAGE LABELS SHORTCODE  [lc_label] ... [/lc_label]
add_shortcode( 'lc_label', function( $atts ) {	
	$attributes = shortcode_atts( array(
		'text' => '',   
		'domain' =>'default'
	), $atts );
	
	return __($attributes['text'], $attributes['domain']);
});


// SHORTCODE to call  'templating' functions
//Example for breadcrumbs nav xt integration: [lc_function name="bcn_display"] 

add_shortcode('lc_function', function( $atts ) {

	$args = shortcode_atts( array(
		'name' => '', 
	), $atts );

	if ($args['name']=='') return "<h2>lc_function shortcode: Please specify name parameter</h2>";
	if (!function_exists($args['name']))  return "<h2>lc_function shortcode: Function ".$args['name']." does not exist</h2>";

	ob_start();
	
	try {
		$out = call_user_func($args['name']);
	} catch(Exception $e) {
		return 'Error Message: ' .$e->getMessage();
	} catch(Error $ex) {
		return 'Error Message: ' .$ex->getMessage();
	} catch(Throwable $t) {
		return 'Error Message: ' .$t->getMessage();
	}
	 
	$html = ob_get_contents();

	ob_end_clean();
	
    return ($out=='') ? $html : $out; 
});

// SHORTCODE TO GET HTML FILES FROM PLUGIN: lc_insert
// eg login / register forms

add_shortcode('lc_insert', function ($atts) {
    // Extract the 'snippet' attribute from the shortcode
    $atts = shortcode_atts(array(
        'snippet' => '', // Default to an empty string if not provided
    ), $atts, 'lc_insert');

    if (empty($atts['snippet'])) {
        return 'No snippet specified.'; // Error message if no snippet is provided
    }

    $snippetName = str_replace('_', '-', $atts['snippet']); // Ensure the snippet name follows your file naming convention
    $htmlFilePath = __DIR__ . '/' . $snippetName . '.html'; // Construct the file path

    if (file_exists($htmlFilePath)) {
        return do_shortcode(file_get_contents($htmlFilePath)); // Return the content of the HTML file
    } else {
        return 'File not found.'; // Error message if the file doesn't exist
    }
});

