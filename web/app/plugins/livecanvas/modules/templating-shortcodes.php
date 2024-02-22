<?php

/* ********************** TEMPLATING SHORTCODES: HELPER FUNCTIONS ********************** */
//grab a class from a html element
function lc_filter_html_by_class($class, $html, $keep_wrapper=TRUE){
    
    $dom = new DomDocument();

	//@ $dom->loadHTML($html);
	@$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));

	$xpath = new DOMXpath($dom);
	
	//https://devhints.io/xpath
	$matching_nodes = $xpath->query("//*[contains(@class, '". $class ."')]");

	@$node = $matching_nodes[0];
	
	if (!$node) return;
 
    //default case, return the element with its wrapper DIV
	if($keep_wrapper) return $node->ownerDocument->saveHTML($node); 

    //grab inner node
    $innerHTML= ''; 
    $children = $node->childNodes;
    foreach ($children as $child) {
        $innerHTML .= $child->ownerDocument->saveXML( $child );
    } 
    
    return $innerHTML;
 
}


/* ********************** TEMPLATING SHORTCODES ********************** */

// dynamic shortcode
add_shortcode('lc_wp_func', function($atts = []) {
	$atts = array_change_key_case( (array) $atts, CASE_LOWER );

	$args = shortcode_atts( array(
		'func' => 'echo("specify func parameter as a wordpress function")',
	), $atts );

	try {
		return call_user_func($args['func']);
	} catch(Exception $e) {
		return 'Error Message: ' .$e->getMessage();
	} catch(Error $ex) {
		return 'Error Message: ' .$ex->getMessage();
	} catch(Throwable $t) {
		return 'Error Message: ' .$t->getMessage();
	}
});

/* ============================== FOR THE SINGLE POST ============================== */

add_shortcode( 'lc_the_title', function( $atts ) {	
	$attributes = shortcode_atts( array(
		'link' => 0,
		'link_class' => '',   
	), $atts );
	global $post;		
	
	if ($attributes['link']!=0) {
		return  '<a  class="' . esc_attr( $attributes['link_class'] ).'" href="' . esc_attr( get_permalink( $post->id)).'"> ' . get_the_title( absint( $post->id ) ) . '</a>';
	} else {
		return get_the_title( absint( $post->id ) );
	}
});

add_shortcode( 'lc_the_content', function( $atts ) {	
	global $post;		
	//remove_filter('the_content', 'wptexturize'); 
	return apply_filters('the_content', $post -> post_content);
});

add_shortcode( 'lc_edit_post_link', function( $atts ) {	
	global $post;
	if(!current_user_can( 'edit_post', $post->ID )) return;
	$attributes = shortcode_atts( array(
		'text' => 'Edit This',
		'class' => 'post-edit-link',   
	), $atts );
	global $post;
	return "<a class='".$attributes['class']."' href='".get_edit_post_link()."'>".$attributes['text']."</a>";
});

add_shortcode( 'lc_the_id', function( $atts ) {	
	global $post;		
	return  get_the_ID();
});

add_shortcode( 'lc_the_date', function( $atts ) {	
	global $post;		
	return get_the_date();
});

add_shortcode( 'lc_the_time', function( $atts ) {	
	global $post;		
	return get_the_time();
});

add_shortcode( 'lc_the_author', function( $atts ) {	
	global $post;		
	$attributes = shortcode_atts( array(	  
		'field' => 'display_name', 
	), $atts );
	$author_id = $post->post_author;
	return get_the_author_meta( 'display_name', $author_id );
});

add_shortcode( 'lc_the_avatar', function( $atts ) {	
	global $post;		
	$attributes = shortcode_atts( array(	  
		'size' => 96,	 
		'class' => '',
		'placeholder_url' => ''
	), $atts );

	$author_id = $post->post_author;
	return get_avatar( $author_id,
					$attributes['size'], 
					$attributes['placeholder_url'],
					get_the_author_meta( 'display_name', $author_id ),
					array('class' => $attributes['class'], 'loading' => 'lazy'),
				);
});

