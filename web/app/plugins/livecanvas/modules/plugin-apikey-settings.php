<?php

function lc_is_staging_site($string){
	$staging_array=array('.dev','.local','.staging','.test','.example','.invalid',
						'dev.','local','test','staging'); 
	foreach ($staging_array as $url_part) { 
		if (strpos($string, $url_part) !== FALSE) { 
			return true;
		}
	} 
	return false;
}

function lc_get_apikey(){
	$apikey = get_site_option('lc_apikey');
	if ($apikey=="") return FALSE;
	return $apikey;
}

function lc_license_page_func(){
	if (!current_user_can('administrator')) return;
	?>
	<div class="wrap">
	<img src="<?php echo plugins_url("/livecanvas/images/lc-logo.svg") ?>" style="width:200px;height: auto;margin:20px 0 10px;">
	<h1>Product Activation</h1>
	<?php

	//CASE SUBMIT OF ACTIVATION FORM	
	if (isset($_POST['lc-activate-product'])):
		check_admin_referer('lc_activate_product');
		$response = wp_remote_post( 'https://updater.livecanvas.com/issue-apikey/', array(
			'timeout' => 10, 
			'method' => 'POST', 
			'body' => array(
				'siteurl' => get_bloginfo("url"), 
				'license_code' => $_POST['license_code'],
				'is_staging' => (isset($_POST['is_staging']) ? $_POST['is_staging'] : "N"),
			) 
		) ); 

		if ( is_array( $response ) && ! is_wp_error( $response ) ) 	{
			//WE HAVE A RESPONSE
			$response_body = wp_remote_retrieve_body($response); 
			if(substr($response_body,0,7)=='APIKEY:') {
				//POSITIVE RESPONSE: WE HAVE A NEW APIKEY
				$new_apikey= substr($response_body,7,57);
				update_site_option('lc_apikey', $new_apikey); //echo $new_apikey;
				echo "<div class='notice notice-success'><h3>Success</h3><p>Product activated successfully. <br><br><a class='button button-primary button-large' href='". admin_url( 'admin.php?page=livecanvas') . "'>Get started now</a></p></div>";
			
			} else {
				//NEGATIVE RESPONSE
				echo "<div class='notice notice-warning'><p>Error: ".$response_body."</p></div>";
				unset($_POST['lc-activate-product']);
			}
		}  else {
			//ERROR CONTACTING SERVER
			echo "<div class='notice notice-warning'><p>Activation was not possible at this time. Please try later.</p></div>";
		}
		
	endif; //END CASE PROCESS SUBMIT OF ACTIVATION FORM

	//CASE SUBMIT OF FORM FOR DISCONNECT
	if (isset($_POST['lc-deactivate-product'])):
		check_admin_referer('lc_deactivate_product');

		$response = wp_remote_post( 'https://updater.livecanvas.com/disable-apikey/', array(
			'timeout' => 10, 
			'method' => 'POST', 
			'body' => array(
				'siteurl' => get_bloginfo("url"), 
				'apikey' => lc_get_apikey(),
			) 
		) ); 

		if ( is_array( $response ) && ! is_wp_error( $response ) ) 	{
			//WE HAVE A RESPONSE
			$response_body = wp_remote_retrieve_body($response); 
			if ($response_body =='OK') {
				//POSITIVE RESPONSE: WE HAVE DISCONNECTED
				delete_site_option('lc_apikey');
				echo "<div class='notice notice-info'><p>Site has been disconnected from your account</p></div>";
			} else {
				//NEGATIVE RESPONSE
				echo "<div class='notice notice-warning'><p>Error: Could not disconnect (".$response_body.")</p></div>";
			}
		}  else {
			//ERROR CONTACTING SERVER
			echo "<div class='notice notice-warning'><p>Deactivation was not possible at this time. Please try later.</p></div>";
		}
		
	endif; //END CASE DISCONNECT

	//STANDARD / FORM CASE
	if (!isset($_POST['lc-activate-product'])):	 

		if (!lc_get_apikey()) {  
			// CASE NO APIKEY: SHOW ACTIVATION FORM 
			?>
			<p>Please retrieve your license from the <a target="_new" href="https://livecanvas.com/members-area/">members area</a> </p>
			</p>		
			<form method="post" style="margin:30px 0; width:400px; background: #ddd;padding: 20px" >
				<?php wp_nonce_field('lc_activate_product'); ?>
							
				<input required name="license_code" type="text" style="min-width: 100%;margin-bottom:10px;"  value="<?php if (isset($_POST['license_code'])) echo esc_attr($_POST['license_code']) ?>" placeholder="Paste your license code here..." > 
				
				<input name="is_staging" type="checkbox" value="Y" <?php if (lc_is_staging_site(get_bloginfo("url"))) echo "checked" ?> >
				</label>This is a staging / test site</label>

				<input class="button-primary" type="submit" style="min-width: 100%;margin-top:30px;" name="lc-activate-product" value="Activate Product">
			</form>
			<?php
		} else { 
			//CASE APIKEY PRESENT
			//STANDARD CASE: FEEDBACK
			echo "<div class='notice notice-success'><p>Activation status: <strong style='margin-left:12px;color:green'>ACTIVE</strong>  </p></div>";
			?>
			<form method="post" onsubmit="return confirm('Do you really want to remove the product activation from this site?');">
				<?php wp_nonce_field('lc_deactivate_product'); ?>
				<input class="button" type="submit"  name="lc-deactivate-product" value="Disconnect Site">
			</form>	
			<?php
		}	?>
		<?php
	endif; //END FORM

	?>
	</div>
	<?php 
	

}


 
