<?php
// EXIT IF ACCESSED DIRECTLY.
defined( 'ABSPATH' ) || exit;


add_action( 'admin_menu', 'lc_register_media_selector_settings_page' );
function lc_register_media_selector_settings_page() {
	 add_submenu_page( 'media.php', 'LC Media Selector', 'LC Media Selector', 'upload_files', 'lc-media-selector', 'lc_media_selector_settings_page_callback' );
}
function lc_media_selector_settings_page_callback() {
 
	wp_enqueue_media();
	?>
	<style>
	body #wpwrap {opacity: 0}
	body .media-modal {	position: fixed;top: 0;	left: 0;	right: 0;	bottom: 0;	z-index: 160000; }
	.setting[data-setting="title"], .setting[data-setting="caption"], .setting[data-setting="description"]  {display: none !important}
	</style>
	<?php 
	lc_media_selector_print_scripts();
}

add_action( 'admin_head', 'lc_remove_media_selector_settings_page' );
		   
		   
 function lc_remove_media_selector_settings_page() {
    remove_submenu_page( 'options-general.php', 'lc-media-selector' );
}


//add_action( 'admin_footer', 'lc_media_selector_print_scripts' );
function lc_media_selector_print_scripts() {
	 
	?><script type='text/javascript'>
		jQuery( document ).ready( function( $ ) {
			// Uploading files
			var file_frame;

			// Create the media frame.
			file_frame = wp.media.frames.file_frame = wp.media({
				title: 'Select image',
				button: {
					text: 'Choose this image',
				},
				library: {
					type: [ 'image' ]
				},
				multiple: false	// Set to true to allow multiple files to be selected
			});
			
			// When an image is selected, run a callback.
			file_frame.on( 'select', function() {
				// We set multiple to false so only get one image from the uploader
				attachment = file_frame.state().get('selection').first().toJSON();
				//console.log("alt:"+attachment.alt);
				console.log(attachment);
				
				//reset all for clarity
				window.parent.$('.wpadmin-image-format-chooser a[format]').attr("url","");
				window.parent.$('.wpadmin-image-format-chooser .preview').css("background-image","");
				
				//console.log(attachment);
				if(attachment.id)  window.parent.$('.wpadmin-image-format-chooser a').attr("attachment-id",attachment.id);

				// Set preview image and image urls in interface links
				if(attachment.sizes){
					if(attachment.sizes.medium) window.parent.$('.wpadmin-image-format-chooser .preview').css("background-image","url("+attachment.sizes.medium.url+")");
						else window.parent.$('.wpadmin-image-format-chooser .preview').css("background-image","url("+attachment.sizes.full.url+")");
					//if(attachment.sizes.thumbnail) window.parent.$('.wpadmin-image-format-chooser a[format=thumbnail]').attr("url",attachment.sizes.thumbnail.url);
					//if(attachment.sizes.medium) window.parent.$('.wpadmin-image-format-chooser a[format=medium]').attr("url",attachment.sizes.medium.url);
					//if(attachment.sizes.large) window.parent.$('.wpadmin-image-format-chooser a[format=large]').attr("url",attachment.sizes.large.url);
					//if(attachment.sizes.full) window.parent.$('.wpadmin-image-format-chooser a[format=full]').attr("url",attachment.sizes.full.url);
					//if(attachment.alt) window.parent.$('.wpadmin-image-format-chooser').attr("alt",attachment.alt);
				} else {
					if(attachment.url){
						//window.parent.$('.wpadmin-image-format-chooser a[format]').attr("url",attachment.url);
						window.parent.$('.wpadmin-image-format-chooser .preview').css("background-image","url("+attachment.url+")");
						}
				}
				window.parent.$('.wpadmin-image-format-chooser').slideDown();
				//$( '#image-preview' ).attr( 'src', attachment.url ).css( 'width', 'auto' );
				//$( '#image_attachment_id' ).val( attachment.id );
				 
				
				
			});
			
			file_frame.on( 'close', function() { 
				window.parent.$('.lc-wpadmin-imagesearch-wrap iframe').hide();
				//HIDE PRELOADER
				window.parent.$("#sidepanel .wpadmin-loading").hide();
				//if(window.parent.$('.wpadmin-image-format-chooser').)
			});
			
			// Finally, open the modal
			file_frame.open();
 
		});
	</script><?php
}