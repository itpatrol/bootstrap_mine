(function ($) {

Backdrop.bootstrap = Backdrop.bootstrap || {};
Backdrop.bootstrap.behaviors = Backdrop.bootstrap.behaviors || {};
/**
 * Attach administrative behaviors.
 */
Backdrop.bootstrap.attachBehaviors = function (context, settings) {
  $("body.navbar-is-fixed-top" ).scroll(function() {
    alert('test');
  });
};

})(jQuery);
