<?php

class SQ_BlockStatus extends SQ_BlockController {

    public $progress = array();

    public function hookGetContent() {
        global $wpdb;
        $this->progress = $this->model->getGlobalProgress();
    }

}

?>