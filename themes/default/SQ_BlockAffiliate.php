<div id="sq_settings" >
    <?php SQ_ObjController::getBlock('SQ_BlockSupport')->init(); ?>
    <form id="sq_settings_affiliate_form" name="settings" action="" method="post" enctype="multipart/form-data">
        <div id="sq_settings_title" ><?php _e('Join Squirrly today!', _PLUGIN_NAME_); ?> </div>
        <div id="sq_settings_body">
            <?php
            if (SQ_Tools::$options['sq_api'] == '') {
                echo '<fieldset style="padding: 0; border: none;" ><div id="sq_settings_login">';
                SQ_ObjController::getBlock('SQ_Blocklogin')->init();
                echo '</div></fieldset>';
            }
            ?>
            <fieldset>
                <legend><?php _e('Join Squirrly today!', _PLUGIN_NAME_); ?></legend>
                <div>
                    <p class="sq_settings_affiliate_bigbutton" style="margin-bottom:35px;">
                        <?php
                        if (SQ_Tools::$options['sq_api'] <> '')
                            if (SQ_Tools::$options['sq_affiliate_link'] <> '') {
                                echo '<span>' . SQ_Tools::$options['sq_affiliate_link'] . '</span>';
                                echo '<span class="sq_settings_info">' . __('To redirect users to your site, just change "squirrly.co" with your domain.', _PLUGIN_NAME_) . '</span>';
                            } else {
                                ?><input type="submit" name="sq_affiliate_link" value="<?php _e('Generate affiliate link', _PLUGIN_NAME_) ?> &raquo;" /><?php
                            }
                        ?>
                    </p>
                    <p class="sq_settings_affiliate_bigtitle">
                        <?php _e('Affiliate Benefits', _PLUGIN_NAME_); ?>
                    </p>
                    <ul class="sq_settings_affiliate_info">
                        <li>
                            <div>
                                <span><?php echo sprintf(__('- Recurring 45%s commission', _PLUGIN_NAME_), '%'); ?></span>
                            </div>
                        </li>
                        <li>
                            <div>
                                <span><?php _e('- No cost', _PLUGIN_NAME_); ?></span>
                            </div>
                        </li>
                        <li>
                            <div>
                                <span><?php _e('- Monthly payments in your Paypal account', _PLUGIN_NAME_); ?></span>
                            </div>
                        </li>
                    </ul>
                    <?php
                    if (SQ_Tools::$options['sq_api'] <> '') {
                        if (SQ_Tools::$options['sq_affiliate_link'] <> '') {
                            echo __('Your affiliate account is set and ready to go. Above you have the affiliate link. ', _PLUGIN_NAME_);
                            echo '<br />';
                            echo sprintf(__('Check your affiliate page: %sAffiliate page%s', _PLUGIN_NAME_), '<a href="' . _SQ_DASH_URL_ . 'login/?token=' . SQ_Tools::$options['sq_api'] . '&redirect_to=' . _SQ_DASH_URL_ . 'user/affiliate' . '" target="_blank" style="font-weight:bold">', '</a>');
                        } else {
                            echo sprintf(__('%sTerms of Use for our Affiliate Program%s', _PLUGIN_NAME_), '<a href="http://www.squirrly.co/partnertermsofuse-pag68354.html" target="_blank" style="font-weight:bold">', '</a>');
                        }
                    } else {
                        echo __('After you connect to Squirrly you can begin to use your free Squirrly affiliate link immediately!', _PLUGIN_NAME_);
                    }
                    ?>
                </div>

            </fieldset>
            <?php if (SQ_Tools::$options['sq_affiliate_link'] <> '') { ?>
                <fieldset>
                    <legend><?php _e('Squirrly banners you can use', _PLUGIN_NAME_); ?></legend>
                    <div>
                        <ul class="sq_settings_affiliate_info">
                            <?php
                            $sq_affiliate_images[] = _SQ_STATIC_API_URL_ . 'default/img/banners/banner1.jpg';
                            $sq_affiliate_images[] = _SQ_STATIC_API_URL_ . 'default/img/banners/banner2.jpg';

                            foreach ($sq_affiliate_images as $sq_affiliate_image) {
                                echo '<li><a href="' . SQ_Tools::$options['sq_affiliate_link'] . '" target="_blank"><img src="' . $sq_affiliate_image . '" alt="Seo Plugin by Squirrly" /></a>';
                                echo '<span class="sq_affiliate_banner" >';
                                echo '<textarea style="width: 500px; height: 45px;" onclick="this.focus(); this.select();"><a href="' . SQ_Tools::$options['sq_affiliate_link'] . '" target="_blank"><img src="' . $sq_affiliate_image . '" /></a></textarea>';
                                echo '</span></li>';
                            }
                            ?>
                        </ul>
                    </div>

                </fieldset>
            <?php } ?>
        </div>
        <div id="sq_settings_title" style="text-align: right">
            <a href="post-new.php" id="sq_goto_newpost" <?php echo (($view->options['sq_api'] <> '') ? '' : 'style="display:none"') ?> /><?php _e('Optimize with Squirrly', _PLUGIN_NAME_) ?></a>
            <input id="sq_goto_dashboard" type="button" <?php echo (($view->options['sq_api'] <> '') ? '' : 'style="display:none"') ?> value="<?php _e('See dashboard', _PLUGIN_NAME_) ?>" />
            <?php if ($view->options['sq_api'] <> '') { ?><input id="sq_goto_settings" type="button" value="<?php _e('Go to settings', _PLUGIN_NAME_) ?> &raquo;" /><?php } ?>
        </div>

        <input type="hidden" name="action" value="sq_settings_affiliate" />
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(_SQ_NONCE_ID_); ?>" />
    </form>
</div>