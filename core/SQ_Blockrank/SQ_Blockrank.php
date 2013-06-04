<?php
class SQ_Blockrank extends SQ_BlockController {

    /**
     * Load the Rank block header
     *
     * @global type $sq_postID
     */
    function hookHead() {
       global $sq_postID;

       $this->post_id = $sq_postID;
        echo '<script type="text/javascript">
             var __sq_goto_allposts = "'.__('See it in \'All Posts\'', _PLUGIN_NAME_).'";

           </script>';

       SQ_ObjController::getController('SQ_DisplayController', false)
                  ->loadMedia(_SQ_STATIC_API_URL_.SQ_URI.'/css/sq_postslist.css?ver='.SQ_VERSION_ID);
       SQ_ObjController::getController('SQ_DisplayController', false)
                  ->loadMedia(_SQ_STATIC_API_URL_.SQ_URI.'/js/sq_rank.js?ver='.SQ_VERSION_ID);

    }

}
?>