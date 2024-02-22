<?php
// EXIT IF ACCESSED DIRECTLY.
defined( 'ABSPATH' ) || exit;

add_action('admin_menu', 'lc_main_options_page');
function lc_main_options_page(){
	$lc_settings = get_option('lc_settings');
	
	// add top level menu page
	add_menu_page('LiveCanvas - Web Authoring Suite', 'LiveCanvas', 'manage_options', 'livecanvas', 'lc_options_page_func', plugins_url() . '/livecanvas/images/lc-micrologo.svg');
	
	add_submenu_page('livecanvas', // Parent slug
		'LiveCanvas Home', // Page title
		'Home', // Menu title
		'manage_options', // Capability
		'livecanvas', // Slug
		false // Function
	);
	
	// add child pages 
	
	add_submenu_page('livecanvas', // Parent slug
		'Your Custom HTML Blocks', // Page title
		'Blocks', // Menu title
		'manage_options', // Capability
		'edit.php?post_type=lc_block', // Slug
		false // Function
	);
	
	add_submenu_page('livecanvas', // Parent slug
		'Your Custom HTML Sections', // Page title
		'Sections', // Menu title
		'manage_options', // Capability
		'edit.php?post_type=lc_section', // Slug
		false // Function
	);
	
	//if (isset($lc_settings['header']) or isset($lc_settings['footerV2'])) //this is commented as it makes sense to always show template partials
	add_submenu_page('livecanvas', // Parent slug
		'Template Partials', // Page title
		'Template Partials', // Menu title
		'manage_options', // Capability
		'edit.php?post_type=lc_partial', // Slug
		false // Function
	);

	if (isset($lc_settings['enable-dynamic-templating'])) 
	 add_submenu_page('livecanvas', // Parent slug
		'Dynamic Templates', // Page title
		'Dynamic Templates', // Menu title
		'manage_options', // Capability
		'edit.php?post_type=lc_dynamic_template', // Slug
		false // Function
	);

	if (isset($lc_settings['gtblocks'])) 
	 add_submenu_page('livecanvas', // Parent slug
		'Gutenberg Blocks', // Page title
		'Gutenberg Blocks', // Menu title
		'manage_options', // Capability
		'edit.php?post_type=lc_gt_block', // Slug
		false // Function
	);

	if(is_main_site())
	 add_submenu_page('livecanvas', // Parent slug
		'License', // Page title
		'License', // Menu title
		'manage_options', // Capability
		'livecanvas_license', // Slug
		'lc_license_page_func'	// Function
	);
	
	
}

function lc_admin_menu_active(){
	global $parent_file, $post_type;
	//if ( $post_type == 'CPT' ) {
	$parent_file = 'post';
	//}
				
}
//add_action('admin_head', 'lc_admin_menu_active'); //commented to fix learndash issue




