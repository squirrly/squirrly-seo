jQuery(document).ready(function() {
    var snippet_timeout;
    
    jQuery('#sq_customize').bind('click',function(){
        jQuery('#sq_customize_settings').show();
        jQuery('#sq_snippet_disclaimer').show();
        
    });
    jQuery('#sq_automatically').bind('click',function(){
        jQuery('#sq_customize_settings').hide();
        jQuery('#sq_snippet_disclaimer').hide();
    });
   
    jQuery("#sq_settings_howto_close").bind('click',function() {
        jQuery('#sq_settings_howto').hide();
        
        jQuery.getJSON(
            sqQuery.ajaxurl,
            {
                action: 'sq_howto',
                sq_howto: '0',
                nonce: sqQuery.nonce
            }
        );
    });

    jQuery('#sq_settings').find('input[type=radio]').bind('click',function(){
        sq_submitSettings();
        sq_getSnippet();
    });
    
    jQuery('#sq_settings').find('input[name=sq_fp_title]').bind('keyup',function() {
        if (snippet_timeout){
            clearTimeout(snippet_timeout);
        }
                            
        snippet_timeout = setTimeout(function(){
            sq_submitSettings();
            sq_getSnippet();
        },1000)
    });
    
    jQuery('#sq_settings').find('textarea[name=sq_fp_description]').bind('keyup',function() {
        if (snippet_timeout){
            clearTimeout(snippet_timeout);
        }
                            
        snippet_timeout = setTimeout(function(){
            sq_submitSettings();
            sq_getSnippet();
        },1000)
    });
    
    if (jQuery('#sq_settings').find('input[name=sq_auto_seo]').length > 0){
        sq_getSnippet();
    }
    
    jQuery('#sq_use_on').bind('click',function(){
        jQuery('#sq_settings_sq_use .sq_option_content_small .sq_switch').show();
        jQuery('#sq_title_description_keywords').slideDown('fast');
        
        if(parseInt(jQuery('.sq_count').html())>0) { 
            var notif = (parseInt(jQuery('.sq_count').html()) - 1); 
            if (notif > 0) {jQuery('.sq_count').html(notif); }else{ jQuery('.sq_count').html(notif); jQuery('.sq_count').hide(); }
        }
        jQuery('#sq_fix_auto').slideUp('show');
    });
    
    jQuery('#sq_use_off').bind('click',function(){
        jQuery('#sq_settings_sq_use .sq_option_content_small .sq_switch').hide();
        jQuery('#sq_title_description_keywords').slideUp('fast');
        
         if(parseInt(jQuery('.sq_count').html())>=0) { 
            var notif = (parseInt(jQuery('.sq_count').html()) + 1); 
            if (notif > 0) {jQuery('.sq_count').html(notif).show();}
        }
        jQuery('#sq_fix_auto').slideDown('show');
    });
     
    
    jQuery('#sq_google_index1').bind('click',function(){
        if(parseInt(jQuery('.sq_count').html())>0) { 
            var notif = (parseInt(jQuery('.sq_count').html()) - 1); 
            if (notif > 0) {jQuery('.sq_count').html(notif); }else{ jQuery('.sq_count').html(notif); jQuery('.sq_count').hide(); }
        }
        jQuery('#sq_fix_private').slideUp('show');
        
    });
    jQuery('#sq_google_index0').bind('click',function(){
        if(parseInt(jQuery('.sq_count').html())>=0) { 
            var notif = (parseInt(jQuery('.sq_count').html()) + 1); 
            if (notif > 0) {jQuery('.sq_count').html(notif).show();}
        }
        jQuery('#sq_fix_private').slideDown('show');
    });
    
    jQuery('#sq_auto_twitter1').bind('click',function(){
        jQuery('#sq_twitter_account').show();
        
    });
    jQuery('#sq_auto_twitter0').bind('click',function(){
        jQuery('#sq_twitter_account').hide();
    });
});
function sq_checkSEOLength(){
    
}
function sq_getSnippet(url, show_url){
    if (typeof url == 'undefined') url = '';
    if (typeof sq_blogurl != 'undefined') url = sq_blogurl;
    
    if (typeof show_url == 'undefined') show_url = '';
    
    jQuery('#sq_snippet_ul').addClass('sq_minloading');
    
    jQuery('#sq_snippet_title').html('');
    jQuery('#sq_snippet_url').html('');
    jQuery('#sq_snippet_description').html('');
    jQuery('#sq_snippet').show();
    jQuery('#sq_snippet_update').hide();
    
    setTimeout(function(){
        jQuery.getJSON(
            sqQuery.ajaxurl,
            {
                action: 'sq_get_snippet',
                url: url,
                nonce: sqQuery.nonce
            }
        ).success(function(response) { 
            jQuery('#sq_snippet_ul').removeClass('sq_minloading');
            jQuery('#sq_snippet_update').show();
            if (response){
                jQuery('#sq_snippet_title').html('<a href="'+url+'" target="_blank">'+response.title+'</a>');
                if (show_url != '')
                    jQuery('#sq_snippet_url').html('<a href="'+url+'" target="_blank">'+show_url+'</a>');
                else
                    jQuery('#sq_snippet_url').html(response.url);

                jQuery('#sq_snippet_description').html(response.description);
            }
         }).error(function() { 
            jQuery('#sq_snippet_ul').removeClass('sq_minloading');
            jQuery('#sq_snippet_update').show();
         }).complete(function() { 
            jQuery('#sq_snippet_ul').removeClass('sq_minloading');
            jQuery('#sq_snippet_update').show();
         });
    },500);
}

