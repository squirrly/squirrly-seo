<?php
/**
 * Affiliate settings
 */
class SQ_BlockHelp extends SQ_BlockController {


    function hookHead() {
       parent::hookHead();
       SQ_ObjController::getController('SQ_DisplayController', false)
                  ->loadMedia('sq_menu.css?ver='.SQ_VERSION_ID);
    }

    function action(){
        parent::action();
        switch (SQ_Tools::getValue('action')){
            case 'sq_howto':
                SQ_Tools::saveOptions('sq_howto', (int)SQ_Tools::getValue('sq_howto'));
                exit();
                break;
        }
    }

    function hookGetContent(){
        $this->options = SQ_Tools::$options;
    }

}
?>