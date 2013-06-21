<?php
/**
 * Affiliate settings
 */
class SQ_BlockDashboard extends SQ_BlockController {

    function hookGetContent(){
        $this->options = SQ_Tools::$options;
        
    }

}
?>