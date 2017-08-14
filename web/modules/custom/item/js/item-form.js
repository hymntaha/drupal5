(function ($, Drupal) {

    'use strict';

    Drupal.behaviors.item_form = {
        attach: function (context, settings) {
            $('.field--name-field-date .form-time', context).val('00:00');
        }
    };

})(jQuery, Drupal);