
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



function highlightColumn(selector, el) {
    previewFrame.contents().find("#lc-contextual-menu-column .lc-contextual-actions").hide();

    var curOffset = el.offset();
    var top = curOffset.top + (themeNoGutter ? 23 : 0);
    var left = curOffset.left + (themeNoGutter ? 110 : 0);
    var right = (previewFrame.width() - (curOffset.left + el.outerWidth()));

    //console.log(selector);

    var elHeight = previewFrame.contents().find("#lc-contextual-menu-column").outerHeight();
    previewFrame.contents().find("#lc-contextual-menu-column").css({
        'top': top - elHeight,
        'left': left - 1,
        'right_NO': right
    }).show().attr("selector", selector);
    previewFrame.contents().find(selector).addClass("lc-highlight-column");
}

function highlightBlock(selector, el, callingFrom = '') {

    var depth = el.parents(".lc-block").length;

    previewFrame.contents().find("#lc-contextual-menu-block .lc-contextual-actions").hide();
    var top = el.offset().top;
    var left = el.offset().left;

    previewFrame.contents().find("#lc-contextual-menu-block").hide().attr("lc-depth", depth).css({
        'top': top,
        'left': left,
        /* 'right_NO':right */
    })
        .show()
        .attr("selector", selector);
    previewFrame.contents().find(".lc-highlight-block").removeClass("lc-highlight-block"); //for security
    previewFrame.contents().find(selector).addClass("lc-highlight-block");
}

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
    previewFrameBody.on("mouseenter", lc_main_parts_selector, function (e) {
        if ($(this).closest(".lc-rendered-shortcode-wrap").length > 0) return; //exit if we're hovering a shortcode
        if (e.metaKey) return; // exit if cmd is pressed
        if ($(this).attr("ID") == "global-footer")
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
    previewFrameBody.on("mouseleave", lc_main_parts_selector, function () {
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
    previewFrameBody.on("mouseenter", lc_containers_selector, function (e) {

        if ($(this).closest(".lc-rendered-shortcode-wrap").length > 0) return; //exit if we're hovering a shortcode
        if (e.metaKey) return; // exit if cmd is pressed
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
    previewFrameBody.on("mouseleave", lc_containers_selector, function () {
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
    previewFrameBody.on("mouseenter", lc_rows_selector, function (e) {
        if ($(this).closest(".lc-rendered-shortcode-wrap").length > 0) return; //exit if we're hovering a shortcode
        if (e.metaKey) return; // exit if cmd is pressed
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
    previewFrameBody.on("mouseleave", lc_rows_selector, function () {
        if (previewFrame.contents().find('#lc-contextual-menu-row').is(":hover")) return;
        if (previewFrame.contents().find('#lc-contextual-menu-block').is(":hover")) return;
        previewFrame.contents().find("#lc-contextual-menu-row .lc-contextual-actions").hide();
        previewFrame.contents().find("#lc-contextual-menu-row").hide();

        $(this).removeClass("lc-highlight-row");
    }); //end function



    //MOUSE ENTERS COLUMN ////////////////////////
    previewFrameBody.on("mouseenter", lc_columns_selector, function (e) {

        //if($(".lc-contextual-window").is(":visible")) return;
        if ($(this).closest(".lc-rendered-shortcode-wrap").length > 0) return; //exit if we're hovering a shortcode
        if (e.metaKey) return; // exit if cmd is pressed

        highlightColumn(CSSelector($(this)[0]), $(this));
    }); //end function

    //MOUSE LEAVES COLUMN
    previewFrameBody.on("mouseleave", lc_columns_selector, function () {
        if (previewFrame.contents().find('#lc-contextual-menu-mainpart').is(":hover")) return;
        if (previewFrame.contents().find('#lc-contextual-menu-column').is(":hover")) return;
        if (previewFrame.contents().find('#lc-contextual-menu-block').is(":hover")) return;
        previewFrame.contents().find("#lc-contextual-menu-column .lc-contextual-actions").hide();
        previewFrame.contents().find("#lc-contextual-menu-column").hide();

        $(this).removeClass("lc-highlight-column");
    }); //end function


    //MOUSE ENTERS BLOCK ////////////////////////
    previewFrameBody.on("mouseenter", lc_blocks_selector, function (e) { //was mouseenter
        if ($(this).closest(".lc-rendered-shortcode-wrap").length > 0) return; //exit if we're hovering a shortcode
        if (e.metaKey) return; // exit if cmd is pressed
        //console.log("mouseenter block");

        var selector = CSSelector($(this)[0]);
        highlightBlock(selector, $(this));
    }); //end function

    //MOUSE LEAVES BLOCK
    previewFrameBody.on("mouseleave", ".lc-block", function () {
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
