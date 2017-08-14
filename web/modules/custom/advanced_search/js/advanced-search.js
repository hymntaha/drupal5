(function ($, Drupal) {

    'use strict';

    Drupal.behaviors.advanced_search = {
        attach: function (context, settings) {
            var $autocomplete = $('input.form-autocomplete', context);

            if($autocomplete.val() !== ''){
                $autocomplete.val($autocomplete.val().replace(/.\(([^\)]+)\)/, ''));
                $autocomplete.addClass('text-visible');
            }
            else{
                $autocomplete.addClass('text-visible');
            }

            $autocomplete.click(function(e){
                $(this).select();
            });

            $autocomplete.on('autocompleteselect', function(e, ui){
                $('#edit-keyword').val(ui.item.value);
                ui.item.value = ui.item.value.replace(/.\(([^\)]+)\)/, '');
            });
        }
    };

})(jQuery, Drupal);