function sq_submitSettings(){
     jQuery.getJSON(
            sqQuery.ajaxurl,
            {
                action: 'sq_settings_update',
                
                sq_use: jQuery('#sq_settings').find('input[name=sq_use]:checked').val(),
                sq_auto_title: jQuery('#sq_settings').find('input[name=sq_auto_title]:checked').val(),
                sq_auto_description: jQuery('#sq_settings').find('input[name=sq_auto_description]:checked').val(),
                sq_auto_canonical: jQuery('#sq_settings').find('input[name=sq_auto_canonical]:checked').val(),
                sq_auto_sitemap: jQuery('#sq_settings').find('input[name=sq_auto_sitemap]:checked').val(),
                sq_auto_meta: jQuery('#sq_settings').find('input[name=sq_auto_meta]:checked').val(),
                sq_auto_favicon: jQuery('#sq_settings').find('input[name=sq_auto_favicon]:checked').val(),
                sq_auto_facebook: jQuery('#sq_settings').find('input[name=sq_auto_facebook]:checked').val(),
                sq_auto_twitter: jQuery('#sq_settings').find('input[name=sq_auto_twitter]:checked').val(),
                sq_twitter_account: jQuery('#sq_settings').find('input[name=sq_twitter_account]').val(),
                
                sq_auto_seo: jQuery('#sq_settings').find('input[name=sq_auto_seo]:checked').val(),
                sq_fp_title: jQuery('#sq_settings').find('input[name=sq_fp_title]').val(),
                sq_fp_description: jQuery('#sq_settings').find('textarea[name=sq_fp_description]').val(),
                sq_fp_keywords: jQuery('#sq_settings').find('input[name=sq_fp_keywords]').val(),
                
                ignore_warn: jQuery('#sq_settings').find('input[name=ignore_warn]:checked').val(),
                sq_keyword_help: jQuery('#sq_settings').find('input[name=sq_keyword_help]:checked').val(),
                sq_keyword_information: jQuery('#sq_settings').find('input[name=sq_keyword_information]:checked').val(),
                
                sq_google_plus: jQuery('#sq_settings').find('input[name=sq_google_plus]').val(),
                sq_google_wt: jQuery('#sq_settings').find('input[name=sq_google_wt]').val(),
                sq_google_analytics: jQuery('#sq_settings').find('input[name=sq_google_analytics]').val(),
                sq_facebook_insights: jQuery('#sq_settings').find('input[name=sq_facebook_insights]').val(),
                sq_bing_wt: jQuery('#sq_settings').find('input[name=sq_bing_wt]').val(),
                
                nonce: sqQuery.nonce
            }
    );
    
}

function sq_getUserInfo(api_url, token){    
    //jQuery('#sq_userinfo').addClass('sq_loading');
    
    jQuery.getJSON(
        api_url + 'sq/user/info/?callback=?',
        {
          token: token                    
        }
    ).success(function(response) { 
       //jQuery('#sq_userinfo').removeClass('sq_loading').removeClass('sq_error');
       if (response.info != ''){
         jQuery('#sq_userinfo').html(response.info);
       }
    }).error(function() { 
       // jQuery('#sq_userinfo').removeClass('sq_loading');
        jQuery('#sq_userinfo').html('');
    })
}