add_shortcode( 'lc_the_author_posts_url', function( $atts ) {	
	global $post;		
	$author_id = $post->post_author;
	return get_author_posts_url($author_id);
});

add_shortcode( 'lc_the_permalink', function( $atts ) {	
	global $post;		
	return get_permalink();
});

//for categories and tags and custom taxonomies
// categories: [lc_the_terms taxonomy=category link=1] 
// tags: [lc_the_terms taxonomy=post_tags link=1] 

add_shortcode( 'lc_the_terms', 'lc_the_terms_func');

function lc_the_terms_func( $atts ) {	
	
	$attributes = shortcode_atts( array(	  
		'taxonomy' => 'category',
		'parent' => '',
		'link' => 1,
		'class' => 'badge rounded-pill bg-secondary',
		'link_class' => '',
		'prefix'=>'',
		'suffix'=>''
	), $atts );

	global $post;		
	$output=''; 
	$term_obj_list = get_the_terms( $post->ID, $attributes['taxonomy'] );
	
	if ($term_obj_list):
		foreach($term_obj_list as $tag):
			if (!is_object($tag)) continue; //for safety

			if ( $attributes['parent'] != '' && $tag->parent != $attributes['parent'] ) continue;

			if ($attributes['link']==1) {
				$output .= '<a class="lc-term-'.esc_attr($attributes['taxonomy']).'-'.esc_attr($tag->slug).' '.esc_attr($attributes['link_class']).'" href="' . esc_attr( get_tag_link( $tag->term_id ) )
				.'"><span class="'.$attributes['class'].'">' . __( $tag->name )
				. '</span></a> ';
			}
			else {
				$output.= '<span class="lc-term-'.$attributes['taxonomy'].'-'.esc_attr($tag->slug).' '.$attributes['class'].'">'.$tag->name.'</span> '; 	
			}
		endforeach;
	endif;

	if (trim($output)  != '') {
		return $attributes['prefix'].$output.$attributes['suffix']; 
	} else {
		if( defined('LC_DOING_DYNAMIC_TEMPLATE_SHORTCODE_RENDERING'))  return "[No ".$attributes['taxonomy']." assigned]";
	}

}

add_shortcode( 'lc_the_categories', function( $atts ) {	
	$attributes = shortcode_atts( array(	  
		'taxonomy' => 'category',
		'parent' => '',
		'link' => 1,
		'class' => 'badge rounded-pill bg-warning',
		'link_class' => '',
		'prefix'=>'',
		'suffix'=>''
	), $atts );

	return lc_the_terms_func($attributes);
});

add_shortcode( 'lc_the_tags', function( $atts ) {	
	$attributes = shortcode_atts( array(	  
		'taxonomy' => 'post_tag',
		'parent' => '',
		'link' => 1,
		'class' => 'badge rounded-pill bg-info',
		'link_class' => '',
		'prefix'=>'',
		'suffix'=>''
	), $atts );  

	return lc_the_terms_func($attributes);
});

