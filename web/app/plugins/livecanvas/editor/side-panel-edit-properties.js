/*
  __  __                                    __  _                              __
 / /_/ /  ___   ___  _______  ___  ___ ____/ /_(_)__ ___   ___  ___ ____  ___ / /
/ __/ _ \/ -_) / _ \/ __/ _ \/ _ \/ -_) __/ __/ / -_|_-<  / _ \/ _ `/ _ \/ -_) / 
\__/_//_/\__/ / .__/_/  \___/ .__/\__/_/  \__/_/\__/___/ / .__/\_,_/_//_/\__/_/  
             /_/           /_/                          /_/                      
*/

//SOME SUPPORT FUNCTIONS
function getCustomIcon(iconName) {
    let theIconEl = document.querySelector("template[icon-for=" + iconName + "]");
    if (theIconEl)  {
        theIconHTML = theIconEl.outerHTML.replaceAll('template', 'custom-icon'); 
    } else {
        console.log("Missing Icon: " +  iconName);
        theIconHTML ='';
    }
    return theIconHTML;
}

////////////////// INTERFACE BUILDING /////////////////////////////
function drawBreakpoints() {

    //loop all breakpoints
    for (const [b_name, b_data] of Object.entries(theFramework.breakpoints)) {

        //draw the property
        document.getElementById('sidepanel').innerHTML += `
                    <div>
                        <h2>${b_name}</h2>
                        <small> ${b_data.infix}</small>
                        <p> ${b_data.dimensions}</p> 
                    </div>
                `;
    }
}

//build navigation links to reveal property subsets
function buildPropertyNavigation(layoutElementName) { 
    
    let html = "<div class='sidebar-panel-navigation'>";

    //loop all property groups 
    for (const [lg_name, lg_data] of Object.entries(getCompleteProperties(layoutElementName, theFramework.properties))) {
        
        html += `<a href="#" name="${lg_name.toLowerCase().replaceAll(' ', '-')}"> ${lg_name} </a> `;
    }

    html += "</div>";

    return html;
}


//builds all the "edit properties" widgets
function buildPropertyWidgets(layoutElementName) {

    let html = "<the-property-widgets>";

    //MAIN LOOP: all property groups in general
    for (const [lg_name, lg_data] of Object.entries(getCompleteProperties(layoutElementName, theFramework.properties))) {

        html += `<property-group name="${lg_name.toLowerCase().replaceAll(' ', '-')}"> `;
        //html += `<h2 class="property-group-title">${lg_name}</h2> `;

        //loop all property subgroups 
        for (const [g_name, g_data] of Object.entries(lg_data)) {

            html += `<property-subgroup name="${g_name.toLowerCase().replaceAll(' ', '-')}"> `;
            if (g_name != 'General') html += `<property-subgroup-title> ${g_name} </property-subgroup-title> `;
            else html += `<property-subgroup-title> ${lg_name} </property-subgroup-title> `;

            //loop all properties of group
            for (const [p_name, p_data] of Object.entries(g_data)) {
                //console.log(p_data);
                html += getSingleProperty(p_name, p_data);
            }

            html += `</property-subgroup> `;
        }
        html += `</property-group> `;
    }
    html += "</the-property-widgets>";
    return html;
}

function getSingleProperty(p_name, p_data) {

    let theResponsiveTrigger = (p_data.responsive && (p_data.widget == "select" || p_data.widget== "icons")) ?  ` <a title="View Responsive Settings" class="responsive-properties-trigger" > <svg viewBox="0 0 52 37" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M0 31C0 30.4477 0.447715 30 1 30H30V37H1C0.447716 37 0 36.5523 0 36V31Z" fill="currentColor"/> <path d="M4.67368 30.3789V4.67368C4.67368 2.09248 6.76616 0 9.34737 0V30.3789H4.67368Z" fill="currentColor"/> <path d="M5.06316 4.2488C5.06316 1.90225 6.96541 9.53674e-07 9.31196 9.53674e-07H50.0211C50.5733 9.53674e-07 51.021 0.447716 51.021 1V3.67369C51.021 4.22597 50.5733 4.67369 50.021 4.67369H5.48804C5.25338 4.67369 5.06316 4.48346 5.06316 4.2488Z" fill="var(--color-lightgrey);"/> <path fill-rule="evenodd" clip-rule="evenodd" d="M34 34C34 35.6569 35.3431 37 37 37H49C50.6569 37 52 35.6569 52 34V13C52 11.3432 50.6569 10 49 10H37C35.3431 10 34 11.3432 34 13V34ZM39 15V30H47V15H39Z" fill="currentColor"/> </svg> </a > `:'';

    let theColorsSuggestionTrigger = (  p_data.widget == "colors" ) ? ` <a class="show-customize-palette-suggestion" href="#">Customize palette...</a> `: '';
    
    return `
        <single-property data-widget="${p_data.widget}"  data-property="${p_name.toLowerCase().replaceAll(' ', '-')}">
            
            <div class="d-flex">
                
                <propertylabel>
                    ${(p_name.replaceAll('-', ' ').replace(/\b\w/g, l => l.toUpperCase()))} 
                    <sup><a class="the-prop-doc-link" href="${p_data.docs ?? '#'}" target="_blank" title="${p_data.about}">?</a></sup>
                </propertylabel>

               
                <propertydetails>
                    
                    <valuetags style="display:none">

                        <valuetag>  <a href="">test1</a> </valuetag> <valuetag> <a href="">test2</a> </valuetag>

                    </valuetags>


                    <div class="property-options">
                        ${theResponsiveTrigger}
                        ${theColorsSuggestionTrigger}
                    </div>

                </propertydetails>
                
            </div>
            
            ${(getPropertyWidget(p_name, p_data))} 
        
        </single-property>
    `;

}

