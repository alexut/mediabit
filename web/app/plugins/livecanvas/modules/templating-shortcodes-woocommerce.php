<?php
/* ********************** WOOCOMMERCE TEMPLATING SHORTCODES ********************** */

//docs: https://docs.livecanvas.com/wp-admin/post.php?post=39499&action=edit


//PRODUCT DATA SHORTCODE, eg: [lc_wc_product get="id"]
add_shortcode( 'lc_wc_product',  function ( $atts ) {
	$attributes = shortcode_atts( array(
		'get' => 'name', //see https://wpdavies.dev/how-to-get-all-product-info-in-woocommerce/
		'filter' => false, 
		 
	), $atts );
	
	global $post;

	$product = wc_get_product( $post->ID ); 
	
	if (!is_object($product)) return;

	if($attributes['get']=="debug") return (print_r($product,1)); 
	
	if($attributes['get']=="") return "Empty get parameter in lc_wc_product shortcode";

	try { 
		$output =   call_user_func(array($product, 'get_'.$attributes['get'])); 
	} catch(Exception $e) {
		return 'Error Message: ' .$e->getMessage();
	} catch(Error $ex) {
		return 'Error Message: ' .$ex->getMessage();
	} catch(Throwable $t) {
		return 'Error Message: ' .$t->getMessage();
	}

	//case empty array as output, early exit
	if  ($output == array()) return;

	//case filter function
	if ($attributes['filter']) {
		if(function_exists($attributes['filter'])){
			$output = call_user_func(  $attributes['filter'], $output);
		} else {
			if (current_user_can("administrator")) return "Filter function " . esc_attr($attributes['filter'])." is not defined (referenced in  lc_wc_product shortcode). ";
		}
	}

	//is array?
	if(is_array($output)) {
		$html="";
		foreach ($output as $element){
			$html.= $element.' ';
			return $html;
		}
	}

	if( $output=='' && defined('LC_DOING_DYNAMIC_TEMPLATE_SHORTCODE_RENDERING')) {
		return "[Empty field for this product]";
	}

	//is a string
	return $output; 
} );


//PRODUCT CATEGORY SHORTCODE, eg: [lc_wc_product_category]
add_shortcode( 'lc_wc_product_category',  function ( $atts ) {

	$atts = shortcode_atts(
      array(
          'get' => 'name',
      ), $atts, 'get_current_product_category' );

    global $post;
    $terms = get_the_terms( $post->ID, 'product_cat' );
	if ($terms)
		foreach ($terms as $term ) {
		if( $atts['get'] === 'url' ){
			return  esc_url( get_term_link( $term ));
		} else {
			return esc_attr($term->name);
		}
		break;
		}
	 
} );


// IS ON SALE BADGE
add_shortcode( 'lc_wc_on_sale_badge',  function ( $atts ) {
    $attributes = shortcode_atts( array(
        'class' => 'badge bg-success rounded-pill',
        'label' => 'Sale!'
    ), $atts );
    
    global $post;
    
    $product = wc_get_product( $post->ID );  
    
    if (!is_object($product)) return;
    
    if(!$product->is_on_sale() && defined('LC_DOING_DYNAMIC_TEMPLATE_SHORTCODE_RENDERING')) {
		return "[Product Not on sale]";
	}

    if ($product->is_on_sale()) return ' <span class="'.$attributes['class'].'">'.
			do_shortcode('[lc_wc_label text="'.$attributes['label'].'"]').
			'</span> ';
} );


// PRODUCT RATING
add_shortcode( 'lc_wc_rating',  function ( $atts ) {
	
	global $post;

    $product = wc_get_product( $post->ID );
	
	if (!is_object($product)) return;

	// The product average rating (or how many stars this product has)
	$average_rating = $product->get_average_rating();

	// The product stars average rating html formatted.
	$html =  wc_get_rating_html($average_rating);

	if ( $html == '' && defined('LC_DOING_DYNAMIC_TEMPLATE_SHORTCODE_RENDERING')) {
		$html ="[No rating yet]";
	}

	return $html;

} );