add_shortcode( 'lc_the_cf', function($atts){ 
	global $post;
	$attributes = shortcode_atts( array(
		'field' => '', 
		'filter' => false, 
		'class' =>'',
		'prefix'=>'',
		'prefix_class' =>'',
		'suffix'=>'',
		'suffix_class' =>'',
		'output' =>'span',
		'anchor_text' => 'More Details...',
		'target' => '',
		'title' => '',
		), $atts );

	//case empty field name
	if  ( $attributes['field'] == "" ):
		if(  defined('LC_DOING_DYNAMIC_TEMPLATE_SHORTCODE_RENDERING')) {
			return "[Empty field name]";
		} else {
			return "";
		}
	endif;

	//READ POST METADATA OF THAT KEY / FIELD NAME	
	$value =  get_post_meta($post->ID, $attributes['field'], TRUE);

	//case empty value
	if ( $value == "" ):
		if(  defined('LC_DOING_DYNAMIC_TEMPLATE_SHORTCODE_RENDERING')) {
			return "[Empty value]";
		} else {
			return "";
		}
	endif;

	//case filter function
	if ($attributes['filter']) {
		if(function_exists($attributes['filter'])){
			$value = call_user_func(  $attributes['filter'], $value);
		} else {
			if (current_user_can("administrator")) return "Filter function " . $attributes['filter']." is not defined. ";
		}
	}
	
	if ($attributes['output'] == 'raw') return $attributes['prefix'] . $value . $attributes['suffix'];

	if ($attributes['output'] == 'a') return 
		
		'<span class="' . $attributes['prefix_class'] . '">' . $attributes['prefix'] . '</span>'. 
		'<a target="' . $attributes['target'] . '" title="' . $attributes['title'] . '" href="' . $value . '" class="'. $attributes['class'] . '">' . $attributes['anchor_text'] . '</a>'.
		'<span class="' . $attributes['suffix_class'] . '">' . $attributes['suffix'] . '</span>';

	if ($attributes['output'] == 'span') return  
		
		'<span class="'.$attributes['prefix_class'] . '">'.$attributes['prefix'] . '</span>'. 
		'<span class="'.$attributes['class'] . '" title="' . $attributes['title'] . '" >' . $value . '</span>'.
		'<span class="'.$attributes['suffix_class'] . '">'.$attributes['suffix'] . '</span>';
		 
});

//FILTER FUNC EXAMPLES
//Super simple. Define your own custom filter functions in your child theme or site plugin as needed!
function lc_from_url_to_name($input){
	return str_replace(["https://", "http://", "/","www."], "", $input);
}
function lc_to_uppercase($input){
	return strtoupper( $input);
}

	
add_shortcode( 'lc_the_thumbnail', function($atts){
	
	$attributes = shortcode_atts( array(
		'size' => 'post-thumbnail',	 
		'class' => 'img-fluid',
		'style' => '',
		'placeholder' => 1,
		'placeholder_url' => ''
	), $atts );

	global $post;

	$out = get_the_post_thumbnail($post->ID, $attributes['size'],  array( 'class' => $attributes['class'], 'style' => $attributes['style'] ) );

	if ($attributes['placeholder']!=0):
		$placeholder_url = 'https://via.placeholder.com/1500x1500.png?text=Placeholder';
		if ($attributes['placeholder_url']!='') $placeholder_url=$attributes['placeholder_url'];
		if ( empty( $out ) ) $out = '<img loading="lazy" class="'.$attributes['class'].'" style="'.$attributes['style'].'" src="'.$placeholder_url.'" alt="" />';
			
	endif;

	return $out;
});


add_shortcode( 'lc_the_thumbnail_url', function($atts){
	
	$attributes = shortcode_atts( array(
		'size' => 'post-thumbnail',	 
		'placeholder' => 1,
		'placeholder_url' => ''
	), $atts );

	global $post;

	$out = get_the_post_thumbnail_url($post->ID, $attributes['size'],   );

	if ($attributes['placeholder']!=0):
		$placeholder_url = 'https://via.placeholder.com/1500x1500.png?text=Placeholder';
		if ($attributes['placeholder_url']!='') $placeholder_url=$attributes['placeholder_url'];
		if ( empty( $out ) ) return $placeholder_url;	
	endif;

	return $out;
});


add_shortcode( 'lc_the_sharing', function($atts){
	$attributes = shortcode_atts( array( 
		'class' => 'mt-1 mb-5', 
	), $atts );
	ob_start();
	lc_the_sharing_buttons($attributes['class']);
	$output_string = ob_get_contents();
	ob_end_clean();
	return $output_string;
});

