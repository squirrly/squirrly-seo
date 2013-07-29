<?php
/**
 * Class for Traffic Record
 */
class SQ_Traffic extends SQ_FrontController {

    private $analytics_table = 'sq_analytics';
    private $keyword_table = 'sq_keywords';

    function __construct() {
        parent::__construct();
        $this->now = current_time('timestamp');

        //Check if database is created
        if((!isset(SQ_Tools::$options['sq_dbtables']) || (isset(SQ_Tools::$options['sq_dbtables']) && SQ_Tools::$options['sq_dbtables'] == 0)))
            $this->createBdTables();

    }

    function init() { return; }
    function action() {}

    public function getAnalyticsTable(){
        return $this->analytics_table;
    }

    public function getKeywordTable(){
        return $this->keyword_table;
    }

    /**
     * Create the tables for traffic and keyword
     * @global type $wpdb
     */
    private function createBdTables(){
        global $wpdb;
        if($wpdb->get_var("SHOW TABLES LIKE '".$this->analytics_table."'") != $this->analytics_table) {
            $sql = "CREATE TABLE `".$this->analytics_table."` (
                    `id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
                    `count` INT( 9 ) NOT NULL DEFAULT 0,
                    `unique` INT( 9 ) NOT NULL DEFAULT 0,
                    `post_id` bigint( 20 ) NOT NULL DEFAULT 0,
                    `home` tinyint( 1 ) NOT NULL DEFAULT 0,
                    `indexed` tinyint( 1 ) NOT NULL DEFAULT -1,
                    `global_rank` int( 3 ) NOT NULL DEFAULT -1,
                    `local_rank` int( 3 ) NOT NULL DEFAULT -1,
                    `keyword` varchar(255) collate utf8_unicode_ci NOT NULL default '',
                    `other_keywords` text collate utf8_unicode_ci NOT NULL default '',
                    `date` DATE default NULL,
                     PRIMARY KEY ( `id` ),
                     KEY `post_id` USING BTREE (`post_id`)
                    ) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ";

            $wpdb->query($sql);
        }

        if($wpdb->get_var("SHOW TABLES LIKE '".$this->keyword_table."'") != $this->keyword_table) {
            $sql = "CREATE TABLE `".$this->keyword_table."` (
                    `id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
                    `post_id` bigint( 20 ) NOT NULL DEFAULT 0,
                    `home` tinyint( 1 ) NOT NULL DEFAULT 0,
                    `referral` varchar(96) collate utf8_unicode_ci NOT NULL default '',
                    `keyword` varchar(255) collate utf8_unicode_ci NOT NULL default '',
                    `date` DATE default NULL,
                     PRIMARY KEY ( `id` ),
                     KEY `post_id` USING BTREE (`post_id`)
                    ) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ";

            $wpdb->query($sql);
        }
        //Save database created
        SQ_Tools::saveOptions('sq_dbtables', 1);
    }

    /**
     * Get the aray accourding to the passed days
     * @param integer $interval
     * @return string
     */
    private function getDays($interval){
        $days = 30;
        switch ($interval){
            case 'day':
                $days = 0;
                break;
            case 'week':
                $days = 6;
                break;
            case 'month':
                $days = 30;
                break;
            default:
                $days= 30;
        }
        return $days;
    }

    /**
     * Get the traffic history for the specific inverval
     * @global type $wpdb
     * @param type $post_id
     * @param type $interval
     * @param type $order
     * @return type
     */
    function getHistory($post_id, $interval, $order){
        global $wpdb;


        $days = $this->getDays($interval);
        $sql = "SELECT analytics.`date`, analytics.`count`,analytics.`unique`
                       FROM `".$this->analytics_table."` analytics
                       WHERE ".(((int)$post_id>0) ? "analytics.`post_id`=".(int)$post_id : "analytics.`home`=1") ." AND analytics.`date` >= '".date('Y-m-d', mktime(0, 0, 0, date('m',$this->now), date('d',$this->now)-(int)$days, date('Y',$this->now)))."' GROUP BY analytics.`date` ORDER BY analytics.`date` $order" ;
       //echo "History: ".$sql;
        return $wpdb->get_results($sql);
    }

    /**
     * Get the average value for a specific interval
     * @global type $wpdb
     * @param type $interval
     * @param type $post_id
     * @return type
     */
    function getAverage($interval = null, $post_id = 0){
        global $wpdb;

        $average = array('count' => 0, 'unique' => 0, 'old' => 0);
        $days = 0;

        if (isset($interval))
            $days = $this->getDays($interval);

        $sql = "SELECT old, AVG(count) as `count`, AVG(count) as `unique`
                FROM
                (
                    SELECT count(analytics.`date`) as old , SUM(analytics.`count`) as `count`, SUM(analytics.`unique`) as `unique`
                    FROM `".$this->analytics_table."` analytics
                    INNER JOIN ".$wpdb->posts." wp ON wp.ID = analytics.`post_id` AND wp.post_status = 'publish'
                    WHERE analytics.`post_id` > 0 ".(((int)$post_id>0) ? "
                    AND analytics.`post_id`=".(int)$post_id : "") ."
                    ".(((int)$post_id == 0) ? " AND analytics.`post_id` not in (SELECT option_value FROM ".$wpdb->options." o WHERE o.`option_name` = 'page_on_front')" : '')."
                ";

        if ($days > 0)
            $sql .= " AND analytics.`date` >= '".date('Y-m-d', mktime(0, 0, 0, date('m',$this->now), date('d',$this->now)-(int)$days, date('Y',$this->now)))."'" ;

        $sql.= " GROUP BY analytics.`post_id`
                ) as totals";


        $row = $wpdb->get_row($sql);
        $average = array(
                         'count' => number_format($row->count,2,'.',''),
                         'unique' => number_format($row->unique,2,'.',''),
                         'old' => $row->old,
                        );

        return $average;
    }

    /**
     * Add the global average for the current traffic
     *
     */
    public function getGlobalAverage(){
        return $this->getAverage();
    }

    /**
     * Get the count, unique and average value for day,week and month
     * @param integer $post_id
     * @param string $interval: day | week | month
     * @return array
     */
    public function getTraffic($post_id, $interval = 'month'){

        $traffic = array('day'=>array('count'=>0,'unique'=>0),'week' =>array('count'=>0,'unique'=>0),'month' =>array('count'=>0,'unique'=>0));
        $rows = $this->getHistory($post_id, $interval, 'DESC');
        $traffic[$interval]['average'] = $this->getAverage($interval, $post_id);

        $sum = array('count'=>0,'unique'=>0);
        foreach ($rows as $row){

            if ($row->date == date('Y-m-d',$this->now)){
               $traffic['day']['count'] = $row->count;
               $traffic['day']['unique'] = $row->unique;
            }

            if ($row->date >= date('Y-m-d', mktime(0, 0, 0, date('m',$this->now), date('d',$this->now)-6, date('Y',$this->now)))){
               $traffic['week']['count'] += $row->count;
               $traffic['week']['unique'] += $row->unique;
            }

            if ($row->date >= date('Y-m-d', mktime(0, 0, 0, date('m',$this->now), date('d',$this->now)-30, date('Y',$this->now)))){
               $traffic['month']['count'] += $row->count;
               $traffic['month']['unique'] += $row->unique;
            }

        }
        return $traffic;

    }



    /**
     * Save the cookie for the unique visitor
     */
    public function saveCookie(){
        @setcookie ('sq_visited', 1, time () + 60 * 60 * 24, '/', COOKIE_DOMAIN);
    }

    /**
     * Save the visit in database
     *
     * @return int|false
     */
    public function saveVisit(){
        global $wpdb, $wp_query;
        $post_id = 0;
        $home = 0;
        $sql = '';

        //Be sure not to save the bots
        $botlist = array("bot", "crawl", "crawler", "spider", "google", "yahoo", "msn", "ask", "ia_archiver", "@", "ripper", "robot", "radian", "python", "perl", "java");
        foreach($botlist as $bot){
            if(isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], $bot) !== false)
                    return;
        }

        //Dont save if is admin
        if (is_admin() || is_super_admin() || isset($_COOKIE['sq_snippet']) || SQ_Tools::getIsset('sq_bot')){
            return;
        }

        //Save only the home page, posts and pages
        if (is_single() || is_page()) {
            $post_id = $wp_query->posts[0]->ID;
        }elseif (is_home()) {
            $home = 1;
        }else return;

        //Save Keyword
        if ($referral  = $this->getReferralKeyword())
            if ($referral['keyword'] <> ''){
                $sql = "INSERT INTO `".$this->keyword_table."`
                            (`post_id`,`home`,`domain`,`keyword`,`date`)
                            VALUES (".(int)$post_id.",".(int)$home.",'".$referral['domain']."','".$referral['keyword']."','".date("Y-m-d",$this->now)."')";

                $wpdb->query($wpdb->prepare($sql));
            }

        $sql = "SELECT analytics.`id`,analytics.`count`,analytics.`unique`
                       FROM `".$this->analytics_table."` analytics
                       WHERE ".(((int)$post_id>0) ? "analytics.`post_id`=".(int)$post_id : "analytics.`home`=1") ." AND analytics.`date`='".date('Y-m-d',$this->now)."'" ;

        $row = $wpdb->get_row($sql);

        $sql = '';
        if($row){
            $row->count += 1;
            if (!isset($_COOKIE['sq_visited'])){
                $row->unique += 1;
            }

            $sql = "UPDATE `".$this->analytics_table."` analytics
                       SET analytics.`count`='". (int)$row->count ."',
                           analytics.`unique`='". (int)$row->unique ."'
                       WHERE analytics.`id`=". (int)$row->id;
        }else{
            $sql = "INSERT INTO `".$this->analytics_table."`
                    (`count`,`unique`,`post_id`,`home`,`date`)
                    VALUES (1,1,".(int)$post_id.",".(int)$home.",'".date("Y-m-d")."')";
        }
        if ($sql <> '')
            return $wpdb->query($sql);
        else
            return false;
    }

    /**
     * Save the keyword from the referral
     * @return string
     */
    private function getReferralKeyword(){
        if (!function_exists('parse_url') || !function_exists('preg_match')) return '';

        $keywords = '';
        if (!isset($_SERVER['HTTP_REFERER']) || $_SERVER['HTTP_REFERER'] == '')
            return false;

        $refer = parse_url($_SERVER['HTTP_REFERER']);

        //echo "Referer:".'<pre>'.print_R($_SERVER,true).'</pre>';
        if (!isset($refer['host']) || !isset($refer['query'])) return;

        $host = $refer['host'];
        $refer = $refer['query'];

        $return = array('domain'=>$host,'keyword'=>'');

        if(strstr($host,'google') || strstr($host,'ask'))
        {
            //do google stuff
            $match = preg_match('/&q=([a-zA-Z0-9%\s+-]+)/',$refer, $output);
            $querystring = $output[0];
            $keyword = str_replace('&q=','',$querystring);
            $return['keyword'] =  $this->clearKeyword($keyword);
        }
        elseif(strstr($host,'yahoo'))
        {
            //do yahoo stuff
            $match = preg_match('/p=([a-zA-Z0-9%\s+-]+)/',$refer, $output);
            $querystring = $output[0];
            $keyword = str_replace('p=','',$querystring);
            $return['keyword'] =  $this->clearKeyword($keyword);

        }
        elseif(strstr($host,'bing'))
        {
            //do msn stuff
            $match = preg_match('/q=([a-zA-Z0-9%\s+-]+)/',$refer, $output);
            $querystring = $output[0];
            $keyword = str_replace('q=','',$querystring);
            $return['keyword'] = $this->clearKeyword($keyword);
        }

        return $return;
    }

    /**
     * Clear the keyword from referrals
     */
    private function clearKeyword($keyword){
        $keyword = str_replace(array("+"), array(" "), trim($keyword));
        $keyword = urldecode($keyword);
        //echo 'keyword: '.$keyword;
        return $keyword;
    }
}
?>