//RELATED PRODUCTS SHORTCODE
add_shortcode( 'lc_wc_related',  function ( $atts ) {
	$attributes = shortcode_atts( array(
		'posts_per_page' => 4,
        'columns'        => 4,
        'orderby'        => 'rand',
        'order'          => 'desc',
	), $atts );
	
	global $post,$product;
    
    $product = wc_get_product( $post->ID );  
	
	if (!is_object($product)) return;

    setup_postdata($post->ID);  
    //return (print_r($product,1));
	global $woocommerce_loop;
    $args = $attributes;

	$args['related_products'] = array_filter( array_map( 'wc_get_product', wc_get_related_products( $product->get_id(), $args['posts_per_page'], $product->get_upsell_ids() ) ), 'wc_products_array_filter_visible' );
	$args['related_products'] = wc_products_array_orderby( $args['related_products'], $args['orderby'], $args['order'] );

	//for the live preview, if no related products, simulate related products with the same product n times
	if ( $args['related_products'] == array() && defined('LC_DOING_DYNAMIC_TEMPLATE_SHORTCODE_RENDERING')) {
		$related_products = array();
		for ($i=0; $i < $args['posts_per_page']; $i++) { 
			$related_products[]=$product;
		}
		$args['related_products'] = $related_products;
	}

	// Set global loop values.
	wc_set_loop_prop( 'name', 'related' );
	wc_set_loop_prop( 'columns', $args['columns'] );

	return wc_get_template_html( 'single-product/related.php', $args);
 
} );


//DEFINE SHORTCODE FOR PRODUCT IMAGES CAROUSEL
 
add_shortcode( 'lc_wc_carousel',  function ( $atts ) {
	$attributes = shortcode_atts( array(
		'image_class' => '',
	), $atts );

	global $post; 

    $product = wc_get_product( $post->ID );  
	if (!is_object($product)) return;

	//   variation images 

	$variations_image_ids = array();

    if ($product->is_type('variable')) {
        $variations = $product->get_available_variations();
		//var_dump($variations);die;
        if ($variations) {
            foreach ($variations as $variation):
                $variation_id = $variation['variation_id'];
                $variation_image_id = get_post_meta($variation_id, '_thumbnail_id', true);
				 
                if ($variation_image_id) $variations_image_ids[] = $variation_image_id;
			endforeach;
        }
    }
 
	$attachment_ids = array_merge(array($product->get_image_id()), $product->get_gallery_image_ids(), $variations_image_ids);

	//return (print_r($attachment_ids,1)); 

	if (!$attachment_ids) return;

	//init
	$carousel_items='';
	$carousel_indicators='';
	$count=0;

	foreach( $attachment_ids as $attachment_id ):
		
		$data_variation_attribute='';
		if (in_array($attachment_id, $variations_image_ids)) {
			foreach ($variations as $variation):
                $variation_id = $variation['variation_id'];
                $variation_image_id = get_post_meta($variation_id, '_thumbnail_id', true);
				 
                if ($attachment_id == $variation_image_id) {
					
					foreach ( $variation['attributes'] as  $name => $value):

						$data_variation_attribute .= "data-variation-".$name."='".$value."'";
					
					endforeach;
					
				}
			endforeach;
		}
		$carousel_items.=' 
			<div '.$data_variation_attribute.' class="carousel-item '.($carousel_items=='' ? "active":"").'"> 
			'.wp_get_attachment_image($attachment_id, 'full', '', array( "class" => $attributes['image_class'] )).'
				</div>
				'; 
        if (count($attachment_ids)>1)
            $carousel_indicators.=' 
                <button type="button" data-bs-target="#carouselLiveCanvas" 
                    data-bs-slide-to="'.$count++.'" '.($count==1 ? "class=active aria-current=true":"")
                        .' aria-label="Slide '.$count.'">
                </button>
                    '; 
	endforeach;

    $prevnext_buttons="";
    if (count($attachment_ids)>1) $prevnext_buttons='
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselLiveCanvas" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselLiveCanvas" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
        ';

	$js="";	
	if ($product->is_type('variable')) {
		$js='
		
		<script>
		jQuery(function() {

			//triigger the event manually at page load, for URL eg /?attribute_pa_color=blue&attribute_logo=Yes
			setTimeout(function(){
				jQuery(".variations select").change();
			}, 300);

			//when user changes select
			jQuery(".variations select").on("change", function onchangeselect() {
				
				const chosen_parameters_obj = {};
				
				//loop each select
				jQuery.each(jQuery(".variations select"), function(i, attrib){
					var name = attrib.name;
					var value = attrib.value;
					//console.log(name +":"+value);
					if (value=="") {
						console.log ("one attribute, "+name+" is unset");
						//throw new Error("This is not an error. This is just to abort javascript");
					}
					chosen_parameters_obj[name] = value;
				}); //end each
				
				console.log(chosen_parameters_obj);
				
				let selector=".carousel-item";
				//for each element of the array, build the selector
				for (const [key, value] of Object.entries(chosen_parameters_obj)) {

					var attr = jQuery(".carousel-item").attr(`data-variation-${key}`);

					if (attr !== "" && value !== "") {
					 	selector += `[data-variation-${key}=${value}]`; 
					}
				}
				console.log(selector);
				const index = jQuery(selector).index();
				
				//slide the carousel
				jQuery("#carouselLiveCanvas").carousel(index);

			});
		}); //end ready
		</script>

		';
	}
	
	return '
		<!-- carousel loop -->
		<div id="carouselLiveCanvas" class="carousel slide">
			<div class="carousel-indicators">
				'.$carousel_indicators.'
				</div>
			<div class="carousel-inner">
				'.$carousel_items.'
			</div>
			'.$prevnext_buttons.'
		</div>
		<!-- END carousel loop -->		
		'.$js;
});
 

