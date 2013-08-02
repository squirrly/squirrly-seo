<?php

class SQ_PostsList extends SQ_FrontController {

    /** @var array Posts types in */
    private $types = array();

    /** @var integer Set the column index for Squirrly */
    private $pos = 5;

    /** @var string Set the column name for Squirrly */
    private $column_id = 'sq_rank_column';

    /** @var boolean Is post list colled */
    private $is_list = false;
    private $posts = array();

    /**
     * Called in SQ_Menu > hookMenu
     */
    function init() {
        $this->types = array('post_posts',
            'page_posts',
            'edit-product',
            'product_posts');
    }

    /**
     * Create the column and filter for the Posts List
     *
     */
    function hookInit() {
        $browser = SQ_Tools::getBrowserInfo();

        if ($browser['name'] == 'IE' && (int) $browser['version'] < 9 && (int) $browser['version'] > 0)
            return;

        if (isset(SQ_Tools::$options['sq_api']) && SQ_Tools::$options['sq_api'] <> '') {
            foreach ($this->types as $type) {

                if (isset($options['hideeditbox-post']) && $options['hideeditbox-post'])
                    continue;
                add_filter('manage_' . $type . '_columns', array($this, 'add_column'), 10, 1);
                add_action('manage_' . $type . '_custom_column', array($this, 'add_row'), 10, 2);
            }
            add_filter('posts_where', array($this, 'filterPosts'));


            //add_filter( 'request', array( $this, 'sortPosts' ) );
        }
    }

    /**
     * Filter the Posts when sq_post_id is set
     *
     * @param string $where
     * @return string
     */
    function filterPosts($where) {
        if (!is_admin())
            return;


        if (SQ_Tools::getIsset('sq_post_id')) {
            $where .= " AND ID = " . (int) SQ_Tools::getValue('sq_post_id');
        }

        return $where;
    }

    /**
     * Sorting option
     *
     * @param type $request
     * @return type
     */
    function sortPosts($request) {
        if (!is_admin())
            return;

        return $request;
    }

    /**
     * Hook the Wordpress header
     */
    function hookHead() {
        parent::hookHead();
        echo '<script type="text/javascript">
                   google.load("visualization", "1", {packages: ["corechart"]});
              </script>';
    }

    /**
     * Add the Squirrly column in the Post List
     *
     * @param array $columns
     * @return array
     */
    function add_column($columns) {
        $this->is_list = true;
        SQ_ObjController::getController('SQ_DisplayController', false)
                ->loadMedia(_SQ_STATIC_API_URL_ . SQ_URI . '/css/sq_postslist.css?ver=' . SQ_VERSION_ID);
        SQ_ObjController::getController('SQ_DisplayController', false)
                ->loadMedia(_SQ_STATIC_API_URL_ . SQ_URI . '/js/sq_rank.js?ver=' . SQ_VERSION_ID);

        return $this->insert($columns, array($this->column_id => __('Squirrly')), $this->pos);
    }

