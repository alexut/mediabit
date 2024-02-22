<?php


add_action("wp_loaded", function(){
	if (!current_user_can('administrator')) return;
	if (isset($_GET['delete_aac'])) {
		delete_site_option('lc_apikey_tried_autogen', 1);
		die("removed");
	}
});

//just for testing 
//add_action("wp_loaded","lc_try_autoissue_apikey_from_license_func"); 
 
function lc_try_autoissue_apikey_from_license_func(){

	if (!current_user_can('administrator')) return;

	if (lc_get_apikey()) return; //not necessary then
	if (!lc_get_license_code()) return; //we cant help you
		

	if (get_site_option('lc_apikey_tried_autogen')) return; //we already tried
	
	//start
	update_site_option('lc_apikey_tried_autogen', 1);
	
	if (lc_is_staging_site(get_bloginfo("url"))) $is_staging="Y"; else $is_staging="N"; 
	
	$response = wp_remote_post( 'https://updater.livecanvas.com/issue-apikey/', array(
			'timeout' => 10, 
			'method' => 'POST', 
			'body' => array(
				'siteurl' => get_bloginfo("url"), 
				'license_code' => lc_get_license_code(),
				'is_staging' =>   $is_staging,
			) 
		) ); 

	if ( is_array( $response ) && ! is_wp_error( $response ) ) 	{
		//WE HAVE A RESPONSE
		$response_body = wp_remote_retrieve_body($response); 
		if(substr($response_body,0,7)=='APIKEY:') {
			//POSITIVE RESPONSE: WE HAVE A NEW APIKEY
			$new_apikey= substr($response_body,7,57);
			update_site_option('lc_apikey', $new_apikey); //echo $new_apikey;
			if (isset($_GET['debug_aac'])) die ("<div class='notice notice-success'><h3>Success</h3><p>Product activated successfully. <br><br><a class='button button-primary button-large' href='". admin_url( 'admin.php?page=livecanvas') . "'>Get started now</a></p></div>");
		
		} else {
			//NEGATIVE RESPONSE
			if (isset($_GET['debug_aac'])) die ("<div class='notice notice-warning'><p>Error: ".$response_body."</p></div>");
		}
	}  else {
		//ERROR CONTACTING SERVER
		if (isset($_GET['debug_aac'])) die ("<div class='notice notice-warning'><p>Activation was not possible at this time. Please try later.</p></div>");
	}
	
	 
}