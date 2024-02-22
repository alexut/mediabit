<?php

//NO DIRECT ACCESS TO FILE
defined( 'ABSPATH' ) || exit;

///////////////////////////// 'POSTLIST' SHORTCODE TO GET POSTS - a simple wrap for the get_posts function /////////////
function cs_get_posts_func( $atts )
{
	//EXTRACT VALUES FROM SHORTCODE CALL
	$get_posts_shortcode_atts=shortcode_atts( array(
			///INPUT
			'posts_per_page'   	=> 10, // number of posts to return, -1 returns all
			// 'paged'			=> 1, navigate sets of posts when using posts_per_page (2 would return 2nd page of 10 posts)
			'offset'           	=> 0, // offset returned posts by X amount
			'category'         	=> '', // ID of post category
			'category_name'    	=> '',
			'orderby'          	=> 'date',
			'order'            	=> 'DESC', // ascending or descending order
			'include'          	=> '', // comma separated list of post IDs which will be INcluded
			'exclude'          	=> '', // comma separated list of post IDs which will be EXcluded
			'meta_key'         	=> '', // posts which have the key will be returned
			'meta_value'       	=> '', // posts matching the meta_value for the meta_key is returned
			'post_type'        	=> 'post', // retrieves content based on post, page or custom post type
			'post_mime_type'   	=> '',
			'post_parent'      	=> '',
			'author'	   		=> '',
			'post_status'      	=> 'publish', // Retrieves posts by status of the post. Possible values are: “publish”, “pending”, “draft”, “future”, “any” or “trash”
			'suppress_filters' 	=> TRUE,
			'tax_query' 		=> '', //custom: taxonomy=term_id
			///OUTPUT ////////////
			'unique_id' => '', // used to specify a random number for id attributes 
			'output_view' => 'lc_get_posts_default_view',
			'output_wrapper_id' => '',
			'output_wrapper_class' => '',
			'output_number_of_columns' => 0, // 3
			'output_article_class' => '',
			'output_heading_tag' => 'h2',
			'output_hide_elements'  => '',
			'output_excerpt_length' => 45,
			'output_excerpt_text' => '&hellip;',
			'output_article_link' => FALSE,
			'output_article_link_class' => 'btn btn-primary',
			'output_article_link_text' => 'More',
			'output_featured_image_before' => '',
			'output_featured_image_w' => '1920',
			'output_featured_image_h' => '1080',
			'output_featured_image_format' =>'large',
			'output_featured_image_class' => ''
     ), $atts );
	 
	extract($get_posts_shortcode_atts);

	$cs_index = $unique_id;
	
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
	
	//CHECK IF NO RESULTS
	if(!$the_posts && current_user_can("administrator")) return "<h2>No results found</h2>"; 
	
	//LAUNCH OUTPUT CALLBACK FUNCTION
	return call_user_func(  $output_view, $the_posts, $get_posts_shortcode_atts, $cs_index );
}
add_shortcode( 'cs_get_posts', 'cs_get_posts_func' );



function get_featuredImg( $atts = array() )
{
	// set up default parameters
    extract(shortcode_atts(array(
		'class' => 'img-fluid',
	), $atts));
	
	global $post;
	$id = $post->ID;

    if( has_post_thumbnail( $id ) )
	{
		$alt = get_post_meta ( get_post_thumbnail_id(), '_wp_attachment_image_alt', true );
		$featured_img = '<img class="'.$class.'" alt="'.$alt.'" src="'.get_the_post_thumbnail_url($id, 'full').'" />';
	}
	else
	{
		$featured_img = '<span>Featured image not found</span>';
	}
	
	return $featured_img; // can't echo or output directly from shortcode
	// theoretically we could output Custom Fields in this way as well...
}
add_shortcode( 'cs-featuredImg', 'get_featuredImg' );


