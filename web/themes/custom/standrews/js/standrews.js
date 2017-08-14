(function ($, Drupal) {

    'use strict';

    Drupal.behaviors.standrews = {
        attach: function (context, settings) {
            var resizeTimer;

            loadFunctions(context);

            $(window, context).resize(function(){
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(resizeFunctions(context), 250);
            });

            function loadFunctions(context) {
                setBodyBreakpoint(context);
            }

            function resizeFunctions(context){
                setBodyBreakpoint(context);
            }

            function setBodyBreakpoint(context){
                $('body', context).attr('data-breakpoint',Drupal.behaviors.standrews.getCurrentBootstrapBreakpoint());
            }
        },

        getCurrentBootstrapBreakpoint: function () {
            if (viewportSize.getWidth() < 768) {
                return 'xs';
            }
            else if (viewportSize.getWidth() < 992) {
                return 'sm';
            }
            else if (viewportSize.getWidth() < 1200) {
                return 'md';
            }
            else {
                return 'lg';
            }
        }
    };

})(jQuery, Drupal);