(function ($) {
  /**
 * Attach administrative behaviors.
 */
Backdrop.bootstrap.attachBehaviors = function (context, settings) {
  $( "body" ).scroll(function() {
    alert('test');
  });
};

})(jQuery);