//these two might be framework-dependant
function getClassName(p_data, val) {
    return p_data.class + ((p_data.class != '' && val != '') ? '-' : '') + val;
}

function getResponsiveClassName(p_data, b_data, val) {
    return p_data.class + '-' + ((b_data.infix && val != '') ? b_data.infix + '-' : '') + val;
}

//builds the widget for each case: select, colors, numeric, icons
function getPropertyWidget(p_name, p_data) {

    let html = "";

    if (p_data.disable) return "<span style='font-size:10px'>Disabled Property</span>";

    switch (p_data.widget) {

        case 'select':

            html += ` 
                            <select class="form-control" name="" target="${(p_data.target ?? 'classes')}" >
                                <option value=""> </option>
                                        
            `;

            //loop all values 
            for (const [index, val] of Object.entries(p_data.values)) {

                html += `     <option value="${(getClassName(p_data, val))}">${(getClassName(p_data, val))}</option> `;

            }

            html += `   </select>    
                        `;

            if (p_data.responsive) {

                //open the div
                html += `<div hidden class="responsive-cases NO-d-flex " >`;

                //loop all breakpoints
                for (const [b_name, b_data] of Object.entries(theFramework.breakpoints)) {

                    if (b_name=="XS") continue;
                    //draw the property
                    html += `
                            <div  class="res-prop-${b_name.replaceAll(" ", "-")}">

                                <label>${b_name}</label>

                                <i hidden> ${b_data.dimensions}</i>

                                <select class="form-control" name="" target="${(p_data.target ?? 'classes')}"  >
                                    <option value=""> </option>    
                            `;

                    //loop all values 
                    for (const [index, val] of Object.entries(p_data.values)) {

                        html += `   <option value="${(getResponsiveClassName(p_data, b_data, val))}">${(getResponsiveClassName(p_data, b_data, val))}</option> `;

                    }

                    html += `   </select>
                            </div>
                            `;
                }

                //close the div
                html += `</div> `;


            }  

            break;

        case 'colors':

            //WIDGET COLORS 
            
            html += `<colors-widget class="custom-color-widget">`;

            //loop all values 
            for (const [index, val] of Object.entries(p_data.values)) { 
                let color_value = getComputedStyle(previewiframe.contentWindow.document.documentElement).getPropertyValue('--' + getCssVariablesPrefix() + val);
                if (!color_value)  color_value = "";

                html += ` <span style="background:${color_value}" value="${(getClassName(p_data, val))}" title="${val}"></span> `;
            }

            html += `   <span value="" title="None (Default)"></span> `;
            html += `</colors-widget>`;

            break;

        case 'numeric':

            if (p_data.responsive) {

                //NUMERIC RESPONSIVE  WIDGET

                //open the div 
                html = `<div class="d-flex widget-wrapper-flex numeric-responsive-widget" >`;

                //loop all breakpoints
                for (const [b_name, b_data] of Object.entries(theFramework.breakpoints)) {

                    //draw the property
                    html += `
                                <div class="activate-input-numbers res-prop-${b_name.replaceAll(" ", "-")}" >

                                    <label>${b_name.replace('XS', 'ALL')}</label>

                                    <i hidden> ${b_data.dimensions}</i>

                                    <input type="number" name="${p_data.class}${(b_data.infix) ? '-' + b_data.infix : ''}" min="${p_data.min}" max="${p_data.max}" step="${p_data.step ?? '1'}" >
                                        
                                </div>
                            `;
                }

                //close the div
                html += `  </div> `;


            } else {

                //NUMERIC NON RESPONSIVE SELECT

                html += ` NUMERIC NON RESPONSIVE SELECT not implemented   `;


            }

            break;

        case 'icons':

            //WIDGET ICONS eg float  

            //open div
            html = ` <radioicons>   `;

            //add the first, default icon
            html += `<radioicon> <input checked type="radio" name="${p_name}" value=""> ${(getCustomIcon('reset-property'))} </radioicon>`;

            //loop all values and draw icons as radio buttons
            for (const [index, val] of Object.entries(p_data.values)) {
                
                html += `<radioicon> <input type="radio" name="${p_name}" value="${(getClassName(p_data, val))}">  ${(getCustomIcon(getClassName(p_data, val)))} </radioicon>`;
            }

            //close div
            html += `                   
                    </radioicons>
                        `;

            if (p_data.responsive) {

                //ICONS WIDGET: responsive part: selects

                //open div
                html += ` <div class="responsive-cases" hidden>   `;

                //loop all breakpoints
                for (const [b_name, b_data] of Object.entries(theFramework.breakpoints)) {

                    //skip the XS breakpoint as the icons have just handled it
                    if (b_name == 'XS') continue;

                    //draw the property
                    html += `
                        <div class="res-prop-${b_name.replaceAll(" ", "-")}">

                            <label>${b_name}</label>

                            <i hidden> ${b_data.dimensions}</i>

                            <select class="form-control" name="" target="classes">
                                <option value="">None</option>
                                            
                                    `;

                    //loop all values 
                    for (const [index, val] of Object.entries(p_data.values)) {


                        html += `   <option value="${getResponsiveClassName(p_data, b_data, val)}">${getResponsiveClassName(p_data, b_data, val)}</option> `;

                    }

                    html += `   </select>
                        </div>
                            `;
                }

                //close div
                html += ` </div>   `;

            }

        break;

        case 'custom':
            html += ` ${p_data.custom}  `;
        break;

        default:
            html += `No widget defined of type ${p_data.widget} `;
    }

    return html;
}






