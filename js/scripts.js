jQuery(document).ready(function ($) {

  // Check for style 1 class
  if ($('.et_divi_100_custom_login_page--style-1').length) {
    $('.input').each(function (index) {

      // Prepend Ion Icon
      if (!index == 1) {
        $(this).parent().prepend('<span class="icon ion-person"></span>');
      } else {
        $(this).parent().prepend('<span class="icon ion-locked"></span>');
      }

      // Add focused class to input with value
      if (!$(this).val() == '') {
        $(this).parent().addClass('focused');
      }
    });

    // Add class on focus
    $('.input').focus(function () {
      $(this).parent().addClass('focused');
    });

    // Remove class on blur
    $('.input').blur(function () {
      if ($(this).val() === '') {
        $(this).parent().removeClass('focused');
      }
    });
  }
});