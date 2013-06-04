<?php if (current_user_can('administrator')){?>
<div id="sq_options">
    <ul>
        <li id="sq_options_feedback">
                    
            <span class="sq_icon <?php if(isset($_COOKIE['sq_feedback_face']) && (int)$_COOKIE['sq_feedback_face'] > 0) {echo 'sq_label_feedback_' . ((int)$_COOKIE['sq_feedback_face'] - 1); } ?>" <?php if(!isset($_COOKIE['sq_feedback_face'])) { echo 'title="'.__('How was your Squirrly experience today?',_PLUGIN_NAME_).'"';  } ?>></span>
            <?php if(!isset($_COOKIE['sq_feedback_face']) || (isset($_COOKIE['sq_feedback_face']) && (int)$_COOKIE['sq_feedback_face'] < 3)) { ?>
            <?php if(!isset(SQ_Tools::$options['sq_feedback'])) {?>
                <span class="sq_push">1</span>
            <?php }?>
            <ul class="sq_options_feedback_popup" style="display: none;">
              <div id="sq_options_feedback_close" >x</div>
                  <li><?php echo __('How was Squirrly today?',_PLUGIN_NAME_) ?></li>
                  <li>
                    <table width="100%" cellpadding="2" cellspacing="0" border="0">
                        <tr>
                            <td><label class="sq_label_feedback_smiley sq_label_feedback_0" for="sq_feedback_0"></label><input class="sq_feedback_smiley" type="radio" name="sq_feedback_face" id="sq_feedback_0" value="1" /></td>
                            <td><label class="sq_label_feedback_smiley sq_label_feedback_1" for="sq_feedback_1"></label><input class="sq_feedback_smiley" type="radio" name="sq_feedback_face" id="sq_feedback_1" value="2" /></td>
                            <td><label class="sq_label_feedback_smiley sq_label_feedback_2" for="sq_feedback_2"></label><input class="sq_feedback_smiley" type="radio" name="sq_feedback_face" id="sq_feedback_2" value="3" /></td>
                            <td><label class="sq_label_feedback_smiley sq_label_feedback_3" for="sq_feedback_3"></label><input class="sq_feedback_smiley" type="radio" name="sq_feedback_face" id="sq_feedback_3" value="4" /></td>
                            <td><label class="sq_label_feedback_smiley sq_label_feedback_4" for="sq_feedback_4"></label><input class="sq_feedback_smiley" type="radio" name="sq_feedback_face" id="sq_feedback_4" value="5" /></td>
                        </tr>
                    </table>
                    <div id="sq_options_feedback_error"></div>
                    <p id="sq_feedback_msg" style="display: none;" >
                        <?php echo __('Please tell us why?',_PLUGIN_NAME_) ?>
                        <textarea class="sq_small_input" name="sq_feedback_message" cols="30" rows="2"></textarea>
                        <br />
                    <input id="sq_feedback_submit" type="button" value="<?php _e('Send feedback',_PLUGIN_NAME_) ?>">
                    </p>
                      
                  </li>
                  <li><?php _e('Go to:',_PLUGIN_NAME_) ?> <a href="<?php echo _SQ_SUPPORT_URL_?>" title="<?php _e('support page',_PLUGIN_NAME_) ?>" target="_blank"><?php _e('support page',_PLUGIN_NAME_) ?></a></li>
            </ul>
            <?php }else{?>
            <ul class="sq_options_feedback_popup" style="display: none;">
              <div id="sq_options_feedback_close" >x</div>
              <li><?php echo __('Thank you! You can send us a happy face tomorow too.',_PLUGIN_NAME_) ?></li>
            </ul>
            <?php }?>
        </li>
        <li id="sq_options_support">
                    
            <span class="sq_text" ><?php _e('Support',_PLUGIN_NAME_) ?></span><span class="sq_icon"></span>
            <ul class="sq_options_support_popup" style="display: none;">
              <div id="sq_options_close" >x</div>
                  <li><?php echo __('Need Help with Squirrly SEO?',_PLUGIN_NAME_) ?></li>
                  <li>
                          <p id="sq_support_msg"><textarea class="sq_small_input" name="sq_support_message" cols="30" rows="5"></textarea></p>
                          <div id="sq_options_support_error"></div>
                          <p><input id="sq_support_submit" type="button" value="<?php _e('Send Question',_PLUGIN_NAME_) ?>"></p>
                      
                  </li>
                  <li><?php _e('Go to:',_PLUGIN_NAME_) ?> <a href="<?php echo _SQ_SUPPORT_URL_?>" title="<?php _e('support page',_PLUGIN_NAME_) ?>" target="_blank"><?php _e('support page',_PLUGIN_NAME_) ?></a></li>
            </ul>                
        </li>
        
        <li id="sq_options_dasboard">
            <span class="sq_push" style="display:none;">1</span>
            <span class="sq_text" ><a href="<?php echo _SQ_DASH_URL_?>user/" title="<?php _e('Go to Profile',_PLUGIN_NAME_) ?>" target="_blank" ><span><?php _e('Profile',_PLUGIN_NAME_) ?></span></a></span><a href="<?php echo _SQ_DASH_URL_?>user/" title="<?php _e('Profile',_PLUGIN_NAME_) ?>" target="_blank" ><span class="sq_icon"></span></a>
        </li>
        
    </ul>
</div>
<?php }?>