<div id="sq_research" style="display: none">
    <div id="sq_research_title" ><?php _e('Squirrly Keyword Research', _PLUGIN_NAME_); ?>
        <input class="sq_keywords_research_clear" type="button" value="<?php _e('Clear', _PLUGIN_NAME_); ?>" />
    </div>
    <div id="sq_research_body">

        <ul id="sq_keywords_research">
            <li>
                <div style="clear:left"><?php _e('Keyword:', _PLUGIN_NAME_); ?></div>
                <input type="text" name="sq_keyword_research" id="sq_keyword_research[]" value="" />
                <input type="button" class="sq_research_selectit" value="<?php _e('Use this keyword', _PLUGIN_NAME_); ?>" style="display: none" />

                <div class="sq_keywords_info"></div>
            </li>

        </ul>


        <div id="sq_research_help" >
            <input class="sq_keywords_research_add" type="button" value="<?php _e('+ Add keyword', _PLUGIN_NAME_); ?>"  />
            <input class="sq_keywords_research_submit" type="button" value="<?php _e('Do the research', _PLUGIN_NAME_); ?>" />

            <ul>
                <li><?php _e('Enter even more keywords.', _PLUGIN_NAME_); ?></li>
                <li style="display:none; color: brown; font-weight: bold;"><?php _e('Let some keywords for the next time as well!', _PLUGIN_NAME_); ?></li>
            </ul>
        </div>


    </div>
    <div id="sq_research_close" style="display: none;" >x</div>

</div>