function lc_the_sharing_buttons($class){

	global $post;
	$url_to_share=esc_attr(get_permalink($post->ID));
	$icon_size = "1.4em";
	?>
	<div class="lc-sharing-buttons <?php echo $class ?>" >

		 
		<!-- Facebook (url) -->
		<a class="btn btn-outline-dark btn-sm btn-facebook" href="https://www.facebook.com/sharer.php?u=<?php echo $url_to_share ?>" target="_blank" rel="nofollow" title="Share on Facebook">
		<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" width="<?php echo $icon_size ?>" height="<?php echo $icon_size ?>" lc-helper="svg-icon" fill="currentColor">
			<path d="M279.14 288l14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z"></path>
		</svg>
			<span class="d-none d-lg-inline"> Facebook</span>
		</a>
		
		<!-- Whatsapp (url) -->
		<a class="btn btn-outline-dark  btn-sm btn-whatsapp" href="https://api.whatsapp.com/send?text=<?php echo $url_to_share ?>" target="_blank" rel="nofollow" title="Share on Whatsapp">
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" width="<?php echo $icon_size ?>" height="<?php echo $icon_size ?>" lc-helper="svg-icon" fill="currentColor">
				<path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"></path>
			</svg>
			<span class="d-none d-lg-inline"> Whatsapp</span>
		</a>
		
		<!-- Telegram (url) -->
		<a class="btn btn-outline-dark  btn-sm btn-telegram" href="https://telegram.me/share/url?url=<?php echo $url_to_share ?>&text=" target="_blank" rel="nofollow" title="Share on Telegram">
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" width="<?php echo $icon_size ?>" height="<?php echo $icon_size ?>" lc-helper="svg-icon" fill="currentColor">
				<path d="M446.7 98.6l-67.6 318.8c-5.1 22.5-18.4 28.1-37.3 17.5l-103-75.9-49.7 47.8c-5.5 5.5-10.1 10.1-20.7 10.1l7.4-104.9 190.9-172.5c8.3-7.4-1.8-11.5-12.9-4.1L117.8 284 16.2 252.2c-22.1-6.9-22.5-22.1 4.6-32.7L418.2 66.4c18.4-6.9 34.5 4.1 28.5 32.2z"></path>
			</svg>
			<span class="d-none d-lg-inline"> Telegram</span>
		</a>
		
		
		<!-- Twitter (url, text, @mention) -->
		<a class="btn btn-outline-dark  btn-sm btn-twitter" href="https://twitter.com/share?url=<?php echo $url_to_share ?>&text=<?php echo esc_attr(get_the_title()) ?>&via=<?php echo get_the_author_meta( 'twitter', get_the_author_meta( 'ID' ) ) ?>" target="_blank" rel="nofollow" title="Share on Twitter">
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="<?php echo $icon_size ?>" height="<?php echo $icon_size ?>" lc-helper="svg-icon" fill="currentColor">
				<path d="M459.37 151.716c.325 4.548.325 9.097.325 13.645 0 138.72-105.583 298.558-298.558 298.558-59.452 0-114.68-17.219-161.137-47.106 8.447.974 16.568 1.299 25.34 1.299 49.055 0 94.213-16.568 130.274-44.832-46.132-.975-84.792-31.188-98.112-72.772 6.498.974 12.995 1.624 19.818 1.624 9.421 0 18.843-1.3 27.614-3.573-48.081-9.747-84.143-51.98-84.143-102.985v-1.299c13.969 7.797 30.214 12.67 47.431 13.319-28.264-18.843-46.781-51.005-46.781-87.391 0-19.492 5.197-37.36 14.294-52.954 51.655 63.675 129.3 105.258 216.365 109.807-1.624-7.797-2.599-15.918-2.599-24.04 0-57.828 46.782-104.934 104.934-104.934 30.213 0 57.502 12.67 76.67 33.137 23.715-4.548 46.456-13.32 66.599-25.34-7.798 24.366-24.366 44.833-46.132 57.827 21.117-2.273 41.584-8.122 60.426-16.243-14.292 20.791-32.161 39.308-52.628 54.253z"></path>
			</svg>
			<span class="d-none d-lg-inline"> Twitter</span>
		</a>


		<!-- Email (subject, body) --> 
		<!-- 
		<a class="btn btn-outline-dark  btn-sm btn-email" href="mailto:?subject=<?php echo esc_attr(get_the_title()) ?>&amp;body=<?php echo $url_to_share ?>" target="_blank" rel="nofollow" title="Share via Email">
			<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="<?php echo $icon_size ?>" height="<?php echo $icon_size ?>" viewBox="0 0 24 24" lc-helper="svg-icon" fill="currentColor">
				<path d="M22 6C22 4.9 21.1 4 20 4H4C2.9 4 2 4.9 2 6V18C2 19.1 2.9 20 4 20H20C21.1 20 22 19.1 22 18V6M20 6L12 11L4 6H20M20 18H4V8L12 13L20 8V18Z"></path>
			</svg>
			<span class="d-none d-lg-inline"> Email</span>
		</a>
		-->

	</div>
	<?php
	} //end function

