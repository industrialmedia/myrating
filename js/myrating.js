(function ($, window, Drupal, drupalSettings) {

  'use strict';

  Drupal.behaviors.myrating = {
    attach: function (context, settings) {


      //========================================================================
      // [[ BEGIN ]] Fix star submit form
      //========================================================================
      $('form[class^="fivestar-form-"]', context).once('myrating').each(function () {
        var $form = $(this);
        $form.find('select').change(function () {
          $form.find('input[type="submit"]').click();
        });
      });
      // [[ END ]]
      //========================================================================


    }
  };


})(jQuery, window, Drupal, drupalSettings);
