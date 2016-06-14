jQuery(document).ready(function ($) {
  /**
   * Checks if custom login page class exist
   */
  if ($('.et_divi_100_custom_login_page').length >= 1) {
    /**
     * Loop through each input that exist on the page
     */
    $('.input').each(function (index) {
      /**
       * Appends ion icon
       * Adds class to parent
       */
      if ($(this).is('#user_login')) {
        $(this)
          .parent()
          .parent()
          .prepend('<span class="icon ion-person"></span>');
        $(this)
          .parent()
          .parent()
          .addClass('divi-login__input--user');
      } else if ($(this).is('#user_pass')) {
        $(this)
          .parent()
          .parent()
          .prepend('<span class="icon ion-locked"></span>');
        $(this)
          .parent()
          .parent()
          .addClass('divi-login__input--password');
      } else if ($(this).is('#user_email')) {
        $(this)
          .parent()
          .parent()
          .prepend('<span class="icon ion-email"></span>');
        $(this)
          .parent()
          .parent()
          .addClass('divi-login__input--email');
      }

      /**
       * Appends input to parent
       */
      $(this)
        .parent()
        .parent()
        .append($(this));

      /**
       * Removes label
       */
      $(this)
        .parent()
        .children('label')
        .remove();

      /**
       * Replace <p /> with <div />
       */
      $(this)
        .parent()
        .addClass('divi-login__input')
        .replaceWith('<div class="divi-login__input">' + $(this).parent().html() + '</div>');

      /**
       * Adds class to login form
       */
      $('#loginform, #lostpasswordform')
        .addClass('divi-login');

      /**
       * Adds class to remember me
       */
      $('.forgetmenot')
        .addClass('divi-login__remember');

      /**
       * Adds class to submit
       */
      $('.submit')
        .addClass('divi-login__submit');

      /**
       * Adds focused class to inputs with value
       */
      if (!$(this).val() == '') {
        $(this)
          .parent()
          .addClass('focused');
      }
    });

    /**
     * Adds focused class on focus
     */
    $('.input').focus(function () {
      $(this).parent().addClass('focused');
    });

    /**
     * Removes focused class on blur
     */
    $('.input').blur(function () {
      if ($(this).val() === '') {
        $(this).parent().removeClass('focused');
      }
    });
  }

  if ($('.et_divi_100_custom_login_page--style-5').length >= 1 || $('.et_divi_100_custom_login_page--style-6').length >= 1) {
    $('div#login h1').prependTo('.divi-login');
  }
});