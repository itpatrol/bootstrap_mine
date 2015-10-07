/**
 * @file
 * JS fixed top nav bar .
 */
(function($, Backdrop, window, document, undefined) {
  $(document).ready(function() {
    alert('test');
    $("#navbar").addClass('navbar-fixed-top');
    $("#navbar").removeClass('navbar-static-top');

    jQuery(window).scroll(function() {
      var win = jQuery(this);
      if (win.scrollTop() > 33) {
        $("#navbar").addClass('navbar-fixed-top');
        $("#navbar").removeClass('navbar-static-top');
      } else {
        $("#navbar").removeClass('navbar-fixed-top');
        $("#navbar").addClass('navbar-static-top');
      }
    });


  });
})(jQuery, Backdrop, this, this.document);
