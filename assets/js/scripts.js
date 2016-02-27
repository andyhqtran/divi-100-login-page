jQuery(document).ready(function ($) {
  var styleCheck = function (value) {
    if (value) {
      return $('.et_divi_100_custom_login_page--style-' + value).length;
    } else {
      return $('.et_divi_100_custom_login_page').length;
    }
  }

  if (styleCheck()) {
    $('.input').each(function (index) {

      // Prepend Ion Icon
      if ($(this).is('#user_login')) {
        $(this).parent().prepend('<span class="icon ion-person"></span>');
      } else if ($(this).is('#user_pass')) {
        $(this).parent().prepend('<span class="icon ion-locked"></span>');
      } else if ($(this).is('#user_email')) {
        $(this).parent().prepend('<span class="icon ion-email"></span>');
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