//DEFINE SHORTCODE FOR ADD TO CART FORM
add_shortcode( 'lc_wc_product_add_to_cart',  function ( $atts ) {

	$attributes = shortcode_atts( array(
		'class' => '',
		'qty_class' => '',
		'select_class' => '',     
	), $atts );
	
	global $post;

	//using add_to_cart_form shortcode by helgatheviking
	$html =  do_shortcode( '[add_to_cart_form style="" show_price="false" id='.$post->ID.']' );  

	//for the live preview, if no rating for this product, set a  demo message
	$needle='>

				
							</div>';
	if ((strpos($html, $needle) !== false)  && defined('LC_DOING_DYNAMIC_TEMPLATE_SHORTCODE_RENDERING')) {
		return "[No Add to Cart button for this product]";
	}

	//if a class is specified, add custom class to the button
	if (isset($attributes['class'])) {
		$html = str_replace(' button ', 
			' '.$attributes['class'].' ', 
			$html);
	}

	//if a class is specified, add custom class to the input type number
	if (isset($attributes['qty_class'])) {
		$html = str_replace(' qty ', 
			' qty '.$attributes['qty_class'].' ', 
			$html);
	}

	//if a class is specified, add custom class to the input type number
	if (isset($attributes['select_class'])) {
		$html = str_replace(' class="" name', 
			' class="'.$attributes['select_class'].'" name', 
			$html);
	}
	
	return $html;

    //alternatively, use the standard WC product_page shortcode and parse it
    //return lc_filter_html_by_class('cart', do_shortcode( '[product_page id='.$post->ID.']' ), TRUE);
} );


//DEFINE SHORTCODE FOR NOTICES
add_shortcode( 'lc_wc_notices',  function ( $atts ) {
	
	global $post;
	return do_shortcode('[shop_messages]'); 
} );


