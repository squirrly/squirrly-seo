<?php

/**
 * Class for Google Ranking Record
 */
class SQ_Ranking extends SQ_FrontController {

    private $analytics_table = 'sq_analytics';
    private $keyword_table = 'sq_keywords';
    private $keyword;

    function __construct() {
        parent::__construct();
        $this->now = current_time('timestamp');

        SQ_ObjController::getController('SQ_Traffic', false);
    }

    function init() {
        return;
    }

    function action() {

    }

    /**
     * Process Ranking on brief request
     * @param type $return
     */
    public function processRanking(&$return, $post_id) {
        if (isset($return->seo) && is_object($return->seo)) {
            $return->global_rank = $this->findTodayRank($post_id, 'global_rank');
            $saved_keyword = $this->findTodayRank($post_id, 'keyword');

            if ($return->global_rank == -1 || $return->seo->keyword <> $saved_keyword) {
                //   echo "global_rank: " .$return->global_rank;
                $return->global_rank = $this->getGoogleRank($post_id, $return->seo->keyword);
                //Save results
                $this->saveRank($post_id, $return->global_rank, true);
            }

            //Local search
            $country = $this->getLocalGoogleExtension();
            if ($country <> '') {

                $return->local_rank = $this->findTodayRank($post_id, 'local_rank');
                if ($return->local_rank == -1 || $return->seo->keyword <> $saved_keyword) {
                    // echo 'local_rank: '.$return->local_rank;
                    sleep(1);
                    $return->local_rank = $this->getGoogleRank($post_id, $return->seo->keyword, $country);
                    //Save results
                    $this->saveRank($post_id, $return->local_rank, false);
                }
            }
        }
    }

    /**
     * Process Ranking on brief request
     * @param type $return
     */
    public function checkIndexed(&$return, $post_id) {
        $return->indexed = $this->findHistoryIndexed($post_id);

        //If is indexed search is a keyword is indexed
        if ($return->indexed == 1) {
            $this->processRanking($return, $post_id);
        }

        if ($return->indexed == -1 || $return->indexed == 0)
        //Save if indexed in any position
            if (isset($return->seo))
                if ($this->getGoogleRank($post_id, $return->seo->permalink) > 0) {
                    $this->saveIndexed($post_id);
                    $return->indexed = 1;
                }
    }

    /**
     * Check if other keywords were find with search engines
     *
     * @param integer $post_id
     * @return boolean
     */
    public function getOtherKeywords($post_id) {
        global $wpdb;
        $other_keywords = array();
        $sql = "SELECT count(kw.`keyword`) as searched, kw.`keyword`
                       FROM `" . $this->keyword_table . "` kw
                       WHERE " . (((int) $post_id > 0) ? "kw.`post_id`=" . (int) $post_id : "kw.`home`=1") . "
                       GROUP BY kw.`keyword`
                       ORDER BY searched DESC LIMIT 1";

        $row = $wpdb->get_row($sql);

        if ($row) {
            $other_keywords = json_decode($this->findTodayRank($post_id, 'other_keywords'), true);

            if (!isset($other_keywords[$row->keyword]['global']) || $other_keywords[$row->keyword]['global'] == 0) {
                $row->global_rank = $this->getGoogleRank($post_id, $row->keyword);
                if ($row->global_rank > 0) {
                    //Save results
                    $this->saveOtherRank($post_id, $row->global_rank, 'global', $row->keyword);
                }
            }

            $country = $this->getLocalGoogleExtension();
            if ($country <> '') {
                if (!isset($other_keywords[$row->keyword]['local']) || $other_keywords[$row->keyword]['local'] == 0) {
                    $row->local_rank = $this->getGoogleRank($post_id, $row->keyword);
                    if ($row->local_rank > 0) {
                        //Save results
                        $this->saveOtherRank($post_id, $row->global_rank, 'local', $row->keyword);
                    }
                }
            }

            return $row;
        }
        else
            return false;
    }

