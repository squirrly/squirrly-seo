<?php

/**
 * Affiliate settings
 */
class SQ_BlockDashboard extends SQ_BlockController {

    function hookGetContent() {
        $this->options = SQ_Tools::$options;
    }

    /**
     * Called when Post action is triggered
     *
     * @return void
     */
    public function action() {
        parent::action();

        switch (SQ_Tools::getValue('action')) {
            case 'sq_beginner_set':
                SQ_Tools::saveOptions('sq_beginner_user', (int) SQ_Tools::getValue('sq_beginner_user'));
                break;
        }
    }

}

?>