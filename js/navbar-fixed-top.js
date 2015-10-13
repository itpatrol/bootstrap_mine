/**
 * @file
 * JS fixed top nav bar .
 */
(function($, Backdrop, window, document, undefined) {
  $(document).ready(function() {
    $("#navbar").detach().prependTo('body');

    jQuery(window).scroll(function() {
      var win = jQuery(this);
      if (win.scrollTop() > 33) {
        $("body").addClass('navbar-is-fixed-top-padding');
      } else {
        $("body").removeClass('navbar-is-fixed-top-padding');
      }
    });


  });
})(jQuery, Backdrop, this, this.document);
