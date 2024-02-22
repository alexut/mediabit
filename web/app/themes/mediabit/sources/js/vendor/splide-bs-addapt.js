document.addEventListener("DOMContentLoaded", function () {
  // Slider

   // get width of each bsBreakpoint by --bs-breakpoint-xx
   var getBreakpointWidth = function (breakpoint) {
      var value = getComputedStyle(document.body).getPropertyValue("--splide-breakpoint-" + breakpoint);
      return parseInt(value);
    };
    // get classnames that begin with "col-" from first child of each slider element
  
    var getColClasses = function ( element ) {
      var classes = element.firstElementChild.classList;
      var colClasses = [];
      for (var i = 0; i < classes.length; i++) {
        if (classes[i].startsWith("col-")) {
          colClasses.push(classes[i]);
        }
      }
      return colClasses;
    };

   // foreach column class, get the breakpoint and the number of columns
    var getColBreakpoints = function ( element) {
      var colBreakpoints = [];
      var colClasses = getColClasses( element );

      for (var i = 0; i < colClasses.length; i++) {
        var colClass = colClasses[i];
        var colClassParts = colClass.split("-");
        var breakpoint = colClassParts[1];
        var col = colClassParts[2];
        colBreakpoints.push({
          breakpoint: breakpoint,
          col: col,
        });

      }
 
      return colBreakpoints;
    };
    
    // based on column classes in bootstrap create splide breakpoint options
    var getBreakpointOptions = function ( element ) {
      var breakpointOptions = {};
      var colBreakpoints = getColBreakpoints(element);

      for (var i = 0; i < colBreakpoints.length; i++) {
        var colBreakpoint = colBreakpoints[i];
        var breakpoint = colBreakpoint.breakpoint;
        var col = colBreakpoint.col;
        var colnr = 12 / col;
        var breakpointWidth = getBreakpointWidth(breakpoint);
        var breakpointOption = {
          perPage: colnr,
          perMove: 1,
        };
        breakpointOptions[breakpointWidth] = breakpointOption;
      }
      return breakpointOptions;
    };


    var elms = document.getElementsByClassName( 'splide' );
    for ( var i = 0; i < elms.length; i++ ) {


      var elm = elms[i];
      var children = elm.children;
      var sliderElGutter = getComputedStyle(elm).getPropertyValue("--bs-gutter-x");
      // get element width
      var sliderElWidth = elm.offsetWidth;
      // get total children width
      var childrenWidth = 0;
     
      for (var j = 0; j < children.length; j++) {
        childrenWidth += children[j].offsetWidth + parseInt(sliderElGutter) -1;
      }
      console.log(sliderElWidth);
      console.log(childrenWidth);
      if ( (sliderElWidth + 20) < childrenWidth ) {
    
      var options = {
        type: "loop",
        perPage: 4,
        perMove: 1,
        gap: sliderElGutter,
        breakpoints: getBreakpointOptions(elm),
      };
      console.log(options);
      // from each direct children of splide remove class "col-xx-xx"

      console.log(children);
      var colClasses = getColClasses(elm);
      for (var j = 0; j < children.length; j++) {
        var child = children[j];
        console.log(j);
        for (var k = 0; k < colClasses.length; k++) {
          // for each child 
          child.classList.remove(colClasses[k]);
          child.classList.add("splide__slide");
          console.log(colClasses[k]);
        }
      }

      var splideTrack = document.createElement("div");
      splideTrack.classList.add("splide__track");
      var splideList = document.createElement("div");
      splideList.classList.add("splide__list");
      splideTrack.appendChild(splideList);
      // move all children to splideList
      while (elm.firstChild) {
        splideList.appendChild(elm.firstChild);
      }
      elm.appendChild(splideTrack); 
    
      new Splide(elm, options).mount();
    } else {
      elm.classList.remove("splide");
    }
  }
});