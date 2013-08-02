<div id="sq_settings" >
    <?php
    if ($view->options['sq_api'] == '') {
        echo '<div id="sq_settings_login">';
        SQ_ObjController::getBlock('SQ_Blocklogin')->init();
        echo '</div>';
    }
    ?>
<?php if ($view->options['sq_api'] == '' || $view->options['sq_howto'] == 1) { ?>
        <div id="sq_settings_howto">
            <div id="sq_settings_howto_title" ><?php _e('With Squirrly SEO, your Wordpress will get Excellent SEO on each article you write.', _PLUGIN_NAME_); ?></div>
            <div id="sq_settings_howto_body">
                <p><span><?php _e('SEO Software', _PLUGIN_NAME_); ?></span><?php _e('delivered as a plugin for Wordpress. <br /><br />We connect your wordpress with Squirrly, so that we can find the best SEO opportunities, give you reports and analyse your competitors.', _PLUGIN_NAME_); ?></p>
                <p><object width="420" height="315"><param name="movie" value="http://www.youtube.com/v/HYTcdLXNhhw?hl=en_US&amp;version=3"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/HYTcdLXNhhw?hl=en_US&amp;version=3" type="application/x-shockwave-flash" width="420" height="315" allowscriptaccess="always" allowfullscreen="true"></embed></object></p>

            </div>
        </div>
<?php } ?>

    <div id="sq_settings_title" style="text-align: right">
<?php if ($view->options['sq_api'] <> '') { ?><span id="sq_settings_howto_close" ><?php _e('Don\'t show this page', _PLUGIN_NAME_) ?> </span><?php } ?>
        <a href="post-new.php" id="sq_goto_newpost" <?php echo (($view->options['sq_api'] <> '') ? '' : 'style="display:none"') ?> /><?php _e('<< START HERE >>', _PLUGIN_NAME_) ?></a>
    </div>
</div>