add_shortcode( 'lc_the_next_post_link', function( $atts ) {	
	$attributes = shortcode_atts( array(	
		'class' => '',  
		'format' => '%link &raquo;',
		'link' => '%title',
		'in_same_term' => false,
		'excluded_terms' => '',
		'taxonomy'=> 'category'
	), $atts );  
	
	$out =  get_next_post_link($attributes['format'], $attributes['link'], $attributes['in_same_term'], $attributes['excluded_terms'], $attributes['taxonomy']);
	$out = str_replace('<a ','<a class="'.$attributes['class'].'" ',$out);
	return $out;
});

add_shortcode( 'lc_the_previous_post_link', function( $atts ) {	
	$attributes = shortcode_atts( array(	  
		'class' => '',  
		'format' => '&laquo; %link',
		'link' => '%title',
		'in_same_term' => false,
		'excluded_terms' => '',
		'taxonomy'=> 'category'
	), $atts );  

	$out =   get_previous_post_link($attributes['format'], $attributes['link'], $attributes['in_same_term'], $attributes['excluded_terms'], $attributes['taxonomy']);
	$out = str_replace('<a ','<a class="'.$attributes['class'].'" ',$out);
	return $out;
});

add_shortcode( 'lc_the_comments_number', function( $atts ) {
	global $post;
	return get_comments_number();
});


