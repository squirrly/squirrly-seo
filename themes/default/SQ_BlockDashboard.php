<div id="sq_settings" >
    <?php  if ($view->options['sq_api'] <> ''){ ?>
    <div id="sq_settings_title" ><?php _e('Squirrly dashboard', _PLUGIN_NAME_); ?> </div>
    <div id="sq_settings_body">
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
