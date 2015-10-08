/**
 * @file
 * JS fixed top nav bar .
 */
(function($, Backdrop, window, document, undefined) {
  $(document).ready(function() {
    $("#navbar").detach().prependTo('body');
    $("#navbar").removeClass('navbar-fixed-top');
    $("#navbar").addClass('navbar-static-top');

    jQuery(window).scroll(function() {
      var win = jQuery(this);
      if (win.scrollTop() > 33) {
        $("#navbar").addClass('navbar-fixed-top');
        $("#navbar").removeClass('navbar-static-top');
        $("body").addClass('navbar-is-fixed-top-padding');
      } else {
        $("#navbar").removeClass('navbar-fixed-top');
        $("#navbar").addClass('navbar-static-top');
        $("body").removeClass('navbar-is-fixed-top-padding');
      }
    });


  });
})(jQuery, Backdrop, this, this.document);
