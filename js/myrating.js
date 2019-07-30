(function ($, window, Drupal, drupalSettings) {

  'use strict';

  Drupal.behaviors.myrating = {
    attach: function (context, settings) {


      //========================================================================
      // [[ BEGIN ]] Fix star submit form - Отключил, уже работает
      //========================================================================
      /*
      $('form[class^="fivestar-form-"]', context).once('myrating').each(function () {
        $(this).find('select').change(function(){

          alert(5);

        });
      });
      */
      // [[ END ]]
      //========================================================================


    }
  };







})(jQuery, window, Drupal, drupalSettings);