// TEMPLATING: CAROUSEL ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function cs_get_posts_carousel_view( $the_posts, $get_posts_shortcode_atts )
{
	$cs_index = rand(0,1000);
	extract($get_posts_shortcode_atts); 
	$out = ''; // INIT
	$output_hide_elements = strtolower($output_hide_elements);

	$out = '<ol class="carousel-indicators">';

	$item_count = 0; // init counter
	
	// loop the carousel indicators
	foreach ( $the_posts as $the_post ):
		$out .= '<li data-target="#lc-carousel-index-'.$cs_index.'" data-slide-to="'.$item_count.'"'; 
		
		if ($item_count == 0)
		{
			$out .= ' class="active"';
		}

		$out .= '></li>';

		$item_count++;
	endforeach; // end carousel indicators loop

	$out .= '</ol><!-- /.carousel-indicators -->';

	$out .= '<div class="carousel-inner">';

	$item_count = 0; // reset counter

	// loop the carousel items
	foreach ( $the_posts as $the_post ):
		
		$item_class = 'item '.$output_article_class;

		if ($item_count == 0)
		{
			$item_class .= ' active'; // add active class if we're at the first item
		} 
		
		$out .= '<div id="post_'.$the_post->ID.'" class="carousel-item '.$item_class.'">';

		// get the image
		if ( get_the_post_thumbnail($the_post) ):
			$image = get_the_post_thumbnail($the_post, $output_featured_image_format, array( 'class' => 'd-block w-100' ));
		else: 
			// fpo image
			$image = '<img class="d-block w-100" alt="FPO" src="http://placehold.it/'.$output_featured_image_w.'x'.$output_featured_image_h.'">';
		endif;
		
		$out .= $image;
		
		$out .= '<div class="carousel-caption d-none d-md-block">';
			
		if (strpos( $output_hide_elements,'title')  === false   ):
			$out .= '<'.$output_heading_tag.'><a href="'.get_the_permalink($the_post).'">'.get_the_title($the_post).'</a></'.$output_heading_tag.'>';
		endif;


		if (strpos( $output_hide_elements,'author')  === false OR strpos( $output_hide_elements,'datetime')  === false  ):
			$out .= '<h4><em>';
					
			if (strpos( $output_hide_elements,'author')  === false ):
				$out .= '<span class="text-muted author">'.get_the_author_meta('user_nicename',$the_post->post_author).':</span> ';
			endif;
			
			if (strpos( $output_hide_elements,'datetime')  === false ):
				$out .= '<time class="text-muted">'.get_the_date('',$the_post).'</time>';
			endif;

			$out .= '</em></h4>';
		endif;
					
		if (strpos( $output_hide_elements,'excerpt')  === false  && $output_excerpt_length != 0 ):
			$out .= '<div class="excerpt">'.apply_filters( 'NOOO_the_content',  wp_trim_words ( wp_strip_all_tags( ($the_post->post_content)), $output_excerpt_length, $output_excerpt_text )).'</div><!-- /.excerpt -->';
		endif;
		
			
		$out .= '</div> <!-- .carousel-caption -->';
		$out .= '</div> <!-- .carousel-item -->';

		$item_count++;
	endforeach;
	

	$out .= '</div> <!-- .carousel-inner -->';
	
	$out .= '<!-- Controls -->';
		
	$out .= '<a class="carousel-control-prev" href="#lc-carousel-index-'.$cs_index.'" role="button" data-slide="prev">
			<span class="carousel-control-prev-icon" aria-hidden="true"></span>
		  	<span class="sr-only">Previous</span>
		</a>
		<a class="carousel-control-next" href="#lc-carousel-index-'.$cs_index.'" role="button" data-slide="next">
		  	<span class="carousel-control-next-icon" aria-hidden="true"></span>
		  	<span class="sr-only">Next</span>
		</a>';
	
	wp_reset_postdata();

	// wrap the carousel in a container pre export
	$out = '<div id="lc-carousel-index-'.$cs_index.'" class="carousel slide" data-ride="carousel">'.$out.'</div><!-- /#lc-carousel-index-'.$cs_index.' -->';

	return $out;
}


// get posts tabs nav 
// usage: [cs_get_posts unique_id="666" posts_per_page="3" output_view="cs_get_posts_tabs_nav" output_wrapper_class="flex-column nav-pills"]
function cs_get_posts_tabs_nav ( $the_posts, $get_posts_shortcode_atts, $my_unique_id )
{
	extract($get_posts_shortcode_atts);
	
	// set a unique identifier 
	if ( $my_unique_id ) 
	{
		$cs_index = $my_unique_id; // we can pass in the same unique id - first try to use the one passed in
	}
	else if ( $unique_id )
	{
		$cs_index = $unique_id; // else use the one specified in the shortcode
	}
	else 
	{
		$cs_index = rand(0,1000); // this shouldn't happen technically
	}

	// init
	$out = '';
	$active_class = '';
	$selected = '';
	$item_count = 0;

	$out = '<div id="tabs-'.$cs_index.'" class="nav nav-tabs '.$output_wrapper_class.'" role="tablist">';

	// loop the tabs nav bar
	foreach ( $the_posts as $the_post ):
		if ($item_count == 0)
		{
			$active_class = 'active';
			$selected = 'true';
		}

		$out .= '<a class="nav-link '.$active_class.'" id="tab-'.$cs_index.'-'.$the_post->ID.'" data-toggle="tab" href="#pane-'.$cs_index.'-'.$the_post->ID.'" role="tab" aria-controls="pane-'.$cs_index.'-'.$the_post->ID.'" aria-selected="'.$selected.'">'.get_the_title($the_post).'</a>';

		$item_count++;
		$active_class = ''; // reset
		$selected = 'false';
	endforeach;

	$out .= '</div><!-- /.nav nav-tabs -->';

	wp_reset_postdata();

	return $out; // return tab nav
}