    /**
     * Get the keword google  position
     * @param integer $post_id
     * @return string
     */
    public function getRank($post_id = "") {
        global $wpdb;

        //Check if ranks is saved in database
        $sql = "SELECT analytics.`id`,analytics.`indexed`,analytics.`global_rank`,analytics.`local_rank`, analytics.`keyword`,analytics.`other_keywords`,analytics.`date`
                FROM `" . $this->analytics_table . "` analytics
                WHERE analytics.`post_id`=" . (int) $post_id . " AND analytics.`date`='" . date('Y-m-d', $this->now) . "'";
        $row = $wpdb->get_row($sql);

        $sql = "SELECT analytics.`id`,analytics.`indexed`,analytics.`global_rank`,analytics.`local_rank`, analytics.`keyword`,analytics.`other_keywords`,analytics.`date`
                FROM `" . $this->analytics_table . "` analytics
                WHERE analytics.`post_id`=" . (int) $post_id . " AND (analytics.`global_rank` > 0  OR analytics.`local_rank` > 0) AND analytics.`date`<'" . date('Y-m-d', $this->now) . "'
                ORDER BY analytics.`date` DESC LIMIT 1";
        $last_row = $wpdb->get_row($sql);

        if ($last_row && $row && $last_row->keyword == $row->keyword) {
            $serp['change'] = array('global' => (($last_row->global_rank > 0 && $row->global_rank > 0) ? ($last_row->global_rank - $row->global_rank) : ($row->global_rank > 0 ? 'new' : 0)), 'local' => (($last_row->local_rank > 0 && $row->local_rank > 0) ? ($last_row->local_rank - $row->local_rank) : ($row->local_rank > 0 ? 'new' : 0)));
            $serp['change']['lastcheck'] = $last_row->date;
        } else {
            $serp['change'] = array('global' => 0, 'local' => 0);
        }


        if ($row) {
            $serp['position'] = array('global' => $row->global_rank, 'local' => $row->local_rank);
            $serp['others'] = json_decode($row->other_keywords, true);
        } elseif ($last_row) {
            $serp['position'] = array('global' => $last_row->global_rank, 'local' => $last_row->local_rank);
            $serp['others'] = json_decode($last_row->other_keywords, true);
        }

        $serp['keyword'] = $row->keyword;
        $country = $this->getLocalGoogleExtension();
        $serp['flag'] = array('global' => 'com', 'local' => $country, 'local_ico' => $this->getLocalLanguage());

        return $serp;
    }

    /**
     * Check if there are records for today
     * @param integer $post_id
     * @param integer $rank - the default rank position
     * @return integer
     */
    private function findTodayRank($post_id, $rank) {
        global $wpdb;
        //Check if ranks is saved in database
        $sql = "SELECT analytics.`id`,analytics.`indexed`,analytics.`global_rank`,analytics.`local_rank`,analytics.`keyword`,analytics.`other_keywords`
                FROM `" . $this->analytics_table . "` analytics
                WHERE analytics.`post_id`=" . (int) $post_id . " AND DATE(analytics.`date`)='" . date('Y-m-d', $this->now) . "'";
        $row = $wpdb->get_row($sql);

        //If rank is already saved for today then return r=tha value
        if ($row) {
            return $row->$rank;
        }
        else
            return -1;
    }

    /**
     * Check the rank in history
     *
     * @param integer $post_id
     * @return int
     */
    private function findHistoryIndexed($post_id) {
        global $wpdb;
        //Check if ranks is saved in database
        $sql = "SELECT analytics.`indexed`
                FROM `" . $this->analytics_table . "` analytics
                WHERE analytics.`indexed` = 1 AND analytics.`post_id`=" . (int) $post_id . " AND DATE(analytics.`date`) < '" . date('Y-m-d', $this->now) . "'
                LIMIT 1";
        //echo $sql;
        $row = $wpdb->get_row($sql);

        //If rank is already saved for today then return r=tha value
        if ($row)
            return 1;
        else
            return $this->findTodayRank($post_id, 'indexed');
    }

    /**
     * Call google to get the keyword position
     *
     * @param integer $post_id
     * @param string $keyword
     * @param string $country: com | country extension
     * @param string $language: en | local language
     * @return boolean|int
     */
    function getGoogleRank($post_id = 0, $keyword = "", $country = "com", $language = "en") {
        global $wpdb;
        $this->keyword = $keyword;

        $keyword = str_replace(" ", "+", urlencode(strtolower($keyword)));
        if (!function_exists('preg_match_all'))
            return;

        //Search google for rank
        $url = "http://www.google.$country/search?q=$keyword&amp;hl=$language&amp;ie=utf-8&as_qdr=all&amp;aq=t&amp;rls=org:mozilla:us:official&amp;client=firefox&num=100&filter=0&nfpr=1";

        //Grab the remote informations from google
        $response = SQ_Tools::sq_remote_get($url, array('timeout' => 10));
        $response = utf8_decode($response);

        //echo ;
        //Check the values for block IP
        if (strpos($response, "computer virus or spyware application") !== false ||
                strpos($response, "entire network is affected") !== false ||
                strpos($response, "http://www.download.com/Antivirus") !== false ||
                strpos($response, "/images/yellow_warning.gif") !== false) {
            return -1;
            return false;
        }

        //Get the permalink of the current post
        $permalink = get_permalink($post_id);
        //$permalink = 'sq.cifnet.ro';
        //$response = @preg_replace('/<ol[^>]*>(.*?)<\/ol>/', '', $response);
        //echo $response;
        @preg_match_all('/<li[^>]*>[^h3]*<h3 class="r">(.*?)<\/h3>/', $response, $matches);
        //print_R($matches);
        if (is_array($matches[0]))
            foreach ($matches[0] as $key => $match) {
                if (strpos($match, $permalink) !== false) {
                    $pos = $key + 1;
                    return $pos;
                    break;
                }
            }

        return 0;
    }

