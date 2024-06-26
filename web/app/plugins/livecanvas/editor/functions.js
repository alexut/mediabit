 


/* ******************* UTILITY FUNCTIONS ************************* */

//////DEBOUNCE UTILITY  
function debounce(func, wait, immediate) {
	var timeout;
	return function () {
		var context = this,
			args = arguments;
		var later = function () {
			timeout = null;
			if (!immediate) func.apply(context, args);
		};
		var callNow = immediate && !timeout;
		clearTimeout(timeout);
		timeout = setTimeout(later, wait);
		if (callNow) func.apply(context, args);
	};
}


function capitalize(s){
	if (typeof s !== 'string') return ''
	return s.charAt(0).toUpperCase() + s.slice(1)
}


function generateReadableName() {
	const vowels = ['a', 'e', 'i', 'o', 'u'];
	const consonants = [
		'b', 'c', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'm',
		'n', 'p', 'q', 'r', 's', 't', 'v', 'w', 'x', 'y', 'z'
	];

	function getRandomElement(arr) {
		return arr[Math.floor(Math.random() * arr.length)];
	}

	let name = '';
	for (let i = 0; i < 2; i++) {
		name += getRandomElement(consonants);
		name += getRandomElement(vowels);
	}

	return name;
}

function lc_parseParams(str) {

	str = str.split('?')[1]; //eliminate part before ?

	return str.split('&').reduce(function (params, param) {
		var paramSplit = param.split('=').map(function (value) {
			return decodeURIComponent(value.replace('+', ' '));
		});
		params[paramSplit[0]] = paramSplit[1];
		return params;
	}, {});
}


function lc_get_parameter_value_from_shortcode(paramName, theShortcode) {
	theShortcode = theShortcode.replace(/ =/g, '=').replace(/= /g, '=');
	var array1 = theShortcode.split(paramName + '="');
	var significant_part = array1[1];
	if (significant_part === undefined) return "";
	var array2 = significant_part.split('"');
	return array2[0];
}

function determineScrollBarWidth() {

	var $outer = $('<div>').css({
		visibility: 'hidden',
		width: 100,
		overflow: 'scroll'
	}).appendTo('body'),
		widthWithScroll = $('<div>').css({
			width: '100%'
		}).appendTo($outer).outerWidth();
	$outer.remove();
	theScrollBarWidth = widthWithScroll;
}

function getScrollBarWidth() {
	//base case
	if (previewFrameBody.height() <= $(window).height()) return 0;
	else return 100 - theScrollBarWidth;
}

function download(filename, text) {
	var element = document.createElement('a');
	element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text));
	element.setAttribute('download', filename);

	element.style.display = 'none';
	document.body.appendChild(element);

	element.click();

	document.body.removeChild(element);
}

function usingChromeBrowser() {

	var isChromium = window.chrome;
	var winNav = window.navigator;
	var vendorName = winNav.vendor;
	var isOpera = typeof window.opr !== "undefined";
	var isIEedge = winNav.userAgent.indexOf("Edge") > -1;
	var isIOSChrome = winNav.userAgent.match("CriOS");

	if (isIOSChrome) {
		// is Google Chrome on IOS
		return true;
	} else if (
		isChromium !== null &&
		typeof isChromium !== "undefined" &&
		vendorName === "Google Inc." &&
		isOpera === false &&
		isIEedge === false
	) {
		return true;
	} else {
		return false;
	}

}

function myConsoleLog(message) {
	let baseStyles = [
		"color: #fff",
		"background-color: #444",
		"padding: 2px 4px",
		"border-radius: 2px"
	].join(";");
	console.log('%c' + message, baseStyles);
}

function getCssVariablesPrefix() {

	var css_variables_prefix = ""; //default for bs4

	//check if bs5 vanilla: bs-primary is defined
	if (getComputedStyle(previewiframe.contentWindow.document.documentElement).getPropertyValue('--bs-primary')) css_variables_prefix = "bs-";

	//check if mdb: mdb-primary is defined
	if (getComputedStyle(previewiframe.contentWindow.document.documentElement).getPropertyValue('--mdb-primary')) css_variables_prefix = "mdb-";

	return css_variables_prefix;
}


///// SELECTOR GENERATOR /////////////////////////////////
function CSSelector(el) {
	var names = [];
	while (el.parentNode) {
		if (el.nodeName == "MAIN" && el.id == "lc-main") {
			names.unshift(el.nodeName + '#' + el.id);
			break;
		} else {
			if (el === el.ownerDocument.documentElement || el === el.ownerDocument.body) {
				names.unshift(el.tagName);
			} else {
				for (var c = 1, e = el; e.previousElementSibling; e = e.previousElementSibling, c++) { }
				names.unshift(el.tagName + ':nth-child(' + c + ')');
			}
			el = el.parentNode;
		}
	}
	return names.join(' > ');
}

/////////WYSIWYG HARD SANITIZER ////////////////
function sanitize_editable_rich(input) {
	var output = input;
	//output = output.replace(/<\/?span[^>]*>/g, ""); //removed in aug 2020 as we now TAKE CARE OF CONTENT MERGING THAT CREATES USELESS SPANs 200 lineas below

	//kill useless DIVs
	output = output.replace(/<\/?div[^>]*>/g, "");
	//output= output.replace(/&nbsp;/g,"");

	//convert b to strong
	output = output.replace(/<b>/g, "<strong>");
	output = output.replace(/<b c/g, "<strong c"); // case for <b class=
	output = output.replace(/<\/b>/g, "</strong>");

	//convert i to em
	output = output.replace(/<i>/g, "<em>");
	output = output.replace(/<i c/g, "<em c");
	output = output.replace(/<\/i>/g, "</em>");

	//kill useless double tags
	output = output.replace(/<\/em><em>/g, "");
	output = output.replace(/<em> <\/em>/g, " ");
	output = output.replace(/<\/strong><strong>/g, "");
	output = output.replace(/<strong> <\/strong>/g, " ");

	return output;
}

///FILTER HTML BLOCKS / SECTIONS BEFORE PLACING THEM INSIDE THE PAGE
function prepareElement(html) {
	const theRandomName = generateReadableName();
	html = html.replaceAll('-RANDOMID', '-' + theRandomName); ///substitute random IDs for components
	html = html.replaceAll('RANDOMID(',  capitalize(theRandomName) + '(' ); /// for function names
	html = html.replaceAll('RANDOMID (', capitalize(theRandomName) + '(' ); /// for function names
	//html = html.replaceAll('@zero_to_ten@', Math.floor((Math.random() * 10) + 1)); ///substitute random vars for demo images
	html = html.replaceAll('-RANDOMNUMBER', '-' + Math.floor((Math.random() * 10000) + 1)); ///substitute with random number
	return html;
}

///DETERMINE IF CODE  BLOCKS NEED A HARD REFRESH
function code_needs_hard_refresh(new_html) {
	if (new_html.includes("lc-needs-hard-refresh")) return true;
	var bsNative = (previewiframe.srcdoc.includes('/bootstrap-native.min.js">'));
	if (bsNative) if (
		new_html.includes("carousel-item") ||
		new_html.includes("data-toggle=")
	) return true;
	return false;
}

///////////////////////////////////////////////
////////// UTILITIES EVENTS ///////////////////
///////////////////////////////////////////////

const lcDocAvailableEmitter = function(doc) {
	const evt = new CustomEvent('lcDocAvailable', {
		bubbles: true,
		cancelable: true,
		detail: {
			doc: doc
		}
	})
	document.dispatchEvent(evt);
}

// $(document).on('lcUpdatePreview', function(e) {})
const lcUpdatePreviewEmitter = function(details) {
	const evt = new CustomEvent('lcUpdatePreview', {
			bubbles: true,
			cancelable: true,
			detail: details
		})
	document.dispatchEvent(evt);
}


////////////////////////////////////////////////////////////////
////////// CREATE A STORE GLOBAL TO ATTACH TO WINDOW ///////////
////////////////////////////////////////////////////////////////
const lcMainStore = {
	doc: null,
	setDoc(newValue, creation = false) {
		lcDocAvailableEmitter(newValue);
		this.doc = newValue;
	},
	getDoc() {
		return this.doc;
	},
}


window.lcMainStore = lcMainStore;
document.dispatchEvent(new CustomEvent('lcMainStoreReady', {
	bubbles: true,
	cancelable: true,
	detail: {
		store: lcMainStore
	}
}))

////////// MAIN BEHAVIORS //////////////////////////
function loadURLintoEditor(url) {
	fetch(url)
		.then(function(response) {
			return response.text();
		}).then(function(page_html) {
			doc = new DOMParser().parseFromString(page_html, 'text/html');

			// set the doc in the store
			window.lcMainStore.setDoc(doc, true);

			if (!doc.querySelector("main#lc-main")) { alert("The page loading seems to fail. This is generally due to peculiar host environments. Please reach support for advice."); }
			
			original_document_html = getPageHTML(); //for alert exit without saving
			previewiframe.srcdoc = filterPreviewHTML(doc.querySelector("html").outerHTML);
			previewiframe.onload = tryToEnrichPreview();
			saveHistoryStep();
		}).catch(function(err) {
			swal("Error " + err + " fetching URL " + url);
		});
}

function loadStarterintoEditor(url, selector = 'main') {
	fetch(url)
		.then(function (response) {
			return response.text();
		}).then(function (page_html) {
			remote_doc = new DOMParser().parseFromString(page_html, 'text/html'); 
			doc.querySelector("main#lc-main").innerHTML = remote_doc.querySelector(selector).innerHTML;
			updatePreview();
			saveHistoryStep();
		}).catch(function (err) {
			swal("Remote Error   " + err + " fetching URL " + url);
		});
}

function filterPreviewHTML(input){

	//fix stretched links so they don't mess preview
	input=input.replace(/stretched-link/g,'');
	
	//for dynamic templates only
	if (lc_editor_post_type == "lc_dynamic_template") {
		
		//wrap lc_ shortcodes in <lc-dynamic-element> tags
		//TODO: replace with regexp
		input = input.replaceAll("\[lc_", "<lc-dynamic-element hidden>[lc_");
		input = input.replaceAll("\[/lc_", "<lc-dynamic-element hidden>[/lc_");
		input = input.replaceAll("\]", "]</lc-dynamic-element>");
	}

	return input;
}

function tryToEnrichPreview() {
	console.log("tryToEnrichPreview");
	//we should limit calls to this function at startup	
	previewEle = document.getElementById('previewiframe');
	previewFrameBody = previewEle?.contentDocument?.body?.innerHTML || previewEle?.contentWindow?.document?.body?.innerHTML;

	//check iframe is really  loaded and available
	if (previewFrameBody === "" || previewFrameBody === undefined) {
		//not ready yet
		setTimeout(function() {
			console.log("Schedule back");
			tryToEnrichPreview();
		}, 1000);
		return;
	} //end if
	//iframe seems to be ready and accessible
	enrichPreview();
}

function updatePreview() {

	lcUpdatePreviewEmitter({
		previewHtmlUpdated: doc.querySelector("html").outerHTML,
		selector: "html"
	});

	previewiframe.srcdoc = filterPreviewHTML(doc.querySelector("html").outerHTML);
	previewiframe.onload = enrichPreview();
	saveHistoryStep();
}

