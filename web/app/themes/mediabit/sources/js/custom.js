"use strict";

(function() { // IIFE to avoid polluting the global scope
  const DEBOUNCE_DELAY = 250;
  const SCROLL_THRESHOLD = 30;
  const MOUSE_EVENT_DELAY = 150;
  const TOP_THRESHOLD = 100;

  // debounced function
  function debounce(func, delay) {
    let inDebounce;
    return function() {
      const context = this;
      const args = arguments;
      clearTimeout(inDebounce);
      inDebounce = setTimeout(() => func.apply(context, args), delay);
    };
  }

  // throttled function
  function throttle(func, limit) {
    let lastFunc;
    let lastRan;
    return function() {
      const context = this;
      const args = arguments;
      if (!lastRan) {
        func.apply(context, args);
        lastRan = Date.now();
      } else {
        clearTimeout(lastFunc);
        lastFunc = setTimeout(function() {
          if (Date.now() - lastRan >= limit) {
            func.apply(context, args);
            lastRan = Date.now();
          }
        }, limit - (Date.now() - lastRan));
      }
    };
  }

  // Returns the difference between inner window height and offset body height
  function getHeightDiff() {
    return document.body.offsetHeight - window.innerHeight;
  }

  // Handles mouseover and mouseleave events
  function handleMouseEvents() {
    document.body.addEventListener("mouseover", debounce(function() {
      this.classList.add("mouse-in");
      this.classList.remove("mouse-out");
    }, MOUSE_EVENT_DELAY));

    document.body.addEventListener("mouseleave", debounce(function() {
      this.classList.add("mouse-out");
      this.classList.remove("mouse-in");
    }, MOUSE_EVENT_DELAY));
  }

  // Handles scroll events
  function handleScrollEvents() {
    let scrollPosition = 0;
    window.addEventListener("scroll", debounce(function() {
      const scrollY = window.pageYOffset;
      if (Math.abs(scrollPosition - scrollY) > SCROLL_THRESHOLD) {
        document.body.classList.toggle("scroll-down", scrollY > scrollPosition);
        document.body.classList.toggle("scroll-up", scrollY <= scrollPosition);
        document.body.classList.remove("body-offcanvas");

        scrollPosition = scrollY;
        scrollPosition < TOP_THRESHOLD
            ? document.body.classList.add("at-top")
            : document.body.classList.remove("at-top");

        Math.abs(scrollPosition - getHeightDiff()) < SCROLL_THRESHOLD
            ? document.body.classList.add("at-bottom")
            : document.body.classList.remove("at-bottom");
      }
    }, DEBOUNCE_DELAY));
  }

// Hamburger icon click event
function handleHamburgerIconClick() {
    (function($) {
        $('.hamburger-icon').on('click', function () {
            $('body').toggleClass('body-offcanvas');
        });
    })(jQuery);
}


  // On carousel item change event
  function handleCarouselSlideChange() {
    var carousel = document.querySelector(".carousel");
    if (carousel) {
      carousel.addEventListener("slide.bs.carousel", function(e) {
        const indicators = document.querySelectorAll(".carousel-triggers .carousel-trigger");
        indicators.forEach(indicator => indicator.classList.remove("active"));
        indicators[e.to].classList.add("active");
      });
    }
  }

  // Step click event
  function handleGoToStepClick() {
    const steps = document.querySelectorAll(".go-to-step");
    if (steps) {
      steps.forEach(step => {
        step.addEventListener("click", function(e) {
          e.preventDefault();
          const targetStep = document.querySelector(this.getAttribute("href"));
          document.querySelectorAll(".step").forEach(step => step.classList.remove("active"));
          targetStep.classList.add("active");
        });
      });
    }
  }

  // Executing all the handlers once the DOM is fully loaded
  document.onreadystatechange = function() {
    if (document.readyState === 'complete') {
      handleScrollEvents();
      handleMouseEvents();
      handleHamburgerIconClick();
      handleCarouselSlideChange();
      handleGoToStepClick();
    }
  }
})();
