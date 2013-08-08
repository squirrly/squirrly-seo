<?php

/**
 * The model class for SQ_BlockStatus
 *
 */
class Model_SQ_BlockStatus {

    /** @var string */
    private $analytics_table;

    /** @var integer Days to the latest traffic check */
    private $prev_check;

    public function __construct() {
        $traffic = SQ_ObjController::getController('SQ_Traffic', false);

        //get the analytic table name
        $this->analytics_table = $traffic->getAnalyticsTable();

        $this->prev_check = 7;
    }

    /**
     * Add the global progress of the traffic
     *
     */
    public function getGlobalProgress() {
        global $wpdb;

        if ($wpdb->get_var("SHOW TABLES LIKE '" . $this->analytics_table . "'") != $this->analytics_table)
            return false;

        $progress = $this->setProgress();
        if (isset(SQ_Tools::$options['sq_posts_status_close'])) {
            $latest = $this->setProgress(date('Y-m-d', time() - 3600 * 24 * $this->prev_check));
            //Chech if there is an inrease in trafic sinse previous check
            $progress['increase'] = (int) ($latest['gapr'] < $progress['gapr']);
            $progress['latest'] = $latest;
        } else {
            $progress['increase'] = 1;
        }
        return $progress;
    }

    /**
     *
     * @param type $until
     * @return type
     */
    private function setProgress($until = '') {
        $progress = array('op' => 0, 'nop' => 0, 'opc' => 0, 'nopc' => 0, 'gapr' => 0);

        if ($tno = $this->getNOProgress($until))
            if ($to = $this->getOProgress($until))
                if ($to->count > 0 && $tno->count > 0 && $to->posts > 0 && $tno->posts > 0) {
                    $gapr = ((($to->count / $to->posts) * 100) / ($tno->count / $tno->posts)) - 100;
                    $progress = array('op' => $to->posts,
                        'nop' => $tno->posts,
                        'opc' => $to->count,
                        'nopc' => $tno->count,
                        'gapr' => $gapr
                    );
                }

        return $progress;
    }

    /**
     * Get the unoptimized traffic from database
     * @global type $wpdb
     * @return type
     */
    public function getNOProgress($until) {
        global $wpdb;
        if ($until <> '')
            $until = " AND date < '" . $until . "'";

        $sql = "SELECT SUM(analytics.`count`) as `count`, COUNT(DISTINCT analytics.`post_id`) as `posts`
                FROM `" . $this->analytics_table . "` analytics
                INNER JOIN " . $wpdb->posts . " wp ON wp.ID = analytics.`post_id` AND wp.post_status = 'publish'
                WHERE keyword = ''
                " . $until;

        return $wpdb->get_row($sql);
    }

    /**
     * Get the optimized traffic from database
     * @global type $wpdb
     * @return type
     */
    public function getOProgress($until) {
        global $wpdb;
        if ($until <> '')
            $until = " AND date < '" . $until . "'";

        $sql = "SELECT SUM(analytics.`count`) as `count`, COUNT(DISTINCT analytics.`post_id`) as `posts`
                FROM `" . $this->analytics_table . "` analytics
                INNER JOIN " . $wpdb->posts . " wp ON wp.ID = analytics.`post_id` AND wp.post_status = 'publish'
                WHERE keyword <> ''
                " . $until;

        return $wpdb->get_row($sql);
    }

    /**
     * Pack the traffic status popup
     * @param array $progress
     * @return string
     */
    public function packStatus($progress) {
        $str = '';
        //Check show conditions
        if (!$this->showPopup($progress))
            return;
        if (isset(SQ_Tools::$options['sq_posts_status_close']) && (time() - SQ_Tools::$options['sq_posts_status_close'] < 3600 * 24 * $this->prev_check))
            return; //min 7 days before show it again



//Set the percent string to progress
        $progress['gapr'] = number_format($progress['gapr'], 0) . '%';
        $str .= '
                <div id="sq_status">
                    <ul class="sq_box">
                       <li class="sq_header"><span>Squirrly SEO</span>' . sprintf(__('%s More Traffic for you!'), $progress['gapr']) . '<span class="sq_status_close">' . __('[close this box]') . ' X</span></li>
                       <li>
                        <div class="sq_status_image"></div>
                        <table>
                         <tr>
                           <td class="sq_status_text1">' . __('CONGRATULATIONS!!') . ' :-)</td>
                           <td class="sq_status_bigtext" rowspan="2">' . $progress['gapr'] . '</td>
                         </tr>
                           <td class="sq_status_text2">' . __('Your Traffic Increased by:') . '</td>
                         <tr>
                         </tr>
                        </table>
                       </li>
                       <li class="sq_status_text3">' . sprintf(__('the articles you’ve optimized with Squirrly SEO bring you %s more traffic than the other ones.'), $progress['gapr']) . '
                       </li>
                    </ul>
                <div>
                ';

        return $str;
    }

    /**
     * Show popup condition
     * @return type
     */
    private function showPopup($progress) {
        return ($progress['op'] > 0 && $progress['nop'] > 0 && ($progress['op'] < $progress['nop']) && $progress['opc'] > 100 && $progress['gapr'] > 100 && $progress['increase'] == 1);
    }

}

?>