//DEFINE SHORTCODE FOR RATING HTML
add_shortcode( 'lc_wc_product_rating',  function ( $atts ) {
	
	global $post, $product;
	$product = wc_get_product( $post->ID ); 
	if (!is_object($product)) return;
	$html = wc_get_template_html(  'single-product/rating.php', array());

	//for the live preview, if no rating for this product, set a  demo message
	if ( $html =='' && defined('LC_DOING_DYNAMIC_TEMPLATE_SHORTCODE_RENDERING')) {
		$html ="[No rating yet]";
	}

	//alternatively
	//$html = lc_filter_html_by_class('woocommerce-product-rating', do_shortcode( '[product_page id='.$post->ID.']' ), TRUE);

	$js='';

	if (strpos($html, '<a ') !== false)  $js="
		<script>	
			document.addEventListener('click', function (event) {

				// If the clicked element doesnt have the right selector, bail
				if (!event.target.matches('.woocommerce-review-link')) return;

				event.preventDefault();

				document.querySelector('.woocommerce .nav-tabs').scrollIntoView();
				jQuery('.woocommerce .nav-tabs a[href=#reviews]').tab('show');

			}, false);
		</script>
		";
	
	return $html . $js;
} );


//DEFINE SHORTCODE FOR PRODUCT TAB: DESCRIPTION
add_shortcode( 'lc_wc_product_tab_description',  function ( $atts ) { 

	return wc_get_template_html(  'single-product/tabs/description.php', array());
} );


//DEFINE SHORTCODE FOR PRODUCT TAB: additional_information
add_shortcode( 'lc_wc_product_tab_additional_information',  function ( $atts ) {
	
	global $post, $product;
    
    $product = wc_get_product( $post->ID ); 
	
	$out =  wc_get_template_html(  'single-product/tabs/additional-information.php', array()); 
	
	if (strlen($out)<60) {
		$out.= "
		<!-- tabs/additional-information  Seems almost empty  -->
			<style>
				.additional-information-nav-link {display:none}
			</style>
		";
	}

	return $out;

} );


//DEFINE SHORTCODE FOR PRODUCT TAB: reviews & form
add_shortcode( 'lc_wc_product_tab_reviews',  function ( $atts ) {
	
	global $post;

	// working fine but a bit dirty
	return lc_filter_html_by_class('woocommerce-Tabs-panel--reviews', do_shortcode( '[product_page id='.$post->ID.']' ), TRUE);

	//alternative experiment in progress:
	/*
	global $product,$comments,$woocommerce; 

	$product = wc_get_product( $post->ID ); 
	if (!is_object($product)) return;
	setup_postdata($post->ID);    
	$args = array (
		'post_type' => 'product', 
		'post_ID' =>$post->ID,  // Product Id
		'status' => "approve", // Status you can also use 'hold', 'spam', 'trash', 
		'number' => 99  // Number of comment you want to fetch I want latest approved post soi have use 1
	);
	$comments = get_comments($args); 

	return wc_get_template_html(  'single-product-reviews.php', array('comments' => $comments));
	*/
} );


//DEFINE SHORTCODE FOR i18n MULTILANGUAGE LABELS, eg:  [lc_wc_label text='xxx']  
add_shortcode( 'lc_wc_label', function( $atts ) {	
	$attributes = shortcode_atts( array(
		'text' => '',   
	), $atts );
	
	return __($attributes['text'],'woocommerce');
});


//DEFINE SHORTCODE FOR THE LOOP: ADD TO CART
add_shortcode( 'lc_wc_add_to_cart',  function ( $atts ) {
	$attributes = shortcode_atts( array(
        'class' => ''  
	), $atts );
	
	global $post;
    
    $product = wc_get_product( $post->ID ); 

	if (!is_object($product)) return;

	$args = array(
		'quantity' => 1,
		'class' => implode( ' ', array_filter( array(
			$attributes['class'],
			'product_type_' . $product->get_type(),
			$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
			$product->supports( 'ajax_add_to_cart' ) ? 'ajax_add_to_cart' : '',
		) ) ),
	);
 
	//$args['class'] = apply_filters( 'woocommerce_loop_add_to_cart_args', $defaults )['class'];
 
	return apply_filters(
		'woocommerce_loop_add_to_cart_link', // WPCS: XSS ok.
		sprintf(
			'<a href="%s" data-quantity="%s" class="%s" %s>%s</a>',
			esc_url( $product->add_to_cart_url() ),
			esc_attr( isset( $args['quantity'] ) ? $args['quantity'] : 1 ),
			esc_attr( isset( $args['class'] ) ? $args['class'] : 'button' ),
			isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
			esc_html( $product->add_to_cart_text() )
		),
		$product,
		$args
	);

} );


