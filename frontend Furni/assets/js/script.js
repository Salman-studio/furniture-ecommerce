/* furniture/assets/js/script.js
   General site JS for UI interactions.
   Dependencies: jQuery (included via assets/jquery.min.js or CDN).
*/

$(document).ready(function(){
  // Simple mobile menu toggle
  $('.navbar-toggler').on('click', function(){
    $('.navbar-collapse').toggleClass('show');
  });

  // Smooth scroll for anchor links
  $('a[href^="#"]').on('click', function(e){
    e.preventDefault();
    var target = $(this.hash);
    if(target.length) {
      $('html, body').animate({scrollTop: target.offset().top - 70}, 600);
    }
  });

  // Tiny accessibility enhancement: focus outlines on keyboard navigation
  $(document).on('keyup', function(e){
    if(e.key === "Tab"){
      $('body').addClass('user-is-tabbing');
    }
  });
});