var enrichPreview = debounce(function() {
	console.log("debounced: enrichPreview");
	previewFrame = $("#previewiframe");
	previewFrameBody = previewFrame.contents().find("body"); //dont add main

	//distance from parent frame, if no menu or no distance we need to increase the labels
	themeMain = document.getElementById("previewiframe").contentWindow.document.getElementById("theme-main");
	themeMainDistance = getDistanceFromParent(themeMain);
	themeNoGutter = themeMainDistance < 1;

	//ADD A CLASS TO BODY EL
	previewFrame.contents().find("body").addClass("livecanvas-is-editing");

	//ADD the iframe CSS stylesheet
	previewFrame.contents().find("head").append($("<link/>", {
        id: "lc-preview-iframe",
        rel: "stylesheet",
		href: lc_editor_root_url + "preview-iframe.css",
		type: "text/css"
	}));

	///ADD eventually STYLE TO IFRAME HEADER
	// previewFrame.contents().find("head").append($("<link/>",  { rel: "stylesheet", href: "https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css", type: "text/css" })); ///ADD ICON FONT TO IFRAME HEADER

	//ADD contextual menus' HTML INTERFACE ELEMENTS TO IFRAME BODY
	previewFrameBody.append($("#add-to-preview-iframe-content").html());
	
	//IN CONTEXTUAL MENUS, REPLACE .fa CLASSES  WITH  INLINE SVG BOOTSTRAP ICONS  
	previewFrame.contents().find(".lc-contextual-menu .fa-bars").removeClass('fa').removeClass('fa-bars').html('<svg style="padding-bottom:1px;" width="1.3em" height="1.3em" viewBox="0 0 16 16" class="bi bi-list" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M2.5 11.5A.5.5 0 0 1 3 11h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4A.5.5 0 0 1 3 7h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4A.5.5 0 0 1 3 3h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/></svg>');
	previewFrame.contents().find(".lc-contextual-menu .fa-code").removeClass('fa').removeClass('fa-code').html('<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-code-slash" fill="currentColor" xmlns="http://www.w3.org/2000/svg">  <path fill-rule="evenodd" d="M4.854 4.146a.5.5 0 0 1 0 .708L1.707 8l3.147 3.146a.5.5 0 0 1-.708.708l-3.5-3.5a.5.5 0 0 1 0-.708l3.5-3.5a.5.5 0 0 1 .708 0zm6.292 0a.5.5 0 0 0 0 .708L14.293 8l-3.147 3.146a.5.5 0 0 0 .708.708l3.5-3.5a.5.5 0 0 0 0-.708l-3.5-3.5a.5.5 0 0 0-.708 0zm-.999-3.124a.5.5 0 0 1 .33.625l-4 13a.5.5 0 0 1-.955-.294l4-13a.5.5 0 0 1 .625-.33z"/></svg>');
	previewFrame.contents().find(".lc-contextual-menu .fa-cog").removeClass('fa').removeClass('fa-cog').html('<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-gear" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8.837 1.626c-.246-.835-1.428-.835-1.674 0l-.094.319A1.873 1.873 0 0 1 4.377 3.06l-.292-.16c-.764-.415-1.6.42-1.184 1.185l.159.292a1.873 1.873 0 0 1-1.115 2.692l-.319.094c-.835.246-.835 1.428 0 1.674l.319.094a1.873 1.873 0 0 1 1.115 2.693l-.16.291c-.415.764.42 1.6 1.185 1.184l.292-.159a1.873 1.873 0 0 1 2.692 1.116l.094.318c.246.835 1.428.835 1.674 0l.094-.319a1.873 1.873 0 0 1 2.693-1.115l.291.16c.764.415 1.6-.42 1.184-1.185l-.159-.291a1.873 1.873 0 0 1 1.116-2.693l.318-.094c.835-.246.835-1.428 0-1.674l-.319-.094a1.873 1.873 0 0 1-1.115-2.692l.16-.292c.415-.764-.42-1.6-1.185-1.184l-.291.159A1.873 1.873 0 0 1 8.93 1.945l-.094-.319zm-2.633-.283c.527-1.79 3.065-1.79 3.592 0l.094.319a.873.873 0 0 0 1.255.52l.292-.16c1.64-.892 3.434.901 2.54 2.541l-.159.292a.873.873 0 0 0 .52 1.255l.319.094c1.79.527 1.79 3.065 0 3.592l-.319.094a.873.873 0 0 0-.52 1.255l.16.292c.893 1.64-.902 3.434-2.541 2.54l-.292-.159a.873.873 0 0 0-1.255.52l-.094.319c-.527 1.79-3.065 1.79-3.592 0l-.094-.319a.873.873 0 0 0-1.255-.52l-.292.16c-1.64.893-3.433-.902-2.54-2.541l.159-.292a.873.873 0 0 0-.52-1.255l-.319-.094c-1.79-.527-1.79-3.065 0-3.592l.319-.094a.873.873 0 0 0 .52-1.255l-.16-.292c-.892-1.64.902-3.433 2.541-2.54l.292.159a.873.873 0 0 0 1.255-.52l.094-.319z"/><path fill-rule="evenodd" d="M8 5.754a2.246 2.246 0 1 0 0 4.492 2.246 2.246 0 0 0 0-4.492zM4.754 8a3.246 3.246 0 1 1 6.492 0 3.246 3.246 0 0 1-6.492 0z"/></svg>');
	previewFrame.contents().find(".lc-contextual-menu .fa-sign-in").removeClass('fa').removeClass('fa-sign-in').html('<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-box-arrow-in-down-right" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M14.5 13a1.5 1.5 0 0 1-1.5 1.5H3A1.5 1.5 0 0 1 1.5 13V8a.5.5 0 0 1 1 0v5a.5.5 0 0 0 .5.5h10a.5.5 0 0 0 .5-.5V3a.5.5 0 0 0-.5-.5H9a.5.5 0 0 1 0-1h4A1.5 1.5 0 0 1 14.5 3v10z"/><path fill-rule="evenodd" d="M4.5 10a.5.5 0 0 0 .5.5h5a.5.5 0 0 0 .5-.5V5a.5.5 0 0 0-1 0v4.5H5a.5.5 0 0 0-.5.5z"/>  <path fill-rule="evenodd" d="M10.354 10.354a.5.5 0 0 0 0-.708l-8-8a.5.5 0 1 0-.708.708l8 8a.5.5 0 0 0 .708 0z"/></svg>');
	previewFrame.contents().find(".lc-contextual-menu .fa-copy").removeClass('fa').removeClass('fa-copy').html('<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-clipboard-plus" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1v-1z"/><path fill-rule="evenodd" d="M9.5 1h-3a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3zM8 7a.5.5 0 0 1 .5.5V9H10a.5.5 0 0 1 0 1H8.5v1.5a.5.5 0 0 1-1 0V10H6a.5.5 0 0 1 0-1h1.5V7.5A.5.5 0 0 1 8 7z"/></svg>');
	previewFrame.contents().find(".lc-contextual-menu .fa-paste").removeClass('fa').removeClass('fa-paste').html('<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-clipboard-check" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1v-1z"/>  <path fill-rule="evenodd" d="M9.5 1h-3a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3zm4.354 7.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 9.793l2.646-2.647a.5.5 0 0 1 .708 0z"/></svg>');
	previewFrame.contents().find(".lc-contextual-menu .fa-files-o").removeClass('fa').removeClass('fa-files-o').html('<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-union" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M0 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v2h2a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-2H2a2 2 0 0 1-2-2V2z"/></svg>');
	previewFrame.contents().find(".lc-contextual-menu .fa-trash").removeClass('fa').removeClass('fa-trash').html('<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-trash" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/><path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4L4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/></svg>');
	previewFrame.contents().find(".lc-contextual-menu .fa-plus").removeClass('fa').removeClass('fa-plus').html('	<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-plus-circle" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8 3.5a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-.5.5H4a.5.5 0 0 1 0-1h3.5V4a.5.5 0 0 1 .5-.5z"/><path fill-rule="evenodd" d="M7.5 8a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1H8.5V12a.5.5 0 0 1-1 0V8z"/><path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/></svg>');
	previewFrame.contents().find(".lc-contextual-menu .fa-arrow-left").removeClass('fa').removeClass('fa-arrow-left').html('<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-left" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M5.854 4.646a.5.5 0 0 1 0 .708L3.207 8l2.647 2.646a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 0 1 .708 0z"/>  <path fill-rule="evenodd" d="M2.5 8a.5.5 0 0 1 .5-.5h10.5a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/></svg>');
	previewFrame.contents().find(".lc-contextual-menu .fa-arrow-right").removeClass('fa').removeClass('fa-arrow-right').html('<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-right" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10.146 4.646a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L12.793 8l-2.647-2.646a.5.5 0 0 1 0-.708z"/>  <path fill-rule="evenodd" d="M2 8a.5.5 0 0 1 .5-.5H13a.5.5 0 0 1 0 1H2.5A.5.5 0 0 1 2 8z"/></svg>');
	previewFrame.contents().find(".lc-contextual-menu .fa-arrow-up").removeClass('fa').removeClass('fa-arrow-up').html('<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-up" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8 3.5a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-1 0V4a.5.5 0 0 1 .5-.5z"/><path fill-rule="evenodd" d="M7.646 2.646a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8 3.707 5.354 6.354a.5.5 0 1 1-.708-.708l3-3z"/></svg>');
	previewFrame.contents().find(".lc-contextual-menu .fa-arrow-down").removeClass('fa').removeClass('fa-arrow-down').html('<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-down" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.646 9.646a.5.5 0 0 1 .708 0L8 12.293l2.646-2.647a.5.5 0 0 1 .708.708l-3 3a.5.5 0 0 1-.708 0l-3-3a.5.5 0 0 1 0-.708z"/><path fill-rule="evenodd" d="M8 2.5a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-1 0V3a.5.5 0 0 1 .5-.5z"/></svg>');	 
	previewFrame.contents().find(".lc-contextual-menu .fa-floppy-o").removeClass('fa').removeClass('fa-floppy-o').html('<svg  width="1em" height="1em"  viewBox="0 0 16 16" fill="currentColor"><path d="M9.293 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.707A1 1 0 0 0 13.707 4L10 .293A1 1 0 0 0 9.293 0zM9.5 3.5v-2l3 3h-2a1 1 0 0 1-1-1zM8.5 7v1.5H10a.5.5 0 0 1 0 1H8.5V11a.5.5 0 0 1-1 0V9.5H6a.5.5 0 0 1 0-1h1.5V7a.5.5 0 0 1 1 0z"></path></svg>');	 
	
	// previewFrame.contents().find("span.fa-xxx").removeClass('fa-xxx').html('');
	
	//ADD TRIGGER FOR ADDING SECTIONS (VINTAGE)
	//previewFrame.contents().find("main#lc-main").after($("#lc-add-section-to-page").html());

	//ADD MINIPREVIEW
	previewFrame.contents().find("main#lc-main").after('<div id="lc-minipreview" style="display: none"><div class="lc-minipreview-content"></div></div>'); //#lc-add-section-to-page").html());

	//if there's only an empty section (user has just reset page) hide the section creating button
	//if (  getPageHTML("main#lc-main")=="<section></section>") {
	//    previewFrame.contents().find("#lc-add-new-container-section-wrap").hide();
	//}
	
	//GET BOOTSTRAP COLORS and paint COLOR WIDGETS
	//check which bootstrap is in use and find out css variables prefix
	
	//loop color widgets and paint each element
	$(".custom-color-widget").each(function(index, the_widget) { //foreach color widget
		$(the_widget).find("span,a[data-class]").each(function(index, span_element) {  //foreach  color element in the widget
			color_variable_name = getCssVariablesPrefix() + $(span_element).attr("title").trim().toLowerCase().replace(' ', '-');  //console.log(color_variable_name); 
			var color_value = getComputedStyle(previewiframe.contentWindow.document.documentElement).getPropertyValue('--' + color_variable_name); 
			if (color_value) $(span_element).css("background",color_value);
		}); //end each 
	}); //end each widget
	
	//SPECIAL CASE WHEN EDITING lc_section or a lc_block cpts : HIDE ADD SECT BUTTON
	if(( previewFrame.contents().find("body").hasClass('lc_section-template'))  || (previewFrame.contents().find("body").hasClass('lc_block-template')) ) {
		$('#primary-tools').hide();
		$('.open-main-html-editor').click();
	}
	
	//INITIALIZE CONTENTEDITABLE DEFAULT PARAGRAPH SEPARATOR  
	previewiframe.contentDocument.execCommand("DefaultParagraphSeparator", false, "p");
	
	//BIND ACTIONS TO PREVIEW
	initialize_live_editing();
	initialize_contextual_menus();
	initialize_contextual_menu_actions();
	initialize_content_building();

	//BIND KEYBOARD EVENTS TO PREVIEW
	previewFrameBody.keydown(function(e) {
		handleKeyboardEvents(e);
	});

	//PREVENT CLICKING AWAY
	previewFrame.contents().on("click", "a", function(e) {
		e.preventDefault();
		e.stopPropagation();
		console.log("Click handled.");
	});

	//HIDE PRELOADER 
	$("#loader").fadeOut(500);

	//RENDER SHORTCODES
	render_dynamic_content("main");  

    //UPDATE TREE IF OPEN
    if ($("#tree-body").is(":visible")) {
        document.getElementById('tree-body').innerHTML = renderTreeHTMLStructure('main#lc-main');
        $('#tree-body').find(".tree-view-item-content-wrapper").first().click(); 
    }

	//ALL IS READY, CHECK IF USER WANTS TO START FROM A READYMADE
	if (lc_editor_post_type != 'lc_block' && lc_editor_post_type != 'lc_section' &&	lc_editor_post_type != 'lc_partial' &&
        lc_editor_main_bootstrap_version == 5 && doc.querySelector("main#lc-main").innerHTML.trim() == "" &&
		(!lc_editor_simplified_client_ui)
	) {
		swal({
			title: "Do you want to start from a readymade template?",
			text: "You can do this later as well from  the Options menu.",
			icon: "info",
			buttons: ["No, I'll build from scratch.", "Yes, browse templates"],
			dangerMode: false,
		})
			.then((willDo) => {
				if (willDo) {
					$(".readymade-pages").click();
				}
			});
	}

}, 400);