//DEFINE SHORTCODE FOR THE LOOP: ORDER BY
add_shortcode( 'lc_wc_order_by',  function ( $atts ) {
	$attributes = shortcode_atts( array(
		'form_class' => 'woocommerce-ordering',
		'select_class' =>'orderby',
	), $atts );

	//if ajax preview shortcode, return demo
	if(  isset($_POST['action']) and $_POST['action']=='lc_process_dynamic_templating_shortcode') return ' 
	
		<form class="woocommerce-ordering" method="get">
			<select name="orderby" class="orderby"  >
					<option value="menu_order" selected="selected">Order by...</option> 
			</select>
	
		</form>

	';

	ob_start();
	woocommerce_catalog_ordering();
	$output_string = ob_get_contents();
	ob_end_clean();

	$output_string=str_replace('class="woocommerce-ordering"', 'class="'.$attributes['form_class'].'"',$output_string);
	$output_string=str_replace('class="orderby"', 'class="'.$attributes['select_class'].'"',$output_string);

	return $output_string;

});


//DEFINE SHORTCODE FOR THE LOOP: NUMBER OF RESULTS
add_shortcode( 'lc_wc_result_count',  function ( $atts ) {

	//if ajax preview shortcode, return demo
	if(isset($_POST['action']) and $_POST['action']=='lc_process_dynamic_templating_shortcode') return 'Showing XXX results';

	// call $wp_query global variable once (if not included)
	global $wp_query; 

	$current = get_query_var('paged'); //to be tested
	if($current<=0) $current = 1; //eg for shop page

	// Define each variable again (before using it)
	$paged    = max( 1, $wp_query->get( 'paged' ) );
	$per_page = $wp_query->get( 'posts_per_page' );
	$total    = $wp_query->found_posts;
	$first    = ( $per_page * $paged ) - $per_page + 1;
	$last     = min( $total, $wp_query->get( 'posts_per_page' ) * $paged );

	// phpcs:disable WordPress.Security
	if ( 1 === intval( $total ) ) {
		$output_string = __( 'Showing the single result', 'woocommerce' );
	} elseif ( $total <= $per_page || -1 === $per_page ) {
		/* translators: %d: total results */
		$output_string = sprintf( _n( 'Showing all %d result', 'Showing all %d results', $total, 'woocommerce' ), $total );
	} else {
		$first = ( $per_page * $current ) - $per_page + 1;
		$last  = min( $total, $per_page * $current );
		/* translators: 1: first result 2: last result 3: total results */
		$output_string = sprintf( _nx( 'Showing %1$d&ndash;%2$d of %3$d result', 'Showing %1$d&ndash;%2$d of %3$d results', $total, 'with first and last result', 'woocommerce' ), $first, $last, $total );
	}
	// phpcs:enable WordPress.Security

	return $output_string;
});


//DEFINE SHORTCODE TO GET SHOP URL...myaccount, edit_address, shop, cart, checkout, pay, view_order, terms.
add_shortcode( 'lc_wc_get_page_url', function( $atts ) {
	$attributes = shortcode_atts( array(
		'page' => 'shop',  
	), $atts );

	$out = get_permalink( wc_get_page_id( $attributes['page']) );
	return ($out!='') ? $out:'#';
});


//DEFINE SHORTCODE FOR WC SIDEBAR
add_shortcode( 'lc_wc_sidebar', function( $atts ) {
	ob_start();
    dynamic_sidebar('wc-sidebar');
    $out =  ob_get_clean();
	if ($out=='') return "<b><i>Please populate the WooCommerce Shop Sidebar using the Customizer</i></b>";
	return $out;
});


//DEFINE SHORTCODE FOR WC CUSTOM BLOCKS
add_shortcode( 'lc_wc_block', function( $atts ) {
	$attributes = shortcode_atts( array(
		'name' => 'mini-cart',  
	), $atts );

	$block_name = 'woocommerce/'.$attributes['name'];
    return do_blocks( "<!-- wp:$block_name /-->" );
});