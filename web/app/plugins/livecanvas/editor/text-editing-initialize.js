
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////// INITIALIZE TEXT LIVE EDITING BEHAVIOURS /////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


function initialize_live_editing() {

    //previewiframe.contentDocument.querySelector("h1").style.display = "none"; // WOULD HIDE ANY H1
    //console.log("Start initialize_live_editing function");

    previewFrameBody.on('mouseenter', '[lc-helper="svg-icon"], [lc-helper="image"]', function (e) {
        parent = $(this).closest('.lc-block');
        selector = CSSelector(parent[0]);
        column = $(this).closest('.col');

        //stop propagate to other elements up the tree
        e.stopPropagation();

        highlightBlock(selector, parent);
        //highlightColumn(CSSelector(column[0]), column);
    });

    ////OBJECTS LIVE  EDITING ///
    //ON CLICK OF LC-HELPER  ITEMS 
    previewFrameBody.on("click", "*[lc-helper]:not(.lc-rendered-shortcode-wrap *)", function (e) {
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
    previewFrameBody.on("click", "[editable=rich]:not(.lc-rendered-shortcode-wrap *),[editable=inline]:not(.lc-rendered-shortcode-wrap *)", function (e) {

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
        if ($("#toggle-classes-submenu").hasClass("is-active")) $("#classes-palette").show();

    }); //end on click


    //ON BLUR OF EDITABLE ITEMS:
    //previewFrameBody.on("blur", "[editable=rich],[editable=inline]", function(e) {
    $('#previewiframe').contents().find("body").on("blur", "[editable=rich],[editable=inline]", function () {
        console.log("Handling Blur event: Reapplying content changes on code");
        console.log("Blur event on " + $(this).attr("editable") + " element");

        $(this).removeAttr("contenteditable").removeClass("lc-content-is-being-edited");

        $(this).find("*[style]").removeAttr("style"); //kill any inline styling
        $(this).find("*[lc-helper]").removeAttr("lc-helper"); //kill lc-helper attributes if present

        var newValue = $(this).html(); //get field content from preview  
        if ($(this).attr("editable") == "rich") newValue = sanitize_editable_rich(newValue); //kill shit like span when deleting

        var selector = CSSelector($(this)[0]); //generate selector

        if (selector === "") { console.log("Warning: Empty selector on blur"); return; }

        doc.querySelector(selector).innerHTML = newValue; //update the content

        //if were dealing with an editable inline element, take care of external classes too
        if ($(this).attr("editable") == "inline") {
            var theClasses = $(this).attr("class");  //alert(theClasses);
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
    previewiframe.contentDocument.onselectionchange = function () {

        console.log("onselectionchange triggered");
        // 1. hilite active command
        $("#ww-toolbar a[data-command]").removeClass("is-active"); //remove all highlights for cases #1 and #2

        const array_commands = ['bold', 'italic', 'insertUnorderedList', 'insertOrderedList'];

        array_commands.forEach(command_name => {
            if (previewiframe.contentDocument.queryCommandState(command_name)) document.querySelector("#ww-toolbar a[data-command=" + command_name + "]").classList.add("is-active");
        });

        // 2. hilite active tag
        var el = previewiframe.contentDocument?.getSelection()?.focusNode?.parentNode;

        const array_tag_names = ['p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'createlink', 'bold'];

        array_tag_names.forEach(tag_name => {
            currentTagName = tag_name;
            switch (tag_name) {
                case 'createlink':
                    currentTagName = 'a';
                    break;
                case 'bold':
                    currentTagName = 'strong';
                    break;
            }
            if (el?.nodeName?.toLowerCase() == currentTagName) {
                document.querySelector("#ww-toolbar a[data-command=" + tag_name + "]").classList.add("is-active");
            }
        });

        // 3. hilite buttons for active classes
        const classLinks = document.querySelectorAll("#classes-palette a[data-class]");
        for (let index = 0; index < classLinks.length; index++) {

            if (el.classList.contains(classLinks[index].getAttribute("data-class"))) classLinks[index].classList.add("is-active"); else classLinks[index].classList.remove("is-active");
        }
    };

    //TAKE CARE OF CONTENT MERGING THAT CREATES USELESS SPANs //ATTENTION
    previewFrameBody.on('DOMNodeInserted', " *[editable=rich]", $.proxy(function (e) {
        if (e.target.tagName == "SPAN") {
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
        if (focus.textContent.match(/^\s+/g)) return;
        if (focus.textContent.match(/\s+$/g)) return;

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
        return (!str || str.length === 0);
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
    previewFrameBody.on('paste', " *[editable]", function (e) {
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
    previewFrameBody.on("keydown", ' *[editable="inline"]', function (e) {
        if (e.keyCode === 13) {
            previewiframe.contentDocument.execCommand('insertHTML', false, '<br>');
            return false;
        }
    });

    //TAKE CARE OF RICH-EDITABLE when field gets empty
    previewFrameBody.on("keyup", '[editable="rich"]', function () {
        if ($(this).html() === "") {
            $(this).html("<p>Enter some text...</p>");
            previewiframe.contentDocument.execCommand('selectAll', false, null);
        }
    });



} //end init editor func