// get posts tabs panels 
// usage: [cs_get_posts unique_id="666" posts_per_page="3" output_view="cs_get_posts_tabs_panels"]
function cs_get_posts_tabs_panels ( $the_posts, $get_posts_shortcode_atts, $my_unique_id )
{
	extract($get_posts_shortcode_atts);
	
	// set a unique identifier 
	if ( $my_unique_id ) 
	{
		$cs_index = $my_unique_id; // we can pass in the same unique id - first try to use the one passed in
	}
	else if ( $unique_id )
	{
		$cs_index = $unique_id; // else use the one specified in the shortcode
	}
	else 
	{
		$cs_index = rand(0,1000); // this shouldn't happen technically
	}

	$out = ''; // init
	$active_class = ''; // init
	$item_count = 0;

	$out .= '<div id="tabs-content-'.$cs_index.'" class="tab-content">';

	// loop the tab panels
	foreach ( $the_posts as $the_post ):
		
		if ($item_count == 0)
		{
			$active_class = 'show active'; // add active class if we're at the first item
		} 
		
		$out .= '<div id="pane-'.$cs_index.'-'.$the_post->ID.'" class="tab-pane fade '.$active_class.'" role="tabpanel" aria-labelledby="tab-'.$cs_index.'-'.$the_post->ID.'">';

		// get the content
		$out .= '<p class="tab-pane-content">' . apply_filters( 'NOOO_the_content',  wp_trim_words ( $the_post->post_content ), $output_excerpt_length, $output_excerpt_text ) . '</p>';

		if ( $output_article_link )
		{
			$out .= '<a class="'.$output_article_link_class.'" href="'.get_the_permalink($the_post).'">'.$output_article_link_text.'</a>';
		}
		
		$out .= '</div> <!-- .tab-pane -->';

		$item_count++;
		$active_class = ''; // reset
	endforeach;

	$out .= '</div><!-- .tab-content -->';

	wp_reset_postdata();

	return $out; // return tab panels
}


// tabs and pill-tabs posts view shortcode 
// TEMPLATING: TABS /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
// 
// usage: [cs_get_posts posts_per_page="3" output_view="cs_get_posts_tabs_view"]
// 
// or: [cs_get_posts posts_per_page="3" output_view="cs_get_posts_tabs_view" output_wrapper_class="nav-pills"]
// 
// uses the above to functions in tandem to output nav and panels in one go
function cs_get_posts_tabs_view( $the_posts, $get_posts_shortcode_atts, $my_unique_id )
{
	extract($get_posts_shortcode_atts);
	
	// set a unique identifier
	if ( $my_unique_id )  // can get passed within $get_posts_shortcode_atts
	{
		$cs_index = $my_unique_id;
	}
	elseif ( $unique_id )
	{
		$cs_index = $unique_id;
	}
	else 
	{
		$cs_index = rand(0,1000);
	}
	 
	// $output_hide_elements = strtolower($output_hide_elements);
	$out = ''; // init
	
	// get the tabs nav
	$out = cs_get_posts_tabs_nav( $the_posts, $get_posts_shortcode_atts, $cs_index );

	// get the tabs panel
	$out .= cs_get_posts_tabs_panels( $the_posts, $get_posts_shortcode_atts, $cs_index );

	wp_reset_postdata();

	return $out;
}



//TEMPLATING: ACCORDIONS ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// usage: [cs_get_posts posts_per_page="5" output_view="cs_get_posts_accordion_view"]