///////////////////////////// BEHAVIOURS /////////////////////////
$(document).ready(function ($) {

    //USER CLICKS PROPERTY GROUP ELEMENT IN PROPERTY NAVIGATION MENU
    $("body").on("click", ".sidebar-panel-navigation a", function () {
        var theSection = $(this).closest("section");
        theSection.find(".sidebar-panel-navigation a").removeClass("active");
        $(this).addClass("active");
        theSection.find("property-group").hide();
        theSection.find("property-group[name=" + $(this).attr("name") + "]").show();
    });

    //USER CLICKS ICON TO VIEW RESPONSIVE PROPERTIES  
    $("body").on("click", ".responsive-properties-trigger", function () {
        var theProp = $(this).closest("single-property");
        theProp.find(".responsive-cases").slideToggle(); 
    });
    

    //USER CLICKS TOGGLE IN ID / CLASS BOX
    /*
    $("body").on("click", ".toggle-common-form-fields", function (e) {
        e.preventDefault();
        $(this).toggleClass("is-closed");
        $(".common-form-fields-content").slideToggle(); 
    });
    */

    //USER CLICKS PROPERTY QUESTION MARK DOCUMENTATION LINK, AND NO EXTERNAL LINK IS PROVIDED IN CONFIG  
    $("body").on("click", ".the-prop-doc-link[href='#']", function (e) {
        e.preventDefault();
        swal({
            title: "About this property",
            text: $(this).attr("title").replaceAll('.','.\n'),
            icon: "info",
            buttons: false,
        });
    });

    //USER CHANGES a RADIO in icons widget
    $("body").on("change", "single-property[data-widget=icons] input", function () {
        
        myConsoleLog("single-property data-widget=icons input change " + $(this).val());
        
        var selector = $(this).closest("[selector]").attr("selector");
        var elem = doc.querySelector(selector); 

        //eliminate all classes in select
        $(this).closest("single-property").find("input[type=radio]").each(function (index, element) {
            the_value = $(element).val().trim(); //myConsoleLog("Eliminate"+the_value);
            if (the_value !== "") elem.classList.remove(the_value);
        });
        var current_selected_item = $(this).val().trim();
        if (current_selected_item !== "") elem.classList.add(current_selected_item); //myConsoleLog("Add class"+current_selected_item);
        $(this).closest("[selector]").find("textarea[attribute-name=class]").val(elem.classList).change();
    });

    //USER CHANGES a SELECT in the sidepanel: miscellaneous classes  
    $("body").on("change", "#sidepanel section select[target=classes]", function () {
        myConsoleLog("Adding class from select: " + $(this).val());
        var selector = $(this).closest("[selector]").attr("selector");
        var elem = doc.querySelector(selector);
        //eliminate all classes in select
        $(this).find("option").each(function (index, element) {
            the_value = $(element).val().trim(); //myConsoleLog("Eliminate"+the_value);
            if (the_value !== "") elem.classList.remove(the_value);
        });
        var current_selected_item = $(this).val().trim();
        if (current_selected_item !== "") elem.classList.add(current_selected_item); //myConsoleLog("Add class"+current_selected_item);
        $(this).closest("[selector]").find("textarea[attribute-name=class]").val(elem.classList).change();
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
        theSection.find('textarea[attribute-name=class]').val(elem.className).change();

        //UPDATE THE PREVIEW
        updatePreviewSectorial(selector);
    });

    //USER CHANGES CUSTOM COLOR WIDGET
    $("body").on("click", ".custom-color-widget span", function () {
        myConsoleLog("Color widget change");
        var selector = $(this).closest("[selector]").attr("selector");
        var elem = doc.querySelector(selector);
        //eliminate all classes in select
        $(this).parent().find("span").each(function (index, element) {
            the_value = $(element).attr("value").trim(); //myConsoleLog("Eliminate"+the_value);
            if (the_value !== "") elem.classList.remove(the_value);
        });
        var current_selected_item = $(this).attr("value").trim();
        if (current_selected_item !== "") elem.classList.add(current_selected_item); //myConsoleLog("Add class"+current_selected_item);
        $(this).closest("[selector]").find("textarea[attribute-name=class]").val(elem.classList).change();
        $(this).parent().find("span.active").removeClass("active");
        $(this).addClass("active");
    });


    //USER CLICKS CUSTOMIZE COLORS RECOMMENDATION LINK
    $("body").on("click", ".customize-colors, .show-customize-palette-suggestion", function () {
        swal({
            title: "Customizing Bootstrap",
            text: "If you're using the picostrap Theme, you can easily customize the font styles and the color palette using the WordPress Customizer. \n\n Just save and exit the LiveCanvas editor. \n Then, click the 'Customize' link in the top admin bar.",
            icon: "warning",
            buttons: false,
            /* dangerMode: true, */
        });
    });

    /////BOX FOR ID CLASS and STYLE ///////////

    //USER CHANGES AN INPUT (text or textarea), eg edit ID, classes, style: trigger change in document | attribute values editing
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

    //USER PRESSES ENTER KEY INSIDE CLASSES TEXTAREA: IGNORE KEY
    $("body").on("keypress", "#sidepanel section textarea[attribute-name='class']", function (e) {
        var code = (e.keyCode ? e.keyCode : e.which);
        if (code == 13) e.preventDefault();
    });

    //USER RELEASES A KEY IN TEXTAREA: TRIGGER THE CHANGE  (eg for Classes & Style)
    $("body").on("keyup", "#sidepanel section textarea[attribute-name]", function (e) {
        $(this).change();
    });

    /*
    //INLINE STYLE READYMADES 
    $('#sidepanel').on('change', 'select.inline-style-readymades', function (event) {
        event.preventDefault();
        currentValue = $(this).val();
        var theSection = $(this).closest("section[selector]");
        var currentStyle = theSection.find("textarea[attribute-name=style]").val();
        //loop  all items in select
        $(this).find("option").each(function (index, element) {
            the_value = $(element).val();
            //myConsoleLog('replace '+the_value+ ' with '+currentValue);
            currentStyle = currentStyle.replace(the_value, currentValue);
        }); //end each
        theSection.find("textarea[attribute-name=style]").val(currentStyle).change();

    }); //end function
    */

    //   fake SELECT inputs //////////////////////////////////////
    //// toggle state
    $("body").on("click", '.ul-to-selection li.first', function () {
        $(this).closest(".ul-to-selection").toggleClass("opened");
    });

    ///SHAPE DIVIDERS: CLICK AND APPLY
    $("body").on("click", 'ul#shape_dividers li ', function () {
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
    /*
    $('#backgrounds .automatic-library-filler').each(function (index, el) {
        var count;
        for (count = 1; count <= $(el).attr("max"); count++) {
            $(el).append('<li style="' + $(el).attr("the-style").replace(/@id@/g, count) + '"></li>');
        }
    }); //end each
    */

    ///BACKGROUNDS: CLICK AND APPLY
    $("body").on("click", "ul#backgrounds li", function () {
        if ($(this).hasClass("first")) return;
        $(this).closest(".ul-to-selection").find("li.first").attr("style", $(this).attr("style"));
        $(this).closest("section").find("textarea[attribute-name=style]").val($(this).attr("style").replace(";"," !important")).change();
    }); //end on click


    ///////////BACKGROUND IMAGE
    $("body").on("click", ".open-background-image-panel", function (e) {
        e.preventDefault();
        var selector = $(this).closest("[selector]").attr("selector");
        revealSidePanel("background", selector);
    }); //end on click



});

