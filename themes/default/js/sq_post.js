jQuery(document).ready(function() {

    jQuery('#sq_options_support').find('span').bind('click',function(){
        jQuery('.sq_options_support_popup').show();
        jQuery('.sq_options_feedback_popup').hide();
    });
    jQuery('#sq_options_close').bind('click',function() {
        jQuery('.sq_options_support_popup').hide();
    });


    jQuery('#sq_options_feedback').find('span').bind('click',function(){
        jQuery('.sq_options_feedback_popup').show();
        jQuery("#sq_options_feedback").find('.sq_push').hide();
        jQuery('.sq_options_support_popup').hide();
    });
    jQuery("#sq_options_feedback_close").bind('click',function() {
        jQuery('.sq_options_feedback_popup').hide();
    });

    jQuery("#sq_feedback_0").bind('click',function() {
        jQuery('#sq_feedback_msg').show();
    //    for(i=0;i<5;i++) jQuery('#sq_options_feedback').find('.sq_icon').removeClass('sq_label_feedback_' + i);
    //    jQuery('#sq_options_feedback').find('.sq_icon').addClass('sq_label_feedback_0');
    });
    jQuery("#sq_feedback_1").bind('click',function() {
        jQuery('#sq_feedback_msg').show();
    //    for(i=0;i<5;i++) jQuery('#sq_options_feedback').find('.sq_icon').removeClass('sq_label_feedback_' + i);
    //    jQuery('#sq_options_feedback').find('.sq_icon').addClass('sq_label_feedback_1');
    });
    jQuery("#sq_feedback_2").bind('click',function() {
        jQuery("#sq_feedback_submit").trigger('click');
        for(i=0;i<5;i++) jQuery('#sq_options_feedback').find('.sq_icon').removeClass('sq_label_feedback_' + i);
        jQuery('#sq_options_feedback').find('.sq_icon').addClass('sq_label_feedback_2');
    });
    jQuery("#sq_feedback_3").bind('click',function() {
        jQuery("#sq_feedback_submit").trigger('click');
        for(i=0;i<5;i++) jQuery('#sq_options_feedback').find('.sq_icon').removeClass('sq_label_feedback_' + i);
        jQuery('#sq_options_feedback').find('.sq_icon').addClass('sq_label_feedback_3');
    });
    jQuery("#sq_feedback_4").bind('click',function() {
        jQuery("#sq_feedback_submit").trigger('click');
        for(i=0;i<5;i++) jQuery('#sq_options_feedback').find('.sq_icon').removeClass('sq_label_feedback_' + i);
        jQuery('#sq_options_feedback').find('.sq_icon').addClass('sq_label_feedback_4');
    });

    jQuery("#sq_feedback_submit").bind('click',function() {
        jQuery('#sq_feedback_msg').hide();
        jQuery('#sq_options_feedback_error').html('<p class="sq_minloading" style="margin:0 auto; padding:2px;"></p>')
        jQuery('#sq_feedback_submit').attr("disabled", "disabled");


        document.cookie = "sq_feedback_face="+jQuery("input[name=sq_feedback_face]:radio:checked").val()+"; expires=" + (60*12) + "; path=/";

        jQuery.getJSON(
            sqQuery.ajaxurl,
            {
                action: 'sq_feedback',
                feedback: jQuery("input[name=sq_feedback_face]:radio:checked").val(),
                message: jQuery("textarea[name=sq_feedback_message]").val(),
                nonce: sqQuery.nonce
            }
        ).success(function(response) { 
           jQuery('#sq_feedback_submit').removeAttr("disabled");
           jQuery('#sq_feedback_submit').val('Send feedback');
           jQuery("textarea[name=sq_feedback_message]").val('');

           if (typeof response.message != 'undefined'){
            jQuery('#sq_options_feedback_error').removeClass('sq_error').addClass('sq_message').html(response.message);
           }else
            jQuery('#sq_options_feedback_error').removeClass('sq_error').html('');



        }).error(function(response) { 
            if( response.status == 200 && response.responseText.indexOf('{') > 0){
                    response.responseText = response.responseText.substr(response.responseText.indexOf('{'),response.responseText.lastIndexOf('}'));
                    try {
                        response = jQuery.parseJSON(response.responseText);
                        jQuery('#sq_feedback_submit').removeAttr("disabled");
                        jQuery('#sq_feedback_submit').val('Send feedback');
                        jQuery("textarea[name=sq_feedback_message]").val('');

                        if (typeof response.message != 'undefined'){
                         jQuery('#sq_options_feedback_error').removeClass('sq_error').addClass('sq_message').html(response.message);
                        }else
                         jQuery('#sq_options_feedback_error').removeClass('sq_error').html('');
                    }catch(e){}
            
            }else{
                jQuery('#sq_feedback_submit').removeAttr("disabled");
                jQuery('#sq_feedback_submit').val('Send feedback');
                jQuery('#sq_feedback_submit').removeClass('sq_minloading');
                jQuery('#sq_options_feedback_error').addClass('sq_error').removeClass('sq_message').html('Could not send the feedback');
            }
        });
    });

    jQuery("#sq_support_submit").bind('click',function() {
        jQuery('#sq_options_support_error').html('<p class="sq_minloading" style="margin:0 auto; padding:2px;"></p>')
        jQuery('#sq_support_submit').attr("disabled", "disabled");

        jQuery.getJSON(
            sqQuery.ajaxurl,
            {
                action: 'sq_support',
                message: jQuery("textarea[name=sq_support_message]").val(),
                nonce: sqQuery.nonce
            }
        ).success(function(response) { 
           jQuery('#sq_support_submit').removeAttr("disabled");
           jQuery("textarea[name=sq_support_message]").val('');

           if (typeof response.message != 'undefined'){
            jQuery('#sq_options_support_error').removeClass('sq_error').addClass('sq_message').html(response.message);
           }else
            jQuery('#sq_options_support_error').removeClass('sq_error').html('');



        }).error(function(response) { 
            if( response.status == 200 && response.responseText.indexOf('{') > 0){
                    response.responseText = response.responseText.substr(response.responseText.indexOf('{'),response.responseText.lastIndexOf('}'));
                    try {
                        response = jQuery.parseJSON(response.responseText);
                        jQuery('#sq_support_submit').removeAttr("disabled");
                        jQuery("textarea[name=sq_support_message]").val('');

                        if (typeof response.message != 'undefined'){
                         jQuery('#sq_options_support_error').removeClass('sq_error').addClass('sq_message').html(response.message);
                        }else
                         jQuery('#sq_options_support_error').removeClass('sq_error').html('');
                    }catch(e){}
            
            }else{            
                jQuery('#sq_support_submit').removeAttr("disabled");
                jQuery('#sq_support_submit').val('Send feedback');
                jQuery('#sq_support_submit').removeClass('sq_minloading');
                jQuery('#sq_options_support_error').addClass('sq_error').removeClass('sq_message').html('Could not send the feedback');
            }
        });
    });
    
    
       
});