function lc_options_page_func(){
	if (!current_user_can('administrator')) return;
	//show current_settings
	//echo "<pre>";  var_dump(get_option('lc_settings'));  echo "</pre>";
	//delete current settings
	//delete_option('lc_settings');die("DELETED");
	
	
	//GET SETTINGS ARRAY FROM DB
	$lc_settings = get_option('lc_settings');
	?>
	<div class="wrap">
		<img id="lc-logo" src="<?php echo plugins_url("/livecanvas/images/lc-logo.svg") ?>" style="width:200px;height: auto;margin:20px 0 10px;">
		<h1>Welcome to LiveCanvas!</h1>
		<iframe width="560" height="315" src="https://www.youtube.com/embed/P-LsFfZ3o68?rel=0" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
		 <p> <br></p>
		<?php if (lc_get_apikey()): ?>	
		<a href="#" onclick='document.querySelector("#wp-admin-bar-lc-add-new-page a").click();' class="button large">Create new LiveCanvas Page Draft</a>
		<a style="margin-left:40px;display:inline-block;margin-top:5px;" target="_blank" href="https://docs.livecanvas.com/">Plugin Documentation</a>
		<br><br>
		<?php endif ?>	
		
	
	 
		
		<style>
			table#lc-settings-table {padding:10px 0 30px;}
			table#lc-settings-table th[scope=row] { text-align: left;padding-right:10px;}
			table#lc-settings-table tr {line-height: 40px;     white-space: nowrap;}
		</style>
		
		<form method="post">
			<?php wp_nonce_field('lc_settings_update'); ?>
		   
			<table id="lc-settings-table">
			  
				<tr>
					<td colspan=2> <h3>Templating Settings</h3> </td>
				</tr>

				<tr>
					<th scope="row"  >Handle Header  </th>
					<td>
						<label>
							<input name="header" type="checkbox" value="1" <?php if (isset($lc_settings['header'])) echo "checked"; ?> > Use LiveCanvas to design the header <a target="_blank" href="https://docs.livecanvas.com/header-builder/">Learn more...</a> <i style="color:red">(requires picostrap)</i>
							<?php if (isset($lc_settings['header'])): ?>		<a style="margin-left:40px;margin-top:6px" target="_blank" class="button" href="<?php  echo add_query_arg(array('lc_action_launch_editing' => '1'),
																																		get_permalink(    lc_get_partial_postid('is_header', "1")  ));  ?>">Launch Header Editor</a>		<?php endif ?>
							
						</label>
					 </td>
				</tr>
				<tr>
					<th scope="row"  >Handle Footer  </th>
					<td>
						<label>
							<input name="footerV2" type="checkbox" value="1" <?php if (isset($lc_settings['footerV2'])) echo "checked"; ?> > Use LiveCanvas to design the footer <a target="_blank" href="https://docs.livecanvas.com/handling-the-footer/">Learn more...</a> <i style="color:red">(requires picostrap)</i>
							<?php if (isset($lc_settings['footerV2'])): ?>		<a style="margin-left:40px;margin-top:6px" target="_blank" class="button" href="<?php  echo add_query_arg(array('lc_action_launch_editing' => '1'),
																																		get_permalink(    lc_get_partial_postid('is_footer', "1")  ));  ?>">Launch Footer Editor</a>		<?php endif ?>
							
						</label>
					 </td>
				</tr>
				<tr>
					<th scope="row">  Handle WordPress Templates   </th>
					<td>
						<label>
							<input name="enable-dynamic-templating" type="checkbox" value="1" <?php if (isset($lc_settings['enable-dynamic-templating'])) echo "checked"; ?> >
							  Allows building template pages with LiveCanvas. See the <b>Dynamic Templates</b> submenu on the left side. <a target="_blank" href="https://docs.livecanvas.com/dynamic-templating/">Learn more...</a>
						</label>
					 </td>
				</tr>

				<tr>
					<td colspan=2> <h3>Optional Extras</h3> </td>
				</tr>
				<tr>
					<th scope="row" >Add Animations</th>
					<td>
						 <label> 	<input name="aos" type="checkbox" value="1" <?php if (isset($lc_settings['aos'])) echo "checked"; ?> > Adds the <b><a href="https://michalsnik.github.io/aos/" target="_blank">Animate On Scroll</a></b> Library. <a target="_blank" href="https://docs.livecanvas.com/adding-animations-with-the-aos-library/">Learn more...</a></label> 
					</td>
				</tr>
				
				<tr>
					<th scope="row" > Animations CSS Priority Loading</th>
					<td>
						 <label> 	<input name="aos-priority" type="checkbox" value="1" <?php if (isset($lc_settings['aos-priority'])) echo "checked"; ?> > Check to load it in the Header. Default loading happens on the footer for performance optimization, but can result in some <a href="https://en.wikipedia.org/wiki/Flash_of_unstyled_content" target="_blank">FOUC</a>. </label> 
					</td>
				</tr>

				<tr>
					<th scope="row" > Gutenberg Blocks</th>
					<td>
						 <label> 	<input name="gtblocks" type="checkbox" value="1" <?php if (isset($lc_settings['gtblocks'])) echo "checked"; ?> > Add admin UX to craft custom blocks with Gutenberg (embeddable via Shortcodes) </label> 
					</td>
				</tr>
				
				
							
				<tr>
					<th scope="row"  >  Use on any Theme   </th>
					<td>
						<label>
							<input name="force-embedded-template-for-lc-pages" type="checkbox" value="1" <?php if (isset($lc_settings['force-embedded-template-for-lc-pages'])) echo "checked"; ?> >
							  Enforce Embedded Single Template for pages/posts where LC is enabled. <i style="color:red">(Still requires that Theme is using the  BS4 or BS5 CSS)</i>
						</label>
					 </td>
				</tr>

				<tr>
					<th scope="row"  > Use Bootstrap 5    </th>
					<td>
						<label>
							<input name="enable-bs-5" type="checkbox" value="1" <?php if (isset($lc_settings['enable-bs-5'])) echo "checked"; ?> > My Theme is using Bootstrap v5 (instead of v4) <i style="color:red">(Not necessary to check when using picostrap5)</i>
						</label>
					 </td>
				</tr>

				<tr>
					<th scope="row"  >  White Labeling    </th>
					<td>
						<label>
							<input name="whitelabel" type="checkbox" value="1" <?php if (isset($lc_settings['whitelabel'])) echo "checked"; ?> >  Whitelabel the editor</i>
						</label>
					 </td>
				</tr>

				<!-- 
				<tr class="lc-experimental-feature">
					<th scope="row"  > Legacy Footer </th>
					<td>
						  <label style="opacity:0.4">    <input name="footer" type="checkbox" value="1" <?php if (isset($lc_settings['footer'])) echo "checked"; ?> > [LEGACY - obsolete]	Use a #global-footer SECTION in homepage as a global site footer </label> 
					</td>
				</tr>	
				-->
 

				<tr>
					<th scope="row"> Enable LC on Post Types</th>
					<td>
						<label>    
								<input checked disabled type="checkbox" value="1">
								page &nbsp; &nbsp;  
						 </label> 
						<?php foreach(array_merge(array('post'), (get_post_types(  array('public'   => true,'_builtin' => false ), 'names', 'and' ))) as $post_type): ?>
						  <label>    
								<input name="<?php echo 'enable-on-post-type-'.$post_type ?>" type="checkbox" value="1" <?php if (isset($lc_settings['enable-on-post-type-'.$post_type])) echo "checked"; ?> >
								<?php echo $post_type ?> &nbsp; &nbsp;  
						 </label> 
						 <?php endforeach; ?>
						 
					</td>
				</tr>
				 


				<tr>
					<td colspan=2> <h3>Editor Settings</h3> </td>
				</tr>
				<tr>
					<th scope="row">Simplified Client UI </th>
					<td>
						  <label>    
								<input name="simplified-client-ui" type="checkbox" value="1" <?php if (isset($lc_settings['simplified-client-ui'])) echo "checked"; ?> >
								For non-administrator users, leaves only simple editing features for basic content editing
								<a href="https://docs.livecanvas.com/simplified-client-ui/" target="_new">Learn more</a>
						 </label> 
					</td>
				</tr>

				<tr>
					<th scope="row">Hide Built-in Readymade Sections </th>
					<td>
						  <label>    
								<input name="hide-readymade-sections" type="checkbox" value="1" <?php if (isset($lc_settings['hide-readymade-sections'])) echo "checked"; ?> >
								This makes sense if you want your users just to assemble your own custom HTML Sections 
						 </label> 
					</td>
				</tr>

				
				<tr>
					<th scope="row">Hide Built-in Readymade Blocks </th>
					<td>
						  <label>    
								<input name="hide-readymade-blocks" type="checkbox" value="1" <?php if (isset($lc_settings['hide-readymade-blocks'])) echo "checked"; ?> >
								This makes sense if you want your users just to assemble your own custom HTML blocks 
						 </label> 
					</td>
				</tr>

				<tr>
					<td colspan=2> <h3>Minor Tweaks</h3> </td>
				</tr>
				<tr class="NOT-lc-experimental-feature">
					<th scope="row"> Disable OB handling </th>
					<td>
						  <label>    
								<input name="disable-ob-handling" type="checkbox" value="1" <?php if (isset($lc_settings['disable-ob-handling'])) echo "checked"; ?> >
                           		Can help in peculiar PHP environments that crash the editor.  Breaks optimization plugins. Generally leave unchecked
						 </label> 
					</td>
				</tr>

				<tr>
					<th scope="row"> Disable the Compatibility Filters Plugin </th>
					<td>
						  <label>    
								<input name="disable-mu-plugin" type="checkbox" value="1" <?php if (isset($lc_settings['disable-mu-plugin'])) echo "checked"; ?> >
								Keep unchecked for better compatibility with performance optimization  plugins such as caching, EWWW, etc.
						 </label> 
					</td>
				</tr>

				<tr>
					<th scope="row"  > ACF Compatibility Extra    </th>
					<td>
						<label>
							<input name="allow_multiple_editors" type="checkbox" value="1" <?php if (isset($lc_settings['allow_multiple_editors'])) echo "checked"; ?> >  Allow Custom Wysiwyg Editors in the post editing screen (VERY rare use case, leave unchecked)</i>
						</label>
					 </td>
				</tr>

			</table>

			 
			<?php if (isset($lc_settings['license-code'])): ?>
				<input type="hidden" name="license-code" value="<?php echo esc_attr($lc_settings['license-code']) ?>">
			<?php endif ?>
			 

			<input class="button-primary" type="submit" name="lc-save-settings" value="Save Settings">
		</form>
	
	</div>
	
	<style>
		.lc-experimental-feature {color:red; display: none}
	</style>
	<script>
		///enable experimental features: CTRL ALT E
		jQuery("#lc-logo").dblclick(function(e) {
			 jQuery('.lc-experimental-feature').show(); 
		});
	</script>
	<?php
}



//OPTIONS SAVING / SUBMIT
add_action('plugins_loaded', function(){
	if (!current_user_can('administrator') OR !is_admin()) return;
	//process eventual submit
	if (isset($_POST['lc-save-settings'])):
		check_admin_referer('lc_settings_update');
		unset($_POST['lc-save-settings']);
		update_option('lc_settings', $_POST, true);
	endif;
	
});
 