function render_dynamic_content (selector){
	if (lc_editor_post_type == "lc_dynamic_template")
		render_dynamic_templating_shortcodes_in(selector);
	else
		render_shortcodes_in(selector);
}

function updatePreviewSectorial(selector) {

	myConsoleLog("updatePreviewSectorial "+selector);
	/*
	if (selector.trim() =='MAIN#lc-main'){
		myConsoleLog("updatePreviewSectorial of main: calling main UpdatePreview");
		updatePreview(); //this does also enrich and save step
		return;
	}
	*/
	lcUpdatePreviewEmitter({
		selector: selector,
		previewHtmlUpdated: filterPreviewHTML(doc.querySelector(selector).outerHTML)
	})

	previewiframe.contentWindow.document.body.querySelector(selector).outerHTML = filterPreviewHTML(doc.querySelector(selector).outerHTML);
	enrichPreviewSectorial(selector);
	saveHistoryStep();
}

var enrichPreviewSectorial = debounce(function(selector) {
	console.log("Heavy task: enrichPreviewSectorial " + selector);
	add_helper_attributes_in_preview();

	//RENDER SHORTCODES  
	render_dynamic_content(selector); 

    //UPDATE TREE IF OPEN
    if ($("#tree-body").is(":visible")) redrawTreePart(selector);
	
}, 400);

 // HISTORY ///////////
 var saveHistoryStep = debounce(function() {
	var today = new Date();
	$("#history-steps").append("<li> "+today.toLocaleTimeString()+ " <template>"+getPageHTML("main")+"</template></li>");
	//localStorage.setItem("last_step_html", getPageHTML());    //auto save on localstorage, eventually
}, 2000);

//QUICK DOCUMENT EDITING SUPPORT FUNCTIONS      ///////////////////////////
function getPageHTML(selector) {
	if (selector === undefined) selector = "html";
	if (!doc.querySelector(selector)) { console.log(selector + " could not be found"); return ""; }
	return (doc.querySelector(selector).innerHTML);
}

function setPageHTML(selector, newValue) {
	doc.querySelector(selector).innerHTML = newValue;
}

function setPageHTMLOuter(selector, newValue) {
	doc.querySelector(selector).outerHTML = newValue;
}

function getAttributeValue(selector, attribute_name) {
	if (selector === undefined || selector === '' || attribute_name === undefined || attribute_name === '') {
		console.log("getAttributeValue is called with an undefined parameter");
		return "";
	}
	return (doc.querySelector(selector).getAttribute(attribute_name));
}

function setAttributeValue(selector, attribute_name, newValue) {
	doc.querySelector(selector).setAttribute(attribute_name, newValue);
}

function setEditorPreference(option_name, option_value) {
	editorPrefsObj[option_name] = option_value;
	editorPreferencesString = JSON.stringify(editorPrefsObj);
	localStorage.setItem("lc_editor_prefs_json", editorPreferencesString);
}

/* ******************* KEYBOARD EVENTS HANDLING  ******************* */
function handleKeyboardEvents(e){

	//HANDLE CMD-ALT SOMETHING

	//enable experimental features: CTRL ALT E
	if (e.keyCode == 69 && e.ctrlKey && e.altKey) {	$(".lc-experimental-feature").show(); return;}

	//HANDLE CMD-SOMETHING
	if (e.ctrlKey || e.metaKey) {
		switch (String.fromCharCode(e.which).toLowerCase()) {
			case 's':
				e.preventDefault();
				$('#main-save').trigger("click");
				break;

			case 'p':
				e.preventDefault();
				updatePreview();
				break;

			case 'e':
				e.preventDefault();
				if (lc_editor_simplified_client_ui) return;
				$(".open-main-html-editor").click();
				break;

			case 'l':
				var text = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc et metus id ligula malesuada placerat sit amet quis enim. Aliquam erat volutpat. In pellentesque scelerisque auctor. Ut porta lacus eget nisi fermentum lobortis. Vestibulum facilisis tempor ipsum, ut rhoncus magna ultricies laoreet. Proin vehicula erat eget libero accumsan iaculis.';
				var tmp = document.createElement("DIV");
				tmp.innerHTML = text;
				//text=text.replace(/(<([^>]+)>)/ig,"");
				text = tmp.textContent || tmp.innerText;
				previewiframe.contentDocument.execCommand('insertHTML', false, text);
				break;
				
		}
	}
	//HANDLE CMD ALONE
	//ON METAKEY PRESS, HIDE CONTEXTUAL MENUS	
	if(e.metaKey)  previewFrame.contents().find(".lc-contextual-menu").hide();
	
	// HANDLE ESC KEY PRESS
	if (e.keyCode == 27) { 
		e.preventDefault();
		$(".close-sidepanel").click();
		$(".lc-editor-close").click();
		$("#readymades-close").click();
		previewFrame.contents().find(".lc-contextual-menu").hide();
	}

}
 


/* ******************* SIDE PANEL  ******************* */

//FUNCTION TO OPEN SIDE PANEL TO EDIT AN ITEM IDENTIFIED BY A GIVEN SELECTOR,
// USE ME
function openSidePanel(theSelector) {

    //check if it is a layout element
    if (getLayoutElementType(theSelector)) {
        revealSidePanel("edit-properties", theSelector, getLayoutElementType(theSelector));
        return;
    }

    //check if it an "clickable" html element
    if (getHtmlElementType(theSelector)) {
        revealSidePanel(getHtmlElementType(theSelector), theSelector);
        return;
    }

    //fallback: if it's neither, but is a div, allow simple editing
    if (doc.querySelector(theSelector).matches("div")) {
        revealSidePanel("edit-properties", theSelector);
    }

    //if there's nothing to do
    alert("Please select parent element to edit properties");
}

//FUNCTION TO RETURN WHICH LAYOUT ELEMENT IS IT, READING FROM FRAMEWORK CONFIGURATION LAYOUT ELEMENTS
function getLayoutElementType(theSelector) {
    //loop all layout_elements and see if theSelector matches
    for (const [name, data] of Object.entries(theFramework.layout_elements)) {
        if (doc.querySelector(theSelector).matches(data.selector)) {
            //selector is matching, return layout element name, with a small exception for main sections
            return (name.replace("Main", "Section"));
        }
    }
    //if not found
    return false;
}

//FUNCTION TO RETURN IF ITS AN EDITABLE ELEMENT AND WHICH ELEMENT IS IT, READING FROM EDITOR CONFIG
function getHtmlElementType(theSelector) {
    //loop all editable_elements and see if theSelector matches
    for (const [name, data] of Object.entries(theEditorConfig.editable_elements)) {
        if (doc.querySelector(theSelector).matches(data.selector)) {
            //selector is matching,  
            return (name);
        }
    }
    //if not found
    return false;
}

//classic function to make the panel appear
function revealSidePanel(item_type, selector, layoutElementName="") {
	
	$(".lc-editor-close").click();//close code editor

	//hide ux since well be moving the thing
	previewFrame.contents().find(".lc-contextual-menu").fadeOut(500);
	previewFrame.contents().find(".lc-highlight-mainpart").removeClass("lc-highlight-mainpart");
	previewFrame.contents().find(".lc-highlight-container").removeClass("lc-highlight-container");
	previewFrame.contents().find(".lc-highlight-column").removeClass("lc-highlight-column");
	previewFrame.contents().find(".lc-highlight-row").removeClass("lc-highlight-row");
	previewFrame.contents().find(".lc-highlight-block").removeClass("lc-highlight-block");

	$(".nanotoolbar").hide(); //hide text editing tools

	//hide all "panels"
	$("#sidepanel > section").hide(); 

	//set a data attribute to identify the element we're editing
	var sectionSelector = "#sidepanel > section[item-type=" + item_type + "]";
	$(sectionSelector).attr("selector", selector);

	//show only appropriate properties relevant for the current item type. Not currently needed.
	//$(sectionSelector).find('*[show-on]').hide();
	//$(sectionSelector).find('*[show-on="' + item_type + '"]').show();
	
	//inits main field values
	initializeSidePanelSection(sectionSelector, layoutElementName); 
	$(sectionSelector).show(); //triggers init of other fields

	//move the preview
	$("#previewiframe-wrap").addClass("push-aside-preview");

	$("#sidepanel form").scrollTop(0); //scroll panel to top
	//animate and show the panel
	$("#sidepanel").hide().fadeIn(300); //addClass("slideInLeft")

}
//distance of div from parent
function getDistanceFromParent(el) {
	if(!el) return 0;
	var rect = el.getBoundingClientRect();
	return rect.top - el.parentNode.getBoundingClientRect().top;
}

