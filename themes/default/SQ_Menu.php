<div id="sq_settings" >
  <form id="sq_settings_form" name="settings" action="" method="post" enctype="multipart/form-data">

  <div id="sq_settings_title" ><?php _e('Squirrly settings', _PLUGIN_NAME_); ?> <input type="submit" name="sq_update" value="<?php _e('Save settings', _PLUGIN_NAME_)?> &raquo;" /> </div>
  <div id="sq_settings_body">
    <div id="sq_settings_left" >
        <?php
        /*if (SQ_Tools::$options['sq_api'] == ''){
            echo '<fieldset style="padding: 0; border: none;" ><div id="sq_settings_login">';
            SQ_ObjController::getBlock('SQ_Blocklogin')->init();
            echo '</div></fieldset>';
        }*/
        ?>
        <fieldset>
            <legend><?php _e('Let Squirrly automatically optimize my blog', _PLUGIN_NAME_); ?></legend>
            <div>

                <div class="sq_option_content">
                   <div class="sq_switch">
                     <input id="sq_use_on" type="radio" class="sq_switch-input" name="sq_use"  value="1" <?php echo (($view->options['sq_use'] == 1) ? "checked" : '')?> />
                     <label for="sq_use_on" class="sq_switch-label sq_switch-label-off"><?php _e('Yes', _PLUGIN_NAME_); ?></label>
                     <input id="sq_use_off" type="radio" class="sq_switch-input" name="sq_use" value="0" <?php echo ((!$view->options['sq_use']) ? "checked" : '')?> />
                     <label for="sq_use_off" class="sq_switch-label sq_switch-label-on"><?php _e('No', _PLUGIN_NAME_); ?></label>
                     <span class="sq_switch-selection"></span>
                   </div>
               </div>

                  <ul id="sq_settings_sq_use" class="sq_settings_info">
                      <span ><?php _e('What does Squirrly automatically do for SEO?', _PLUGIN_NAME_); ?></span>

                      <li>
                          <?php
                              $auto_option = false;
                              if($view->options['sq_auto_canonical'] == 1)
                                  $auto_option = true;
                          ?>
                          <div class="sq_option_content sq_option_content_small">
                            <div <?php echo ($view->options['sq_beginner_user'] == 1) ? '' : 'class="sq_switch"'?> style="<?php echo (($view->options['sq_use'] == 0 || $view->options['sq_beginner_user'] == 1) ? 'display:none;' : ''); ?>">
                              <input id="sq_auto_canonical1" type="radio" class="sq_switch-input" name="sq_auto_canonical"  value="1" <?php echo ($auto_option ? "checked" : '')?> />
                              <label for="sq_auto_canonical1" class="sq_switch-label sq_switch-label-off"><?php _e('Yes', _PLUGIN_NAME_); ?></label>
                              <input id="sq_auto_canonical0" type="radio" class="sq_switch-input" name="sq_auto_canonical" value="0" <?php echo (!$auto_option ? "checked" : '')?> />
                              <label for="sq_auto_canonical0" class="sq_switch-label sq_switch-label-on"><?php _e('No', _PLUGIN_NAME_); ?></label>
                              <span class="sq_switch-selection"></span>
                            </div>
                            <span><?php _e('Add <strong>canonical</strong> link in home page', _PLUGIN_NAME_); ?></span>
                          </div>
                      </li>
                      <li>
                          <?php
                              $auto_option = false;
                              if($view->options['sq_auto_sitemap'] == 1)
                                  $auto_option = true;
                          ?>
                          <div class="sq_option_content sq_option_content_small">
                            <div <?php echo ($view->options['sq_beginner_user'] == 1) ? '' : 'class="sq_switch"'?> style="<?php echo (($view->options['sq_use'] == 0 || $view->options['sq_beginner_user'] == 1) ? 'display:none;' : ''); ?>">
                              <input id="sq_auto_sitemap1" type="radio" class="sq_switch-input" name="sq_auto_sitemap"  value="1" <?php echo ($auto_option ? "checked" : '')?> />
                              <label for="sq_auto_sitemap1" class="sq_switch-label sq_switch-label-off"><?php _e('Yes', _PLUGIN_NAME_); ?></label>
                              <input id="sq_auto_sitemap0" type="radio" class="sq_switch-input" name="sq_auto_sitemap" value="0" <?php echo (!$auto_option ? "checked" : '')?> />
                              <label for="sq_auto_sitemap0" class="sq_switch-label sq_switch-label-on"><?php _e('No', _PLUGIN_NAME_); ?></label>
                              <span class="sq_switch-selection"></span>
                            </div>
                            <span><?php _e('Add the <strong>XML Sitemap</strong> for search engines', _PLUGIN_NAME_); ?>: <strong><?php echo '/sitemap.xml' ?></strong></span>
                          </div>
                      </li>
                      <li>
                          <?php
                              $auto_option = false;
                              if($view->options['sq_auto_meta'] == 1)
                                  $auto_option = true;
                          ?>
                          <div class="sq_option_content sq_option_content_small">
                            <div <?php echo ($view->options['sq_beginner_user'] == 1) ? '' : 'class="sq_switch"'?> style="<?php echo (($view->options['sq_use'] == 0 || $view->options['sq_beginner_user'] == 1) ? 'display:none;' : ''); ?>">
                              <input id="sq_auto_meta1" type="radio" class="sq_switch-input" name="sq_auto_meta"  value="1" <?php echo ($auto_option ? "checked" : '')?> />
                              <label for="sq_auto_meta1" class="sq_switch-label sq_switch-label-off"><?php _e('Yes', _PLUGIN_NAME_); ?></label>
                              <input id="sq_auto_meta0" type="radio" class="sq_switch-input" name="sq_auto_meta" value="0" <?php echo (!$auto_option ? "checked" : '')?> />
                              <label for="sq_auto_meta0" class="sq_switch-label sq_switch-label-on"><?php _e('No', _PLUGIN_NAME_); ?></label>
                              <span class="sq_switch-selection"></span>
                            </div>
                            <span><?php _e('Add the required METAs for home page (<strong>icon, author, language, dc publisher</strong>, etc.)', _PLUGIN_NAME_); ?></span>
                          </div>
                      </li>
                      <li>
                          <?php
                              $auto_option = false;
                              if($view->options['sq_auto_favicon'] == 1)
                                  $auto_option = true;
                          ?>
                          <div class="sq_option_content sq_option_content_small">
                            <div <?php echo ($view->options['sq_beginner_user'] == 1) ? '' : 'class="sq_switch"'?> style="<?php echo (($view->options['sq_use'] == 0 || $view->options['sq_beginner_user'] == 1) ? 'display:none;' : ''); ?>">
                              <input id="sq_auto_favicon1" type="radio" class="sq_switch-input" name="sq_auto_favicon"  value="1" <?php echo ($auto_option ? "checked" : '')?> />
                              <label for="sq_auto_favicon1" class="sq_switch-label sq_switch-label-off"><?php _e('Yes', _PLUGIN_NAME_); ?></label>
                              <input id="sq_auto_favicon0" type="radio" class="sq_switch-input" name="sq_auto_favicon" value="0" <?php echo (!$auto_option ? "checked" : '')?> />
                              <label for="sq_auto_favicon0" class="sq_switch-label sq_switch-label-on"><?php _e('No', _PLUGIN_NAME_); ?></label>
                              <span class="sq_switch-selection"></span>
                            </div>
                            <span><?php _e('Add the <strong>favicon</strong> and the <strong>icon for Apple devices</strong>.', _PLUGIN_NAME_); ?></span>
                          </div>
                     </li>
                     <li>
                          <?php
                              $auto_option = false;
                              if($view->options['sq_auto_facebook'] == 1)
                                  $auto_option = true;
                          ?>
                          <div class="sq_option_content sq_option_content_small">
                            <div <?php echo ($view->options['sq_beginner_user'] == 1) ? '' : 'class="sq_switch"'?> style="<?php echo (($view->options['sq_use'] == 0 || $view->options['sq_beginner_user'] == 1) ? 'display:none;' : ''); ?>">
                              <input id="sq_auto_facebook1" type="radio" class="sq_switch-input" name="sq_auto_facebook"  value="1" <?php echo ($auto_option ? "checked" : '')?> />
                              <label for="sq_auto_facebook1" class="sq_switch-label sq_switch-label-off"><?php _e('Yes', _PLUGIN_NAME_); ?></label>
                              <input id="sq_auto_facebook0" type="radio" class="sq_switch-input" name="sq_auto_facebook" value="0" <?php echo (!$auto_option ? "checked" : '')?> />
                              <label for="sq_auto_facebook0" class="sq_switch-label sq_switch-label-on"><?php _e('No', _PLUGIN_NAME_); ?></label>
                              <span class="sq_switch-selection"></span>
                            </div>
                            <span><?php echo __('Add the <strong>Facebook meta objects</strong> for a good looking share. ', _PLUGIN_NAME_) . '<a href="https://developers.facebook.com/tools/debug/og/object?q='. urlencode(get_bloginfo('wpurl')) .'" target="_blank" title="Facebook Object Validator">Check here</a>'; ?></span>
                          </div>
                     </li>
                     <li>
                          <?php
                              $auto_option = false;
                              if($view->options['sq_auto_twitter'] == 1)
                                  $auto_option = true;
                          ?>
                          <div class="sq_option_content sq_option_content_small">
                            <div <?php echo ($view->options['sq_beginner_user'] == 1) ? '' : 'class="sq_switch"'?> style="<?php echo (($view->options['sq_use'] == 0 || $view->options['sq_beginner_user'] == 1) ? 'display:none;' : ''); ?>">
                              <input id="sq_auto_twitter1" type="radio" class="sq_switch-input" name="sq_auto_twitter"  value="1" <?php echo ($auto_option ? "checked" : '')?> />
                              <label for="sq_auto_twitter1" class="sq_switch-label sq_switch-label-off"><?php _e('Yes', _PLUGIN_NAME_); ?></label>
                              <input id="sq_auto_twitter0" type="radio" class="sq_switch-input" name="sq_auto_twitter" value="0" <?php echo (!$auto_option ? "checked" : '')?> />
                              <label for="sq_auto_twitter0" class="sq_switch-label sq_switch-label-on"><?php _e('No', _PLUGIN_NAME_); ?></label>
                              <span class="sq_switch-selection"></span>
                            </div>
                            <span><?php echo __('Add the <strong>Twitter card</strong> in your tweets. ', _PLUGIN_NAME_) . '<a href="https://dev.twitter.com/docs/cards/validation/validator" target="_blank" title="Twitter Card Validator">Check here</a> <em>(Select Summary > Validate URLs)</em>'; ?></span>
                            <span id="sq_twitter_account" style="float:left; font-weight: bold; color: darksalmon; <?php echo (!$auto_option ? 'display:none;' : ''); ?>" ><?php _e('Your twitter account: ', _PLUGIN_NAME_); ?><input type="text" name="sq_twitter_account" value="<?php echo (($view->options['sq_twitter_account'] <> '') ? $view->options['sq_twitter_account'] : '')?>" size="30" style="width:150px;" /> </span>
                          </div>
                     </li>
                  </ul>


            </div>
        </fieldset>

        <fieldset id="sq_title_description_keywords" style="<?php echo (($view->options['sq_use'] == 0) ? 'display:none;' : ''); ?>">
            <legend><?php _e('First page optimization (Title, Description, Keywords)', _PLUGIN_NAME_); ?></legend>
            <ul id="sq_settings_sq_use" class="sq_settings_info">
                <li>
                    <?php
                        $auto_option = false;
                        if($view->options['sq_auto_title'] == 1)
                            $auto_option = true;
                    ?>
                    <div class="sq_option_content sq_option_content_small">
                      <div <?php echo ($view->options['sq_beginner_user'] == 1) ? '' : 'class="sq_switch"'?> style="<?php echo (($view->options['sq_use'] == 0 || $view->options['sq_beginner_user'] == 1) ? 'display:none;' : ''); ?>">
                        <input id="sq_auto_title1" type="radio" class="sq_switch-input" name="sq_auto_title"  value="1" <?php echo ($auto_option ? "checked" : '')?> />
                        <label for="sq_auto_title1" class="sq_switch-label sq_switch-label-off"><?php _e('Yes', _PLUGIN_NAME_); ?></label>
                        <input id="sq_auto_title0" type="radio" class="sq_switch-input" name="sq_auto_title" value="0" <?php echo (!$auto_option ? "checked" : '')?> />
                        <label for="sq_auto_title0" class="sq_switch-label sq_switch-label-on"><?php _e('No', _PLUGIN_NAME_); ?></label>
                        <span class="sq_switch-selection"></span>
                      </div>
                      <span><?php _e('Add the correct <strong>title</strong> in the home page', _PLUGIN_NAME_); ?></span>
                    </div>
                </li>
                <li>
                    <?php
                        $auto_option = false;
                        if($view->options['sq_auto_description'] == 1)
                            $auto_option = true;
                    ?>
                    <div class="sq_option_content sq_option_content_small">
                      <div <?php echo ($view->options['sq_beginner_user'] == 1) ? '' : 'class="sq_switch"'?> style="<?php echo (($view->options['sq_use'] == 0 || $view->options['sq_beginner_user'] == 1) ? 'display:none;' : ''); ?>">
                        <input id="sq_auto_description1" type="radio" class="sq_switch-input" name="sq_auto_description"  value="1" <?php echo ($auto_option ? "checked" : '')?> />
                        <label for="sq_auto_description1" class="sq_switch-label sq_switch-label-off"><?php _e('Yes', _PLUGIN_NAME_); ?></label>
                        <input id="sq_auto_description0" type="radio" class="sq_switch-input" name="sq_auto_description" value="0" <?php echo (!$auto_option ? "checked" : '')?> />
                        <label for="sq_auto_description0" class="sq_switch-label sq_switch-label-on"><?php _e('No', _PLUGIN_NAME_); ?></label>
                        <span class="sq_switch-selection"></span>
                      </div>
                      <span><?php _e('Add the correct <strong>description</strong> and <strong>keywords</strong> in all pages', _PLUGIN_NAME_); ?></span>
                    </div>
                </li>
            </ul>

            <?php

                $auto_option = false;
                if($view->options['sq_fp_title'] == '' || $view->options['sq_auto_seo'] == 1)
                 $auto_option = true;
            ?>
            <div id="sq_snippet">
                <div id="sq_snippet_name"><?php _e('Squirrly Snippet',_PLUGIN_NAME_)?></div>

                <ul id="sq_snippet_ul">
                    <li id="sq_snippet_title"></li>
                    <li id="sq_snippet_url"></li>
                    <li id="sq_snippet_description"></li>
                    <li id="sq_snippet_source"><a href="http://www.google.com/webmasters/tools/richsnippets?url=<?php echo urlencode(get_bloginfo('wpurl')) ?>" target="_blank"><?php _e('Check with google ...',_PLUGIN_NAME_) ?></a></li>
                </ul>

                <div id="sq_snippet_disclaimer" <?php echo (!$auto_option ? '' : 'style="display: none;"')?>><?php _e('If you don\'t see any changes in custom optimization, check if another SEO plugin affects Squirrly SEO',_PLUGIN_NAME_)?></div>
            </div>


            <div class="sq_option_content">
                <div class="sq_switch">
                  <input id="sq_automatically" type="radio" class="sq_switch-input" name="sq_auto_seo" value="1" <?php echo ($auto_option ? "checked" : '')?> />
                  <label for="sq_automatically" class="sq_switch-label sq_switch-label-off"><?php _e('Auto', _PLUGIN_NAME_); ?></label>
                  <input id="sq_customize" type="radio" class="sq_switch-input" name="sq_auto_seo"  value="0" <?php echo (!$auto_option ? "checked" : '')?> />
                  <label for="sq_customize" class="sq_switch-label sq_switch-label-on"><?php _e('Custom', _PLUGIN_NAME_); ?></label>
                  <span class="sq_switch-selection"></span>
                </div>
            </div>

           <div id="sq_customize_settings" <?php echo (!$auto_option ? '' : 'style="display: none;"')?>>

             <p class="withborder">
              <?php _e('Title:', _PLUGIN_NAME_); ?><input type="text" name="sq_fp_title" value="<?php echo (($view->options['sq_fp_title'] <> '') ? $view->options['sq_fp_title'] : '')?>" size="75" /><span id="sq_title_info" />
              <span id="sq_fp_title_length"></span><span class="sq_settings_info"><?php _e('Tips: Length 10-75 chars', _PLUGIN_NAME_); ?></span>
             </p>
             <p class="withborder">
              <?php _e('Description:', _PLUGIN_NAME_); ?><textarea name="sq_fp_description" cols="70" rows="3" ><?php echo (($view->options['sq_fp_description'] <> '') ? $view->options['sq_fp_description'] : '')?></textarea><span id="sq_description_info" />
              <span id="sq_fp_description_length"></span><span class="sq_settings_info"><?php _e('Tips: Length 70-165 chars', _PLUGIN_NAME_); ?></span>
             </p>
             <p class="withborder">
              <?php _e('Keywords:', _PLUGIN_NAME_); ?><input type="text" name="sq_fp_keywords" value="<?php echo (($view->options['sq_fp_keywords'] <> '') ? $view->options['sq_fp_keywords'] : '')?>" size="70" />
              <span id="sq_fp_keywords_length"></span><span class="sq_settings_info"><?php _e('Tips: 2-4 keywords', _PLUGIN_NAME_); ?></span>
             </p>
           </div>
        </fieldset>

        <fieldset>
            <legend><?php _e('Squirrly Options', _PLUGIN_NAME_); ?></legend>
            <div class="sq_option_content" <?php echo ($view->options['sq_beginner_user'] == 1) ? 'style="display:none"' : ''?>>
                <div class="sq_switch">
                  <input id="ignore_warn_yes" class="sq_switch-input" type="radio" name="ignore_warn" value="0" <?php echo (($view->options['ignore_warn'] == 0) ? "checked" : '')?> />
                  <label for="ignore_warn_yes" class="sq_switch-label sq_switch-label-off"><?php _e('Yes', _PLUGIN_NAME_); ?></label>
                  <input id="sq_ignore_warn" class="sq_switch-input" type="radio" name="ignore_warn" value="1" <?php echo (($view->options['ignore_warn'] == 1) ? "checked" : '')?> />
                  <label for="sq_ignore_warn" class="sq_switch-label sq_switch-label-on"><?php _e('No', _PLUGIN_NAME_); ?></label>
                  <span class="sq_switch-selection"></span>
                </div>
                <span><?php _e('Let Squirrly warn me if there are errors related to SEO settings', _PLUGIN_NAME_); ?></span>
            </div>

            <div class="sq_option_content"<?php echo ($view->options['sq_beginner_user'] == 1) ? 'style="display:none"' : ''?>>
                <div class="sq_switch">
                  <input id="sq_keyword_help1" type="radio" class="sq_switch-input" name="sq_keyword_help" value="1" <?php echo (($view->options['sq_keyword_help'] == 1) ? "checked" : '')?> />
                  <label for="sq_keyword_help1" class="sq_switch-label sq_switch-label-off"><?php _e('Yes', _PLUGIN_NAME_); ?></label>
                  <input id="sq_keyword_help0" type="radio" class="sq_switch-input" name="sq_keyword_help"  value="0" <?php echo (($view->options['sq_keyword_help'] == 0) ? "checked" : '')?> />
                  <label for="sq_keyword_help0" class="sq_switch-label sq_switch-label-on"><?php _e('No', _PLUGIN_NAME_); ?></label>
                  <span class="sq_switch-selection"></span>
                </div>
                <span><?php _e('Show <strong>"Enter a keyword"</strong> bubble when posting a new article.', _PLUGIN_NAME_); ?></span>
            </div>

            <div class="sq_option_content">
                <div class="sq_switch">
                  <input id="sq_keyword_information1" type="radio" class="sq_switch-input" name="sq_keyword_information" value="1" <?php echo (($view->options['sq_keyword_information'] == 1) ? "checked" : '')?> />
                  <label for="sq_keyword_information1" class="sq_switch-label sq_switch-label-off"><?php _e('Yes', _PLUGIN_NAME_); ?></label>
                  <input id="sq_keyword_information0" type="radio" class="sq_switch-input" name="sq_keyword_information"  value="0" <?php echo (($view->options['sq_keyword_information'] == 0) ? "checked" : '')?> />
                  <label for="sq_keyword_information0" class="sq_switch-label sq_switch-label-on"><?php _e('No', _PLUGIN_NAME_); ?></label>
                  <span class="sq_switch-selection"></span>
                </div>
                <span><?php _e('Always show <strong>Keyword Informations</strong> about the selected keyword.', _PLUGIN_NAME_); ?></span>
            </div>
       </fieldset>
   </div>


    <div id="sq_settings_right">
           <fieldset>
            <legend><?php _e('Change the Website Icon', _PLUGIN_NAME_); ?></legend>
            <div>
		<p>
                    <?php _e('File types: JPG, JPEG, GIF and PNG.', _PLUGIN_NAME_); ?>
                </p>
		<p>
                    <?php _e('Upload file:', _PLUGIN_NAME_); ?><br />
                    <?php if(file_exists(ABSPATH.'/favicon.ico')){ ?>
                    <img src="<?php echo get_bloginfo('url') . '/favicon.ico' . '?' . time() ?>"  style="float: left; margin-top: 5px; width: 20px; height: 20px;" />
                    <?php }?>
                    <input type="file" name="favicon" id="favicon" style="float: left;" />
                    <input type="submit" name="sq_update" value="<?php _e('Upload', _PLUGIN_NAME_)?>" style="float: left; margin-top: 0;" />
                    <span class="sq_settings_info"><?php echo ((defined('SQ_MESSAGE_FAVICON')) ? SQ_MESSAGE_FAVICON : '')?></span>
               </p>
            </div>
            <span class="sq_settings_info"><?php _e('If you don\'t see the new icon in your browser, empty the browser cache and refresh the page.', _PLUGIN_NAME_); ?></span>
          </fieldset>

          <fieldset >
            <legend><?php _e('Tool for Search Engines', _PLUGIN_NAME_); ?></legend>
            <div>
             <p class="withborder withcode">
              <span class="sq_icon sq_icon_googleplus"></span>
              <?php _e('Google Plus URL:', _PLUGIN_NAME_); ?><br /><strong><input type="text" name="sq_google_plus" value="<?php echo (($view->options['sq_google_plus'] <> '') ? $view->options['sq_google_plus'] : '')?>" size="60" /> (e.g. https://plus.google.com/00000000000000/posts)</strong>
             </p>
             <p class="withborder withcode"<?php echo ($view->options['sq_beginner_user'] == 1) ? 'style="display:none"' : ''?>>
              <span class="sq_icon sq_icon_googlewt"></span>
              <?php echo sprintf(__('Google META verification code for %sWebmaster Tool%s`:', _PLUGIN_NAME_), '`<a href="http://maps.google.com/webmasters/" target="_blank">','</a>'); ?><br><strong>&lt;meta name="google-site-verification" content=" <input type="text" name="sq_google_wt" value="<?php echo (($view->options['sq_google_wt'] <> '') ? $view->options['sq_google_wt'] : '')?>" size="15" /> " /&gt;</strong>
             </p>
             <p class="withborder withcode">
              <span class="sq_icon sq_icon_googleanalytics"></span>
              <?php echo sprintf(__('Google  %sAnalytics ID%s`:', _PLUGIN_NAME_), '`<a href="http://maps.google.com/analytics/" target="_blank">','</a>'); ?><br><strong><input type="text" name="sq_google_analytics" value="<?php echo (($view->options['sq_google_analytics'] <> '') ? $view->options['sq_google_analytics'] : '')?>" size="15" /> (e.g. UA-XXXXXXX-XX)</strong>
             </p>
             <p class="withborder withcode" <?php echo ($view->options['sq_beginner_user'] == 1) ? 'style="display:none"' : ''?>>
              <span class="sq_icon sq_icon_facebookinsights"></span>
              <?php echo sprintf(__('Facebook META code (for %sInsights%s )`:', _PLUGIN_NAME_), '`<a href="http://www.facebook.com/insights/" target="_blank">','</a>'); ?><br><strong>&lt;meta property="fb:admins" content=" <input type="text" name="sq_facebook_insights" value="<?php echo (($view->options['sq_facebook_insights'] <> '') ? $view->options['sq_facebook_insights'] : '')?>" size="15" /> " /&gt;</strong>
             </p>
             <p class="withcode" <?php echo ($view->options['sq_beginner_user'] == 1) ? 'style="display:none"' : ''?>>
              <span class="sq_icon sq_icon_bingwt" ></span>
              <?php echo sprintf(__('Bing META code (for %sWebmaster Tool%s )`:', _PLUGIN_NAME_), '`<a href="http://www.bing.com/toolbox/webmaster/" target="_blank">','</a>'); ?><br><strong>&lt;meta name="msvalidate.01" content=" <input type="text" name="sq_bing_wt" value="<?php echo (($view->options['sq_bing_wt'] <> '') ? $view->options['sq_bing_wt'] : '')?>" size="15" /> " /&gt;</strong>
             </p>
            </div>
        </fieldset>
   </div>

   <div id="sq_settings_submit">
     <input type="hidden" name="action" value="sq_settings_update" />
     <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(_SQ_NONCE_ID_); ?>" />
     <input type="submit" name="sq_update" value="<?php _e('Save settings', _PLUGIN_NAME_)?> &raquo;" />
   </div>
  </div>
  </form>
  <script type="text/javascript">
       var sq_blogurl = "<?php echo get_bloginfo('url') ?>";
       var __snippetshort = "<?php echo __('Too short', _PLUGIN_NAME_) ?>";
       var __snippetlong = "<?php echo __('Too long', _PLUGIN_NAME_) ?>";
  </script>
</div>