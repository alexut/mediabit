/////////////////////////////////////////////////        			
// DOCUMENT READY
/////////////////////////////////////////////// 
$(document).ready(function($) {

	
	/////////////////////////////INITIALIZE / SETUP THE APP //////////////////////////////////////////////////////////////////

	//SET NETWORK TIMEOUT
	$.ajaxSetup({
		timeout: 45000
	});

	//DETERMINE SCROLLBAR WIDTH so we can use this value later
	determineScrollBarWidth();

	//LOAD PREFERENCES object editorPrefsObj from LOCALSTORAGE
	if (localStorage.getItem("lc_editor_prefs_json") === null) editorPrefsObj = {};
		else editorPrefsObj = JSON.parse(localStorage.getItem("lc_editor_prefs_json"));
		
	//CHECK BROWSER and display a message if user lives in the past  /////////////////////////
	if (!usingChromeBrowser() && !('already_recommended_browser' in editorPrefsObj)) {setEditorPreference("already_recommended_browser", 1); swal("Please use the Google Chrome browser to run LiveCanvas for best results. There is an ever stronger reason to use Chrome as a web developer's main tool: you will see things as most web users do today.");}
	
	//LOAD THE PAGE TO EDIT
	loadURLintoEditor(lc_editor_url_to_load);

	//SIDEBAR BUILD: TWEAK BLOCKS depending on editing case
	if(lc_editor_post_type == 'lc_dynamic_template') {
		$("*[hide-if=lc_dynamic_template]").attr('hidden','1')
		$("*[unhide-if=lc_dynamic_template]").removeAttr ('hidden');
		
	} else {
		$("*[hide-if-not=lc_dynamic_template]").attr('hidden', '1');
		$("*[unhide-if-not=lc_dynamic_template]").removeAttr('hidden');
	}
	
	//SIDEBAR BUILD: LOAD READYMADES 
	fetch(lc_editor_root_url + "readymades/sections-bs" + lc_editor_main_bootstrap_version + ".html")
		.then(function(response) {
			return response.text();
		}).then(function(page_html) {
			$("#readymade-sections").html(page_html);
			
			//normal standard case
			if (lc_editor_fragment_type == '') {
				//kill header and footer
				$("#readymade-sections *[section-type='headers']").hide();
				$("#readymade-sections *[section-type='footers']").hide();
			}

			if(lc_editor_fragment_type=='header') {
				//kill all but headers 
				$("#readymade-sections *[section-type]:not(*[section-type='headers'])").hide();
				//open it
				$("#readymade-sections *[section-type='headers']").click();
			}

			if(lc_editor_fragment_type=='footer') {
				//kill all but footers 
				$("#readymade-sections *[section-type]:not(*[section-type='footers'])").hide();
				//open it
				$("#readymade-sections *[section-type='footers']").click();
			}
			
			//take care of lc_editor_hide_readymade_sections setting
			if (lc_editor_hide_readymade_sections){
				$("#readymade-sections > h4:not(*[data-load])").hide(); //hide all built in readymade sections except custom ones
				$("#readymade-sections > *[data-load]").click(); //force loading of custom html readymade sections
				$("#readymade-sections .show-all-sections").hide();
				setTimeout(() => {
					$("#readymade-sections .open-cpt-archive").hide();
				}, "4000")
			}

			//take care of lc_editor_hide_readymade_blocks setting
			if (lc_editor_hide_readymade_blocks) {
				$("#basic-blocks > h4:not(*[data-load])").hide(); //hide all built in readymade blocks except custom ones
				$("#basic-blocks > *[data-load]").click(); //force loading of custom html readymade blocks
				$("#basic-blocks .show-all-sections").hide();
				setTimeout(() => {
					$("#basic-blocks .open-cpt-archive").hide();
				}, "4000")
			}


		}).catch(function(err) {
			swal("Error " + err + " fetching Readymades");
		});

	//INTERFACE BUILDING: LOAD ICONS
	setTimeout(function() {
		$("#lc-fontawesome-icons").load("?lc_action=load_fa4_icons", function() {
			$("#lc-svg-icons").load("?lc_action=load_bs_icons", function() {});
			});
		
	}, 4000);

	//INTERFACE BUILDING ADD COMMON FIELDS   TO EACH FORM
	$('#sidepanel section form.add-common-form-elements-for-properties-panels').each(function(index, el) {
		$(el).append($("#sidebar-section-form-common-elements-for-properties-panels").html());
	});
	$('#sidepanel section form.add-common-form-elements ').each(function(index, el) {
		$(el).append($("#sidebar-section-form-common-elements").html());
	});

	//INTERFACE BUILDING: copy divs and SELECTs:  
	$(this).find("*[get_content_from]").each(function(index, element) {
		var source_selector = $(element).attr('get_content_from');
		$(element).html($(source_selector).html());
	}); //end each


	//INTERFACE BUILDING: if lc_editor_simplified_client_ui apply classes to govern
	if (lc_editor_simplified_client_ui){
		$('body').addClass('simplified_client_ui');
	}

	/////////////////////////// INIT THE IN-PAGE HTML CODE EDITOR ///////////////////////////
	lc_html_editor = ace.edit("lc-html-editor");
	var Emmet = ace.require("ace/ext/emmet"); // important to trigger script execution
	lc_html_editor.setOptions({
		enableBasicAutocompletion: true, // the editor completes the statement when you hit Ctrl + Space
		enableLiveAutocompletion: true, // the editor completes the statement while you are typing
		showPrintMargin: false, // hides the vertical limiting strip
		highlightActiveLine: false,
		mode: "ace/mode/html",
		wrap: true,
		useSoftTabs: false,
		tabSize: 4,
		enableEmmet: true
	});
	
	///SET EDITOR THEME
	if ('editor_theme' in editorPrefsObj) the_editor_theme = editorPrefsObj.editor_theme;
		else the_editor_theme = "cobalt";
	lc_html_editor.setTheme("ace/theme/" + the_editor_theme);
	$("select#lc-editor-theme option[value=" + the_editor_theme + "]").prop('selected', true);

	//SET EDITOR FONTSIZE
	if ('editor_fontsize' in editorPrefsObj) {
		$("#lc-editor-fontsize").val(editorPrefsObj.editor_fontsize);
		document.getElementById('lc-html-editor').style.fontSize = editorPrefsObj.editor_fontsize + 'px';
		document.getElementById('lc-css-editor').style.fontSize = editorPrefsObj.editor_fontsize + 'px';
	}
   
	/////////////////////////// INIT THE IN-PAGE CSS CODE EDITOR ///////////////////////////
	lc_css_editor = ace.edit("lc-css-editor");
	lc_css_editor.setOptions({
		enableBasicAutocompletion: true, // the editor completes the statement when you hit Ctrl + Space
		enableLiveAutocompletion: true, // the editor completes the statement while you are typing
		showPrintMargin: false, // hides the vertical limiting strip
		highlightActiveLine: false,
		mode: "ace/mode/css",
		wrap: true,
		useSoftTabs: false,
		tabSize: 4,
	});

	///SET CSS EDITOR THEME
	if ('css_editor_theme' in editorPrefsObj) the_css_editor_theme = editorPrefsObj.css_editor_theme;
		else the_css_editor_theme = "chrome";
	lc_css_editor.setTheme("ace/theme/" + the_css_editor_theme);  
  
	// ON UNLOAD, HELP THE USER NOT TO LOSE WORK
	window.addEventListener('beforeunload', (event) => {
		if (original_document_html != getPageHTML()) event.returnValue = `Are you sure you want to leave?`;
	});


	/////////////////////////// USER ACTIONs TRIGGER REACTIONs //////////////////////////////////////////////////////////////////

	//INIT HTML EDITOR REACTION WHEN EDITED
	lc_html_editor.getSession().on('change', function() {
		if ($("#lc-html-editor-window").attr("prevent_live_update") == "1") return;
		myConsoleLog("React to html editor change");
		var selector = $("#lc-html-editor-window").attr("selector");
		var new_html = lc_html_editor.getValue();
		doc.querySelector(selector).innerHTML = new_html;
		//add throttling eventually?
		//if (new_html.includes("<script"))   
		if (new_html.includes("lc-needs-hard-refresh")) {
			updatePreview();
			setTimeout(function() {
				previewFrame.contents().find("html, body").animate({
					scrollTop: previewFrame.contents().find(selector).offset().top
				}, 10, 'linear');
			}, 100);

		} else { updatePreviewSectorial(selector); }
	}); //end onChange

	//INIT CSS EDITOR REACTION WHEN EDITED
	lc_css_editor.getSession().on('change', function() {
		if ($("#lc-css-editor").attr("prevent_live_update") == "1") return;
		myConsoleLog("React to css editor change");
		var new_css = lc_css_editor.getValue();
		doc.querySelector("#wp-custom-css").innerHTML = new_css;
		previewFrame.contents().find("#wp-custom-css").html(new_css);
	}); //end onChange
	
	//MAKE CODE EDITORS WINDOW  RESIZABLE
	const ele = document.querySelector('#lc-html-editor-window');
	const eleTb = document.querySelector('.lc-editor-menubar-draghandle');
	const lcEditorCookie = 'lc-editor-height';
	const lcEditorHeight = editorPrefsObj[lcEditorCookie];
	let y = 0;
	let h = 0;
	ele.style.maxHeight = '100vh';

	//if we already resized the window set the height
	if (lcEditorHeight) {
		ele.style.height = lcEditorHeight;
	}

	// mousedown event
	const mouseDownHandler = function(e) {
		// current mouse position
		y = e.clientY;
		// element dimension
		const styles = window.getComputedStyle(ele);
		h = parseInt(styles.height, 10);

		//force min and max height
		ele.style.minHeight = '15vh';
		ele.style.maxHeight = '100vh';
		
		// listeners, MUST be attached to document
		document.addEventListener('mousemove', mouseMoveHandler);
		document.addEventListener('mouseup', mouseUpHandler);
	};

	// set editor height at mouse move
	const mouseMoveHandler = function(e) {
		if(ele.classList.contains('lc-editor-window-maximized')) {
			ele.classList.remove('lc-editor-window-maximized');
		}
		const dimensions = window.innerHeight - e.clientY + 10;
		//size of element
		ele.style.height =  `${dimensions}px`;
		//save preference to cookie
		setEditorPreference(lcEditorCookie, dimensions);
		//this prevents loose of drag
		e.preventDefault();
		e.stopPropagation();
	};

	//remove handler at drag stop
	const mouseUpHandler = function() {
		lc_html_editor.resize();
		lc_css_editor.resize();
		document.removeEventListener('mousemove', mouseMoveHandler);
		document.removeEventListener('mouseup', mouseUpHandler);
	};

	//attach handler to editor
	eleTb.addEventListener('mousedown', mouseDownHandler);
	//end editor drag
	
	//USER CLICKS CODE EDITOR TABBER: INIT CSS PANEL
	$("body").on("click", "#css-tab", function(e) {
		e.preventDefault();
		$(".lc-editor-menubar .only-for-html").hide();
		$(".code-tabber a.active").removeClass("active");
		$(this).addClass("active");
		$("#lc-html-editor").hide();
		var css = getPageHTML("#wp-custom-css");
		set_css_editor(css); 
		$("#lc-html-editor").hide(); 
		$("#lc-css-editor").show();
		lc_css_editor.resize();
		
		$("select#lc-editor-theme option[value=" + the_css_editor_theme + "]").prop('selected', true);
	});
	
	//USER CLICKS HTML TAB
	$("body").on("click", "#html-tab", function(e) {
		e.preventDefault();
		$(".lc-editor-menubar .only-for-html").show();
		var selector = $("#lc-html-editor-window").attr("selector");
		if(selector.toLowerCase() ==="main#lc-main") $(".lc-editor-goto-parent-element").hide();
		$(".code-tabber a.active").removeClass("active");
		$(this).addClass("active");
		$("#lc-html-editor").show(); 
		$("#lc-css-editor").hide();
		lc_html_editor.resize();
		
		$("select#lc-editor-theme option[value=" + the_editor_theme + "]").prop('selected', true);
	});
	
	
	//USER CLICKS lc-editor-parent WHEN CODE EDITOR IS OPEN
	$("body").on("click", '.lc-editor-goto-parent-element', function(e) {
		if (!$('#lc-html-editor-window').is(':visible')) {alert("Code editor is closed.");return;}
		var selector = $('#lc-html-editor-window').attr("selector");
		if(selector.toLowerCase() ==="main#lc-main") {alert("Cannot go beyond <main>");return;}
		//alert("Navigate to parent");
		var selector = CSSelector(doc.querySelector(selector).parentNode);

		$("#lc-html-editor-window").attr("selector", selector);
		myConsoleLog("Open html editor for: " + selector);
		var html = getPageHTML(selector);
		set_html_editor(html);
		$("#lc-html-editor-window").removeClass("lc-opacity-light").fadeIn(100);
		lc_html_editor.focus();
		
		$("#html-tab").click();
	});
	
	
	//////////////////////////////////// MAIN TOOLBAR  ///////////////////////////////////////////////////////////////////////// 
	
	// USER CLICKS (nothing) in the MAIN TOOLBAR  ACCIDENTALLY
	$('#maintoolbar').mousedown(function(e) {
		//myConsoleLog(" #maintoolbar mousedown");
		e.preventDefault(); //coupled with onmousedown, it will prevent the clicked link to gain focus, so the edited area is not blurred
	});
	
	
	//LINK MODAL STUFF
	function lc_get_selection() {
		return previewiframe?.contentDocument?.getSelection();
	}

	function lc_get_selection_element(sel) {
		return sel?.anchorNode?.parentElement;
	}

	function lc_selection_contains_tag(sel, tag, element) {
		
		if (typeof sel === 'undefined' || !sel) return false;
		if (typeof tag === 'undefined' || !tag) return false;
		
		node = sel?.anchorNode; //element
		el = node?.parentElement;
		selTag = el?.tagName; //tagname

		return (selTag?.toLowerCase() == tag.toLowerCase() || element?.tagName?.toLowerCase() == tag.toLowerCase());
	}

	// here for future use, might be useful with a custom toolbar
	function lc_url_contains_schema(url) {
		return url.substr(0, 1) == '/' 
			|| url.substr(0,2) == '//'
			|| url.indexOf('http://') != -1
			|| url.indexOf('https://') != -1;
	}

	//todo detect double click on link and open modal, we already have an event on click of editable, this might be a problem.
	$('.lc-modal-close').click(function () {
		lcModalLinkCleanUp();
	});

	// When the user clicks anywhere outside of the modal, close it
	window.onclick = function (event) {
		if (event.target == document.getElementById('lc-modal-link')) {
			lcModalLinkCleanUp();
		}
	}

	//detect keys on modal
	$(window).on('keydown keyup keypress', function (e) {
		if (!$('#lc-modal-link').is(':visible')) return;
		if (e.keyCode == 27) lcModalLinkCleanUp();
		if (e.keyCode == 13) {
			e.preventDefault();
			$('#link-submit').click();
		}
	});

	document.getElementById('lc-modal-link-form').onsubmit = function (e) {
		e.preventDefault();
		lc_set_anchor_text();
		return false;
	}

	/**
	 * Handles dummy link removal on close or exit of modal
	 * @returns void
	 */
	function lcModalLinkCleanUp() {
		lcModal = document.getElementById('lc-modal-link');
		lcModal.style.display = "none";
		lcModalLinkUuid = lcModal.getAttribute('data-target');
		lcModalLinkWasUpdating = lcModal.getAttribute('data-updating');

		var a = previewiframe.contentDocument.querySelector("[lclink=" + lcModalLinkUuid + "]");
		if (!a || lcModalLinkWasUpdating) return;

		var sel = previewiframe.contentDocument.getSelection();
		sel.removeAllRanges();
		var range = previewiframe.contentDocument.createRange();
		range.selectNodeContents(a);
		sel.addRange(range);

		//remove tag from el variable and insert the text of el before el
		a.parentNode?.insertBefore(a.firstChild, a);
		//delete the tag
		a.parentNode?.removeChild(a);

		previewiframe.contentDocument.execCommand('unlink', false, null);

		//clean up the attributes
		lcModal.removeAttribute('data-target');
		lcModal.removeAttribute('data-updating');
	}

	
	/**
	 * sets the link at caret position
	 * @todo has a bug within ace editor, does not update the dom if focused
	 */
	function lc_set_anchor_text(){
		lcModal = document.getElementById('lc-modal-link');
		lcModalLinkUuid = lcModal.getAttribute('data-target');
		
		//get the link
		var a = previewiframe.contentDocument.querySelector('[lclink="' + lcModalLinkUuid + '"]');
		
		if(!a) {
			myConsoleLog('Something went wrong, no link found');
			//execcommand undo to unlink
			previewiframe.contentDocument.execCommand('undo');
			return;
		}

		lcModalLinkName = document.getElementById("link-name");
		lcModalLinkUrl = document.getElementById("link-url");
		lcModalLinkTarget = document.getElementById("link-target");
		lcModalLinkRel = document.getElementById("link-rel");
		lcModalLinkId = document.getElementById("lc-modal-link-id");
		lcModalLinkClasses = document.getElementById("lc-modal-link-classes");
	
		//set values from modal
		linkName = lcModalLinkName.value;
		linkId = lcModalLinkId.value.trim();
		url = lcModalLinkUrl.value.trim();
		linkTarget = lcModalLinkTarget.checked ? '_blank' : '';
		linkNoFollow = lcModalLinkRel.value ?? null;
		linkClasses = lcModalLinkClasses.value.trim() ?? null;

		//todo move this before hide modal and validate url
		//no url, no party
		if (!url || !url.length || !linkName || !linkName.length) {
			myConsoleLog('no url, no party');
			return;
		}

		//NOT READY YET - check valid schema or prepend https://
		/*
		if (!lc_url_contains_schema(url)) {
			myConsoleLog('url does not contains valid schema');
			url = 'https://' + url;
		}
		*/

		//setup anchor properties
		a.setAttribute('id', linkId);
		a.setAttribute('href', url);
		a.setAttribute('target', linkTarget);
		a.setAttribute('rel', linkNoFollow);
		a.innerText = linkName;
		a.className = linkClasses;
		
		if ( !a.target) a.removeAttribute('target');
		if ( !a.rel) a.removeAttribute('rel');
		if ( !a.id) a.removeAttribute('id');
		if ( !a.className) a.removeAttribute('class');
		a.removeAttribute('lclink');

		//hide the modal, we don't need it anymore
		lcModal.style.display = 'none';

		//some browser might insert &nbsp; in the textarea, so we remove it from richeditable
		$(previewiframe.contentDocument).find('[editable=rich], [editable=inline]').each(function(){
			$(this)[0].innerHTML = $(this)[0].innerHTML.replace(/&nbsp;/gi, " ");
		});
		
		//fake el to launch blur and save dom
		fake = $(previewiframe.contentDocument).find('[editable=rich], [editable=inline]').last();
		fake.trigger('blur');

		//update the dom within the editor
		doc.querySelector('body').innerHTML = previewiframe.contentDocument.querySelector('body').innerHTML;

		//make sure to free the update
		lcModal.removeAttribute('data-target');
		lcModal.removeAttribute('data-updating');

		return;
	}

	//USER CLICKS TEXT TOOLBAR ITEMS	
	$('#ww-toolbar a').mousedown(function(e) {
		
		e.preventDefault(); //coupled with onmousedown, it will prevent the clicked link to gain focus, so the edited area is not blurred
		
		//make sure editable area is focused
		$("#previewiframe").contents().find(".lc-last-clicked-editable-element").focus();
		
		var command = $(this).data('command');
		myConsoleLog("Apply command " + command + " to text");
		
		//bolding
		if (command == 'bold') {  						
			/// special unbolding as per https://stackoverflow.com/questions/21030120/execcommand-not-unbolding
			
			//$sel = $.trim(previewiframe.contentDocument.getSelection().toString());
			//if($sel == ''){	myConsoleLog('Please select some text to bold'); return; } //useless protection
			
			var parentEle = previewiframe.contentDocument.getSelection().getRangeAt(0).commonAncestorContainer;
			parentEle = parentEle.parentNode;
			
			if(parentEle.tagName == 'B' || parentEle.tagName == 'STRONG') { //WE HAVE TO UN-BOLD, which can be critical
				myConsoleLog("We have to Unbold");
				
				if(!previewiframe.contentDocument.queryCommandState("bold")) { //BROWSER DOES NOT RECOGNIZE IT AS A BOLD, EVEN IF IT IS					 
					myConsoleLog("Special Unbolding");
					parentEle.id='unbold19992'; 
					$("#previewiframe").contents().find('#unbold19992').contents().unwrap();
					return;
				} 
			}
			//normal way for bolding or unbolding
			myConsoleLog("Standard Bold/Unbolding");
			previewiframe.contentDocument.execCommand($(this).data('command'), false, null);
		}
		
		//basic styles: italic, ul, ol
		if ( command == 'italic' || command == 'insertUnorderedList' || command == 'insertOrderedList' ) {
			previewiframe.contentDocument.execCommand($(this).data('command'), false, null);
		}
		
		//change tag
		if (command == 'p' ||  command == 'h1' || command == 'h2' || command == 'h3' || command == 'h4' || command == 'h5' || command == 'h6') {
			previewiframe.contentDocument.execCommand('formatBlock', false, command);
		}
		
		//span: broken because span is not well handled in contenteditable apparently
		if (command == 'span') {
			node = '<' + command + '>' + previewiframe.contentDocument.getSelection().toString() + '</' + command + '>';
			previewiframe.contentDocument.execCommand('insertHTML', false, node);
		}
		
		if (command == 'kbd' || command == 'code') {
			node = '<' + command + '>' + previewiframe.contentDocument.getSelection().toString() + '</' + command + '>';
			previewiframe.contentDocument.execCommand('insertHTML', false, node);
		}
		
		if (command == 'blockquote') {
			node = '<' + command + ' class="' + command + '">' + previewiframe.contentDocument.getSelection().toString() + '</' + command + '>';
			previewiframe.contentDocument.execCommand('insertHTML', false, node);
		}
		
		//handles link creations
		if (command == 'createlink') {
			//set the boundaries
			sel = previewiframe.contentDocument.getSelection();
			el = lc_get_selection_element(sel);
			selRange = sel.getRangeAt(0);
			lcLinkRange = selRange.cloneRange();
			lcModal = document.getElementById('lc-modal-link');
			
			//remove possibile wrong attribute
			lcModal.removeAttribute('data-updating');
			lcModal.removeAttribute('data-target');
			
			//if no word is selected, we will select the whole word
			if(sel.anchorOffset == sel.focusOffset) {
				
				var selLeft = sel.anchorOffset ?? sel.extendOffset;
				var selRight = sel.anchorOffset ?? sel.extendOffset;

				//if no full word is selected we expand the selection but only if the left char is not a space
				if(selRange.startContainer.nodeValue.substring(selLeft - 1, selRight).trim() != '') {
					sel.modify("move", "backward", "word");
					/**
					 * a fake bug here
					 * cannot judge if selection is a single word after a period. 
					 * example: "google.com" stops at "google".
					 * User must select the whole word to create a link
					 */
					sel.modify("extend", "forward", "word");
				}
			}
			
			//default attributes
			linkName = sel.toString();
			linkUrl	 = '#';
			linkTarget = '';
			linkNoFollow = '';
			linkId = '';
			linkClasses = '';

			//check if we are updating an existing link
			updateEl = lc_selection_contains_tag(sel, 'a');

			//dummy anchor element
			var a = document.createElement('a');
			lcDataCommand = $(this).data('command');
			lcModalLinkUuid = lcRandomUUID();

			//it's an update, replace anchor with the one found
			if (updateEl) {
				a = el;
				linkName = el.textContent;
				linkUrl = el.getAttribute('href');
				linkTarget = el.getAttribute('target');
				linkNoFollow = el.getAttribute('rel');
				lcModal.setAttribute('data-updating', 'true');	
				linkId = el.getAttribute('id');
				linkClasses = el.className;
			}

			lcModalLinkName = document.getElementById("link-name");
			lcModalLinkUrl = document.getElementById("link-url");
			lcModalLinkTarget = document.getElementById("link-target");
			lcModalLinkRel = document.getElementById("link-rel");
			lcModalLinkId = document.getElementById("lc-modal-link-id");
			lcModalLinkClasses = document.getElementById("lc-modal-link-classes");

			//setup and show the modal
			lcModalLinkName.value = linkName;
			lcModalLinkUrl.value = linkUrl.toString();
			lcModalLinkTarget.checked = linkTarget ? true : false;
			lcModalLinkRel.value = linkNoFollow;
			lcModalLinkId.value = linkId;
			lcModalLinkClasses.value = linkClasses;
			lcModal.setAttribute('data-target', lcModalLinkUuid);

			//anchor attributes
			if (linkId) a.setAttribute('id', linkId);
			if (linkTarget) a.setAttribute('target', linkTarget);
			if (linkNoFollow) a.setAttribute('rel', linkNoFollow);
			a.innerText = linkName;
			a.setAttribute('href', linkUrl);
			a.setAttribute('lclink', lcModalLinkUuid);
			
			//different command here, only not updating
			if ( !updateEl) previewiframe.contentDocument.execCommand('insertHTML', null, a.outerHTML);

			//show the modal
			lcModal.style.display = "block";
			lcModalLinkName.focus();
			return;
		}

		if (command == 'unlink') {
			//get selection
			sel = lc_get_selection();
			el = lc_get_selection_element(sel);
			updateEl = lc_selection_contains_tag(sel, 'a');

			if (!updateEl) return;
			
			//remove tag from el variable and insert the text of el before el
			el.parentNode?.insertBefore(el.firstChild, el);
			//delete the tag
			el.parentNode?.removeChild(el);
			
			//from selection remove link tag and leave text
			previewiframe.contentDocument.execCommand($(this).data('command'), false, null);
			return;
		}
 
	});
	////////////// CLASS PALETTE //////////////////////
	 
	//USER OPENS CLASS PALETTE 
	$("body").on("click", "#toggle-classes-submenu", function(e) {
		e.preventDefault();
		$(this).toggleClass("is-active");
		$("#classes-palette").slideToggle(100);
	});
	// USER CLICKS CLASS PALETTE:DOCK TO BOTTOM
	$("body").on("click", "#classes-palette-dock-to-bottom", function(e) {
		e.preventDefault();
		$(this).toggleClass("is-active");
		$('#classes-palette').toggleClass('classes-palette-to-bottom'); 
	});
	// USER CLICKS CLASS PALETTE: SHOW EXTRA ALIGNMENT CLASSES
	$("body").on("click", "#toggle-extra-alignent-classes", function(e) {
		e.preventDefault();
		$(this).toggleClass("is-active");
		$('#extra-alignment-classes').slideToggle(); 
	});
	
	//USER CLICKS CLASS PALETTE LINK
	$("body").on("mousedown", "#classes-palette a[data-class]", function(e) {
		e.preventDefault(); 
		myConsoleLog("Apply class "+ $(this).attr("data-class")); 
		
		//make sure editable area is focused
		$("#previewiframe").contents().find(".lc-last-clicked-editable-element").focus();
		
		//check if editable area is not empty
		if ($("#previewiframe").contents().find(".lc-last-clicked-editable-element").html()==''){
			swal({
				title: "Element  is empty",
				text: "Please add some content before adding classes",
				icon: "warning",
				/* dangerMode: true, */
			});
			return;
		}
		
		//let's get the dom element where we have to work
		var el = previewiframe.contentDocument.getSelection().focusNode.parentNode; 
		
		//handle exception to fix contenteditable bug in rich editor / contenteditable, when selecting first item of an editable area
		//@todo ripristinare
		if (el.getAttribute("editable")=='rich' && true === false) {  
				previewiframe.contentDocument.getSelection().modify('move', 'left', 'character');
				myConsoleLog("Adjusted selection to circumvent Chrome bug in selecting first item of an editable area");
				el = previewiframe.contentDocument.getSelection().focusNode.parentNode;
				//reset the selection
				previewiframe.contentDocument.getSelection().removeAllRanges();
				//re-select programmatically the right item
				const range = previewiframe.contentDocument.createRange();
				range.selectNodeContents(el);
				previewiframe.contentDocument.getSelection().addRange(range);
		}
		
		//if the class is not already there, remove 'logically' conflicting conflicting classes if any
		if (!$(el).hasClass($(this).attr("data-class"))) $(this).closest(".class-group").find("a[data-class]").each(function(index, element) {  
			//myConsoleLog("remove class " + $(element).attr("value")); 
			$(el).removeClass($(element).attr("data-class"));
		 });
		
		//add the chosen class to preview
		$(el).toggleClass($(this).attr("data-class"));
		 
		//find out active classes and highlight 
		const classLinks = document.querySelectorAll("#classes-palette a[data-class]");
		for (let i= 0; i < classLinks.length; i++) {
			
			if (el.classList.contains(classLinks[i].getAttribute("data-class"))) classLinks[i].classList.add("is-active");  else classLinks[i].classList.remove("is-active"); 
		}
		

	});
	
	
	//USER OPENS EXTRAS MENU
	$("body").on("click", "#toggle-extras-submenu", function(e) {
		e.preventDefault();
		$("#extras-submenu").slideToggle(100);
		$(this).toggleClass("is-active");
	});

	//USER CLICKS ANY LINK IN EXTRAS SUBMENU
	$("body").on("click", '#extras-submenu a', function(e) {
		e.preventDefault();
		$('#extras-submenu').slideUp();
		$("#toggle-extras-submenu").removeClass("is-active");
	});

	//PUSH SIDE PANEL //USELESS NOW
	/*
	$("body").on("click", '.toggle-side-mode', function (e){
	    e.preventDefault(); 
	    $('#previewiframe-wrap').toggleClass("push-aside-preview");
	    $(this).find("i").toggleClass("fa-chevron-circle-left").toggleClass("fa-chevron-circle-right");
	});*/
	/*
	//OPEN PROJECT SETTINGS PANELZ
	$("body").on("click", '.edit-project-settings', function(e) {
		e.preventDefault();
		revealSidePanel("project-settings", 'main#lc-main');     
	});
	*/

	//GO FULLSCREEN
	$("body").on("click", '.go-fullscreen', function(e) {
		e.preventDefault();
		if (document.fullscreenElement) {
			document.exitFullscreen();
		} else {
			document.documentElement.requestFullscreen();
		}
	});


	//USER CLICKS EDIT HTML FROM EXTRAS SUBMENU
	$("body").on("click", '.open-main-html-editor', function(e) {
		e.preventDefault();
		$(".close-sidepanel").click(); 
		$("body").addClass("lc-bottom-editor-is-shown");
		//$(  "main .lc-shortcode-preview").remove();
		var selector = "main#lc-main";
		$("#lc-html-editor-window").attr("selector", selector);
		myConsoleLog("open html editor for: " + selector);
		var html = getPageHTML(selector);
		set_html_editor(html);
		$("#lc-html-editor-window").removeClass("lc-opacity-light").fadeIn(100);
		$("#html-tab").click();
		lc_html_editor.focus();
	});
	
	//USER CLICKS EDIT CSS FROM EXTRAS SUBMENU
	$("body").on("click", '.open-main-css-editor', function(e) {
		e.preventDefault();
		$(".open-main-html-editor").click();
		$("#css-tab").click();
		setTimeout(function() { $("#extras-submenu").hide();}, 400);
		
	});
	
	//USER CLICKS EDIT CSS FROM EXTRAS SUBMENU
	$("body").on("click", '.open-editing-history', function(e) {
		e.preventDefault();
		revealSidePanel("history", false);
		
	});
	
	
	//USER CLICKS EXPORT HTML FILE download-static-file
	$("body").on("click", '.download-static-file', function(e) {
		e.preventDefault();

		//get the font loading statement
		var font_loading_element=doc.querySelector("link[href^='https://fonts.googleapis.com/css']");
		var font_loading_statement = font_loading_element ? font_loading_element.outerHTML : "";
		
		//find out if there are animations
		var animated_el=doc.querySelector("*[data-aos]");
		var animations_loading_statement = animated_el ? ' <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet"> <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script> <script> AOS.init(); </script>' :'';

		//get the styles bundle URL
		var styles_bundle_element=doc.querySelector("head #picostrap-styles-css, head #understrap-styles-css"); 
		var styles_bundle_url = styles_bundle_element ? styles_bundle_element.href : "https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css";

		//find out js bundle URL
		var js_bundle_url = (lc_editor_main_bootstrap_version == "5") ? "https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" : "https://cdn.jsdelivr.net/npm/bootstrap.native@3.0.0/dist/bootstrap-native.min.js";



		fetch(styles_bundle_url)  
			.then(function(response) {
				return response.text();
				})
			.then(function(css_bundle) {
					//alert(css_bundle);
					var the_style="<style> " + css_bundle + " " + getPageHTML("#wp-custom-css") + " </style>";
					//standard from Bootstrap documentation (introduction)
					var the_header = '<!doctype html><html lang="en"> <head> <meta charset="utf-8"> <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">  <title>'+lc_editor_current_post_page_title_tag+'</title> '+font_loading_statement+ ' ' + the_style+' </head> <body> ';
				var the_footer = animations_loading_statement + ' <script src="' + js_bundle_url + '"></script> </body></html>';
					//add FontAwesome
					//the_footer = '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">' + the_footer;
					download("index.html", the_header + getPageHTML("main#lc-main") + the_footer);
				}).catch(function(err) {
					swal("Error " + err + " fetching CSS");
				});
					
	});

	//USER CLICKS READYMADE TEMPLATES FROM EXTRAS SUBMENU
	$('.readymade-pages').click(function (e) {
		e.preventDefault();
		$(".close-sidepanel").click(); //to make sure all is closed
		if (!$('#readymades-modal-wrapper').length) initialize_readymade_templates_window();
		$('#readymades-modal-wrapper').css('display', 'block');
		$('#previewiframe-wrap, #maintoolbar').addClass('is-blurred');
	});

	//USER CLICKS RESET HTML FROM EXTRAS SUBMENU
	$("body").on("click", '.reset-html-page', function(e) {
		e.preventDefault();
		swal({
				title: "Are you sure?",
				text: "This will delete the whole page content. Are you sure?",
				icon: "warning",
				buttons: true,
				/* dangerMode: true, */
			})
			.then((willDelete) => {
				if (willDelete) {
					$(".lc-editor-close").click();
					$(".close-sidepanel").click();
					setPageHTML("main#lc-main", ""); //setPageHTML("main#lc-main","<section></section>");
					updatePreview();
				}
			});
	});

	//RESPONSIVE SWITCH
	$('#responsive-toolbar a').click(function(e) {
		e.preventDefault();
		$('#responsive-toolbar a.is-active').removeClass("is-active");
		$(this).addClass("is-active");
		width_value = $(this).attr("data-width");
		if ($(this).hasClass("add-smartphone-frame")) $("#previewiframe-wrap").addClass("smartphone");
		else $("#previewiframe-wrap").removeClass("smartphone");
		$(this).addClass("is-active");
		$("#previewiframe").css("width", width_value);

		height_value = $(this).attr("data-height");
		if (height_value === undefined) $("#previewiframe").css("height", "");
		else $("#previewiframe").css("height", height_value);

		//take care of superimposed editing buttons
		//previewFrame.contents().find(".lc-helper-link").remove();
		//setTimeout(add_helper_edit_buttons_to_preview, 1500);

		//hide contextual menu interfaces
		$("#previewiframe").contents().find(".lc-contextual-menu").hide();
	});



	// SAVE Page ////////////////////////////////////////
	$("body").on("click", "#main-save", function(e) {
		e.preventDefault();
		$("#previewiframe").contents().find(".lc-last-clicked-editable-element").blur(); //stop text live editing and get those edits into doc
		$('#main-save i').attr("class", "fa fa-spinner fa-spin"); 
		$("#saving-loader").fadeIn(300);
		$.post(
				lc_editor_saving_url, {
					'action': 'lc_save_page',
					'post_id': lc_editor_current_post_id,
					'html_to_save': '\n'+html_beautify(getPageHTML("main#lc-main"), {
										unformatted: ['script', 'style'],
										"indent_size": "1",
										"indent_char": "\t",
									})+'\n',
					'css_to_save': (getPageHTML("#wp-custom-css")),
					'lc_main_save_nonce_field': $("#lc_main_save_nonce_field").val(),
				},
				function(response) {
					//myConsoleLog('The server responded: ', response);
					if (response.includes("Save")) {
						//success
						$('#main-save i').attr("class", "fa fa-save");
						$('#main-save').css("color","#3cbf47");
						setTimeout(function(){$('#main-save').css("color",""); }, 2000);
						$("#saving-loader").fadeOut(100);
						original_document_html = getPageHTML();
					} else {
						//(rare) Error!
						swal({
							title: "Saving error (b)",
							icon: "warning",
							text: response
						});
						$('#main-save i').attr("class", "fa fa-save");
						$("#saving-loader").fadeOut(100);
					}

				}
			)
			//.done(function(msg){  })
			.fail(function(xhr, status, error) {
				// (typical, eg unlogged) Error!
				navigator.clipboard.writeText((getPageHTML("main#lc-main")));
				swal({
					title: "Saving error",
					icon: "warning",
					text: error
				});
				$('#main-save i').attr("class", "fa fa-save");
				$("#saving-loader").fadeOut(100);
			});
	}); //end on click



	//CANCEL HTML SAVING     
	$("body").on("click", "#cancel-main-saving", function(e) {
		e.preventDefault();
		if (original_document_html != getPageHTML()) {
			var r = confirm("There are unsaved changes to the page. Exit anyway?");
			if (r === false) return (false);
		}
		window.location.assign(lc_editor_url_before_editor);
	});


	//BIND KEYBOARD SHORTCUTS TO MAIN UX
	$("body").keydown(function(e) {
		handleKeyboardEvents(e);
	});

		  
	////CODE EDITOR WINDOW UX TWEAKS //////////////////////////////////////////////////////
	
	//MOUSE LEAVES CODE WINDOW: make it translucent
	$("body").on("mouseleave", "#lc-html-editor-window", function() {
		$("#lc-html-editor-window").addClass("lc-opacity-light");
	}); //end function
	
	//Open editor tips
	$("body").on("change", "#lc-editor-tips", function(e) {
		e.preventDefault();
		if ($(this).val() != "") window.open($(this).val());
	});
	//User changes THEME SELECTION
	$("body").on("change", "#lc-editor-theme", function(e) {
		e.preventDefault();
		if ($("#html-tab").hasClass("active")){
			the_editor_theme=$(this).val();
			lc_html_editor.setTheme("ace/theme/" + the_editor_theme);
			setEditorPreference("editor_theme", the_editor_theme);
		} else {
			the_css_editor_theme=$(this).val();
			lc_css_editor.setTheme("ace/theme/" + the_css_editor_theme);
			setEditorPreference("css_editor_theme", the_css_editor_theme);
		}
	});
	//User changes FONT SIZE
	$("body").on("change", "#lc-editor-fontsize", function(e) {
		e.preventDefault();
		document.getElementById('lc-html-editor').style.fontSize = $(this).val() + 'px';
		document.getElementById('lc-css-editor').style.fontSize = $(this).val() + 'px';
		setEditorPreference("editor_fontsize", $(this).val());
	});
	//USER CLICKS CLOSE CODE EDITOR WINDOW
	$("body").on("click", ".lc-editor-close", function(e) {
		e.preventDefault(); 
		$("body").removeClass("lc-bottom-editor-is-shown");
		//$(this).closest("section").removeClass("lc-editor-window-maximized");
		lc_html_editor.resize();lc_css_editor.resize();
		$(this).closest("section").hide();
		initialize_contextual_menus();
	});

	//USER CLICKS MAXIMIZE CODE EDITOR WINDOW
	$("body").on("click", ".lc-editor-maximize", function(e) {
		e.preventDefault();
		let ed = document.getElementById('lc-html-editor-window');
		$(this).closest("section").removeClass("lc-editor-window-sided");
		$(this).closest("section").toggleClass("lc-editor-window-maximized");
		lc_html_editor.resize();lc_css_editor.resize();
	});

	//USER CLICKS SIDE CODE EDITOR WINDOW
	$("body").on("click", ".lc-editor-side", function(e) {
		e.preventDefault();
		$(this).closest("section").removeClass("lc-editor-window-maximized");
		$(this).closest("section").toggleClass("lc-editor-window-sided");
		lc_html_editor.resize();lc_css_editor.resize();
	});
	
	/* *************************** HANDLE CLICKING OF ADD NEW SECTION BUTTON *************************** *///
	$("body").on('click', ".add-new-section", function(e) {
		e.preventDefault();
		$("#sidepanel .close-sidepanel").click();
		myConsoleLog("Let's create a new section");
		//previewFrame.contents().find("#lc-add-new-container-section-wrap").hide();
		var newSectionHTML = "<section></section>";
		var lastSection=doc.querySelector("main#lc-main section:last-child"); 
		//INSERT 
		if(!lastSection || lastSection.getAttribute("ID")!=="global-footer") {
			//normal   case:  no magic footer
			myConsoleLog("No magic footer detected");
			setPageHTML("main#lc-main", getPageHTML("main#lc-main") + newSectionHTML);
			//update preview
			previewFrame.contents().find("main#lc-main").append(newSectionHTML);
			//updatePreviewSectorial("main#lc-main");
		} else {
			//magic footer case
			myConsoleLog("Magic footer detected");
			var footer_code=doc.querySelector("main#lc-main > section#global-footer").outerHTML;
			doc.querySelector("main#lc-main > section#global-footer").remove();
			setPageHTML("main#lc-main", getPageHTML("main#lc-main") + newSectionHTML + footer_code);
			//update preview
			updatePreview();
		}
		//now open the respective panel
		var selector = CSSelector(previewFrame.contents().find("main section:last")[0]); //alert(selector);
		revealSidePanel("sections", selector);
		$(".sidepanel-tabs a:first").click(); //open first tab

		setTimeout(function(){previewFrame.contents().find("html, body").animate({			scrollTop: previewFrame.contents().find(selector).offset().top		}, 500, 'linear'); }, 100);
		

	});


	/* *************************** SIDE PANEL *************************** */
	//HISTORY restore step
	$("body").on("click", "#history-steps li", function(e) {
		e.preventDefault();
		var new_html=$(this).find("template").html();
		setPageHTML("main", new_html);
		
		if (new_html.includes("lc-needs-hard-refresh")) {
			// soft updatePreview()
			previewiframe.srcdoc = doc.querySelector("html").outerHTML;
			previewiframe.onload = enrichPreview();
		
			setTimeout(function() {
				previewFrame.contents().find("html, body").animate({
					scrollTop: previewFrame.contents().find(selector).offset().top
				}, 10, 'linear');
			}, 100);

		} else {
			//soft sectorialupdatePreview
			var selector="main";
			previewiframe.contentWindow.document.body.querySelector(selector).outerHTML = doc.querySelector(selector).outerHTML;
			enrichPreviewSectorial(selector);
		}
	});
	
	//MOUSE ENTERS SIDEPANEL: HILIGHT PAGE ELEMENT ////////////////////////
	$("body").on("mouseenter", "#sidepanel section", function() {
		var selector = $(this).attr("selector");
		previewFrame.contents().find(selector).addClass("lc-highlight-currently-editing");
	});
	//MOUSE LEAVES SIDEPANEL: de-HILIGHT PAGE ELEMENT ////////////////////////
	$("body").on("mouseleave", "#sidepanel section", function() {
		var selector = $(this).attr("selector");
		previewFrame.contents().find(selector).removeClass("lc-highlight-currently-editing");
	});

	///CLICK CLOSE PANEL ICON
	$("body").on("click", "#sidepanel .close-sidepanel", function(e) {
		e.preventDefault();
		previewFrame.contents().find(".lc-contextual-menu").fadeOut(500);
		//un-push preview
		$("#previewiframe-wrap").removeClass("push-aside-preview");

		$('#sidepanel').fadeOut();
		//re-show content creation buttons
		//previewFrame.contents().find("#lc-add-new-container-section-wrap").slideDown(300); 
	});

	//TABBER LOGIC eg IMAGES// for UnSplash /wpadmin / svg
	$("body").on("click", "#sidepanel *[data-reveal]", function(e) {
		e.preventDefault();
		var theSection = $(this).closest("section[selector]");
		var selector = $(this).attr("data-reveal");
		if ($(this).hasClass("highlight-button")) { //we have to hide
			$(this).removeClass("highlight-button");
			theSection.find(selector).slideUp(100);
		} else { //we have to show
			$(this).parent().find(".highlight-button").removeClass("highlight-button");
			$(this).addClass("highlight-button");
			theSection.find(".items-to-reveal > div").hide();
			theSection.find(selector).slideDown(100);
		}
	});

	//PROPERTIES ACCORDION: toggle-next-element
	$("body").on("click", ".toggle-next-element", function() {
		
		if ($(this).next(".property-group").is(':visible')) {
			//we close next
			$(this).parent().find(".opened").removeClass("opened");
			$(this).next(".property-group").hide(); 
		} else {
			//we open the next
			$(this).parent().find(".opened").removeClass("opened");
			$(this).addClass("opened");
			$(this).parent().find(".property-group").hide();
			$(this).next(".property-group").show();
			$(this)[0].scrollIntoView(); 
		}
	});

	//INPUT ZOOMABLE FIELDS: right-click to maximize
	/* $("body").on("contextmenu", "#sidepanel .zoomable", function() {
		$("#sidepanel").addClass("sidepanel-is-maximized");
		return false;
	}); */

	//INPUT ZOOMABLE FIELDS: ON FOCUS, ZOOM
	$("body").on("focus", "#sidepanel .zoomable", function() {
		$("#sidepanel").addClass("sidepanel-is-maximized");
		return false;
	});

	//INPUT un-maximize
	$("body").on("blur", "#sidepanel .zoomable", function() {
		$("#sidepanel").removeClass("sidepanel-is-maximized");
	});
   
   	//USER CHANGES COLOR: CUSTOM COLOR WIDGET CHANGES
   	$("body").on("click", ".custom-color-widget span", function() {
		myConsoleLog("Color widget change");
		var selector = $(this).closest("[selector]").attr("selector");
		var elem = doc.querySelector(selector);
		//eliminate all classes in select
		$(this).parent().find("span").each(function(index, element) {
			the_value = $(element).attr("value").trim(); //myConsoleLog("Eliminate"+the_value);
			if (the_value !== "") elem.classList.remove(the_value);
		});
		var current_selected_item = $(this).attr("value").trim();
		if (current_selected_item !== "") elem.classList.add(current_selected_item); //myConsoleLog("Add class"+current_selected_item);
		$(this).closest("[selector]").find("input[attribute-name=class]").val(elem.classList).change();
		$(this).parent().find("span.active").removeClass("active");
		$(this).addClass("active");
	});
 

	//USER CHANGES COLOR OPACITY: CUSTOM COLOR OPACITY CHANGES
	$("body").on("click", ".custom-opacity-widget span", function () { 
		myConsoleLog("Opacity widget change");
		var selector = $(this).closest("[selector]").attr("selector");
		var elem = doc.querySelector(selector);
		//eliminate all classes of this set
		$(this).parent().find("span").each(function (index, element) {
			the_value = $(element).attr("value").trim(); //myConsoleLog("Eliminate"+the_value);
			if (the_value !== "") elem.classList.remove(the_value);
		});
		var current_selected_item = $(this).attr("value").trim();
		if (current_selected_item !== "") elem.classList.add(current_selected_item); //myConsoleLog("Add class"+current_selected_item);
		$(this).closest("[selector]").find("input[attribute-name=class]").val(elem.classList).change();
		$(this).parent().find("span.active").removeClass("active");
		$(this).addClass("active");
	});
	
	//on clicking custom-opacity-widget-text-opacity, CHECK COLOR IS SELECTED
	$("body").on("click", ".custom-opacity-widget-text-opacity span", function () {
		var theSection = $(this).closest("section[selector]");
		if ($(theSection).find(".custom-color-widget-text-color span.active").attr("value")=="") { //case no color chosen
			swal ("Please choose a color from the palette above to see opacity in action.");
		}
	});

	//on clicking custom-opacity-widget-text-opacity, CHECK COLOR IS SELECTED
	$("body").on("click", ".custom-opacity-widget-bg-opacity span", function () {
		var theSection = $(this).closest("section[selector]");
		if ($(theSection).find(".custom-color-widget-bg-color span.active").attr("value") == "") { //case no color chosen
			swal("Please choose a color from the palette above to see opacity in action.");
		}
	});

   	//  CLICK CUSTOMIZE COLORS
   	$("body").on("click", ".customize-colors", function() {
		swal({
				title: "Customizing Bootstrap",
				text: "If you're using the picostrap Theme, you can easily customize the font styles and the color palette using the WordPress Customizer. \n\n Just save and exit the LiveCanvas editor. \n Then, click the 'Customize' link in the top admin bar.",
				icon: "warning",
				buttons: false,
				/* dangerMode: true, */
		}); 
	});
   
	//USER CHANGES a SELECT in the sidepanel: miscellaneous classes
	$("body").on("change", "#sidepanel section select[target=classes]", function() {
		myConsoleLog("Adding class from select: " + $(this).val());
		var selector = $(this).closest("[selector]").attr("selector");
		var elem = doc.querySelector(selector);
		//eliminate all classes in select
		$(this).find("option").each(function(index, element) {
			the_value = $(element).val().trim(); //myConsoleLog("Eliminate"+the_value);
			if (the_value !== "") elem.classList.remove(the_value);
		});
		var current_selected_item = $(this).val().trim();
		if (current_selected_item !== "") elem.classList.add(current_selected_item); //myConsoleLog("Add class"+current_selected_item);
		$(this).closest("[selector]").find("input[attribute-name=class]").val(elem.classList).change();
	});

	//USER CHANGES AN INPUT (text): trigger change in document | attribute values editing
	$("body").on("change", "#sidepanel section *[attribute-name]", function () {
		myConsoleLog("Attribute values editing");
		var attribute_name = $(this).attr('attribute-name');
		var selector = $(this).closest("section").attr("selector");
		var inner_selector = $(this).attr("inner-selector") ?? "";

		//UNIQUE ID CHECK
		if (attribute_name === "ID" && !!doc.getElementById($(this).val())) {
			swal({ title: "Already existing ID", icon: "warning", text: "Please choose another name for this ID." });
			return;
		}

		//APPLY THE CHANGE
		if (attribute_name === 'html') setPageHTML(selector + " " + inner_selector, $(this).val());
		else setAttributeValue(selector + " " + inner_selector, attribute_name, $(this).val());

		//UPDATE THE PREVIEW
		updatePreviewSectorial(selector);
	});

	//USER CHANGES an INPUT number: trigger change in document | attribute values editing for cols and spacings
	$("body").on("change", ".activate-input-numbers input[type=number]", function () {
		myConsoleLog("Adding class from number input");
		var class_prefix = $(this).attr('name');
		var chosen_class = class_prefix + "-" + $(this).val().replace("-", "n");
		var selector = $(this).closest("[selector]").attr("selector");
		var elem = doc.querySelector(selector);
		var theSection = $(this).closest("section[selector]");
		
		//get rid of the classes 
		for (let i = -50; i <= 50; i++) {
			var the_class = class_prefix + "-" + i.toString().replace("-", "n");
			elem.classList.remove(the_class);
		}
		//add the right class, if not empty
		if ($(this).val()) elem.classList.add(chosen_class);

		//reapply to classes field in class editing interface
		theSection.find('input[attribute-name=class]').val(elem.className).change();

		//UPDATE THE PREVIEW
		updatePreviewSectorial(selector);

	});

	//INLINE STYLE READYMADES 
	$('#sidepanel').on('change', 'select.inline-style-readymades', function(event) {
		event.preventDefault();
		currentValue = $(this).val();
		var theSection = $(this).closest("section[selector]");
		var currentStyle = theSection.find("textarea[attribute-name=style]").val();
		//loop  all items in select
		$(this).find("option").each(function(index, element) {
			the_value = $(element).val();
			//myConsoleLog('replace '+the_value+ ' with '+currentValue);
			currentStyle = currentStyle.replace(the_value, currentValue);
		}); //end each
		theSection.find("textarea[attribute-name=style]").val(currentStyle).change();

	}); //end function

	//   fake SELECT inputs //////////////////////////////////////
	//// toggle state
	$("body").on("click", '.ul-to-selection li.first', function() {
		$(this).closest(".ul-to-selection").toggleClass("opened");
	});

	///SHAPE DIVIDERS: CLICK AND APPLY
	$("body").on("click", 'ul#shape_dividers li ', function() {
		if ($(this).hasClass("first")) return;
		var code = $(this).html();
		$(this).closest(".ul-to-selection").find("li.first").html(code);
		//get current area selector eg section)
		var selector = $(this).closest("[selector]").attr("selector");
		//remove the old shape divider if present   
		var elem = doc.querySelector(selector + ' .lc-shape-divider-bottom');
		if (elem) elem.parentNode.removeChild(elem);

		doc.querySelector(selector).innerHTML += code;
		updatePreviewSectorial(selector);

	});

	//////BACKGROUNDS BUILDING ///////////////////
	$('#backgrounds .automatic-library-filler').each(function(index, el) {
		var count;
		for (count = 1; count <= $(el).attr("max"); count++) {
			$(el).append('<li style="' + $(el).attr("the-style").replace(/@id@/g, count) + '"></li>');
		}
	}); //end each

	///BACKGROUNDS: CLICK AND APPLY
	$("body").on("click", "ul#backgrounds li", function() {
		if ($(this).hasClass("first")) return;
		$(this).closest(".ul-to-selection").find("li.first").attr("style", $(this).attr("style"));
		$(this).closest("section").find("textarea[attribute-name=style]").val($(this).attr("style")).change();
	}); //end on click


	///////////BACKGROUND IMAGE
	$("body").on("click", ".open-background-image-panel", function(e) {
		e.preventDefault();
		var selector = $(this).closest("[selector]").attr("selector");
		revealSidePanel("background", selector);
	}); //end on click




	/* *************************** GRID BUILDER: COLUMNS STRUCTURE BUILDING *************************** */

	//HANDLE CLICKING COLUMN SCHEMA BUTTONS: CREATE CONTAINER AND FIRST ROW
	$("body").on("click", "#sidepanel form#grid-builder button[data-rows]", function(e) {
		e.preventDefault(); //$("#sidepanel .close-sidepanel").click();
		var class_prefix = $(this).closest("section").find("[name='row_breakpoint']").val();
		var html_columns = ""; //init variable
		$(this).attr("data-rows").split("-").forEach(function(columnSize) {
			html_columns = html_columns + '<div class="' + class_prefix + columnSize + '">' + '<div class="lc-block"></div>' + '</div>';
		});
		//get container width setting
		var container_width = $("input[name=container-width]:checked").val();
		if (container_width == "standard") var the_container_class = "container";
		else var the_container_class = "container-fluid";

		//get title checkbox setting
		if ($('#sidepanel form#grid-builder #add-section-title').prop('checked'))
			var the_intro_row = '<div class="row"><div class="col-md-12"><div class="lc-block">' +
				'<h2 class="display-2 text-center mt-3 mb-0" editable="inline"> Section Title</h2>' +
				'<p class="text-muted h4 text-center mb-5" editable="inline">The subheading text goes here. Explain whats going on in here.</p>' +
				'</div></div></div>';
		else var the_intro_row = "";

		//define selector for the  ROW:
		var selector = $(this).closest("section").attr("selector");
		var html = '<div class="' + the_container_class + '">' + the_intro_row + '<div class="row">' + html_columns + '</div></div>';
		setPageHTML(selector, html);
		updatePreviewSectorial(selector);
	});

	/////////ADD ANOTHER ROW ///////////////////////

	//HANDLE CLICKING COLUMN SCHEMA BUTTON from content preview
	$("body").on("click", "#sidepanel form.add-row-buttons-wrap button[data-rows]", function(e) {
		myConsoleLog("lets add rows");
		e.preventDefault();
		var class_prefix = $(this).closest("section").find("[name='row_breakpoint']").val();
		var html_columns = ""; //init variable
		$(this).attr("data-rows").split("-").forEach(function(columnSize) {
			html_columns = html_columns + '<div class="' + class_prefix + columnSize + '">' + '<div class="lc-block"></div>' + '</div>';
		});
		//define selector for the  CONTAINER:
		var selector = $(this).closest("section").attr("selector");
		var html_new = getPageHTML(selector) + ' <div class="row"> ' + html_columns + ' </div> ';
		setPageHTML(selector, html_new); //put columns inside row
		updatePreviewSectorial(selector);
		//$("#sidepanel .close-sidepanel").click();
	});

	/* *************************** SECTIONS / BLOCKS BROWSER / HTML REPLACEMENT / INSTALL *************************** */

	//USER CLICKS BLOCK / SECTION: PUT HTML IN WEBPAGE 
	$("body").on("click", "#sidepanel block", function(e) {
		e.preventDefault();

		//determine which case
		var theCase = ($(this).closest("#readymade-sections").length) ?  "section" : "block";

		//previewFrame.contents().find("#lc-minipreview").hide();
		var selector = $(this).closest("section").attr("selector");
		var new_html = lc_filter_components($(this).closest("block").find("template").html());

		myConsoleLog("Insert HTML content in " + theCase + " at " + selector);

		//now clean the classes so plain version is shown
		classes = getAttributeValue(selector, "class");
		if (!!classes) { 
			classes = classes.replace('text-dark bg-light', '').replace('text-light bg-dark', '');
			setAttributeValue(selector, "class", classes); //reset classes for light/dark
		}

		//check if element contains wrapper tag already, eg a readymade section that starts with <section> tag
		var containsWrapperTag = (theCase=='section' && new_html.substring(0, 20).toUpperCase().includes("<SECTION"));
		
		myConsoleLog('containsWrapperTag: ' + containsWrapperTag);

		if (containsWrapperTag) setPageHTMLOuter(selector, new_html); 
			else setPageHTML(selector, new_html); //insert into document
		
		//check if needs hard refresh
		if  (code_needs_hard_refresh(new_html)) {
			updatePreview();
			setTimeout(function() {
				previewFrame.contents().find("html, body").animate({
					scrollTop: previewFrame.contents().find(selector).offset().top
				}, 10, 'linear');
				//previewFrame.contents().find(selector).hide().fadeIn(2000);
			}, 100);

		} else {
			//vanilla case
			updatePreviewSectorial(selector);
			previewFrame.contents().find(selector).hide().fadeIn(400);
		}

	}); //end on click
	
	//USER CLICKS INSERT LIGHT
	$("body").on("click", "#sidepanel block .insert-light", function(e) {
		e.preventDefault();
		$(this).closest("block").click();//insert the section regularly
		var selector = $(this).closest("section").attr("selector");
		setAttributeValue(selector,"class","text-dark bg-light");
		updatePreviewSectorial(selector);
	});
	//USER CLICKS INSERT DARK
	$("body").on("click", "#sidepanel block .insert-dark", function(e) {
		e.preventDefault();
		$(this).closest("block").click();//insert the section regularly
		var selector = $(this).closest("section").attr("selector");
		setAttributeValue(selector,"class","text-light bg-dark");
		updatePreviewSectorial(selector);
	});
	//USER HOVERS DARK LINK
	$("body").on("mouseover", "#sidepanel block .insert-dark", function() {
		$(this).closest("block").find("img").css("filter","grayscale(1) invert(1)");
	});
	//USER un-HOVERS DARK LINK
	$("body").on("mouseout", "#sidepanel block .insert-dark", function() {
		$(this).closest("block").find("img").css("filter","");
	});

	//USER CLICKS a link in BLOCK / SECTION: visit external page
	$("body").on("click", "#sidepanel a", function(e) {
		e.stopPropagation();
	}); //end on click

	/* *************************** SECTIONS / BLOCKS BROWSER : TABBER *************************** */
	//CHANGE ACTIVE TAB
	$("body").on("click", ".sidepanel-tabs a", function(e) {
		e.preventDefault();
		$(this).parent().find(".active").removeClass("active");
		$(this).closest("section").find("form").hide();
		$(this).addClass("active").closest("section").find("#" + $(this).attr("data-show")).show();
		//if($(this).attr("data-show") =="your-custom-sections") $("#lc-your-html-sections").load("?lc_action=load_cpt&cpt_post_type=lc_section", function () { });
		//if($(this).attr("data-show") =="your-custom-blocks")   $("#lc-your-html-blocks").load("?lc_action=load_cpt&cpt_post_type=lc_block", function () { });
	}); //end on click

	//LOADER UTILITY 
	$("body").on("click", "[data-load]", function(e) {
		e.preventDefault();
		if ($(this).attr("data-load") == "custom-html-sections") $("#custom-html-sections").load("?lc_action=load_cpt&cpt_post_type=lc_section", function() {});
		if ($(this).attr("data-load") == "custom-html-blocks") $("#custom-html-blocks").load("?lc_action=load_cpt&cpt_post_type=lc_block", function() {});
	}); //end on click

	/* *************************** READYMADES / BLOCKS BROWSER ACCORDION *************************** */
	//click item additional panel
	$("body").on("click", ".items-browser h4", function(e) {
		e.preventDefault(); //alert();
		if ($(this).hasClass("opened")) $(this).removeClass("opened");
		else {
			$(this).closest(".items-browser").find("h4.opened").removeClass("opened");
			$(this).addClass("opened");
		}

		$(".items-browser block").css("pointer-events", "none");
		
		$(this).closest(".items-browser").find(">div").not($(this).next("div")).hide();  

		$(this).next("div").toggle();
		if ($(this).is(':visible')) $(this).css('display', 'block');
		$(".items-browser block").css("pointer-events", "");

		//scroll to heading
		$(this).css("position","relative");
		$(this)[0].scrollIntoView();
		$(this).css("position", "sticky");
		 

	}); //end on click

	/* *************************** CUSTOM SECTIONS / CUSTOM BLOCKS HOVER PREVIEW *************************** */
	$("body").on("mouseenter", "#readymade-sections #custom-html-sections block, #basic-blocks #custom-html-blocks block", function() {
		var code = $(this).find("template").html();
		previewFrame.contents().find("#lc-minipreview .lc-minipreview-content").html(code);
		var height = $(this).offset().top - $(document).scrollTop();
		previewFrame.contents().find("#lc-minipreview").css("top", height - 145).show();
	}); //end on hover

	$("body").on("mouseleave", "#readymade-sections #custom-html-sections block, #basic-blocks #custom-html-blocks block", function() {
		previewFrame.contents().find("#lc-minipreview").hide();
	}); //end on hover

	/* *************************** VIEW ALL SECTIONS LINK  *************************** */
	$("body").on("click", ".show-all-sections", function(e) {
		e.preventDefault();
		$("#readymade-sections > h4").show(); 
		$(this).hide();
	}); //end on hover
 



}); //end document ready



//end file. Wow!