    /**
     * Save the rank in database
     *
     * @param integer $post_id
     * @param integer $pos
     * @param boolean $global
     * @return query
     */
    function saveRank($post_id, $pos = 0, $global = true) {
        global $wpdb;
        $home = ($post_id == 0 ? 1 : 0);


        $sql = "SELECT analytics.`id`, analytics.`local_rank`, analytics.`global_rank`, analytics.`keyword`
                FROM `" . $this->analytics_table . "` analytics
                WHERE analytics.`post_id`=" . (int) $post_id . " AND analytics.`date`='" . date('Y-m-d', $this->now) . "'";
        $row = $wpdb->get_row($sql);

        if ($row) {
            $global_rank = ($global ? $pos : $row->global_rank);
            $local_rank = (!$global ? $pos : $row->local_rank);
            $sql = "UPDATE `" . $this->analytics_table . "` analytics
                       SET analytics.`local_rank`='" . (int) $local_rank . "',
                           analytics.`global_rank`='" . (int) $global_rank . "',
                           analytics.`keyword`='" . (($this->keyword <> '') ? $this->keyword : $row->keyword) . "'
                       WHERE analytics.`id`=" . (int) $row->id;
        } else {
            $global_rank = ($global ? $pos : -1);
            $local_rank = (!$global ? $pos : -1);

            $sql = "INSERT INTO `" . $this->analytics_table . "`
                    (`count`,`unique`,`post_id`,`home`,`global_rank`,`local_rank`,`keyword`,`date`)
                    VALUES (0,0," . $post_id . "," . $home . "," . $global_rank . "," . $local_rank . ",'" . $this->keyword . "','" . date("Y-m-d") . "')";
        }
        return $wpdb->query($sql);
    }

    /**
     * Save the rank for other keywords
     *
     * @param integer $post_id
     * @param integer $pos
     * @param string $where
     * @param string $keyword
     * @return query
     */
    function saveOtherRank($post_id, $pos = 0, $where = 'global', $keyword = '') {
        global $wpdb;
        $home = ($post_id == 0 ? 1 : 0);
        $other_keywords = array();

        $sql = "SELECT analytics.`id`, analytics.`other_keywords`
                FROM `" . $this->analytics_table . "` analytics
                WHERE analytics.`post_id`=" . (int) $post_id . " AND analytics.`date`='" . date('Y-m-d', $this->now) . "'";
        $row = $wpdb->get_row($sql);

        if ($row) {
            $other_keywords = json_decode($row->other_keywords, true);
            $other_keywords[$keyword][$where] = $pos;

            $sql = "UPDATE `" . $this->analytics_table . "` analytics
                       SET analytics.`other_keywords`='" . json_encode($other_keywords) . "'
                       WHERE analytics.`id`=" . (int) $row->id;
        } else {
            $sql = "INSERT INTO `" . $this->analytics_table . "`
                    (`count`,`unique`,`post_id`,`home`,`other_keywords`,`date`)
                    VALUES (0,0," . $post_id . "," . $home . ",'" . json_encode($other_keywords) . "','" . date("Y-m-d") . "')";
        }
        return $wpdb->query($sql);
    }

    /**
     * Save google indexed status in database
     *
     * @param integer $post_id
     * @return query
     */
    function saveIndexed($post_id) {
        global $wpdb;

        $sql = "SELECT analytics.`id`, analytics.`indexed`
                FROM `" . $this->analytics_table . "` analytics
                WHERE analytics.`post_id`=" . (int) $post_id . " AND DATE(analytics.`date`)='" . date('Y-m-d', $this->now) . "'";
        $row = $wpdb->get_row($sql);

        if ($row) {
            $sql = "UPDATE `" . $this->analytics_table . "` analytics
                    SET analytics.`indexed`='1'
                    WHERE analytics.`id`=" . (int) $row->id;
        } else {
            $sql = "INSERT INTO `" . $this->analytics_table . "`
                    (`post_id`,`indexed`,`date`)
                    VALUES (" . (int) $post_id . ",1,'" . date("Y-m-d") . "')";
        }

        return $wpdb->query($sql);
    }

    /**
     * Return the local google extension from language
     * @return string
     */
    function getLocalGoogleExtension() {
        return $this->getGoogleExtension($this->getLocalLanguage());
    }

    /**
     * Get the local language
     * @return string
     */
    private function getLocalLanguage() {
        $language = WPLANG;
        if (strpos($language, '_') !== false)
            $language = substr($language, 0, strpos($language, '_'));
        else
            $language = ($language == '' ? 'en' : $language);

        return $language;
    }

    /**
     * Check the google extension according to language
     * @param string $language
     * @return string
     */
    function getGoogleExtension($language) {
        switch ($language) {
            case 'en':
                $country = '';
                break;
            case 'ca':
                $country = 'ca';
                $language = 'en';
                break;
            case 'au':
            case 'ar':
            case 'br':
            case 'co':
            case 'gr':
            case 'pk':
            case 'tr':
            case 'tw':
                $country = 'com.' . $language;
                break;
            case 'jp':
            case 'uk':
            case 'kr':
                $country = 'co.' . $language;
                break;
            default:
                $country = $language;
        }
        return $country;
    }

}

?>