//sets the values of the input fields in the side panel upon opening it 
function initializeSidePanelSection(sectionSelector, layoutElementName) {
	
	theSection = $(sectionSelector);
	var selector   = theSection.attr("selector");
	
    myConsoleLog("Initialize panel for " + theSection.attr("item-type") + ' ' + layoutElementName);
	
	//INTERFACE BUILDING: build the edit properties panel
	if (theSection.attr("item-type")=="edit-properties") {

		document.querySelector('#the-dynamic-editing-form').innerHTML = buildPropertyNavigation(layoutElementName) + 
			buildPropertyWidgets(layoutElementName) + 
			document.querySelector('#sidebar-section-form-common-elements').innerHTML;
		
		// check why
		//const iconName = 'panel-title-' + layoutElementName.toLowerCase;
		//alert(layoutElementName);

		//change window title 
		document.querySelector("#sidepanel section[item-type='edit-properties'] h1").innerHTML = `
			${(getCustomIcon('panel-title-' + layoutElementName))}${layoutElementName} Properties
		`;

		//initialize menu
		$('#the-dynamic-editing-form .sidebar-panel-navigation a:first').click(); 
	}
	
	//INPUTS: initialize value for text fields /////////
	//foreach input field
	theSection.find("*[attribute-name]").each(function(index, element) {
		var attribute_name = $(element).attr('attribute-name');
		if (attribute_name === 'html') {
			$(element).val(getPageHTML(selector, attribute_name).trim());
		} else {
			$(element).val(getAttributeValue(selector, attribute_name));
		}
	}); //end each
    
    //COLOR WIDGETS: initialize highlight active color
	theSection.find(".custom-color-widget").each(function(index, the_widget) { //foreach color widget
		$(the_widget).find("span.active").removeClass("active");
		//foreach  color element in the widget
		var color_assigned=false;
		$(the_widget).find("span").each(function(index, span_element) {
			span_value = $(span_element).attr("value").trim(); //console.log(span_value);
			if (span_value !== "" && doc.querySelector(selector).classList.contains(span_value)) { 
				//CASE AN ACTIVE COLOR WAS FOUND
				$(span_element).addClass("active"); 
				color_assigned=true; 
			}
		}); //end each option
		
		if(!color_assigned) {
			//CASE NO COLOR ASSIGNED
			$(the_widget).find("span[value='']").addClass("active"); 
		}
	}); //end each select

	//NUMBER WIDGETS: initialize values for spacings and col widths
	theSection.find(".activate-input-numbers input[type=number]").each(function (index, el) {
		$(el).val(""); //init
		var class_prefix = $(el).attr('name');
		var elem = doc.querySelector(selector);
		for (let i = -50; i <= 50; i++) {
			var the_class = class_prefix + "-" + i.toString().replace("-", "n");
			if (elem && elem.classList.contains(the_class)) $(el).val(i);
		}
	});

	//SELECT WIDGETS: initialize value for select[target=classes]
	theSection.find("select[target=classes]").each(function(index, select_element) { //foreach select in section
		//apply a default starter option
		$(select_element).find("option:first").prop('selected', true);
		//foreach option in select
		$(select_element).find("option").each(function(index, option_element) {
			option_value = $(option_element).val().trim(); //console.log(option_value);
			if (option_value !== "" && doc.querySelector(selector).classList.contains(option_value)) $(option_element).prop('selected', true);
		}); //end each option

	}); //end each select

	//ICON WIDGETS / RADIO: initialize values
	theSection.find("single-property[data-widget='icons']").each(function(index, select_element) { //foreach select in section
		 
		//foreach option in select
		$(select_element).find("input").each(function(index, option_element) {
			option_value = $(option_element).val().trim(); //console.log(option_value);
			if (option_value !== "" && doc.querySelector(selector).classList.contains(option_value)) $(option_element).prop('checked', true);
		}); //end each option

	}); //end each select

	//CHECK IF ELEMENT IS LINKED, IF SO, UPDATE LINK TARGET FIELD
	if(theSection.find("input.link-target-url").length) if(doc.querySelector(selector).parentNode.tagName==="A") theSection.find(".link-target-url").val(  doc.querySelector(selector).parentNode.getAttribute("href")  ); else theSection.find(".link-target-url").val("");

	//FAKE SELECTS: close all of them
	theSection.find("ul.ul-to-selection.opened").removeClass("opened");

	//FAKE SELECT BACKGROUNDS: initialize value
	var bg_style = previewFrame.contents().find(selector).css("background"); //doc.querySelector(selector).getAttribute("style");
	theSection.find("ul#backgrounds li.first").attr("style", "background:" + bg_style);

	//CUSTOM INIT FOR SHAPE DIVIDERS: initialize value 
	var bottom_shape_divider_element = doc.querySelector(selector + ' .lc-shape-divider-bottom');
	if (bottom_shape_divider_element) shape_html = bottom_shape_divider_element.outerHTML;
	else shape_html = "";
	theSection.find("ul#shape_dividers li.first").html(shape_html);

	//CUSTOM INIT FOR  IMAGES  
	if (theSection.attr("item-type") == "image") {
		//Update Image Preview
		theSection.find(".preview-image").css("background-image", "url(" + theSection.find("*[attribute-name=src]").val() + ")");
		//check if imgix widget is appropriate
		if (theSection.find("*[attribute-name=src]").val().includes("unsplash.com")) theSection.find(".imgix-fx").show();
		else theSection.find(".imgix-fx").hide();
	}

	//CUSTOM INIT FOR BACKGROUNDS  
	if (theSection.attr("item-type") == "background") {
		var bg_url = "";
		if (previewFrame.contents().find(selector).css("background-image").match(/"([^']+)"/) != null)
			bg_url = previewFrame.contents().find(selector).css("background-image").match(/"([^']+)"/)[1];
		else bg_url = "#";
		//update image preview    
		theSection.find(".preview-image").css("background-image", "url(" + bg_url + ")");
		//update bg url input field
		theSection.find("input[name=background-image]").val(bg_url).attr("data-old-url", bg_url);
		//check if imgix widget is appropriate
		if (bg_url.includes("unsplash.com")) theSection.find(".imgix-fx").show();
		else theSection.find(".imgix-fx").hide();
	}

	//CUSTOM INIT FOR BS ICONS
	if (theSection.attr("item-type") == "svg-icon") {
		var icon_width = getAttributeValue(selector , "width");
		if(icon_width){
			//em case
			theSection.find("input[name='size']").val(icon_width.replace("em","")); //set slider
			theSection.find(".size-feedback").text(icon_width);//set feedback label
			theSection.find("select[name=unit]").val("em");//set unit select
		}	else {
			//.rws case
			theSizingClass=false;
			for (i = 0; i < 50; i++) if(doc.querySelector(selector).classList.contains("rws-"+i)) theSizingClass = "rws-"+i; 
			if (theSizingClass) {
				theSection.find("input[name='size']").val(theSizingClass.replace("rws-","")); //set slider
				theSection.find(".size-feedback").text(theSizingClass);//set feedback label
			}
			theSection.find("select[name=unit]").val("rws");//set unit select
		}
		
	}
	
	//CUSTOM INIT FOR VIDEO EMBEDS
	if (theSection.attr("item-type") == "video-embed") {
		var iframe_url = getAttributeValue(selector + " iframe", "src");
		theSection.find("input[name='iframe_src']").val(iframe_url);
	}
	
	//CUSTOM INIT FOR VIDEO BACKGROUND
	if (theSection.attr("item-type") == "video-bg") {
		var video_url = getAttributeValue(selector + " video source", "src");
		theSection.find("input[name='video_mp4_url']").val(video_url);
	}

	//CUSTOM INIT FOR GOOGLE MAP EMBED
	if (theSection.attr("item-type") == "gmap-embed") {
		var iframe_url = getAttributeValue(selector + " iframe", "src");
		var params = lc_parseParams(iframe_url);
		theSection.find("input[name='address']").val(params['q']);
		theSection.find("input[name='zoom']").val(params['z']);
	}

	//CUSTOM INIT FOR SHORTCODES PANEL
	if (theSection.attr("item-type") == "shortcode") {
		//populate shortcode field
		var theShortcode = doc.querySelector(selector).innerHTML.trim();
		theSection.find("*[name=shortcode_text]").val(theShortcode);
	}

	//CUSTOM INIT FOR POSTS LOOP
	if (theSection.attr("item-type") == "posts-loop") {
		var theShortcode = doc.querySelector(selector).innerHTML;
		//loop all input items to initialize input fields
		theSection.find("*[name]").each(function(index, el) {
			fieldName = $(el).attr("name");
			fieldValue = lc_get_parameter_value_from_shortcode(fieldName, theShortcode);
			//console.log("set "+fieldName+" to "+fieldValue);
			if (fieldValue !== "") theSection.find("[name=" + fieldName + "]").val(fieldValue);
		}); //end each    

		//trigger output view change - see side-panel-advanced-helpers
		$("#sidepanel section[item-type='posts-loop'] .lc-post-output-tabcontent select[name=output_view]").change();

	}

	//CUSTOM INIT FOR ANIMATIONS
	if (theSection.find("select[name=aos_animation_type]").length != 0) {
		var animation_type = getAttributeValue(selector, "data-aos");
		$('#sidepanel select[name=aos_animation_type] option[value=""]').prop('selected', true); //default
		if (animation_type != "" && $('#sidepanel select[name=aos_animation_type] option[value=' + animation_type + ']').length > 0)
			$('#sidepanel select[name=aos_animation_type] option[value=' + animation_type + ']').prop('selected', true);
	}

    //ADD MORE INITS...

} //end function

/* ******************* SHORTCODES ************************* */
function render_shortcode(selector, shortcode) {
    
    urlParams = new URLSearchParams(window.location.search); 
    
	$.post(
		lc_editor_saving_url, {
			'action': 'lc_process_shortcode',
			'shortcode': shortcode,
            'post_id': urlParams.get('demo_id') ?? lc_editor_current_post_id,
		},
		function(response) {
			//console.log('The server responded: ', response);
			previewFrame.contents().find(selector).html(response).removeClass("live-shortcode").addClass("lc-rendered-shortcode-wrap");
		}
	);
}

function render_shortcodes_in(selector) {
	
	previewiframe.contentDocument.querySelector(selector).querySelectorAll(".live-shortcode").forEach((wrap) => {
		render_shortcode(CSSelector(wrap), wrap.innerHTML);
	});
}

//FOR DYNAMIC TEMPLATING SHORTCODES

function render_dynamic_templating_shortcodes_in(selector) {

	previewiframe.contentDocument.querySelector(selector).querySelectorAll("lc-dynamic-element").forEach((element) => {
		
		urlParams = new URLSearchParams(window.location.search); 

		$.post(
			lc_editor_saving_url, {
			'action': 'lc_process_dynamic_templating_shortcode',
			'shortcode': element.innerHTML,
			'post_id': lc_editor_current_post_id,
			'demo_id': (urlParams.get('demo_id') ?? false),
		},
			function (response) {
				//console.log('The server responded: ', response);
				element.outerHTML = response;
				//element.classList.add('lc-rendered-shortcode-wrap'); //useless cause reference is lost
				//element.removeAttribute("hidden"); //useless when replacing outerhtml
			}
		);
		
	});
}

 

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////// INITIALIZE LIVE TEXT EDITING BEHAVIOURS /////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 

