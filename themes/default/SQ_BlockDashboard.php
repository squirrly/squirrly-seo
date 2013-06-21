<div id="sq_settings" >
    <?php SQ_ObjController::getBlock('SQ_BlockSupport')->init(); ?>
    <?php  if ($view->options['sq_api'] <> ''){ ?>
    <div id="sq_settings_title" ><?php _e('Squirrly dashboard', _PLUGIN_NAME_); ?> </div>
    <div id="sq_settings_body">
        <fieldset>
            <legend><?php _e('User level', _PLUGIN_NAME_); ?></legend>
            <div class="sq_option_content">
                   <div class="sq_switch sq_beginner_user">
                     <input id="sq_beginner_on" type="radio" class="sq_switch-input" name="sq_beginner_user"  value="1" <?php echo (($view->options['sq_beginner_user'] == 0) ? "checked" : '')?> />
                     <label for="sq_beginner_on" class="sq_switch-label sq_switch-label-off"><?php _e('Beginner', _PLUGIN_NAME_); ?></label>
                     <input id="sq_beginner_off" type="radio" class="sq_switch-input" name="sq_beginner_user" value="0" <?php echo ((!$view->options['sq_beginner_user'] == 1) ? "checked" : '')?> />
                     <label for="sq_beginner_off" class="sq_switch-label sq_switch-label-on"><?php _e('Advanced', _PLUGIN_NAME_); ?></label>
                     <span class="sq_switch-selection"></span>
                   </div>
               </div>
            <span class="sq_settings_info"><?php _e('Select Advanced only if you have SEO knowledge.', _PLUGIN_NAME_); ?></span>

            <div id="sq_beginner_option_details" class="sq_option_details" <?php echo (($view->options['sq_beginner_user'] == 0) ? 'style="display: none;"' : '')?>>
                <div class="sq_header"><?php _e('What does the Beginner option bring you:', _PLUGIN_NAME_); ?></div>
                <ul>
                    <li><?php _e('Squirrly <strong>finds the optimum Title and Description</strong> for each page of your blog but you can still customize the home page Title and Description if you want.', _PLUGIN_NAME_); ?></li>
                    <li><?php _e('Squirrly <strong>manages the sitemap</strong> for your blog and pings it to Google and Bing every time you add a new article. This ensures that your articles get indexed much faster.', _PLUGIN_NAME_); ?></li>
                    <li><?php _e('Squirrly <strong>adds the Facebook required meta</strong>, so that when your readers share your page or article, it looks really good.', _PLUGIN_NAME_); ?></li>
                    <li><?php _e('You can connect your site with your <strong>Google Plus</strong> and <strong>Google Analytics</strong> accounts.', _PLUGIN_NAME_); ?></li>
                    <li><?php _e('You can add the site icon (also known as favicon) and Squirrly will set it up to look good for <strong>Apple devices</strong>.', _PLUGIN_NAME_); ?></li>
                </ul>
            </div>

            <div id="sq_advanced_option_details" class="sq_option_details" <?php echo (($view->options['sq_beginner_user'] == 1) ? 'style="display: none;"' : '')?>>
                <div class="sq_header"><?php _e('What does the Advanced option bring you:', _PLUGIN_NAME_); ?></div>
                <ul>
                    <li><?php _e('More <strong>SEO options</strong> are available on the Settings page.', _PLUGIN_NAME_); ?></li>
                    <li><?php _e('You can connect your site with <strong>Google Webmaster Tools</strong>, <strong>Bing Webmaster Tools</strong> and <strong>Facebook Insights</strong>.', _PLUGIN_NAME_); ?></li>
                    <li><?php _e('You can see the <strong>Snippet</strong> when you edit your Post or Page.', _PLUGIN_NAME_); ?></li>
                    <li><?php _e('You can <strong>customize the Title and Description</strong> of each Post/Page from within the snippet.', _PLUGIN_NAME_); ?></li>
                </ul>
            </div>
        </fieldset>
        <?php
        if ($view->options['sq_api'] <> ''){
                echo '<fieldset><div id="sq_userinfo"></div></fieldset>
                    <fieldset><div id="sq_userstatus"></div></fieldset>
                <script type="text/javascript">
                   jQuery(document).ready(function() {
                        sq_getUserStatus("'._SQ_API_URL_.'", "'.SQ_Tools::$options['sq_api'].'");
                   });
                </script>';
        }?>


        <div id="sq_settings_title" style="text-align: right">
          <input id="sq_goto_settings" type="button" value="<?php _e('Go to settings', _PLUGIN_NAME_)?> &raquo;" />
        </div>

    </div>
    <?php }?>
</div>
