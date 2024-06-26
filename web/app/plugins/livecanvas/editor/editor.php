<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
?>
<html data-active-plugins="<?php echo lc_get_active_plugins_list() ?>">
	<head>
		<title><?php bloginfo("name") ?> <?php if(!lc_plugin_option_is_set("whitelabel")) echo "LiveCanvas Editor"; ?></title>
		<meta name="robots" content="noindex, nofollow">
		<meta charset="UTF-8">
		
		<link rel="shortcut icon" type="image/x-icon" href="<?php lc_print_editor_url() ?>../images/favicon.ico">

		<link rel="preconnect" href="https://cdn.livecanvas.com/" crossorigin>
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

		<script type='text/javascript' src="https://cdn.livecanvas.com/remote/lc-bundle-001-g87r37g84j2312hve6xx2-w.js"></script>
		  
		<script type='text/javascript' src='<?php lc_print_editor_url() ?>libs/ace/src-min-noconflict/ace.js'></script>
		<script type='text/javascript' src='<?php lc_print_editor_url() ?>libs/ace/src-min-noconflict/ext-language_tools.js'></script>
		<script type='text/javascript' src='<?php lc_print_editor_url() ?>libs/ace/src-min-noconflict/ext-emmet.js'></script>
		 
		<script type='text/javascript' src='<?php lc_print_editor_url() ?>libs/emmet-core/emmet.js'></script>


		<script type='text/javascript' src='<?php lc_print_editor_url() ?>libs/js-beautify/beautify-html.min.js'></script>
		<script type='text/javascript' src='<?php lc_print_editor_url() ?>libs/js-beautify/beautify-css.min.js'></script>

        <?php if (!empty(locate_template('lc-framework-config.js')) ){   ?>
                <script type='text/javascript' src='<?php echo get_stylesheet_directory_uri() ?>/lc-framework-config.js?v=<?php echo LC_SCRIPTS_VERSION ?>'></script>
                <?php } else {   ?>
                <script type='text/javascript' src='<?php lc_print_editor_url() ?>configs/bootstrap-<?php echo lc_get_bootstrap_version(); ?>.js?v=<?php echo LC_SCRIPTS_VERSION ?>'></script>
		    
        <?php } //end else ?>

        <?php if (!empty(locate_template('lc-editor-config.js')) ){   ?>
                <script type='text/javascript' src='<?php echo get_stylesheet_directory_uri() ?>/lc-editor-config.js?v=<?php echo LC_SCRIPTS_VERSION ?>'></script>
                <?php } else {   ?>
                <script type='text/javascript' src='<?php lc_print_editor_url() ?>configs/editor-config.js?v=<?php echo LC_SCRIPTS_VERSION ?>'></script>
		    
        <?php } //end else ?>

        <script type='text/javascript' src='<?php lc_print_editor_url() ?>functions.js?v=<?php echo LC_SCRIPTS_VERSION ?>'></script>
		<script type='text/javascript' src='<?php lc_print_editor_url() ?>editor.js?v=<?php echo LC_SCRIPTS_VERSION ?>'></script> 
		<script type='text/javascript' src='<?php lc_print_editor_url() ?>side-panel-edit-properties.js?v=<?php echo LC_SCRIPTS_VERSION ?>'></script>
		<script defer type='text/javascript' src='<?php lc_print_editor_url() ?>side-panel-advanced-helpers.js?v=<?php echo LC_SCRIPTS_VERSION ?>'></script> 

        <script defer type='text/javascript' src='<?php lc_print_editor_url() ?>tree-view.js?v=<?php echo LC_SCRIPTS_VERSION ?>'></script> 

		<?php do_action("lc_editor_header")  ?>

		<?php global $post; ?> 
		<script id="livecanvas-environment-variables">
			lc_editor_main_bootstrap_version=<?php echo lc_get_main_bs_version(); ?>; 
			lc_editor_plugin_version='<?php echo LC_SCRIPTS_VERSION ?>'; 
			lc_editor_fragment_type='<?php  if ( get_post_meta($post->ID,'is_header',TRUE)!='' ) echo "header"; 	if ( get_post_meta($post->ID,'is_footer',TRUE)!='' ) echo "footer"; ?>';
			lc_editor_root_url='<?php lc_print_editor_url() ?>';
			lc_editor_saving_url='<?php echo admin_url( 'admin-ajax.php' ) ?>';
			lc_editor_media_upload_url='<?php echo admin_url( 'media.php?page=lc-media-selector' ) ?>';
			lc_editor_current_post_id=<?php  echo esc_attr($post->ID) ?>;
			lc_editor_current_post_page_title_tag="<?php echo esc_attr( apply_filters( 'wp_title', get_the_title($post->ID), ' | ', 'right' )); ?>";
			lc_editor_url_before_editor="<?php 
                if (isset($_GET['from_url'])) echo urldecode($_GET['from_url'])
                /*
				if(isset($_GET['demo_id'])){
					echo get_permalink($_GET['demo_id']);
				} else {
					echo (   (isset($_GET['from_page_edit'])) ? admin_url( 'post.php' )."?action=edit&post=" . $post->ID : get_permalink($post->ID)   ); 
				}
                */
			?>";
			lc_editor_url_to_load="<?php $current_url = "//$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; $current_url=remove_query_arg( array('lc_action_launch_editing'  ), $current_url );	$current_url=add_query_arg(  'lc_page_editing_mode','1', $current_url );	echo $current_url;	?>" + "&lc_random=" + Math.floor(Math.random() * 1000);
			lc_editor_apikey="<?php echo lc_get_apikey() ?>";
			lc_editor_simplified_client_ui=<?php if (    !current_user_can("administrator") &&    lc_plugin_option_is_set("simplified-client-ui")) echo "true"; else echo "false"; ?>;
			lc_editor_hide_readymade_sections=<?php if (   /*  !current_user_can("administrator") &&  */  lc_plugin_option_is_set("hide-readymade-sections")) echo "true"; else echo "false"; ?>;
			lc_editor_hide_readymade_blocks=<?php if (   /*  !current_user_can("administrator") &&  */  lc_plugin_option_is_set("hide-readymade-blocks")) echo "true"; else echo "false"; ?>;
			lc_editor_post_type="<?php echo  esc_attr(get_post_type($post->ID)) ?>";
			lc_editor_experimental_mode=<?php if (function_exists('lc_enable_experimental_mode') ) echo "true"; else echo "false"; ?>;
            lc_editor_rest_api_url='<?php echo get_rest_url() ?>';
            lc_editor_rest_api_nonce='<?php echo wp_create_nonce('wp_rest') ?>';
		</script>

		<link rel="stylesheet" href="<?php lc_print_editor_url() ?>editor.css?v=<?php echo LC_SCRIPTS_VERSION ?>">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
		
		<?php if(lc_plugin_option_is_set("whitelabel")): ?>
			<style>
				.product-logo {visibility:hidden}
				#loader {     background: linear-gradient(-90deg, #b7b7b7, #ffffff);}

			</style>
		<?php endif; ?>
	</head>
	<body>
		
		<div id="maintoolbar">
			<?php //if (isset($_GET['lc_partial'])) echo '<div id="lc-partial-name">'.esc_attr(ucfirst($_GET['lc_partial'])).' Editor</div>'; ?>
			<?php include('toolbar-bs'.lc_get_main_bs_version().'.html'); ?>
		</div>
		 
		<div id="sidepanel" hidden><?php include('side-panel-bs'.lc_get_main_bs_version().'.html'); ?></div>
		<?php include('side-panel-templates.html'); ?> 

		<div id="loader">
			<div class="product-logo">
				<!-- <span>Live</span><span>Canvas</span> -->
				<svg style="width:30%;margin-bottom:60px;"  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 261 41"  >
					<path fill="#26c6da" d="M4.12 36Q4.12 38.4 6.76 38.4Q9.4 38.4 13.56 35.6Q17.72 32.8 21.64 28Q22.6 26.72 23.16 26.72Q23.72 26.72 23.72 27.2Q23.72 27.68 20.44 31.36Q12.44 40.16 7.08 40.16Q4.44 40.16 2.6 38.96Q0.76 37.68 0.76 35.12Q0.76 32.48 2.12 29.04Q4.44 23.6 9.56 15.36Q18.92 0.16 22.84 0.16Q23.64 0.16 24.44 0.8Q25.32 1.36 25.4 2.08Q23.56 4.56 18.28 11.6Q13.08 18.64 11.08 21.76Q9.08 24.8 8.04 26.32Q7.08 27.84 6.6 28.72Q6.12 29.52 5.48 30.88Q4.12 33.6 4.12 36ZM37.8 7.36Q39 7.36 40.04 8.16Q41.16 8.88 41.16 9.92Q41.16 10.96 40.12 11.76Q39.16 12.48 37.96 12.48Q36.76 12.48 35.72 11.6Q34.76 10.72 34.76 9.6Q34.76 7.36 37.8 7.36ZM23.32 40.88Q19.24 40.88 19.24 35.6Q19.24 31.28 23.96 23.68Q28.68 16 31.56 15.92Q32.36 15.92 33.32 16.56Q34.36 17.2 34.36 17.84Q33.96 18.4 33.4 19.12Q32.84 19.76 32.2 20.56Q31.56 21.36 30.44 22.88Q29.32 24.32 28.28 25.68Q22.28 33.44 22.28 36.4Q22.28 39.28 24.28 39.28Q25.96 39.28 28.84 37.44Q35.56 33.28 39.48 28.32Q40.92 26.48 41.4 26.48Q41.96 26.48 41.96 27.12Q41.96 27.76 40.12 29.68Q38.28 31.6 37.16 32.72Q36.12 33.84 35.72 34.24Q35.32 34.56 34.2 35.6Q33.16 36.56 32.6 36.96Q32.04 37.36 31 38.16Q29.96 38.96 29.16 39.36Q28.36 39.68 27.32 40.16Q25.64 40.88 23.32 40.88ZM60.2 8.56Q62.6 8.56 64.28 10.16Q65.96 11.76 65.96 14.64Q65.96 20.96 55.48 30.48Q46.6 38.48 41.16 40.24Q40.28 40.24 38.84 39.28Q37.4 38.32 37.4 37.52Q37.4 35.76 41.24 25.6Q45.08 15.44 46.52 14.24Q46.84 13.92 47.56 13.92Q48.28 13.92 49.08 14.72Q49.88 15.44 49.96 16.48Q49.96 16.56 45.96 25.76Q42.04 34.96 42.28 36.72Q45.72 35.68 54.52 28.32Q63.4 20.96 63.4 15.12Q63.4 11.6 59.88 10Q59.32 9.76 59.32 9.28Q59.32 8.56 60.2 8.56ZM66.68 41.04Q65.8 41.04 65.08 40.8Q59.96 39.36 59.96 35.04Q59.96 29.44 67 22.08Q74.04 14.72 79.64 14.72Q81.48 14.72 82.6 15.76Q83.8 16.72 83.8 18.16Q83.8 22.64 77 26.72Q70.28 30.8 66.04 30.8Q65.4 30.8 64.92 30.64Q63.72 32.8 63.72 35.2Q63.72 39.36 67.64 39.36Q71.64 39.36 77.96 34Q82.84 30.08 86.28 25.68Q87 24.72 87.48 24.72Q88.04 24.72 88.04 25.2Q88.04 25.68 87.4 26.56Q83.08 32.24 77 36.64Q70.92 41.04 66.68 41.04ZM81.8 18Q81.8 17.04 79.72 17.04Q77.72 17.04 73.96 20.16Q68.92 24.32 66.12 28.88Q66.6 29.04 67.16 29.04Q70.2 29.04 75.24 25.84Q80.36 22.56 81.64 18.72Q81.8 18.32 81.8 18Z" />
					<path fill="#e83e8c" d="M89.24 41.04Q88.36 41.04 87.64 40.8Q82.52 39.36 82.52 35.04Q82.52 29.44 89.56 22.08Q96.6 14.72 102.2 14.72Q104.04 14.72 105.16 15.76Q106.36 16.72 106.36 18.48Q106.36 20.16 105.32 22.08Q104.28 23.92 102.2 23.92Q100.12 23.92 100.12 22.64Q100.12 21.76 101.16 20Q102.28 18.16 102.28 18.08Q102.28 17.92 102.04 17.92Q99.8 18.16 95.88 21.12Q92.04 24.08 90.6 26.08L87.48 30.64Q86.68 31.92 86.28 33.68Q85.96 35.44 85.96 36.32Q85.96 37.2 86.92 38.32Q87.96 39.44 89.8 39.44Q93.8 39.44 99.72 34.8Q105.64 30.08 108.84 25.84Q109.24 25.36 109.8 25.36Q110.36 25.36 110.36 25.68Q110.36 26 109.96 26.48Q105.56 32.32 99.4 36.72Q93.32 41.04 89.24 41.04ZM132.36 16.08L133.48 15.84Q134.36 15.84 135.16 16.56Q135.96 17.28 135.96 17.68Q135.96 18.08 131 25.44Q126.04 32.72 126.04 35.6Q126.04 35.84 126.12 36.08Q126.52 37.92 127.88 37.92Q132.04 37.92 143.08 24.16Q143.32 23.84 143.64 23.84Q143.96 23.84 143.96 24.48Q143.96 25.04 143.24 26Q133 39.68 127.64 39.68Q126.92 39.68 126.28 39.44Q122.76 38.08 122.76 34.16Q122.76 32.8 123.08 31.44L118.12 36.24Q113.4 40.4 111.24 40.4Q109.16 40.4 107.24 39.04Q105.08 37.44 105.08 34.8Q105.08 28.96 113.24 20.88Q121.4 12.72 128.2 12.72Q130.84 12.72 131.72 14.56Q131.96 15.2 132.12 15.68Q132.28 16.08 132.36 16.08ZM108.44 35.76Q108.44 38.64 110.44 38.64Q115.08 38.64 123.08 28.48L131.56 16.8Q130.92 15.68 129.48 15.68Q124.6 15.68 116.52 23.28Q108.44 30.8 108.44 35.76ZM151.64 14.72Q153.8 14.72 153.8 16.4Q153.8 16.88 153.56 17.36Q146.36 27.36 144.92 32.48Q163.56 13.84 167.56 13.84Q167.96 13.76 168.28 13.76Q170.52 13.76 170.52 15.76Q170.52 16.16 170.36 16.56Q170.2 16.96 167.16 20.88Q163.64 25.04 161.16 28.88Q158.76 32.72 158.76 35.28Q158.76 37.84 159.88 37.84Q163.4 37.84 172.12 28.16Q172.28 28 172.68 27.44Q173.88 26 174.12 26Q174.36 26 174.36 26.8Q174.36 27.6 173.24 28.88Q163.32 40.16 159.16 40.16Q155.24 40.16 155.24 35.84Q155.24 31.52 158.44 25.92L163.56 17.6Q158.12 21.6 153.88 26.4Q149.64 31.12 146.12 35.6Q142.68 40.08 142.12 40.08Q140.92 40.08 140.2 39.12Q139.56 38.08 139.56 36.64Q139.56 35.12 140.6 32.64Q141.64 30.16 143.24 26.64L138.44 31.68Q136.68 33.44 136.2 33.44Q135.8 33.44 135.8 32.96Q135.8 32.4 136.44 31.76Q139.4 28.8 141.56 26.08Q143.64 23.36 150.12 15.68Q150.84 14.72 151.64 14.72ZM192.68 8.56Q195.08 8.56 196.76 10.16Q198.44 11.76 198.44 14.64Q198.44 20.96 187.96 30.48Q179.08 38.48 173.64 40.24Q172.76 40.24 171.32 39.28Q169.88 38.32 169.88 37.52Q169.88 35.76 173.72 25.6Q177.56 15.44 179 14.24Q179.32 13.92 180.04 13.92Q180.76 13.92 181.56 14.72Q182.36 15.44 182.44 16.48Q182.44 16.56 178.44 25.76Q174.52 34.96 174.76 36.72Q178.2 35.68 187 28.32Q195.88 20.96 195.88 15.12Q195.88 11.6 192.36 10Q191.8 9.76 191.8 9.28Q191.8 8.56 192.68 8.56ZM221.64 16.08L222.76 15.84Q223.64 15.84 224.44 16.56Q225.24 17.28 225.24 17.68Q225.24 18.08 220.28 25.44Q215.32 32.72 215.32 35.6Q215.32 35.84 215.4 36.08Q215.8 37.92 217.16 37.92Q221.32 37.92 232.36 24.16Q232.6 23.84 232.92 23.84Q233.24 23.84 233.24 24.48Q233.24 25.04 232.52 26Q222.28 39.68 216.92 39.68Q216.2 39.68 215.56 39.44Q212.04 38.08 212.04 34.16Q212.04 32.8 212.36 31.44L207.4 36.24Q202.68 40.4 200.52 40.4Q198.44 40.4 196.52 39.04Q194.36 37.44 194.36 34.8Q194.36 28.96 202.52 20.88Q210.68 12.72 217.48 12.72Q220.12 12.72 221 14.56Q221.24 15.2 221.4 15.68Q221.56 16.08 221.64 16.08ZM197.72 35.76Q197.72 38.64 199.72 38.64Q204.36 38.64 212.36 28.48L220.84 16.8Q220.2 15.68 218.76 15.68Q213.88 15.68 205.8 23.28Q197.72 30.8 197.72 35.76ZM245.32 37.12L242.76 38.8Q240.44 40.16 237 40.16Q233.64 40.16 231.8 37.92Q230.04 35.68 228.76 30.8Q227.32 32.24 226.76 32.24Q226.36 32.24 226.36 31.68Q226.36 31.12 226.76 30.72Q229.4 27.44 235.72 21.28L245.8 11.84Q250.12 7.76 250.92 7.76Q253.48 7.76 253.48 9.68Q253.48 10.64 252.68 11.6Q251.96 12.48 251.56 13.04Q251.24 13.52 250.84 14Q249.4 16.48 247.88 24.24Q246.44 32 245.64 35.04Q252.28 34.96 259.32 26.4Q260.12 25.6 260.52 25.6Q260.92 25.6 260.92 26.16Q260.92 26.64 260.2 27.52Q255.56 32.96 251.56 35.12Q249.16 36.32 245.32 37.12ZM231.96 29.92Q231.96 32.64 233.72 34.4Q235.56 36.08 237.8 36.08Q240.04 36.08 241.24 35.2Q242.84 34.32 244.36 27.84Q245.96 21.36 246.28 16.4L246.6 13.68Q242.2 16.96 237.16 22.16Q232.2 27.36 232.04 29.04Q231.96 29.52 231.96 29.92Z" />
				</svg>
			</div>
			<div class="donut"></div>
		</div>

		<div id="saving-loader" hidden>
			<!-- <div class="saving-message"><span>Saving...</span></div> -->
			<div class="donut"></div>
		</div>
	 
		<div id="previewiframe-wrap">
			<iframe id="previewiframe"></iframe>
		</div>
				
		<template id="add-to-preview-iframe-content" hidden>
			<section id="lc-interface"><?php include('contextual-menus-interface.html'); ?></section>
		</template>
		
		<section id="lc-html-editor-window">
			<div class="lc-editor-menubar">
			  <div class="code-tabber">
					<a id="html-tab" class="active" href="#"> HTML</a>
					<a id="css-tab" href="#">Global CSS</a>
			  </div>
			  <div class="after-tabber-extras"> 
					<a href="#" class="lc-editor-goto-parent-element only-for-html"> <span class="fa fa-level-up"></span> &nbsp;  Go to Parent</a>
			  </div>
			  <div class="lc-editor-menubar-draghandle"></div>
			  <div class="lc-editor-menubar-tools">
					<span>
						Transparency
						<input type="checkbox" id="lc-editor-transparency" >
						 
						&nbsp;&nbsp;&nbsp;
					</span>
					<span>Tips & How tos: 
						
						<select id="lc-editor-tips">
							<option value="" selected>Browse...</option>
							<optgroup label="Editor">
								<option value="https://docs.livecanvas.com/keyboard-tricks/">Keyboard shortcuts</option> 
							</optgroup>
							<optgroup label="HTML">
								<option value="https://docs.livecanvas.com/the-livecanvas-html-structure/">The LiveCanvas HTML structure</option>
								<option value="https://docs.livecanvas.com/creating-editable-regions/">Creating editable regions</option>
								<option value="https://docs.livecanvas.com/shortcodes/">Built-in Shortcodes</option>
								<option value="https://docs.livecanvas.com/dynamic-templating/">Dynamic Templating Shortcodes</option>
								<option value="https://docs.livecanvas.com/woocommerce-shortcodes/">WooCommerce Dynamic Templating Shortcodes</option>
							</optgroup>

						</select>
						&nbsp;&nbsp;&nbsp;
					</span>
					<span>
						Theme: 
						<select id="lc-editor-theme"><optgroup label="Bright"><option value="chrome">Chrome</option><option value="clouds">Clouds</option><option value="crimson_editor">Crimson Editor</option><option value="dawn">Dawn</option><option value="dreamweaver">Dreamweaver</option><option value="eclipse">Eclipse</option><option value="github">GitHub</option><option value="iplastic">IPlastic</option><option value="solarized_light">Solarized Light</option><option value="textmate">TextMate</option><option value="tomorrow">Tomorrow</option><option value="xcode">XCode</option><option value="kuroir">Kuroir</option><option value="katzenmilch">KatzenMilch</option><option value="sqlserver">SQL Server</option></optgroup><optgroup label="Dark"><option value="ambiance">Ambiance</option><option value="chaos">Chaos</option><option value="clouds_midnight">Clouds Midnight</option><option value="dracula">Dracula</option><option value="cobalt">Cobalt</option><option value="gruvbox">Gruvbox</option><option value="gob">Green on Black</option><option value="idle_fingers">idle Fingers</option><option value="kr_theme">krTheme</option><option value="merbivore">Merbivore</option><option value="merbivore_soft">Merbivore Soft</option><option value="mono_industrial">Mono Industrial</option><option value="monokai">Monokai</option><option value="pastel_on_dark">Pastel on dark</option><option value="solarized_dark">Solarized Dark</option><option value="terminal">Terminal</option><option value="tomorrow_night">Tomorrow Night</option><option value="tomorrow_night_blue">Tomorrow Night Blue</option><option value="tomorrow_night_bright">Tomorrow Night Bright</option><option value="tomorrow_night_eighties">Tomorrow Night 80s</option><option value="twilight">Twilight</option><option value="vibrant_ink">Vibrant Ink</option></optgroup></select>
						&nbsp;&nbsp;&nbsp;
					</span>
					<span>Size: <input id="lc-editor-fontsize" type="number" value="13" min="9" max="24"> px &nbsp;&nbsp;&nbsp; </span>
					<a href="#" class="lc-editor-side">Side <span class="fa fa-arrow-circle-left"></a>
					<a href="#" class="lc-editor-maximize">Maximize <span class="fa fa-arrows-alt"></a>
					<a href="#" class="lc-editor-close">Close <span class="fa fa-close"></span></a>
			  </div>
			</div>
			<div id="lc-html-editor"></div>
			<div id="lc-css-editor"></div>
		</section>
		 
		<form id="nonce-only">
			<?php //wp_nonce_field('lc_main_save_nonce','lc_main_save_nonce_field'); // #001 ?>
		</form>
		
		
		<section id="lc-modal-link" class="lc-modal">
			<?php include ('modal-link.html'); ?>
		</section>

		<section>
			<?php include ('icons/editor-icons.html'); ?>
		</section>

        <section>
            <?php include ('tree-view.html'); ?>
        </section>

		<?php do_action("lc_editor_before_body_closing")  ?>

		<!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@6.6.96/css/materialdesignicons.min.css"> -->

		<link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;700&display=swap" rel="stylesheet">

		<script defer type='text/javascript' src='https://updater.livecanvas.com/lc-update-notification.js?v=<?php echo LC_SCRIPTS_VERSION ?>'></script>

		
	</body>

</html>