function initialize_live_editing() {  

	//previewiframe.contentDocument.querySelector("h1").style.display = "none"; // WOULD HIDE ANY H1
	//console.log("Start initialize_live_editing function");
	
	////OBJECTS LIVE  EDITING ///
	//ON CLICK OF LC-HELPER  ITEMS 
	previewFrameBody.on("click", "*[lc-helper]:not(.lc-rendered-shortcode-wrap *)", function(e) {
		if (e.altKey) return;

		e.preventDefault();
		e.stopPropagation();

		var item_type = $(this).attr("lc-helper");
		var selector = CSSelector($(this)[0]);
		console.log("open lc helper panel for " + item_type);
		revealSidePanel(item_type, selector);
	});
	
	////TEXT LIVE  EDITING ////
	
 
	//ON CLICK OF TEXT-EDITABLE ITEMS:
	previewFrameBody.on("click", "[editable=rich]:not(.lc-rendered-shortcode-wrap *),[editable=inline]:not(.lc-rendered-shortcode-wrap *)", function(e) {

		console.log("Clicked editable text");
		e.preventDefault();  
		e.stopPropagation();
		
		$(this).attr("contenteditable", "true").focus().addClass("lc-content-is-being-edited"); //enable contenteditable and focus the area
		
		$(".nanotoolbar").hide(); 
		$("#ww-toolbar").show().attr("selector", CSSelector($(this)[0]));
		$("#sidepanel .close-sidepanel").click(); //close side panel
		$(".lc-editor-close").click(); //close code editor
		
		//show top tools according to element type
		if ($(this).attr("editable") == "rich") {
			$("#ww-toolbar [data-command]").show();
		}
		if ($(this).attr("editable") == "inline") {
			$("#ww-toolbar [data-command]").hide();
			$("#ww-toolbar [data-suitable='inline']").show();
		}
		//if classes toolbar was active, show it again
		if($("#toggle-classes-submenu").hasClass("is-active")) $("#classes-palette").show();

	}); //end on click


	//ON BLUR OF EDITABLE ITEMS:
	//previewFrameBody.on("blur", "[editable=rich],[editable=inline]", function(e) {
	$('#previewiframe').contents().find("body").on("blur", "[editable=rich],[editable=inline]", function(){
		console.log("Handling Blur event: Reapplying content changes on code");
		console.log("Blur event on " + $(this).attr("editable") + " element");
		
		$(this).removeAttr("contenteditable").removeClass("lc-content-is-being-edited");

		$(this).find("*[style]").removeAttr("style"); //kill any inline styling
		$(this).find("*[lc-helper]").removeAttr("lc-helper"); //kill lc-helper attributes if present
		
		var newValue = $(this).html(); //get field content from preview  
		if ($(this).attr("editable") == "rich") newValue = sanitize_editable_rich(newValue); //kill shit like span when deleting

		var selector = CSSelector($(this)[0]); //generate selector
		
		if (selector === "") {console.log("Warning: Empty selector on blur"); return; } 

		doc.querySelector(selector).innerHTML = newValue; //update the content
		
		//if were dealing with an editable inline element, take care of external classes too
		if ($(this).attr("editable") == "inline") {
			var theClasses=$(this).attr("class");  //alert(theClasses);
			doc.querySelector(selector).className = theClasses; //update the classes
		} 

		// SECTORIAL PREVIEW UPDATE for peace of mind
		//console.log($(this).parent().html());
		console.log("SECTORIAL PREVIEW UPDATE");
		updatePreviewSectorial(CSSelector($(this)[0])); 
		
		//hide top tools since they are not needed anymore
		$("#ww-toolbar").hide();
		$("#classes-palette").hide();
		//$("#toggle-classes-submenu").removeClass("is-active");
	});
	
	//ON SELECTION CHANGE, HIGHLIGHT APPROPRIATE TOOLBAR ICONS. A good vanilla js exercise :)
	previewiframe.contentDocument.onselectionchange = function() {

		console.log("onselectionchange triggered");
		// 1. hilite active command
		$("#ww-toolbar a[data-command]").removeClass("is-active"); //remove all highlights for cases #1 and #2
		
		const array_commands = ['bold', 'italic', 'insertUnorderedList', 'insertOrderedList'];

		array_commands.forEach(command_name => {
			if (previewiframe.contentDocument.queryCommandState(command_name))document.querySelector("#ww-toolbar a[data-command="+command_name+"]").classList.add("is-active"); 
		});
		
		// 2. hilite active tag
		var el = previewiframe.contentDocument?.getSelection()?.focusNode?.parentNode; 
		
		const array_tag_names = ['p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'createlink', 'bold'];

		array_tag_names.forEach(tag_name => {
			currentTagName = tag_name;
			switch(tag_name) {
				case 'createlink':
					currentTagName = 'a';
					break;
				case 'bold':
					currentTagName = 'strong';
					break;
			}
			if (el?.nodeName?.toLowerCase() == currentTagName){
				document.querySelector("#ww-toolbar a[data-command="+tag_name+"]").classList.add("is-active"); 
			}
		});
		
		// 3. hilite buttons for active classes
		const classLinks = document.querySelectorAll("#classes-palette a[data-class]");
		for (let index= 0; index < classLinks.length; index++) {
			
			if (el.classList.contains(classLinks[index].getAttribute("data-class"))) classLinks[index].classList.add("is-active"); else classLinks[index].classList.remove("is-active"); 
		}
	};

	//TAKE CARE OF CONTENT MERGING THAT CREATES USELESS SPANs //ATTENTION
	previewFrameBody.on('DOMNodeInserted', " *[editable=rich]", $.proxy(function(e) {
		if (e.target.tagName == "SPAN" ) {
		  var helper = $("<b>helper</b>");
	
		  $(e.target).before(helper);
	
		  helper.after($(e.target).contents());
		  helper.remove();
	
		  $(e.target).remove();
		}
	}));
	
	//expand anchor tag if text is right before or next to it
	function lc_expand_anchor_tag(key) {

		if (key == '' || key == 'undefined' || !key) return;
		//if empty we need to return
		if (isEmpty(key)) return;
		
		sel = previewiframe.contentDocument.getSelection();
		focus = sel.focusNode;
		before = focus.previousElementSibling;
		after = focus.nextElementSibling;
		
		//check if focus contains at least one space at the left and return
		if(focus.textContent.match(/^\s+/g)) return;
		if(focus.textContent.match(/\s+$/g)) return;

		//if before or after are anchors, expand them
		if (before?.tagName === "A") {
			before.innerHTML += key;
			focus.remove();
		}

		if (after?.tagName === "A") {
			after.innerHTML = key + after.innerText;
			//substring the key.length from focus
			newString = focus.textContent.substring(0, focus.textContent.length - key.length);
			focus.nodeValue = newString;
			range = document.createRange();
			range.setStart(sel.focusNode, newString.length);
			range.setEnd(sel.focusNode, newString.length);
			range.deleteContents();
			//Move the caret to end of replace text
			sel.collapse(sel.focusNode, newString.length);
		}
	}

	/**
	 * string.trim() polyfill
	 * 
	 * @param {str} str 
	 * @returns 
	 */
	function isEmpty(str) {
		str = str.trim();
		return (!str || str.length === 0 );
	}

	/**
	 * clipboardAPI
	 * 
	 * @param {event} e 
	 */
	async function pasteEvent(e) {
		const text = await navigator.clipboard.readText();
		lc_expand_anchor_tag(text);
	}

	/**
	 * listen pasted event or use pasteEvent()
	 */
	/*
	previewFrameBody.on('paste', function(e){
		if (!e.clipboardData?.getData) {
			pasteEvent(e);
			return;
		}
		lc_expand_anchor_tag(e?.oringalEvent?.clipboardData?.getData('Text'));
	});
	*/

	//check editing of elements
	/*
	previewFrameBody.on('input', function(el){
		action = el.originalEvent.inputType;
		if (!action.length) return;
		key = el?.originalEvent?.data;

		switch(action) {
			case "insertText":
				console.log("insertText");
				//check if editing next to anchor tag
				//lc_expand_anchor_tag(key);
				break;
			case "insertLineBreak":
				console.log("insertLineBreak");
			case "insertParagraph":
				console.log("insertParagraph");
				break;
			case "deleteContentBackward":
				//@improve check if anchor tag is going to be deleted and ask for confirmation?
				console.log('deleteContentBackward');
				console.log(lc_check_if_anchor_tag_selected());
				break;
			case "deleteContentForward":
				console.log('deleteContentForward');
				break;
			case "deleteByCut":
				console.log('deleteByCut');
				break;
			case "deleteByDrag":
				console.log('deleteByDrag');
				break;
			case "deleteByComposition":
				console.log('deleteByComposition');
				break;
			case "insertFromDrop":
				console.log('insertFromDrop');
				break;
			case "insertFromPaste":
				console.log('insertFromPaste');
				break;
			default:
				console.log("default");
				break;
		}
	});
	*/
	
	// PASTE helper // 
	previewFrameBody.on('paste', " *[editable]", function(e) {
		e.preventDefault(); //alert("paste intercept");
		var text = '';
		if (e.clipboardData || e.originalEvent.clipboardData) {
			text = (e.originalEvent || e).clipboardData.getData('text/plain');
		} else if (window.clipboardData) {
			text = window.clipboardData.getData('Text');
		}
		//alert(text);
		var tmp = document.createElement("DIV");
		tmp.innerHTML = text;
		//text=text.replace(/(<([^>]+)>)/ig,"");
		text = tmp.textContent || tmp.innerText;
		text = text.replace(/\n/g, ' </p><p>');

		//if (document.queryCommandSupported('insertText')) {
		previewiframe.contentDocument.execCommand('insertHTML', false, text);
		// } else {
		//   document.execCommand('paste', false, text);
		// }
	});

	//TAKE CARE OF INLINE-EDITABLE NEWLINE  / ENTER KEY  
	previewFrameBody.on("keydown", ' *[editable="inline"]', function(e) {
		if (e.keyCode === 13) {
			previewiframe.contentDocument.execCommand('insertHTML', false, '<br>');
			return false;
		}
	});

	//TAKE CARE OF RICH-EDITABLE when field gets empty
	previewFrameBody.on("keyup", '[editable="rich"]', function() {
		if ($(this).html() === "") {
			$(this).html("<p>Enter some text...</p>");
			previewiframe.contentDocument.execCommand('selectAll', false, null);
		}  
	});



} //end init editor func


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////// INITIALIZE CONTEXTUAL  MENUS: POSITIONING  //////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function add_helper_attributes_in_preview() {
	/* allows some standard items and framework items to be linked to helper windows  */

	//images
	previewFrame.contents().find("body main img").attr("lc-helper", "image");
	//FA4 icons
	previewFrame.contents().find("body main i.fa").attr("lc-helper", "icon");
	//BS icons
	previewFrame.contents().find("body main svg.bi").attr("lc-helper", "svg-icon");
	//buttons
	previewFrame.contents().find("body main .btn, body main button").attr("lc-helper", "button");
	//carousels
	//previewFrame.contents().find("body main .carousel").attr("lc-helper","carousel");

}

