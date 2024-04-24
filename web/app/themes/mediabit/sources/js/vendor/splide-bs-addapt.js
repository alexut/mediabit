function initializeSplide() {
	// Slider

	// get width of each bsBreakpoint by --bs-breakpoint-xx
	var getBreakpointWidth = function (breakpoint) {
		var value = getComputedStyle(document.body).getPropertyValue(
			"--splide-breakpoint-" + breakpoint
		);
		return parseInt(value);
	};
	// get classnames that begin with "col-" from first child of each slider element

	var getColClasses = function (element) {
		var classes = element.firstElementChild.classList;
		var colClasses = [];
		for (var i = 0; i < classes.length; i++) {
			if (classes[i].startsWith("col-")) {
				colClasses.push(classes[i]);
			}
		}
		return colClasses;
	};

	// A default mapping from bootstrap breakpoints to columns
	var defaultBreakpointsCols = {
		xs: 12,
		sm: 12,
		md: 12,
		lg: 12,
		xl: 12,
		xxl: 12,
	};

	// foreach column class, get the breakpoint and the number of columns
	var getColBreakpoints = function (element) {
		var colBreakpoints = [];
		var colClasses = getColClasses(element);
		var lastColValue = defaultBreakpointsCols["xs"]; // Start with the smallest breakpoint

		// List of bootstrap breakpoints in increasing order
		var breakpointsOrder = ["xs", "sm", "md", "lg", "xl", "xxl"];

		for (var i = 0; i < breakpointsOrder.length; i++) {
			var breakpoint = breakpointsOrder[i];

			// Check if this breakpoint is explicitly defined
			var found = colClasses.find((cc) =>
				cc.startsWith("col-" + breakpoint + "-")
			);
			if (found) {
				var parts = found.split("-");
				lastColValue = parseInt(parts[2]);
			}

			colBreakpoints.push({
				breakpoint: breakpoint,
				col: lastColValue,
			});
		}
		return colBreakpoints;
	};

	// based on column classes in bootstrap create splide breakpoint options
	var getBreakpointOptions = function (element) {
		var breakpointOptions = {};
		var perPages = []; // Array to store perPage values
		var colBreakpoints = getColBreakpoints(element);

		for (var i = 0; i < colBreakpoints.length; i++) {
			var colBreakpoint = colBreakpoints[i];
			var breakpoint = colBreakpoint.breakpoint;
			var col = colBreakpoint.col;
			var colnr = 12 / col;
			perPages.push(colnr); // Push the computed perPage to the array
			var breakpointWidth = getBreakpointWidth(breakpoint);
			var breakpointOption = {
				perPage: colnr,
				perMove: 1,
			};
			breakpointOptions[breakpointWidth] = breakpointOption;
		}
		return { breakpointOptions, perPages };
	};

	let elms = Array.from(document.getElementsByClassName("splide"));

	for (let i = 0; i < elms.length; i++) {
		let elm = elms[i];
		console.log("Processing element:", i);
		console.log("elm", elm);
		let children = elm.children;
		let sliderElGutter = getComputedStyle(elm).getPropertyValue("--bs-gutter-x");
		// get element width
		let sliderElWidth = elm.offsetWidth;
		// get total children width
		let childrenWidth = 0;

		for (let j = 0; j < children.length; j++) {
			childrenWidth += children[j].offsetWidth + parseInt(sliderElGutter) - 1;
		}
		console.log("childrenWidth", childrenWidth);
		console.log("sliderElWidth", sliderElWidth);
		if (sliderElWidth + 20 < childrenWidth) {

			console.log("sliderElWidth", sliderElWidth);
			// Fetch the breakpoint options and perPage values
			let { breakpointOptions, perPages } = getBreakpointOptions(elm);
			let maxPerPage = Math.max(...perPages); // Determine the maximum perPage value

			let options = {
				type: "loop",
				perPage: maxPerPage, // Use the max perPage value as the default
				perMove: 1,
				gap: sliderElGutter,
				breakpoints: breakpointOptions,
			};

			// Check for splide-no-pagination and adjust the options
			if (elm.classList.contains("splide-no-pagination")) {
				options.pagination = false;
			}

			// Check for splide-no-arrows and adjust the options
			if (elm.classList.contains("splide-no-arrows")) {
				options.arrows = false;
			}
			console.log("options", options);
			// from each direct children of splide remove class "col-xx-xx"
			let colClasses = getColClasses(elm);
			for (let j = 0; j < children.length; j++) {
				let child = children[j];
				for (let k = 0; k < colClasses.length; k++) {
					// for each child
					child.classList.remove(colClasses[k]);
					child.classList.add("splide__slide");
				}
			}

			let splideTrack = document.createElement("div");
			splideTrack.classList.add("splide__track");
			let splideList = document.createElement("div");
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
			console.log('remove splide', elm)
		}
	}

};

document.addEventListener("DOMContentLoaded", initializeSplide);