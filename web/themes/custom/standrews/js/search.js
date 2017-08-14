(function ($, Drupal) {

    'use strict';

    Drupal.behaviors.search = {
        attach: function (context, settings) {
            loadFunctions(context);

            function loadFunctions(context) {
                bindSearchIPP(context);
            }

            function bindSearchIPP(context){
                $('.ipp-select', context).change(function(){
                    var uri = new URI();
                    uri.removeQuery('ipp');
                    uri.addQuery('ipp', $(this).val());
                    window.location = uri;
                });
            }
        }
    };

})(jQuery, Drupal);