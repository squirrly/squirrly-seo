<?php

/**
 * Class for Traffic Record
 */
class SQ_Wservice {

    private $analytics_table;
    private $status = array();

    public function __construct() {
        $this->setInfo();
    }

    private function getPublicPosts() {
        global $wpdb;

        $sql = "SELECT `ID` as post_id  FROM " . $wpdb->posts . "   WHERE post_status = 'publish' ";
        return $wpdb->get_results($sql);
    }

    public function getInfo() {
        return $this->status;
    }

    private function setInfo() {
        $info = '';

        if (SQ_Tools::getIsset('sq_info'))
            $info = SQ_Tools::getValue('sq_info');

        $traffic = SQ_ObjController::getController('SQ_Traffic', false);
        $rank = SQ_ObjController::getController('SQ_Ranking', false);
        $this->analytics_table = $traffic->getAnalyticsTable();

        if (strpos($info, 'progress') !== false) {
            $status = SQ_ObjController::getModel('SQ_BlockStatus');
            $this->status['progress'] = $status->getGlobalProgress();
        }

        $posts = $this->getPublicPosts();
        if (strpos($info, 'posts') !== false) {
            $this->status['posts'] = $posts;
        }

        foreach ($posts as $post) {
            if (strpos($info, 'traffic') !== false)
                $this->status['traffic'][$post->post_id] = $traffic->getTraffic($post->post_id, 'month');

            if (strpos($info, 'rank') !== false)
                $this->status['rank'][$post->post_id] = $rank->getRank($post->post_id);
        }

        SQ_Tools::dump($this->status);
    }

}

?>