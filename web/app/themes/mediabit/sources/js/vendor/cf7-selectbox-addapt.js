document.addEventListener('DOMContentLoaded', function() {
    var checkboxes = document.querySelectorAll('.form-check-box .wpcf7-list-item');
  
    checkboxes.forEach(function(item) {
      item.addEventListener('click', function() {
        console.log('clicked');
        // Toggle the selected class on the span label
        var label = this.querySelector('.wpcf7-list-item-label');
        label.classList.toggle('selected');
        
        // Check/uncheck the checkbox
        var checkbox = this.querySelector('input[type=checkbox]');
        checkbox.checked = !checkbox.checked;
      });
    });
});