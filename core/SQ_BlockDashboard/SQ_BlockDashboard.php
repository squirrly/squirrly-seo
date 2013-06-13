<?php
/**
 * Affiliate settings
 */
class SQ_BlockDashboard extends SQ_BlockController {
    function hookHead() {
       parent::hookHead();
       SQ_ObjController::getController('SQ_DisplayController', false)
                  ->loadMedia('sq_menu.css?ver='.SQ_VERSION_ID);
    }

    function hookGetContent(){
        $this->options = SQ_Tools::$options;
    }

}
?>