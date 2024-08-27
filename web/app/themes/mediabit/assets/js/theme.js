"use strict";

(function () {
  // IIFE to avoid polluting the global scope
  var DEBOUNCE_DELAY = 250;
  var SCROLL_THRESHOLD = 30;
  var MOUSE_EVENT_DELAY = 150;
  var TOP_THRESHOLD = 100;

  // debounced function
  function debounce(func, delay) {
    var inDebounce;
    return function () {
      var context = this;
      var args = arguments;
      clearTimeout(inDebounce);
      inDebounce = setTimeout(function () {
        return func.apply(context, args);
      }, delay);
    };
  }

  // throttled function
  function throttle(func, limit) {
    var lastFunc;
    var lastRan;
    return function () {
      var context = this;
      var args = arguments;
      if (!lastRan) {
        func.apply(context, args);
        lastRan = Date.now();
      } else {
        clearTimeout(lastFunc);
        lastFunc = setTimeout(function () {
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
    document.body.addEventListener("mouseover", debounce(function () {
      this.classList.add("mouse-in");
      this.classList.remove("mouse-out");
    }, MOUSE_EVENT_DELAY));
    document.body.addEventListener("mouseleave", debounce(function () {
      this.classList.add("mouse-out");
      this.classList.remove("mouse-in");
    }, MOUSE_EVENT_DELAY));
  }

  // Handles scroll events
  function handleScrollEvents() {
    var scrollPosition = 0;
    window.addEventListener("scroll", debounce(function () {
      var scrollY = window.pageYOffset;
      if (Math.abs(scrollPosition - scrollY) > SCROLL_THRESHOLD) {
        document.body.classList.toggle("scroll-down", scrollY > scrollPosition);
        document.body.classList.toggle("scroll-up", scrollY <= scrollPosition);
        document.body.classList.remove("body-offcanvas");
        scrollPosition = scrollY;
        scrollPosition < TOP_THRESHOLD ? document.body.classList.add("at-top") : document.body.classList.remove("at-top");
        Math.abs(scrollPosition - getHeightDiff()) < SCROLL_THRESHOLD ? document.body.classList.add("at-bottom") : document.body.classList.remove("at-bottom");
      }
    }, DEBOUNCE_DELAY));
  }

  // Hamburger icon click event
  function handleHamburgerIconClick() {
    (function ($) {
      $('.hamburger-icon').on('click', function () {
        $('body').toggleClass('body-offcanvas');
      });
    })(jQuery);
  }

  // On carousel item change event
  function handleCarouselSlideChange() {
    var carousel = document.querySelector(".carousel");
    if (carousel) {
      carousel.addEventListener("slide.bs.carousel", function (e) {
        var indicators = document.querySelectorAll(".carousel-triggers .carousel-trigger");
        indicators.forEach(function (indicator) {
          return indicator.classList.remove("active");
        });
        indicators[e.to].classList.add("active");
      });
    }
  }

  // Executing all the handlers once the DOM is fully loaded
  document.onreadystatechange = function () {
    if (document.readyState === 'complete') {
      handleScrollEvents();
      handleMouseEvents();
      handleHamburgerIconClick();
      handleCarouselSlideChange();
    }
  };
})();
document.addEventListener('DOMContentLoaded', function () {
  // get URL parameter for domeniu
  var urlParams = new URLSearchParams(window.location.search);
  var domeniu = urlParams.get('domeniu'); // 'domeniu' is the URL parameter name
  if (domeniu) {
    var select = document.querySelector('select[name="domeniu-activitate"]');
    select.value = domeniu.replace(/\+/g, ' '); // Replace + with space for URL encoded values
  }
  var rangeInput = document.getElementById('energy-bill-range');
  var selectedAmountSpan = document.getElementById('selected-amount');
  var selectedPackageSpan = document.getElementById('selected-package');
  function updateDescription(value) {
    selectedAmountSpan.textContent = value;
    if (value <= 500) {
      selectedPackageSpan.textContent = 'Site cu funcționalități minime – ideal pentru prezență online rapidă.';
    } else if (value > 500 && value <= 800) {
      selectedPackageSpan.textContent = 'Site personalizat cu funcționalități de bază – potrivit pentru start-up-uri și afaceri locale.';
    } else if (value > 800 && value <= 1200) {
      selectedPackageSpan.textContent = 'Site personalizat cu opțiuni medii – include blog și formular de contact.';
    } else if (value > 1200 && value <= 2000) {
      selectedPackageSpan.textContent = 'Site personalizat cu funcționalități avansate – magazin online și integrare social media.';
    } else if (value > 2000 && value <= 3000) {
      selectedPackageSpan.textContent = 'Site premium – design superior, SEO avansat, și optimizare pentru mobil.';
    } else if (value > 3000 && value <= 4500) {
      selectedPackageSpan.textContent = 'Site premium plus – include analize detaliate ale vizitatorilor și optimizare continuă.';
    } else if (value > 4500 && value <= 6000) {
      selectedPackageSpan.textContent = 'Platformă avansată – pentru medii și mari întreprinderi cu funcționalități complexe.';
    } else if (value > 6000 && value <= 8000) {
      selectedPackageSpan.textContent = 'Platformă extinsă – integrări complexe cu sisteme externe și automații personalizate.';
    } else if (value > 8000) {
      selectedPackageSpan.textContent = 'Soluție enterprise – arhitectură scalabilă, securitate avansată și suport dedicat.';
    }
    if (value > 20000) {
      selectedAmountSpan.textContent = '+20000';
    }
  }
  rangeInput.addEventListener('input', function () {
    updateDescription(this.value);
  });

  // Initialize description
  updateDescription(rangeInput.value);
});
document.addEventListener("DOMContentLoaded", function () {
  // Get the form element dynamically
  var formElement = document.querySelector('form.wpcf7-form');

  // Safeguard in case formElement is not found or id is null
  var html_id = formElement ? formElement.id : null;
  console.log(html_id);

  // Get the select element for domain activity
  var domeniuActivitateSelect = document.querySelector('[name="domeniu-activitate"]');
  if (domeniuActivitateSelect && html_id) {
    // Function to update visibility of page-types fields based on the selected value
    var updatePageTypesVisibility = function updatePageTypesVisibility(selectedValue) {
      var pageTypesFields = document.querySelectorAll('.page-types-field');
      pageTypesFields.forEach(function (field) {
        return field.style.display = 'none';
      }); // Hide all fields by default

      if (selectedValue) {
        // Construct the target field ID
        var targetField = document.getElementById("page-types-".concat(selectedValue, "-wrapper"));
        if (targetField) {
          targetField.style.display = 'block';
        } else {
          console.warn("Field with ID page-types-".concat(selectedValue, "-wrapper not found, showing default."));
          var defaultField = document.getElementById('page-types-default-wrapper');
          if (defaultField) {
            defaultField.style.display = 'block';
          } else {
            console.error("Default page-types field not found");
          }
        }
      } else {
        var _defaultField = document.getElementById('page-types-default-wrapper');
        if (_defaultField) {
          _defaultField.style.display = 'block';
        } else {
          console.error("Default page-types field not found");
        }
      }
    }; // Initial update based on the selected option or fallback to the default
    // Auto-select the option in the dropdown if it matches the form ID
    domeniuActivitateSelect.value = html_id;
    updatePageTypesVisibility(domeniuActivitateSelect.value);

    // Update page-types visibility dynamically when the selection changes
    domeniuActivitateSelect.addEventListener('change', function () {
      updatePageTypesVisibility(this.value);
    });
  } else {
    // nothing
  }
});
document.addEventListener("DOMContentLoaded", function () {
  var letters = document.querySelectorAll('.letters .char-letters');
  var numbers = document.querySelectorAll('.numbers .char-numbers');
  setTimeout(function () {
    letters.forEach(function (letter, index) {
      setTimeout(function () {
        letter.classList.add('fade-out');
        numbers[index].classList.add('fade-in');
      }, index * 140); // Delay each character's animation slightly
    });
  }, 3500); // Start the animation after 1.5 seconds
});