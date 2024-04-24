<?php
defined('ABSPATH') || exit; // EXIT IF ACCESSED DIRECTLY.

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
		if(!theForm.parentElement.querySelector(".lc_form_feedback")) {
            console.log("add feedback div");
            theForm.insertAdjacentHTML("beforeend", "<div class=lc_form_feedback></div>");  
        }
        theForm.parentElement.querySelector(".lc_form_feedback").innerHTML="";
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
            if (response=="-1") {   
                alert("Invalid nonce value. Some kind of caching might be acting on it. Please try to reload the page and submit again the form, thank you."); 
                return false;
            }
			theForm.parentElement.querySelector(".lc_form_feedback").innerHTML=response;
			theForm.parentElement.querySelector(".lc_form_feedback").scrollIntoView({block: "end", inline: "nearest"});
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