    /**
     * Add row in Post list
     *
     * @param object $column
     * @param integer $post_id
     */
    function add_row($column, $post_id) {
        $title = '';
        $description = '';
        $frontend = null;
        $cached = false;

        if ($column == $this->column_id) {
            if (isset($_COOKIE[$this->column_id . $post_id]) && $_COOKIE[$this->column_id . $post_id] <> '') {
                $cached = true;
            } else {
                if (get_post_status($post_id) == 'publish')
                    array_push($this->posts, $post_id);
            }

            echo '<div class="' . $this->column_id . '_row ' . ((!$cached) ? 'sq_minloading' : '') . '" ref="' . $post_id . '">' . (($cached) ? $_COOKIE[$this->column_id . $post_id] : '') . '</div>';

            if ($frontend = SQ_ObjController::getModel('SQ_Frontend')) {
                $title = $frontend->getAdvancedMeta($post_id, 'title');
                $description = $frontend->getAdvancedMeta($post_id, 'description');
                if ($post_id == get_option('page_on_front')) {
                    if (SQ_Tools::$options['sq_fp_title'] <> '' && !$title)
                        $title = SQ_Tools::$options['sq_fp_title'];
                    if (SQ_Tools::$options['sq_fp_description'] <> '' && !$description)
                        $description = SQ_Tools::$options['sq_fp_description'];
                }
                echo '<script type="text/javascript">
                    jQuery(\'#post-' . $post_id . '\').find(\'.row-title\').before(\'' . (($description <> '') ? '<span class="sq_rank_custom_meta sq_rank_customdescription sq_rank_sprite" title="' . __('Custom description: ', _PLUGIN_NAME_) . ' ' . $description . '"></span>' : '') . ' ' . (($title <> '') ? '<span class="sq_rank_custom_meta sq_rank_customtitle sq_rank_sprite" title="' . __('Custom title: ', _PLUGIN_NAME_) . ' ' . $title . '"></span>' : '') . '\');
               </script>';
            }
        }
    }

    /**
     * Hook the Footer
     *
     */
    function hookFooter() {
        if (!$this->is_list)
            return;

        $posts = '';
        foreach ($this->posts as $post) {
            $posts .= '"' . $post . '",';
        }
        if (strlen($posts) > 0)
            $posts = substr($posts, 0, strlen($posts) - 1);

        echo '<script type="text/javascript">
                    var sq_posts = new Array(' . $posts . ');
                    var __sq_article_rank = "' . __('Squirrly article rank', _PLUGIN_NAME_) . '";
                    var __sq_refresh = "' . __('Update', _PLUGIN_NAME_) . '"
                    var __sq_more_details = "' . __('More details', _PLUGIN_NAME_) . '";
                    var __sq_less_details = "' . __('Less details', _PLUGIN_NAME_) . '";
                    var __sq_interval_text = "' . __('Interval: ', _PLUGIN_NAME_) . '";
                    var __sq_interval_day = "' . __('Latest', _PLUGIN_NAME_) . '";
                    var __sq_interval_week = "' . __('Last 7 days', _PLUGIN_NAME_) . '";
                    var __sq_interval_month = "' . __('Last 30 days', _PLUGIN_NAME_) . '";

                    var __sq_goto_allposts = "' . __('See it in \'All Posts\'', _PLUGIN_NAME_) . '";
                    var __sq_rankglobal_text = "' . __('progress', _PLUGIN_NAME_) . '";
                    var __sq_rankoptimized_text = "' . __('optimized', _PLUGIN_NAME_) . '";
                    var __sq_rankseemore_text = "' . __('See rank', _PLUGIN_NAME_) . '";
                    var __sq_rankseeless_text = "' . __('Hide rank', _PLUGIN_NAME_) . '";
                    var __sq_optimize_text = "' . __('Optimize', _PLUGIN_NAME_) . '";
                    if (typeof __token === "undefined") var __token = "' . SQ_Tools::$options['sq_api'] . '";
                    var __sq_ranknotpublic_text = "' . __('Not Public', _PLUGIN_NAME_) . '";

                  </script>';
    }

    /**
     * Push the array to a specific index
     * @param array $src
     * @param array $in
     * @param integer $pos
     * @return array
     */
    function insert($src, $in, $pos) {
        if (is_int($pos))
            $array = array_merge(array_slice($src, 0, $pos), $in, array_slice($src, $pos));
        else {
            foreach ($src as $k => $v) {
                if ($k == $pos)
                    $array = array_merge($array, $in);
                $array[$k] = $v;
            }
        }
        return $array;
    }

    /**
     * Hook Get/Post action
     * @return string
     */
    public function action() {

        parent::action();

        switch (SQ_Tools::getValue('action')) {
            case 'sq_posts_rank':
                $args = array();
                $progress = array();

                //Check the global progress in traffic for optimized and not optimized articles
                $status = SQ_ObjController::getModel('SQ_BlockStatus');
                if (is_object($status) && SQ_Tools::$options['sq_ws'] == 1) {
                    $progress = $status->getGlobalProgress();
                }

                if (is_array(SQ_Tools::getValue('posts'))) {
                    $posts = SQ_Tools::getValue('posts');
                    $args['posts'] = join(',', $posts);

                    //Send totals to api
                    $args['visit'] = '';
                    $args['unique'] = '';
                    $args['avgmonth'] = '';


                    //$args['rank'] = '';
                    foreach ($posts as $post_id) {
                        $this->model->post_id = (int) $post_id;

                        $traffic = array();
                        $traffic = $this->model->getTrafficProgress();
                        if (is_array($traffic)) {
                            $args['visit'] .= (($args['visit'] <> '') ? ',' : '') . (int) $traffic['month']['count'];
                            $args['unique'] .= (($args['unique'] <> '') ? ',' : '') . (int) $traffic['month']['unique'];
                            $args['avgmonth'] .= (($args['avgmonth'] <> '') ? ',' : '') . (int) $traffic['month']['average']['count'];
                        }
                        //$args['average'] = (int)$traffic['global']['average']['count'];
                    }
                    $global = array();
                    $global = $this->model->getGlobalAverage();
                    $args['average'] = $global['count'];
                    $args['progress'] = $progress;
                    //////////////////////////////

                    $response = SQ_Action::apiCall('sq/pack/total', $args);
                    //echo 'responce'.$response;
                    $return = json_decode($response);
                }

                if (!isset($return) || !is_object($return))
                    $return = (object) NULL;

                //Set the progress information for the article
                if (is_array($progress) && !isset($return->status) && is_object($status)) {
                    if (SQ_Tools::$options['sq_ws'] == 1)
                        $return->status = $status->packStatus($progress);
                    else
                        $return->status = '';
                }

                SQ_Tools::setHeader('json');
                echo json_encode($return);
                exit();
            case 'sq_post_rank_brief':

                $args['post_id'] = (int) SQ_Tools::getValue('post');
                $args['permalink'] = get_permalink($args['post_id']);
                $args['permalink'] = $this->getPaged($args['permalink']);
                $args['permalink'] = urlencode($args['permalink']);

                $this->model->post_id = $args['post_id'];

                if (get_post_status($args['post_id']) == 'draft') {
                    $error = array('error' => 'sq_no_information',
                        'message' => __('Publish the article to start Squirrly Article Rank', _PLUGIN_NAME_));

                    exit(json_encode($error));
                }

                $traffic = array();
                $traffic = $this->model->getTrafficProgress();
                if (is_array($traffic)) {
                    $args['visit'] = (int) $traffic['month']['count'];
                    $args['unique'] = (int) $traffic['month']['unique'];
                    $args['avgmonth'] = (int) $traffic['month']['average']['count'];
                }
                //Call the api and get the totals
                $response = SQ_Action::apiCall('sq/pack/brief', $args);
                //echo 'responce'.$response;
                $return = json_decode($response);

                if (!is_object($return))
                    $return = (object) NULL;
                //print_R($return);
                //Get the rank in google for the current post
                $rank = SQ_ObjController::getController('SQ_Ranking', false);
                if (is_object($rank)) {
                    $rank->checkIndexed($return, $args['post_id']);
                }
                //Pack the response in json
                $return = $this->model->packBrief($return);

                SQ_Tools::setHeader('json');
                echo json_encode($return);

                exit();

            case 'sq_post_rank':
                $args['post_id'] = (int) SQ_Tools::getValue('post');
                $args['ctx'] = $this->model->ctx;
                $args['interval'] = SQ_Tools::getValue('interval', 'week');
                $args['title'] = $this->model->reportTitles[$args['interval']];

                $this->model->post_id = $args['post_id'];
                $this->model->interval = $args['interval']; //Get the traffic for the whole month

                $response = SQ_Action::apiCall('sq/pack/detail', $args);
                //echo 'responce'.$response;
                $return = json_decode($response);

                if (!is_object($return))
                    $return = (object) NULL;

                $rank = SQ_ObjController::getController('SQ_Ranking', false);
                if (is_object($rank)) {
                    $rank->processRanking($return, $args['post_id']);
                }

                $return->rank = @str_replace('<!--traffic-->', $this->model->getTrafficZone(), $return->rank);
                $serp = $this->model->packSERP();
                $other = $this->model->packOthersSERP();
                $return->rank = @str_replace('<!--rank-->', $this->model->getSERPZone($serp) . $this->model->getOthersSERPZone($other), $return->rank);
                // echo $return->rank;
                //exit();
                SQ_Tools::setHeader('json');
                echo json_encode($return);
                exit();
            case 'sq_posts_status_close':
                SQ_Tools::saveOptions('sq_posts_status_close', time());
                exit();
        }
    }

    /**
     * Replace string ()
     * @param type $search
     * @param type $replace
     * @param type $subject
     * @return type
     */
    function str_lreplace($search, $replace, $subject) {
        return preg_replace('~(.*)' . preg_quote($search, '~') . '~', '$1' . $replace, $subject, 1);
    }

    /**
     * Add slash to pages
     *
     * @param type $link
     * @return string
     */
    function getPaged($link) {
        $page = get_query_var('paged');
        if ($page && $page > 1) {
            $link = trailingslashit($link) . "page/" . "$page";
            if ($has_ut) {
                $link = user_trailingslashit($link, 'paged');
            } else {
                $link .= '/';
            }
        }
        return $link;
    }

}

