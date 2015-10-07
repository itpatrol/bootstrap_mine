(function ($) {
  /**
 * Attach administrative behaviors.
 */
Backdrop.bootstrap.attachBehaviors = function (context, settings) {
  $("body.navbar-is-fixed-top" ).scroll(function() {
    alert('test');
  });
};

})(jQuery);