if (typeof initialize_contextual_menus !== "function"){
	function initialize_contextual_menus(scope_selector) {

		//FRAMEWORK SETTINGS //////////////
		lc_main_parts_selector = theFramework.layout_elements.Main.selector;
		lc_containers_selector = theFramework.layout_elements.Container.selector;
		lc_rows_selector = theFramework.layout_elements.Row.selector;
		lc_columns_selector = theFramework.layout_elements.Column.selector;
		lc_blocks_selector = theFramework.layout_elements.Block.selector;

		//MICRO TESTING JS
		/*
		previewiframe.contentDocument.querySelectorAll(lc_blocks_selector).addEventListener("mouseenter", function( event ) {
		alert('lc_blocks_selector');
		});
		*/

		////////////////////////////// PLACE CONTEXTUAL MENUS WHEN  HOVERING GRID ELEMENTS //////////////////////
		
		if (lc_editor_simplified_client_ui) return;

        //MOUSE ENTERS ANY ELEMENT: HIGHLIGHT CORRESPONDING TREE VIEW ITEM
        previewFrameBody.on("mouseenter", "main#lc-main *:not('.lc-contextual-menu')", function (e) {
            var selector = CSSelector($(this)[0]);
            //IF TREE IS OPEN
            if ($("#tree-body").is(":visible") && $("#tree-body .tree-view-item[data-selector='" + selector + "']").is(":visible")) {
                //highlight item in tree
                $("#tree-body .tree-view-item.active").removeClass("active");
                $("#tree-body .tree-view-item[data-selector='" + selector + "']").addClass("active");
                //scroll tree to current item
                //document.querySelector("#tree-body li .tree-view-item[data-selector='" + selector + "']").scrollIntoView({ behavior: "smooth"  });
            }
        });

        //MOUSE ENTERS TREE VIEW: UN-HIGHLIGHT  TREE VIEW ITEM
        $("body").on("mouseenter", "#tree-body", function (e) { 
            //IF TREE IS OPEN
            if ($("#tree-body").is(":visible")) {
                //un-highlight item in tree
                $("#tree-body .tree-view-item.active").removeClass("active");     
            }
        });

		//MOUSE ENTERS PAGE PARTs (SECTIONS)  ////////////////////////
		previewFrameBody.on("mouseenter", lc_main_parts_selector, function(e) {
			if ($(this).closest(".lc-rendered-shortcode-wrap").length > 0) return; //exit if we're hovering a shortcode
			if(e.metaKey) return; // exit if cmd is pressed
			if ($(this).attr("ID")=="global-footer")
				previewFrame.contents().find("#lc-contextual-menu-mainpart .lc-contextual-title span").text("Footer Section");
					else previewFrame.contents().find("#lc-contextual-menu-mainpart .lc-contextual-title span").text("Section");
			//<i class="fa fa-bars" aria-hidden="true"></i> Section
			//if($(".lc-contextual-window").is(":visible")) return;

			previewFrame.contents().find("#lc-contextual-menu-mainpart .lc-contextual-actions").hide();
			var top = $(this).offset().top; //-previewFrame.contents().scrollTop();
			var left = $(this).offset().left;
			//var right = previewFrame.width() - ($(this).offset().left + $(this).outerWidth())-15;

			var selector = CSSelector($(this)[0]);
			//console.log(selector);
			//var elHeight=previewFrame.contents().find("#lc-contextual-menu-container").outerHeight();
			previewFrame.contents().find("#lc-contextual-menu-mainpart").css({
				'top': top,
				'left': left,
				/* 'right':right */
			}).show().attr("selector", selector);
			previewFrame.contents().find(".lc-highlight-mainpart").removeClass("lc-highlight-mainpart"); //for security
			previewFrame.contents().find(selector).addClass("lc-highlight-mainpart");

			//hl columns new
			//previewFrame.contents().find(selector+" *[class^='col-']").addClass("lc-highlight-column"); //is it useful?


		}); //end function

		//MOUSE LEAVES PAGE PART
		previewFrameBody.on("mouseleave", lc_main_parts_selector, function() {
			//console.log('go out of container');
			var selector = CSSelector($(this)[0]);
			if (previewFrame.contents().find('#lc-contextual-menu-mainpart').is(":hover")) return;
			if (previewFrame.contents().find('#lc-contextual-menu-block').is(":hover")) return;
			previewFrame.contents().find("#lc-contextual-menu-mainpart .lc-contextual-actions").hide();
			previewFrame.contents().find("#lc-contextual-menu-mainpart").hide();

			$(this).removeClass("lc-highlight-mainpart");
			//hl columns new
			previewFrame.contents().find(selector + " *[class^='col-']").removeClass("lc-highlight-column");

		}); //end function




		//MOUSE ENTERS CONTAINER ////////////////////////
		previewFrameBody.on("mouseenter", lc_containers_selector, function(e) {

			if ($(this).closest(".lc-rendered-shortcode-wrap").length > 0) return; //exit if we're hovering a shortcode
			if(e.metaKey) return; // exit if cmd is pressed
			//if($(".lc-contextual-window").is(":visible")) return;
			previewFrame.contents().find("#lc-contextual-menu-container .lc-contextual-actions").hide();
			var top = $(this).offset().top; //-previewFrame.contents().scrollTop();
			//var left= $(this).offset().left;
			var right = previewFrame.width() - ($(this).offset().left + $(this).outerWidth()) - getScrollBarWidth();

			var selector = CSSelector($(this)[0]);
			//console.log(selector);
			//var elHeight=previewFrame.contents().find("#lc-contextual-menu-container").outerHeight();
			previewFrame.contents().find("#lc-contextual-menu-container").css({
				'top': top,
				/* 'left_NO':left, */ 'right': right
			}).show().attr("selector", selector);
			previewFrame.contents().find(".lc-highlight-container").removeClass("lc-highlight-container"); //for security
			previewFrame.contents().find(selector);

			//hl columns new
			//previewFrame.contents().find(selector+" *[class^='col-']").addClass("lc-highlight-column"); //is it useful?


		}); //end function

		//MOUSE LEAVES CONTAINER
		previewFrameBody.on("mouseleave", lc_containers_selector, function() {
			//console.log('go out of container');
			var selector = CSSelector($(this)[0]);
			if (previewFrame.contents().find('#lc-contextual-menu-container').is(":hover")) return;
			if (previewFrame.contents().find('#lc-contextual-menu-block').is(":hover")) return;
			previewFrame.contents().find("#lc-contextual-menu-container .lc-contextual-actions").hide();
			previewFrame.contents().find("#lc-contextual-menu-container").hide();

			$(this).removeClass("lc-highlight-container");
			//hl columns new
			previewFrame.contents().find(selector + " *[class^='col-']").removeClass("lc-highlight-column");

		}); //end function

		//MOUSE ENTERS ROW ////////////////////////
		previewFrameBody.on("mouseenter", lc_rows_selector, function(e) {
			if ($(this).closest(".lc-rendered-shortcode-wrap").length > 0) return; //exit if we're hovering a shortcode
			if(e.metaKey) return; // exit if cmd is pressed
			//if($(".lc-contextual-window").is(":visible")) return;

			previewFrame.contents().find("#lc-contextual-menu-row .lc-contextual-actions").hide();
			var top = $(this).offset().top;
			var left = $(this).offset().left;
			var right = previewFrame.width() - ($(this).offset().left + $(this).outerWidth()) - getScrollBarWidth(); //

			var selector = CSSelector($(this)[0]);
			//console.log(selector);

			var elHeight = previewFrame.contents().find("#lc-contextual-menu-row").outerHeight();
			previewFrame.contents().find("#lc-contextual-menu-row").css({
				'top': top + elHeight,
				'left_NO': left - 1,
				'right': right
			}).show().attr("selector", selector);
			previewFrame.contents().find(selector).addClass("lc-highlight-row");

		}); //end function

		//MOUSE LEAVES ROW
		previewFrameBody.on("mouseleave", lc_rows_selector, function() {
			if (previewFrame.contents().find('#lc-contextual-menu-row').is(":hover")) return;
			if (previewFrame.contents().find('#lc-contextual-menu-block').is(":hover")) return;
			previewFrame.contents().find("#lc-contextual-menu-row .lc-contextual-actions").hide();
			previewFrame.contents().find("#lc-contextual-menu-row").hide();

			$(this).removeClass("lc-highlight-row");
		}); //end function



		//MOUSE ENTERS COLUMN ////////////////////////
		previewFrameBody.on("mouseenter", lc_columns_selector, function(e) {

			//if($(".lc-contextual-window").is(":visible")) return;
			if ($(this).closest(".lc-rendered-shortcode-wrap").length > 0) return; //exit if we're hovering a shortcode
			if(e.metaKey) return; // exit if cmd is pressed

			previewFrame.contents().find("#lc-contextual-menu-column .lc-contextual-actions").hide();
			var top = $(this).offset().top + (themeNoGutter ? 23 : 0);
			var left = $(this).offset().left + (themeNoGutter ? 110 : 0);
			var right = (previewFrame.width() - ($(this).offset().left + $(this).outerWidth())); //

			var selector = CSSelector($(this)[0]);
			//console.log(selector);

			var elHeight = previewFrame.contents().find("#lc-contextual-menu-column").outerHeight();
			previewFrame.contents().find("#lc-contextual-menu-column").css({
				'top': top - elHeight,
				'left': left - 1,
				'right_NO': right
			}).show().attr("selector", selector);
			previewFrame.contents().find(selector).addClass("lc-highlight-column");

		}); //end function
		
		//MOUSE LEAVES COLUMN
		previewFrameBody.on("mouseleave", lc_columns_selector, function() {
			if (previewFrame.contents().find('#lc-contextual-menu-mainpart').is(":hover")) return;
			if (previewFrame.contents().find('#lc-contextual-menu-column').is(":hover")) return;
			if (previewFrame.contents().find('#lc-contextual-menu-block').is(":hover")) return;
			previewFrame.contents().find("#lc-contextual-menu-column .lc-contextual-actions").hide();
			previewFrame.contents().find("#lc-contextual-menu-column").hide();

			$(this).removeClass("lc-highlight-column");
		}); //end function


		//MOUSE ENTERS BLOCK ////////////////////////
		previewFrameBody.on("mouseenter", lc_blocks_selector, function(e) { //was mouseenter
			if ($(this).closest(".lc-rendered-shortcode-wrap").length > 0) return; //exit if we're hovering a shortcode
			if(e.metaKey) return; // exit if cmd is pressed
			//console.log("mouseenter block");

			//lc_detect_blocks_and_integrate_contextual_menu($(this));
			//if($(".lc-contextual-window").is(":visible")) return;

			previewFrame.contents().find("#lc-contextual-menu-block .lc-contextual-actions").hide();
			var top = $(this).offset().top;
			var left = $(this).offset().left;
			//var right = (previewFrame.width() - ($(this).offset().left + $(this).outerWidth()));

			var selector = CSSelector($(this)[0]);
			//console.log(selector);
			//find out hierarchy
			var depth= $(this).parents(".lc-block").length;
			
			previewFrame.contents().find("#lc-contextual-menu-block").hide().attr("lc-depth",depth).css({
				'top': top,
				'left': left,
				/* 'right_NO':right */
			}).show().attr("selector", selector);
			previewFrame.contents().find(".lc-highlight-block").removeClass("lc-highlight-block"); //for security
			previewFrame.contents().find(selector).addClass("lc-highlight-block");

			

		}); //end function

		//MOUSE LEAVES BLOCK
		previewFrameBody.on("mouseleave", ".lc-block", function() {
			if (previewFrame.contents().find('#lc-contextual-menu-block').is(":hover")) return;
			previewFrame.contents().find("#lc-contextual-menu-block .lc-contextual-actions").hide();
			previewFrame.contents().find("#lc-contextual-menu-block").hide();

			$(this).removeClass("lc-highlight-block");
		}); //end function

		/*
		//MOUSE ENTERS EDITABLE ITEM ////////////////////////
		previewFrameBody.on("mouseover", "*[lc-helper]", function () { //was mouseenter
			$(this).addClass("lc-highlight-item");
			
		}); //end function
		
		//MOUSE LEAVES EDITABLE ITEM that has a HELPER defined
		previewFrameBody.on("mouseleave", "*[lc-helper]", function () {
			if( previewFrame.contents().find('#lc-contextual-menu-item').is(":hover")) return;
			previewFrame.contents().find("#lc-contextual-menu-item .lc-contextual-actions").hide();
			previewFrame.contents().find("#lc-contextual-menu-item").hide();
			
			$(this).removeClass("lc-highlight-item");
		}); //end function
		*/

		//TRIGGER  add_helper_attributes_in_preview 
		add_helper_attributes_in_preview();


	} //end main function
} // end if function defined

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////  CONTEXTUAL MENU ACTIONS  ///////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function set_html_editor(html) { //quick function to beautify and set the html editor content
	$("#lc-html-editor-window").attr("prevent_live_update", "1");
	lc_html_editor.session.setValue(html_beautify(html, {
		unformatted: ['script', 'style'],
		"indent_size": "1",
		"indent_char": "\t",
	}), 1);
	    
    //set autocomplete
    lc_html_editor.completers.push({
        getCompletions: (editor, session, pos, prefix, callback) => {
            let lineTillCursor = session.getDocument().getLine(pos.row).substring(0, pos.column);

            // Check if we are editing a "class" attribute value
            if (/class=["|'][^"']*$/i.test(lineTillCursor)) {
                callback(null, getClassesMappedArray());
            }

			// Check if we are editing a "style" attribute value and using the var( function
			else if (/style=["|'][^"']*var\([^\)]*$/i.test(lineTillCursor)) {
				callback(null, getCSSVariablesMappedArray());
			}

            // Check if we are editing for "editable" attribute value
            else if (/editable=["|'][^"']*$/i.test(lineTillCursor)) {
                callback(null, [
                    { value: 'inline', score: 1000, meta: 'LiveCanvas' },
                    { value: 'rich', score: 1000, meta: 'LiveCanvas' }
                ]);
            }
            // Check for adding new attribute names in general
            else if (/\<[a-zA-Z0-9-]+[\s]+[^>]*$/i.test(lineTillCursor)) {
                var suggestions = [
                    { value: 'editable="rich"', score: 1000, meta: 'LiveCanvas' },
                    { value: 'editable="inline"', score: 1000, meta: 'LiveCanvas' }
                    // Add other attribute suggestions here if needed
                ];
                callback(null, suggestions.filter(item => item.value.startsWith(prefix)));
            }
            else {
                callback(null, []);
            }
        }
    });

	$("#lc-html-editor-window").removeAttr("prevent_live_update");
}

function set_css_editor(css) { //quick function to beautify and set the css editor content
	$("#lc-css-editor").attr("prevent_live_update", "1");

    //Set CSS Completer for Variables
    lc_css_editor.completers.push({
        getCompletions: (editor, session, pos, prefix, callback) => {
            // Get the current line up to the cursor position
            const line = session.getLine(pos.row).substring(0, pos.column);

            // Check if the current context is within a var() function
            if (line.match(/var\([^\)]*$/)) {
                // Trigger the CSS variables autocomplete
                callback(null, getCSSVariablesMappedArray());
            } else {
                // If not in a var() context, do not provide any completions
                callback(null, []);
            }
        }
    }); 
    
	lc_css_editor.session.setValue(css_beautify(css, {
		//unformatted: ['script', 'style'],
		"indent_size": "1",
		"indent_char": "\t",
	}), 1);
	$("#lc-css-editor").removeAttr("prevent_live_update");
}

//FUNCTION TO INITIALIZE AND OPEN THE HTML EDITOR ON A SELECTOR
function openHtmlEditor(selector) {
    $(".close-sidepanel").click();
    $(".lc-editor-close").click();
    $("body").addClass("lc-bottom-editor-is-shown");
    //$("main .lc-shortcode-preview").remove();
    $("#lc-html-editor-window").attr("selector", selector);
    myConsoleLog("Open html editor for: " + selector);
    var html = getPageHTML(selector);
    set_html_editor(html);
    $("#lc-html-editor-window").removeClass("lc-opacity-light").fadeIn(100);
    lc_html_editor.focus();
    $("#html-tab").click();
}
function copyToClipboard(selector) {
    var html = getPageHTML(selector); //console.log("store in clipb:"+html);

    if (navigator.clipboard == undefined) {
        alert("This requires a secure origin - either HTTPS or localhost");
        return;
    }
    navigator.clipboard.writeText(html);
}
function pasteFromClipboard(selector) {
    navigator.clipboard.readText()
        .then(html => {
            if (html === null) {
                alert("Clipboard is Empty");
                return;
            }
            setPageHTML(selector, html);
            updatePreviewSectorial(selector);
        })
        .catch(err => {
            console.error('Failed to read clipboard contents: ', err);
        });
}
function duplicateElement(selector){
    var html = doc.querySelector(selector).outerHTML;
    setPageHTMLOuter(selector, html + html);

    selector = selector.substring(0, selector.lastIndexOf(">")); //get the selector for the parent    
    updatePreviewSectorial(selector);
}
function deleteElement(selector){
    setPageHTMLOuter(selector, "");

    selector = selector.substring(0, selector.lastIndexOf(">")); //get the selector for the parent    
    updatePreviewSectorial(selector);

    $(".close-sidepanel").click(); //as protection if it's panel was opened

}
function moveElementUp(selector){

    if (doc.querySelector(selector).previousElementSibling === null) {
        swal("Element is first already");
        return false;
    }
    const theParentNode = doc.querySelector(selector).parentNode;
    var this_element_outer_HTML = doc.querySelector(selector).outerHTML;
    var previous_outer_HTML = doc.querySelector(selector).previousElementSibling.outerHTML;

    doc.querySelector(selector).previousElementSibling.outerHTML = this_element_outer_HTML;
    doc.querySelector(selector).outerHTML = previous_outer_HTML;

    updatePreviewSectorial(CSSelector(theParentNode));
}
function moveElementDown(selector) {
    if (doc.querySelector(selector).nextElementSibling === null) {
        swal("Element is last already");
        return false;
    }
    const theParentNode = doc.querySelector(selector).parentNode;
    var this_element_outer_HTML = doc.querySelector(selector).outerHTML;
    var next_outer_HTML = doc.querySelector(selector).nextElementSibling.outerHTML;

    doc.querySelector(selector).nextElementSibling.outerHTML = this_element_outer_HTML;
    doc.querySelector(selector).outerHTML = next_outer_HTML;

    updatePreviewSectorial(CSSelector(theParentNode));
}
function initialize_contextual_menu_actions() {

	//USER CLICKS ON EDIT PROPERTIES
	previewFrame.contents().find("body").on("click", ".lc-edit-properties", function (e) {
		e.preventDefault();
		var selector = $(this).closest("[selector]").attr("selector");
		var layoutElementName = $(this).closest(".lc-contextual-menu").find(".lc-contextual-title").text().trim();
		revealSidePanel("edit-properties", selector, layoutElementName);
	}); //end function  
	
	//USER DBL CLICKS CONTEXTUAL BLOCK MENU TITLE: OPEN PROPERTIES PANEL
	previewFrame.contents().on("dblclick", ".lc-contextual-title", function(e) {
		e.preventDefault();
		$(this).closest(".lc-contextual-menu").find(".lc-contextual-actions ul li a[class$='properties']").click();
		
	}); //end function
	
	//USER RIGHT CLICKS CONTEXTUAL BLOCK MENU TITLE: OPEN CODE EDITOR
	previewFrame.contents().on("contextmenu", ".lc-contextual-title", function(e) {
		e.preventDefault();
		$(this).closest(".lc-contextual-menu").find(".lc-contextual-actions ul li a[class$='lc-open-html-editor']").click();
		
	}); //end function
	
	//USER CLICKS CONTEXTUAL BLOCK MENU TITLE: REVEAL SUBMENU
	previewFrame.contents().on("click", ".lc-contextual-title", function(e) {
		e.preventDefault();
		$(".lc-editor-close").click();//close code editor
		$(this).closest(".lc-contextual-menu").find(".lc-contextual-actions").slideToggle(100);
        //IF TREE IS OPEN
        if ($("#tree-body").is(":visible")  ) {
            const selector = $(this).parent().attr("selector");
            //scroll tree to current item
            document.querySelector("#tree-body li .tree-view-item[data-selector='" + selector + "']").scrollIntoView({ behavior: "smooth", block: "center"  });
        }
	}); //end function

	//USER CLICKS ANY SPECIFIC CONTEXTUAL BLOCK MENU ITEM: HIDE CONTEXTUAL MENU  
	previewFrame.contents().on("click", ".lc-contextual-menu ul li a", function(e) {
		e.preventDefault();
		$(this).closest(".lc-contextual-menu").slideUp();
	}); //end function

	//USER CLICKS EDIT HTML IN CONTEXTUAL MENU
	previewFrame.contents().find("body").on("click", '.lc-open-html-editor', function(e) {
		e.preventDefault(); 
		var selector = $(this).closest("[selector]").attr("selector");
        openHtmlEditor(selector); 
	});

	//USER CLICKS ON COPY BLOCK
	previewFrame.contents().find("body").on("click", ".lc-copy-to-clipboard", function(e) {
		e.preventDefault();
		var selector = $(this).closest("[selector]").attr("selector");
		copyToClipboard(selector);
	}); //end function copy block

	//USER CLICKS ON PASTE BLOCK
	previewFrame.contents().find("body").on("click", ".lc-paste-from-clipboard", function(e) {
		e.preventDefault();
		var selector = $(this).closest("[selector]").attr("selector");
        pasteFromClipboard(selector);
	}); //end function paste block


	///////////CONTAINERS ///////////////////

	//USER CLICKS ON ADD ROW&COLS TO CONTAINER from contextual menu
	previewFrame.contents().on('click', " .lc-container-insert-rowandcols", function(e) {
		e.preventDefault();
		var selector = $(this).closest("[selector]").attr("selector");
		revealSidePanel("add-row", selector);
	});


	//////////////////SECTIONS/////////////////////////////

	//USER CLICKS ON OPEN SECTION LIBRARY / REPLACE SECTION
	previewFrame.contents().find("body").on("click", ".lc-replace-section", function(e) {
		e.preventDefault();
		var selector = $(this).closest("[selector]").attr("selector");
		revealSidePanel("sections", selector);
		$("section[item-type=sections] .sidepanel-tabs a:first").click(); //open first tab 
	}); //end function  

	//USER CLICKS ON SAVE SECTION  TO LIBRARY 
	previewFrame.contents().find("body").on("click", ".lc-save-section", function(e) {
		e.preventDefault();
		var selector = $(this).closest("[selector]").attr("selector");
		//var html = getPageHTML(selector); 
		var html = doc.querySelector(selector).outerHTML;
		swal({
			text: 'Enter a descriptive name...',
			content: "input",
			button: {
			  text: "Save",
			  closeModal: true,
			},
		}).then(name => {
			if (!name) throw null;
		   	saveToLibrary('lc_section', name, html);
			swal.stopLoading();
    		swal.close(); 
		});
	}); //end function  


	function saveToLibrary(post_type,post_title,post_content){
		
		$("#previewiframe").contents().find(".lc-content-is-being-edited").blur(); //stop text live editing and get those edits into doc
		$.post(
				lc_editor_saving_url, {
					'action': 'lc_save_element',
					'post_type': post_type,
					'post_title': post_title,
					'post_content': '\n'+html_beautify(post_content, {
										unformatted: ['script', 'style'],
										"indent_size": "1",
										"indent_char": "\t",
									})+'\n', 
					'lc_main_save_nonce_field': $("#lc_main_save_nonce_field").val(),
				},
				function(response) {
					//console.log('The server responded: ', response);
					if (response.includes("Save")) {
						//success  
						swal({	title: "Added to library",	icon: "success"	});
					} else {
						//(rare) Error!
						swal({	title: "Saving error (b)",	icon: "warning",text: response	}); 
					}
				}
			)
			//.done(function(msg){  })
			.fail(function(xhr, status, error) {
				// (typical, eg unlogged) Error!
				swal({ title: "Saving error", 	icon: "warning", text: error }); 
			});
		
	}


	////////////////////BLOCKS ///////////////////////////

	//USER CLICKS ON REPLACE BLOCK
	previewFrame.contents().find("body").on("click", ".lc-replace-block", function(e) {
		e.preventDefault();
		var selector = $(this).closest("[selector]").attr("selector");
		revealSidePanel("blocks", selector);

	}); //end function  

	//USER CLICKS ON BLOCK SECTION  TO LIBRARY 
	previewFrame.contents().find("body").on("click", ".lc-save-block", function(e) {
		e.preventDefault();
		var selector = $(this).closest("[selector]").attr("selector");
		var html = getPageHTML(selector);
		swal({
			text: 'Enter a descriptive name...',
			content: "input",
			button: {
			  text: "Save",
			  closeModal: true,
			},
		}).then(name => {
			if (!name) throw null;
		   	saveToLibrary('lc_block', name, html);
			swal.stopLoading();
    		swal.close();
		});
	}); //end function  

	//USER CLICKS ON DUPLICATE ELEMENT (GENERAL) 
	previewFrame.contents().find("body").on("click", ".lc-duplicate-section, .lc-duplicate-container, .lc-duplicate-row, .lc-duplicate-col, .lc-duplicate-block", function(e) {
		e.preventDefault();
		var selector = $(this).closest("[selector]").attr("selector");
		duplicateElement(selector);
	}); //end function  

	//USER CLICKS ON DELETE BLOCK/ROW
	previewFrame.contents().find("body").on("click", ".lc-delete-row, .lc-delete-col, .lc-delete-block, .lc-remove-container", function(e) {
		e.preventDefault();
		var selector = $(this).closest("[selector]").attr("selector");
        deleteElement(selector);
	}); //end function  

	//USER CLICKS ON ADD BLOCK TO COLUMN
	previewFrame.contents().find("body").on("click", ".lc-add-block-to-column", function(e) {
		e.preventDefault();
		var selector = $(this).closest("[selector]").attr("selector");
		setPageHTML(selector, getPageHTML(selector) + '<div class="lc-block"></div>');
		updatePreviewSectorial(selector);
	}); //end function

	//REODER:: MOVE UP
	previewFrame.contents().find("body").on("click", ".lc-move-up", function(e) {
		e.preventDefault();
		$(".close-sidepanel").click(); //or it's confusing
		var selector = $(this).closest("[selector]").attr("selector");
        moveElementUp(selector);
	}); //end function

	//REODER:: MOVE DOWN
	previewFrame.contents().find("body").on("click", ".lc-move-down", function(e) {
		e.preventDefault();
		$(".close-sidepanel").click(); //or it's confusing
		var selector = $(this).closest("[selector]").attr("selector");
        moveElementDown(selector);
	}); //end function


} //end function


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////// INITIALIZE CONTENT BUILDING ACTIONS  ///////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


function initialize_content_building() {


	/////////////////////LETS DEFINE SOME ACTION BUTTONS ////////////////////////////////////

	//HANDLE CLICKING OF CHOOSE BLOCK , on dummy new blocks
	previewFrame.contents().on('click', ".lc-block:empty", function(e) {
		e.preventDefault();
		console.log("Let's replace the block's contents");
		var selector = CSSelector($(this).closest(".lc-block")[0]);
		//swal(selector);
		revealSidePanel("blocks", selector);
		//$("section[item-type=blocks] .sidepanel-tabs a:first").click(); //open first tab 
	});
	//HANDLE CLICKING OF CHOOSE SECTION, on dummy new sections
	previewFrame.contents().on('click', "main section:empty", function(e) {
		e.preventDefault();
		console.log("Let's replace the sections's contents");
		var selector = CSSelector($(this).closest("section")[0]);
		//swal(selector);
		revealSidePanel("sections", selector);
	});

} //end function

/**
 * 
 * @param {array} array 
 * @param {string} columnName 
 * @returns 
 */
function arrayColumn(array, columnName) {
	returned = [];
    return array.filter(function(value, index){
		if (!(returned.find(el => el == value[columnName]))) {
			returned[index] = value[columnName];
        	return value;
		}
	}).map(function(value,index) {
		return value[columnName];
    });
}

/**
 * needed for templating
 * 
 * @param {obj} props 
 * @returns 
 */
function render(props) {
	return function(token, i) { return (i % 2) ? props[token] : token; };
}

function lcRandomUUID() {
	return Date.now().toString(36) + Math.random().toString(36).substring(2);
}

function sleep(milliseconds) {
	const date = Date.now();
	let currentDate = null;
	do {
		currentDate = Date.now();
	} while (currentDate - date < milliseconds);
}



/////////////// READYMADE TEMPLATES WINDOW ////////////////

function initialize_readymade_templates_window (){
	
	//SHOW PRELOADER
	$("#loader").css("opacity","0.95").show();

	//INTEFACE BUILDING: LOAD READYMADE TEMPLATES WINDOW
	$.get("https://library.livecanvas.com/starters/?lcr_export_readymades_modal&apikey=" + lc_editor_apikey, function (readymadesContent) {

		//no html, no party here
		if (!readymadesContent) {
			console.log('Could not retrieve readymades.');
			return;
		}

		//append the modal before the preview and hide it
		$('#sidebar-section-form-common-elements').after(readymadesContent);
		//$('#readymades-modal-wrapper').css('display', 'none');
		
		//HIDE PRELOADER
		$("#loader").hide().css("opacity", "1");
	});


	//user clicks on button to insert template
	$('body').on('click', '.readymades-modal-button-insert', function (event) {
		event.preventDefault();
		var url = $(this).attr('href')+"?import";
		loadStarterintoEditor(url, '#the-html-sect');
		$('#readymades-modal-wrapper').css('display', 'none');
		$('#previewiframe-wrap,#maintoolbar').removeClass('is-blurred');
	});

	//readymade collections filter
	$('body').on('click', '#readymades-modal-categories a', function () {
		var att = $(this).attr('data-collection-target');
		$('#templatesearch').val('');
		$('.readymades-modal-item').show();
		$('#readymades-modal-categories a').removeClass('active');
		$(this).addClass('active');
		if (att.length) {
			var str = ".readymades-modal-item:not([data-collection='" + att + "'])";
			$(str).hide()
			return;
		}
	});

	//readymades search
	$('body').on('propertychange input', 'input[name="readymades-modal-search"]', function (e) {
		$('.readymades-modal-item').show();
		$('#readymades-modal-categories a').removeClass('active').first().addClass('active');
		var textSearch = $(this).val();
		if (textSearch.length < 3) return;
		$('.readymades-modal-item').hide();
		$('.readymades-modal-item').each(function () {
			if ($(this).text().toUpperCase().indexOf(textSearch.toUpperCase()) != -1) {
				$(this).show();
			}
		});
	});

	//USER CLICKS ON READYMADES CLOSE BUTTON
	$('body').on('click', '#readymades-close', function (e) {
		e.preventDefault();
		$('#previewiframe-wrap,#maintoolbar').removeClass('is-blurred');
		$('#readymades-modal-wrapper').css('display', 'none');
	});

}

// FOR CLASSES AUTOCOMPLETE: BUILD CLASSES LIST FROM PREVIEW IFRAME's STYLESHEETS
function getClassesMappedArray() {
    let classes = new Set();

    if (!previewiframe || !previewiframe.contentWindow || !previewiframe.contentWindow.document) {
        console.error("Invalid or missing iframe or document object");
        return [];
    }

    //loop all stylesheets
    for (let sheet of previewiframe.contentWindow.document.styleSheets) {
        
        //skip some stylesheets
        if (['wp-block-library-css', 'lc-preview-iframe'].includes(sheet.ownerNode.id)) {
            continue; 
        }

        let sheetHref = sheet.href || '';
        let sheetName = sheetHref.split('/').pop() || 'Inline Styles'; // Extract filename or label inline styles

        try {
            Array.from(sheet.cssRules).forEach(rule => {
                // Process regular style rules
                if (rule.type === CSSRule.STYLE_RULE) {
                    processStyleRule(rule, classes, sheetName);
                }

                // Process rules within media queries
                if (rule.type === CSSRule.MEDIA_RULE) {
                    Array.from(rule.cssRules).forEach(innerRule => {
                        if (innerRule.type === CSSRule.STYLE_RULE) {
                            processStyleRule(innerRule, classes, sheetName);
                        }
                    });
                }
            });
        } catch (e) {
            console.error("Error processing stylesheet:", e);
        }
    }

    let theClassesArray = Array.from(classes);

    // Sort the classes
    theClassesArray.sort((a, b) => a.className.localeCompare(b.className));

    let mappedArray = theClassesArray.map(({ className, sheetName }) => {
        return {
            value: className,
            score: sheetName.startsWith('bundle.css') ? 2 : 1,
            meta: (sheetName.startsWith('bundle.css') || sheetName.startsWith('bundle-')) ? 'picostrap' : sheetName // Check for 'bundle.css'
        };
    });

    return mappedArray;
}

function processStyleRule(rule, classes, sheetName) {
    let selectorText = rule.selectorText;
    let classNames = selectorText.match(/\.[\w-]+/g);
    if (classNames) {
        classNames.forEach(className => {
            classes.add({ className: className.substring(1), sheetName });
        });
    }
}

function getCSSVariablesMappedArray() {
    let variables = new Set();

    if (!previewiframe || !previewiframe.contentWindow || !previewiframe.contentWindow.document) {
        console.error("Invalid or missing iframe or document object");
        return [];
    }

    for (let sheet of previewiframe.contentWindow.document.styleSheets) {
        if (['wp-block-library-css', 'lc-preview-iframe'].includes(sheet.ownerNode.id)) {
            continue; 
        }

        let sheetHref = sheet.href || '';
        let sheetName = sheetHref.split('/').pop() || 'Inline Styles';

        try {
            Array.from(sheet.cssRules).forEach(rule => {
                if (rule.type === CSSRule.STYLE_RULE) {
                    processCSSVariableRule(rule, variables, sheetName);
                }
            });
        } catch (e) {
            console.error("Error processing stylesheet for variables:", e);
        }
    }

    let variablesArray = Array.from(variables);
    variablesArray.sort((a, b) => a.variableName.localeCompare(b.variableName));

    let mappedArray = variablesArray.map(({ variableName, sheetName }) => {
        return {
            value: variableName,
            score: sheetName.startsWith('bundle.css') ? 2 : 1,
            meta: (sheetName.startsWith('bundle.css') || sheetName.startsWith('bundle-')) ? 'picostrap' : sheetName
        };
    });

    return mappedArray;
}

function processCSSVariableRule(rule, variables, sheetName) {
    let style = rule.style;
    for (let i = 0; i < style.length; i++) {
        let propName = style[i];
        if (propName.startsWith('--')) {
            variables.add({ variableName: propName, sheetName });
        }
    }
}

//FOR PREVENTING CONCURRENT EDITOR USAGE
function pingServerWhileEditing() {

    
    $.post( //TODO: HANDLE REQUEST FAIL WITH ALERT - because saving will not be possible
        lc_editor_saving_url, {
        'action': 'lc_ping_server_while_editing',
        'post_id':  lc_editor_current_post_id,
    },
        function (response) {
            console.log('The server call to lc_ping_server_while_editing responded: ', response);
            
            if (response.includes('ERROR')) {
                alert(response);
                //exit the editor
                window.location.assign(lc_editor_url_before_editor);
                
            }  
        }
    );

    setTimeout(pingServerWhileEditing, 30000);
    
}




/* ***************************  REMOTE UI KITS FOR READYMADES *************************** */

// Function to get or prompt for the API key
function getServiceApiKey(serviceName) {
    const key = `${serviceName.toLowerCase()}_apikey`;
    let apiKey = localStorage.getItem(key);
    while (!apiKey || apiKey.length !== 64) {
        apiKey = prompt(`Please enter your 64-character API key for ${serviceName}:`);
        if (!apiKey) throw new Error(`No API key entered for ${serviceName}. Aborting.`);
        if (apiKey.length === 64) localStorage.setItem(key, apiKey);
        else alert("The API key must be exactly 64 characters. Please try again.");
    }
    return apiKey;
}