<div id="sq_preloading" ><?php _e('Waiting for your editor to load .. ', _PLUGIN_NAME_); ?></div>
<div class="sq_box" style="display: none">
    <div class="sq_header" ><?php _e('Optimize for Keyword', _PLUGIN_NAME_); ?></div>
    <div id="sq_blocksearch">
        <div class="sq_keyword">
            <?php
            global $sq_postID;
            if (!isset($_COOKIE['sq_keyword_' . $sq_postID]) || $_COOKIE['sq_keyword_' . $sq_postID] == '') {
                if (!isset(SQ_Tools::$options['sq_keyword_help']) || (int) SQ_Tools::$options['sq_keyword_help'] == 1) {
                    ?>
                    <div id="sq_keyword_help" style="display:none" ><span></span><?php _e('Enter a keyword', _PLUGIN_NAME_); ?><p><?php _e('for Squirrly Live SEO optimization', _PLUGIN_NAME_); ?></p></div>
                    <?php
                }
            }
            ?>
            <input type="text" id="sq_keyword" name="sq_keyword" value="<?php echo ((isset($_COOKIE['sq_keyword_' . $sq_postID]) && $_COOKIE['sq_keyword_' . $sq_postID] <> '') ? utf8_decode($_COOKIE['sq_keyword_' . $sq_postID]) : '') ?>" autocomplete="off" />

            <input type="button" id="sq_keyword_check" value=">" />
            <div id="sq_suggestion" style="display:none">
                <div id="sq_suggestion_close">x</div>
                <ul class="sq_progressbar">
                    <li>Weak</li>
                    <li><div id="sq_suggestion_rank"></div></li>
                    <li>Great</li>
                </ul>
                <div class="sq_research_link" style="display:none"><?php _e('Do a research', _PLUGIN_NAME_); ?></div>
                <input type="button" id="sq_selectit" value="<?php _e('Use this keyword', _PLUGIN_NAME_); ?>" style="display: none" />
            </div>
            <div id="sq_suggestion_help" style="display:none">
                <ul>
                    <li><?php _e('Enter a keyword above!', _PLUGIN_NAME_); ?></li>
                    <li class="sq_research_link"><?php _e('I have more then one keyword!', _PLUGIN_NAME_); ?></li>
                </ul>
            </div>
        </div>
        <div id="sq_types" style="display:none">
            <ul>
                <li id="sq_type_img" title="<?php _e('Images', _PLUGIN_NAME_) ?>"></li>
                <li id="sq_type_twitter" title="<?php _e('Twitter', _PLUGIN_NAME_) ?>"></li>
                <li id="sq_type_wiki" title="<?php _e('Wiki', _PLUGIN_NAME_) ?>"></li>
                <li id="sq_type_news" title="<?php _e('News', _PLUGIN_NAME_) ?>"></li>
                <li id="sq_type_blog" title="<?php _e('Blogs', _PLUGIN_NAME_) ?>"></li>
                <li id="sq_type_local" title="<?php _e('My articles', _PLUGIN_NAME_) ?>"></li>
            </ul>
        </div>
        <div style="position: relative;"><div id="sq_search_close" style="display:none">x</div></div>
        <div class="sq_search"></div>
        <div id="sq_search_img_filter" style="display:none" ><label id="sq_search_img_nolicence_label" <?php if (isset(SQ_Tools::$options['sq_img_licence']) && (int) SQ_Tools::$options['sq_img_licence'] == 1) echo 'class="checked"'; ?> for="sq_search_img_nolicence"><span></span><?php _e('Show only Copyright Free images', _PLUGIN_NAME_) ?></label><input id="sq_search_img_nolicence" type="checkbox" value="1" style="display:none" <?php if (isset(SQ_Tools::$options['sq_img_licence']) && (int) SQ_Tools::$options['sq_img_licence'] == 1) echo 'checked="checked"'; ?> /></div>
    </div>
</div>