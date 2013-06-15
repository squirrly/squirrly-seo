<div id="sq_settings" >
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
