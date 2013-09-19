<?php

class SQ_Blockrank extends SQ_BlockController {

    /** @var integer */
    var $post_id;

    /**
     * Load the Rank block header
     *
     * @global type $sq_postID
     */
    function hookHead() {
        global $sq_postID;
        parent::hookHead();
        //Get the current post id from Wordpress
        $this->post_id = $sq_postID;

        //Set some variables
        echo '<script type="text/javascript">
              if(jQuery("#new-post-slug").length == 0){
                setTimeout(function() {
                      jQuery("#sq_rank_default_text").show();
                }, 2000);
              };
            </script>';


        SQ_ObjController::getController('SQ_PostsList', false)->setVars();
        //Load the css and javascript for the rank box
        SQ_ObjController::getController('SQ_DisplayController', false)
                ->loadMedia(_SQ_THEME_URL_ . '/css/sq_postslist.css?ver=' . SQ_VERSION_ID);
        SQ_ObjController::getController('SQ_DisplayController', false)
                ->loadMedia(_SQ_THEME_URL_ . '/js/sq_rank.js?ver=' . SQ_VERSION_ID);
    }

    function hookGetContent() {
        echo '<div id="sq_rank_default_text" style="display:none">' . __('Publish the article to start Squirrly Article Rank', _PLUGIN_NAME_) . '</div>';
    }

}

?>