function cs_get_posts_accordion_view($the_posts, $get_posts_shortcode_atts)
{
	extract($get_posts_shortcode_atts);

	// START inits -------------
	$out = '';
	$counter = 0;
	$expanded = '';
	$active_class = '';
	// END inits ---------------

	$output_hide_elements = strtolower($output_hide_elements);
	
	// generate an accordion ID
	if ( $output_wrapper_id )
	{
		// do nothing, use as-is
	}
    elseif ( $unique_id ) // assign the supplied ID to create the attribute
	{
		$output_wrapper_id = 'accordion-'.$unique_id;
	}
	else  // or generate a random number to create the ID attribute
	{
		$output_wrapper_id = 'accordion-'.rand(0,1000);
	}
	
	foreach ( $the_posts as $the_post ): 
		
		if ( $counter == 0 )
		{
			$expanded = 'true';
			$active_class = 'show';
		}
		else 
		{
			$expanded = 'false';
			$active_class = '';
		}

		$out .= '<div class="card '.$output_article_class.'">';
		$out .= '<div id="heading-'.$the_post->ID.'" class="card-header">';
		
		$out .= '<'.$output_heading_tag.'><button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapse-'.$the_post->ID.'" aria-expanded="'.$expanded.'" aria-controls="collapse-'.$the_post->ID.'">'.get_the_title($the_post).'</button></'.$output_heading_tag.'>';

		$out .= '</div><!-- .card-header -->';
		
		$out .= '<div id="collapse-'.$the_post->ID.'" class="collapse '.$active_class.'" aria-labelledby="heading-'.$the_post->ID.'" data-parent="#'.$output_wrapper_id.'">';
		
		$out .= '<div class="card-body">';
	 
		if (strpos( $output_hide_elements,'excerpt')  === false  && $output_excerpt_length !=0  )
			$out .= "<p>". apply_filters( 'NOOO_the_content',  wp_trim_words ( wp_strip_all_tags( ($the_post->post_content)), $output_excerpt_length, $output_excerpt_text ))."</p>";

		if ( $output_article_link )
		{
			$out .= '<a class="'.$output_article_link_class.'" href="'.get_the_permalink($the_post).'">'.$output_article_link_text.'</a>';
		}
 
		$out .= '</div><!-- .card-body -->';
		$out .= '</div><!-- .collapse -->';
		$out .= '</div><!-- .card -->';
		
		$counter++;
   	endforeach;
	
	// create the accordion wrapper
   	return  '<div id="'.$output_wrapper_id.'" class="accordion '.$output_wrapper_class.'"> '.$out.'</div><!-- .accordion -->';
}





/*
    
//TEMPLATING: CAROUSEL ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function lc_get_posts_carousel_view($the_posts,$get_posts_shortcode_atts) {
	global $post;
	$post_backup=$post;
	$lc_carousel_index=rand(0,1000);
	extract($get_posts_shortcode_atts); 
	$output_hide_elements=strtolower($output_hide_elements);
	
	ob_start();
	?> 
	<section id="lc-carousel-index-<?php echo $lc_carousel_index; ?>" class="carousel slide" data-ride="carousel">
		
		<!-- Indicators -->
		<ol class="carousel-indicators">
			<?php
			$carousel_item_count=0;
			foreach ( $the_posts as $post ): ?>
				 <li data-target="#lc-carousel-index-<?php echo $lc_carousel_index; ?>" data-slide-to="<?php echo $carousel_item_count ?>" <?php if ($carousel_item_count==0): ?>class="active" <?php endif ?>></li>
				<?php $carousel_item_count++;
			endforeach ?>
		</ol>
		
		<!-- Wrapper for slides -->
		<div class="carousel-inner" role="listbox">
			<?php
			$carousel_item_count=0;
			foreach ( $the_posts as $post ):
				setup_postdata( $post );
				$carousel_item_count++;
				$image_url_array = wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), $output_featured_image_format);
				?>
				<div id="post_<?php the_ID()?>" class="item <?php if ($carousel_item_count==1) echo "active "; ?><?php echo $output_article_class ?>">
					<img src="<?php echo $image_url_array[0] ?>" alt="<?php echo esc_attr(get_the_title()); ?>" >																
					<div class="carousel-caption">
						
						<?php if (strpos( $output_hide_elements,'title')  === false   ): ?>
						<<?php echo $output_heading_tag ?>><a href="<?php the_permalink(); ?>"><?php the_title()?></a></<?php echo $output_heading_tag ?>>
						<?php endif ?>
						
						<?php if (strpos( $output_hide_elements,'author')  === false  OR strpos( $output_hide_elements,'datetime')  === false  ): ?>
						<h4> 
						  <em>
							<?php if (strpos( $output_hide_elements,'author')  === false   ): ?>
							<span class="text-muted author"><?php _e('By', 'bbe'); echo " "; the_author() ?>,</span>
							<?php endif ?>
							 <?php if (strpos( $output_hide_elements,'datetime')  === false   ): ?>
							<time  class="text-muted" datetime="<?php the_time('d-m-Y')?>"><?php the_time('jS F Y') ?></time>
							<?php endif ?>
						  </em>
						</h4>
						<?php if (strpos( $output_hide_elements,'excerpt')  === false  && $output_excerpt_length !=0 ): ?>
								<div class="excerpt"><?php the_excerpt() ?></div>
								<?php endif ?>
						<?php endif ?>
					
					</div> <!-- close carousel caption -->
				</div> <!-- close item -->
				<?php
			endforeach;
			?>
		</div>
		
		
		<!-- Controls -->
		<a class="left carousel-control" href="#lc-carousel-index-<?php echo $lc_carousel_index; ?>" role="button" data-slide="prev">
		  <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
		  <span class="sr-only">Previous</span>
		</a>
		<a class="right carousel-control" href="#lc-carousel-index-<?php echo $lc_carousel_index; ?>" role="button" data-slide="next">
		  <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
		  <span class="sr-only">Next</span>
		</a>
		
	</section>

	
	<?php
	$out =   ob_get_contents();
	ob_end_clean();
	wp_reset_postdata();
	$post=$post_backup;
	return $out;
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
*/

