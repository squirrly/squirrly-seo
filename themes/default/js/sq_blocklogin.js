jQuery(document).ready(function() {
    jQuery('#sq_email').bind('keypress', function(event) {

        if (event.keyCode == 13)
            sq_autoLogin();

        return event.keyCode != 13;
    });

    jQuery('#sq_user').bind('keypress', function(event) {

        if (event.keyCode == 13)
            jQuery('#sq_login').trigger('click');

        return event.keyCode != 13;
    });

    jQuery('#sq_password').bind('keypress', function(event) {

        if (event.keyCode == 13)
            jQuery('#sq_login').trigger('click');

        return event.keyCode != 13;
    });

    jQuery('#sq_signin').bind('click', function(event){
        jQuery('#sq_autologin').hide();
        jQuery('#sq_blocklogin').find('ul').show();

        //jQuery('#sq_blocklogin').find('.sq_message').html(response.info).show();
        jQuery('#sq_user').val(jQuery('#sq_email').val());
        jQuery('#sq_email').focus();
    });

    jQuery('#sq_signup').bind('click', function(event){
        jQuery('#sq_autologin').show();
        jQuery('#sq_blocklogin').find('ul').hide();

        //jQuery('#sq_blocklogin').find('.sq_message').html(response.info).show();
        //jQuery('#sq_user').val(jQuery('#sq_email').val());
        jQuery('#sq_email').focus();
    });

    jQuery('#sq_login').bind('click', function(){
      jQuery('#sq_login').addClass('sq_minloading');
      jQuery('#sq_login').attr("disabled", "disabled");
      jQuery('#sq_login').val('');

        jQuery.getJSON(
            sqQuery.ajaxurl,
            {
                action: 'sq_login',
                user: jQuery('#sq_user').val(),
                password: jQuery('#sq_password').val(),
                nonce: sqQuery.nonce
            }
        ).success(function(response) {
           jQuery('#sq_login').removeAttr("disabled");
           jQuery('#sq_login').val('Login');
           jQuery('#sq_login').removeClass('sq_minloading');
           if (typeof response.token != 'undefined'){
             __token = response.token;
             sq_reload(response);
           }else
               if(typeof response.error != 'undefined')
                   jQuery('#sq_blocklogin').find('.sq_error').html(response.error);

        }).error(function(response) {
            if( response.status == 200 && response.responseText.indexOf('{') > 0){
                    response.responseText = response.responseText.substr(response.responseText.indexOf('{'),response.responseText.lastIndexOf('}'));
                    try {
                        response = jQuery.parseJSON(response.responseText);
                        jQuery('#sq_login').removeAttr("disabled");
                        jQuery('#sq_login').val('Login');
                        jQuery('#sq_login').removeClass('sq_minloading');

                        if (typeof response.token != 'undefined'){
                          __token = response.token;
                          sq_reload(response);
                        }else
                          if(typeof response.error != 'undefined')
                              jQuery('#sq_blocklogin').find('.sq_error').html(response.error);

                    }catch(e){}

            }else{
                jQuery('#sq_login').removeAttr("disabled");
                jQuery('#sq_login').val('Login');
                jQuery('#sq_login').removeClass('sq_minloading');
                jQuery('#sq_blocklogin').find('.sq_error').html(__error_login);
            }
        });
    });

});

function sq_autoLogin(){
  if (!checkEmail(jQuery('#sq_email').val())){
    jQuery('#sq_blocklogin').find('.sq_error').html(__invalid_email);
    jQuery('#sq_register_email').show();
    jQuery('#sq_register').html(__try_again);
    return false;
  }

  jQuery('#sq_register').html(__connecting);
  jQuery('#sq_register_wait').addClass('sq_minloading');
  jQuery('#sq_blocklogin').find('.sq_message').hide();


  jQuery.getJSON(
        sqQuery.ajaxurl,
        {
            action: 'sq_register',
            email: jQuery('#sq_email').val(),
            nonce: sqQuery.nonce
        }
    ).success(function(response) {

       jQuery('#sq_register_wait').removeClass('sq_minloading');
       if (typeof response.token != 'undefined'){
         __token = response.token;
         jQuery('#sq_blocklogin').remove();
         if (typeof response.success != 'undefined'){
              jQuery('#sq_login_success').html(response.success);
         }
         //window.sq_main.load();
         sq_reload();
       }else{
           if(typeof response.info != 'undefined'){
               jQuery('#sq_autologin').hide();
               jQuery('#sq_blocklogin').find('ul').show();

               jQuery('#sq_blocklogin').find('.sq_message').html(response.info).show();
               jQuery('#sq_user').val(jQuery('#sq_email').val());
               jQuery('#sq_password').focus();
           }else{
             if(typeof response.error != 'undefined'){
               jQuery('#sq_blocklogin').find('.sq_error').html(response.error);
               jQuery('#sq_register_email').show();
               jQuery('#sq_register').html(__try_again);
             }
           }

       }

    }).error(function(response) {
        if( response.status == 200 && response.responseText.indexOf('{') > 0){
                response.responseText = response.responseText.substr(response.responseText.indexOf('{'),response.responseText.lastIndexOf('}'));
                try {
                    response = jQuery.parseJSON(response.responseText);
                    if(typeof response.info != 'undefined'){
                        jQuery('#sq_autologin').hide();
                        jQuery('#sq_blocklogin').find('ul').show();

                        jQuery('#sq_blocklogin').find('.sq_message').html(response.info).show();
                        jQuery('#sq_user').val(jQuery('#sq_email').val());
                        jQuery('#sq_password').focus();
                    }else{
                      if(typeof response.error != 'undefined'){
                        jQuery('#sq_blocklogin').find('.sq_error').html(response.error);
                        jQuery('#sq_register_email').show();
                        jQuery('#sq_register').html(__try_again);
                      }
                    }
                }catch(e){}

        }else{

            jQuery('#sq_register_wait').removeClass('sq_minloading');
            jQuery('#sq_blocklogin').find('.sq_error').html(__error_login);
            jQuery('#sq_register_email').show();
            jQuery('#sq_register').html(__try_again);
        }
    });
}

function sq_reload(response){
    if (typeof response.success != 'undefined'){
       jQuery('#sq_login_success').html(response.success);
    }
    if (jQuery('#content-html').length > 0){
        jQuery('#sq_blocklogin').remove();
        location.reload();
    }else{
        jQuery('#sq_blocklogin').addClass('sq_login_done');
        jQuery('#sq_blocklogin').html(jQuery('#sq_login_success'));
        jQuery('#sq_blocklogin').append(jQuery('#sq_goto_newpost'));
        jQuery('#sq_login_success').show();
        jQuery('#sq_goto_newpost').show();
    }
}
function checkEmail(email)
{
    var emailRegEx = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i;

    if (email != '')
        if(emailRegEx.test(email)) {
          return true;
        } else {
          return false;
        }

    return true;
}
