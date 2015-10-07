/**
 * @file
 * JS fixed top nav bar .
 */
(function($, Backdrop, window, document, undefined) {
  $(document).ready(function() {

    // run Javascript on page load here
    console.log("Welcome to the console");

    jQuery(window).scroll($.throttle( 100, function() {
      var win = jQuery(this);
      if (win.scrollTop() > 33) {
        $("html.admin-bar header.navbar-fixed-top").css("top", "0px");
      } else {
        $("html.admin-bar header.navbar-fixed-top").css("top",  (33 - win.scrollTop()) + "px");
      }
    }));


  });
})(jQuery, Backdrop, this, this.document);