/*

////SHORTCODE LC NEWSMIXER

add_shortcode( 'lc_newsmixer', 'lc_newsmixer_func' );

function lc_newsmixer_func($atts) {
	
	$the_shortcode_atts=shortcode_atts( array(
			'cats'   => "", //category IDs,comma separated
			'cat_query_args' =>"",
			'heading_postfix'   => "",
			 
     ), $atts );
	 
	extract($the_shortcode_atts);
	
	if($cats==""){
		//If no 'cats' parameter is supplied, list all categories
		$categories = get_categories( $cat_query_args );
		$cats="";
		foreach($categories as $c) $cats .= $c->term_taxonomy_id.",";
	}
	 
	$array_cats=explode(',',$cats);
	 
	$html="";
	$attributes=$atts;
	foreach ($array_cats as $cat_id):
	
		$attributes['category']=$cat_id;
		$html.= ' <div class="page-header "> <h1>  '.get_cat_name($cat_id).' <span class="text-muted">  '.$heading_postfix.' </span></h1>   </div> ';
		$html.= lc_get_posts_func($attributes);
		  
		 
	endforeach;
	return "<div class='lc-newsmixer'>". $html."</div>";
}
		
		
		
//////////////////////////////////////////////


////SHORTCODE LC NEWSMIXER CUSTOM TAX

add_shortcode( 'lc_newsmixer_tax', 'lc_newsmixer_tax_func' );

function lc_newsmixer_tax_func($atts) {
	
	$the_shortcode_atts=shortcode_atts( array(
			'tax_name'   => "",
			'term_ids' =>"",
			'heading_postfix'   => "",
			 
     ), $atts );
	 
	extract($the_shortcode_atts);
	
	if($term_ids==""){
		//If no 'term_ids' parameter is supplied, list all terms in tax
		$terms = get_terms( array( 'taxonomy' => $tax_name,  'hide_empty' => false,) );
		$term_ids="";
		foreach($terms as $t) $term_ids .= $t->term_taxonomy_id.",";
	 
	}
	 
	$array_terms=explode(',',$term_ids);
	 
	$html="";
	$attributes=$atts;
	foreach ($array_terms as $term_id):
		 //$html.=$tax_name.'='.$term_id;
		$attributes['tax_query']=$tax_name.'='.$term_id;
		$TermObject = get_term_by( 'id', $term_id ,$tax_name);
		if(!$TermObject) $html.= '<h3>Wrong '.$term_id .' parameter </h3> ';
			else {
			$html.= ' <div class="page-header "> <h1>    '.$TermObject->name.' <span class="text-muted">  '.$heading_postfix.' </span></h1>   </div> ';
			$html.= lc_get_posts_func($attributes);
		}	  
		 
	endforeach;
	return "<div class='lc-newsmixer-tax'>". $html."</div>";
}
		
*/		