add_shortcode( 'lc_the_comments', function( $atts ) {	 

	$attributes = shortcode_atts( array(	  
	), $atts );  
	ob_start();

	global $post;

	$comments = get_comments( array('post_id' => get_the_ID(),'order' => 'ASC') );

	/// START COMMENTS TEMPLATE ///////
	/*
	* If the current post is protected by a password and
	* the visitor has not yet entered the password we will
	* return early without loading the comments.
	*/
	if ( post_password_required() ) {
		return;
	}
	?>

	<div class="comments-area" id="comments">

		<?php // You can start editing here -- including this comment! ?>

		<?php if (get_comments_number()>0 ) : ?>

			<h2 class="comments-title">

				<?php
				$comments_number = get_comments_number();
				if ( 1 === (int) $comments_number ) {
					printf(
						/* translators: %s: post title */
						esc_html_x( 'One thought on &ldquo;%s&rdquo;', 'comments title', 'livecanvas' ),
						'<span>' . get_the_title() . '</span>'
					);
				} else {
					printf(
						esc_html(
							/* translators: 1: number of comments, 2: post title */
							_nx(
								'%1$s thought on &ldquo;%2$s&rdquo;',
								'%1$s thoughts on &ldquo;%2$s&rdquo;',
								$comments_number,
								'comments title',
								'livecanvas'
							)
						),
						number_format_i18n( $comments_number ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						'<span>' . get_the_title() . '</span>'
					);
				}
				?>

			</h2><!-- .comments-title -->

			<?php if ( $comments_number > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through. ?>

				<nav class="comment-navigation" id="comment-nav-above">

					<h1 class="visually-hidden"><?php esc_html_e( 'Comment navigation', 'livecanvas' ); ?></h1>

					<?php if ( get_previous_comments_link() ) { ?>
						<div class="nav-previous">
							<?php previous_comments_link( __( '&larr; Older Comments', 'livecanvas' ) ); ?>
						</div>
					<?php } ?>

					<?php	if ( get_next_comments_link() ) { ?>
						<div class="nav-next">
							<?php next_comments_link( __( 'Newer Comments &rarr;', 'livecanvas' ) ); ?>
						</div>
					<?php } ?>

				</nav><!-- #comment-nav-above -->

			<?php endif; // Check for comment navigation. ?>

			<ol class="comment-list">

				<?php
				wp_list_comments(
					array(
						'style'      => 'ol',
						'short_ping' => true,
					),$comments
				);
				?>

			</ol><!-- .comment-list -->

			<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through. ?>

				<nav class="comment-navigation" id="comment-nav-below">

					<h1 class="visually-hidden"><?php esc_html_e( 'Comment navigation', 'livecanvas' ); ?></h1>

					<?php if ( get_previous_comments_link() ) { ?>
						<div class="nav-previous">
							<?php previous_comments_link( __( '&larr; Older Comments', 'livecanvas' ) ); ?>
						</div>
					<?php } ?>

					<?php	if ( get_next_comments_link() ) { ?>
						<div class="nav-next">
							<?php next_comments_link( __( 'Newer Comments &rarr;', 'livecanvas' ) ); ?>
						</div>
					<?php } ?>

				</nav><!-- #comment-nav-below -->

			<?php endif; // Check for comment navigation. ?>

		<?php endif; // End of if have_comments(). ?>

		<?php comment_form(); // Render comments form. ?>

	</div><!-- #comments -->

	<?php 
	//END COMMENTS TEMPLATE /////

	$output_string = ob_get_contents();
	ob_end_clean();
	return $output_string;
});

/* ============================== FOR THE ARCHIVES ============================== */

//THE LOOP SHORTCODE  [lc_loop] ... [/lc_loop]
add_shortcode( 'lc_loop',  function ($atts = array(), $enclosed_content = null) {
	$output = '';
	if(have_posts()):
		while (have_posts()) {
			the_post(); 
			$output .= do_shortcode($enclosed_content);
		}
	endif;

	return $output;
});

add_shortcode( 'lc_the_excerpt', function( $atts ) {
	
	$attributes = shortcode_atts( array(
		'link' => 1,	   
		'length' => 0
	), $atts );
	
	global $post;

	if($attributes['link']==0){
		remove_filter( 'wp_trim_excerpt', 'picostrap_all_excerpts_get_more_link' );
	}

	if($attributes['length']!=0){
		remove_all_filters( 'excerpt_length');
		global $excerpt_length;
		$excerpt_length=$attributes['length'];
		add_filter("excerpt_length",function($in){global $excerpt_length; return $excerpt_length;});
	}

	return get_the_excerpt();		 
});

add_shortcode( 'lc_the_archive_title', function( $atts ) {
	$attributes = shortcode_atts( 
		array(
			'strip_label' => '',
		), $atts );

	global $post;		
	$out=  get_the_archive_title();		 
	
	if ( $attributes['strip_label']!='' && strpos($out, ':') !== false ):
		$splitted=explode(':', $out);
		$out = $splitted[1];
	endif;


	return $out;
});

add_shortcode( 'lc_the_archive_description', function( $atts ) {
	global $post;		
	return get_the_archive_description();		 
});

add_shortcode( 'lc_the_pagination', function( $atts ) {
	global $post;		
	$out="";
	$attributes = shortcode_atts( 
		array(
			'mid_size'           => 2,
			'prev_next'          => true,
			'prev_text'          => __( '&laquo;', 'livecanvas' ),
			'next_text'          => __( '&raquo;', 'livecanvas' ),
			'type'               => 'array',
			'current'            => max( 1, get_query_var( 'paged' ) ),
			'screen_reader_text' => __( 'Posts navigation', 'livecanvas' ),
			'class'           => 'pagination',
		), $atts );

	if ( ! isset( $attributes['total'] ) && $GLOBALS['wp_query']->max_num_pages <= 1 ) {
		//case no pagination
		if(  defined('LC_DOING_DYNAMIC_TEMPLATE_SHORTCODE_RENDERING')) {
			return "<div class='".$attributes['class']."'>[Pagination placeholder]</div>";
		} else {
			return;
		} 
	}

	$links = paginate_links( $attributes );

	if ( ! $links ) return;
	
	$out.='<nav aria-labelledby="posts-nav-label"> 
	<h2 id="posts-nav-label" class="visually-hidden">' . esc_html( $attributes['screen_reader_text'] ) . '</h2>';

	$out.='	<ul class="'. esc_attr( $attributes['class'] ).'"> ';
	foreach ( $links as $key => $link ) {
		$out.='	<li class="page-item '. (strpos( $link, 'current' ) ? 'active' : '') .
			'" >'.str_replace( 'page-numbers', 'page-link', $link ) .'</li>';
	}
	$out.=	'</ul></nav>';

	return $out;
});

add_shortcode( 'lc_the_search_query', function( $atts ) {	
	global $post;		
	return  esc_attr(get_search_query());
});


/*
add_shortcode( 'lc_the_permalink_tag', function( $atts, $enclosed_content = null ) {	
	$attributes = shortcode_atts( array(	  
		'tag' => 'a',
		'class' => 'btn btn-primary',
		'id' => '',
		'style' => '',
		'title' => '',
		'target' => '',
		'rel' => '',
	), $atts );

	global $post;		

	$link = '<a href="' . 
		esc_attr( get_permalink( $post->id)).'" '.
		'class="' . esc_attr( $attributes['class'] ) . '" '.
		'id="' . esc_attr( $attributes['id'] ) . '" '.
		'style="' . esc_attr( $attributes['style'] ) . '" '.
		'title="' . esc_attr( $attributes['title'] ) . '" '.
		'target="' . esc_attr( $attributes['target'] ) . '" '.
		'rel="' . esc_attr( $attributes['rel'] ) . '" '.
		'> ' . 
			do_shortcode($enclosed_content) .
		'</a>';

	return $link;
});
*/


add_shortcode( 'lc_get_template_part',  function ( $atts ) {
	$attributes = shortcode_atts( array(
		'slug' => '' ,
		'name' => NULL 
	), $atts );
	
	ob_start();
    get_template_part($attributes['slug'], $attributes['name'], $attributes);
    $var = ob_get_contents();
    ob_end_clean();
    return $var;
} );


//THE LABELS SHORTCODE  [lc_label] ... [/lc_label]
add_shortcode( 'lc_label', function( $atts ) {	
	$attributes = shortcode_atts( array(
		'text' => '',   
		'domain' =>'default'
	), $atts );
	
	return __($attributes['text'], $attributes['domain']);
});


// DEFINE SHORTCODE TO GRAB HTML FROM SHORTCODES    
add_shortcode( 'lc_grab_html',  function ( $atts ) {
	$attributes = shortcode_atts( array(
		'shortcode' => '' , //the shortcode name //think you can add pars as well
		'class' => '', //the class name of the element to grab
		'inner' => FALSE,//return inner content
	), $atts );
	
	global $post;
	
	$html = do_shortcode( '['.$attributes['shortcode'].' id="'.$post->ID.'"]' );  

	//for debug
	//return $html;
 
    return lc_filter_html_by_class($attributes['class'], $html, $attributes['inner']);
} );




