<?php

/**
 * Class for Sitemap Generator
 */
class SQ_Sitemap extends SQ_FrontController {
    /* @var string file path */

    var $file;
    var $opt = array();
    var $data = array();
    var $args = array();
    var $posts_limit = 0;

    function __construct() {
        if (!isset(SQ_Tools::$options['sq_use']) || SQ_Tools::$options['sq_use'] == 0)
            return;
        if (isset(SQ_Tools::$options['sq_auto_sitemap']) && SQ_Tools::$options['sq_auto_sitemap'] == 0)
            return;

        $this->filename = 'sitemap.xml';

        $this->file = ABSPATH . $this->filename;
        //For sitemap ping
        $this->args['timeout'] = 5;
        $this->opt = array('home' => array(1, 'daily'),
            'page' => array(0.6, 'monthly'),
            'post' => array(0.6, 'monthly'),
            'static' => array(0.6, 'weekly'),
            'category' => array(0.4, 'monthly'),
            'archive' => array(0.3, 'daily'),
            'oldarchive' => array(0.3, 'monthly'),
            'tag' => array(0.3, 'weekly'),
            'author' => array(0.3, 'weekly'),
        );



        //Existing posts was deleted
        add_action('delete_post', array($this, 'generateSitemap'), 9999, 1);
        //Existing post was published
        add_action('publish_post', array($this, 'generateSitemap'), 9999, 1);
        //Existing page was published
        add_action('publish_page', array($this, 'generateSitemap'), 9999, 1);
    }

    function init() {
        return;
    }

    function action() {

    }

    /**
     * Generate the XML sitemap for google
     */
    public function generateSitemap() {
        global $wpdb, $wp_query, $wp_version;
        /* get the home url */
        $home = get_bloginfo('url');
        $homeID = 0;
        $wpCompat = (floatval($wp_version) < 2.1);

        /* If the site starts with a page */
        if (get_option('show_on_front') == 'page' && get_option('page_on_front')) {
            $page = get_page(get_option('page_on_front'));
            if ($page) {
                $homeID = $page->ID;
                $lastmod = ($page->post_modified_gmt && $page->post_modified_gmt != '0000-00-00 00:00:00' ? $page->post_modified_gmt : $page->post_date_gmt);
                $this->addLine($home, $this->getTimestamp($lastmod), $this->opt['home'][1], $this->opt['home'][0]);
            }
        }
        else
            $this->addLine($home, $this->getTimestamp(get_lastpostmodified('GMT')), $this->opt['home'][1], $this->opt['home'][0]);

        /*         * ******************************************************************** */
        //Add the pages
        $query = array();

        /* CREATE QUERY */
        $query['what'] = "`ID`, `post_author`, `post_date`, `post_date_gmt`, `post_status`, `post_name`, `post_modified`, `post_modified_gmt`, `post_parent`, `post_type` ";

        /* from */
        $query['from'] = " `" . $wpdb->posts . "` ";
        /* where */
        $query['where'] = '(';


        if ($wpCompat)
            $query['where'] .= "(post_status = 'publish' AND post_date_gmt <= '" . gmdate('Y-m-d H:i:59') . "')";
        else
            $query['where'] .= "(post_status = 'publish' AND (post_type = 'post' OR post_type = '')) ";

        $query['where'] .= " OR ";
        if ($wpCompat)
            $query['where'] .= " post_status='static' ";
        else
            $query['where'] .= " (post_status = 'publish' AND post_type = 'page') ";

        $query['where'] .= ") ";
        $query['where'] .= " AND post_password=''";
        /* order */
        $query['order'] = " ORDER BY post_modified DESC";
        /* limit */
        $query['limit'] = ((int) $this->posts_limit > 0) ? " LIMIT 0," . $this->posts_limit : '';


        $posts = $wpdb->get_results('SELECT ' . $query['what'] . ' FROM ' . $query['from'] . ' WHERE ' . $query['where'] . ' ' . $query['order'] . ' ' . $query['limit'] . ' ');
        if (!$posts) {
            trigger_error(ucfirst(_PLUGIN_NAME_) . " failed to connect to database: " . mysql_error(), E_USER_NOTICE); //E_USER_NOTICE will be displayed on our debug mode
            return;
        }

        /* loop */
        foreach ($posts as $post) {
            $out = array();
            $permalink = get_permalink($post->ID);
            if ($permalink != $home && $post->ID != $homeID) {

                $isPage = false;
                if ($wpCompat)
                    $isPage = ($post->post_status == 'static');
                else
                    $isPage = ($post->post_type == 'page');

                if ($isPage) {
                    $out['priority'] = $this->opt['page'][0];
                    $out['changefreq'] = $this->opt['page'][1];
                } else {
                    $out['priority'] = $this->opt['post'][0];
                    $out['changefreq'] = $this->opt['post'][1];
                }
                $out['lastmod'] = ($post->post_modified_gmt && $post->post_modified_gmt != '0000-00-00 00:00:00' ? $post->post_modified_gmt : $post->post_date_gmt);

                //Add it
                $this->addLine($permalink, $this->getTimestamp($out['lastmod']), $out['changefreq'], $out['priority']);

                $subPage = '';
                if (isset($post->postPages) > 0)
                    for ($i = 1; $i <= $post->postPages; $i++) {
                        if (get_option('permalink_structure') == '') {
                            $subPage = $permalink . '&amp;page=' . ($p + 1);
                        } else {
                            $subPage = trailingslashit($permalink) . user_trailingslashit($p + 1, 'single_paged');
                        }

                        $this->addLine($subPage, $this->getTimestamp($out['lastmod']), $out['changefreq'], $out['priority']);
                    }
            }
        }

        /*         * ******************************************************************** */
        /* Add links ftom categories */
        if (!$this->IsTaxonomySupported()) {
            $query = array();

            /* CREATE QUERY */
            $query['what'] = "c.cat_ID AS ID, MAX(p.post_modified_gmt) AS last_mod";
            $query['from'] = "`" . $wpdb->categories . "` c, `" . $wpdb->post2cat . "` pc, `" . $wpdb->posts . "` p";
            $query['where'] = "pc.category_id = c.cat_ID AND p.ID = pc.post_id AND p.post_status = 'publish' AND p.post_type='post'";
            $query['order'] = "";
            $query['limit'] = "";
            $query['group'] = "GROUP BY c.cat_id";

            $categories = $wpdb->get_results("SELECT " . $query['what'] . " FROM " . $query['from'] . " WHERE " . $query['where'] . " " . $query['order'] . " " . $query['group'] . " " . $query['limit'] . "");

            if ($categories) {
                foreach ($categories as $category) {
                    if ($category && $category->ID && $category->ID > 0) {
                        $this->addLine(get_category_link($category->ID), $this->getTimestamp($category->last_mod), $this->opt['category'][1], $this->opt['category'][0]);
                    }
                }
            }
        } else {
            $categories = get_terms("category", array("hide_empty" => true, "hierarchical" => false));
            if ($categories && is_array($categories) && count($categories) > 0) {
                foreach ($categories AS $category) {
                    $this->addLine(get_category_link($category->term_id), 0, $this->opt['category'][1], $this->opt['category'][0]);
                }
            }
        }

        /*         * ******************************************************************** */
        //Add the archives
        $now = current_time('mysql');
        $query = array();

        /* CREATE QUERY */
        $query['what'] = 'YEAR(post_date_gmt) AS `year`, MONTH(post_date_gmt) AS `month`, MAX(post_date_gmt) as last_mod, count(ID) as posts';
        $query['from'] = "`" . $wpdb->posts . "`";
        $query['where'] = "post_date < '$now' AND post_status = 'publish' AND post_type = 'post' " . (floatval($wp_version) < 2.1 ? "AND {$wpdb->posts}.post_date_gmt <= '" . gmdate('Y-m-d H:i:59') . "'" : "") . "";
        $query['order'] = "ORDER BY post_date_gmt DESC";
        $query['limit'] = "";
        $query['group'] = "GROUP BY YEAR(post_date_gmt),MONTH(post_date_gmt)";

        $archives = $wpdb->get_results("SELECT DISTINCT " . $query['what'] . " FROM " . $query['from'] . " WHERE " . $query['where'] . " " . $query['group'] . " " . $query['order'] . " " . $query['limit'] . "");


        if ($archives) {
            foreach ($archives as $archive) {

                $changeFreq = "";
                //Archive is the current one
                if ($archive->month == date("n") && $archive->year == date("Y")) {
                    $changeFreq = $this->opt['archive'][1];
                } else { // Archive is older
                    $changeFreq = $this->opt['oldarchive'][1];
                }

                $this->addLine(get_month_link($archive->year, $archive->month), $this->getTimestamp($archive->last_mod), $changeFreq, $this->opt['archive'][0]);
            }
        }

        /*         * ******************************************************************** */
        //Add the author pages
        $linkFunc = null;

        //get_author_link is deprecated in WP 2.1, try to use get_author_posts_url first.
        if (function_exists('get_author_posts_url'))
            $linkFunc = 'get_author_posts_url';
        else if (function_exists('get_author_link'))
            $linkFunc = 'get_author_link';

        if ($linkFunc !== null) {
            $query = array();

            /* CREATE QUERY */
            $query['what'] = 'u.ID, u.user_nicename, MAX(p.post_modified_gmt) AS last_post';
            $query['from'] = "`" . $wpdb->users . "` u, `" . $wpdb->posts . "` p";
            $query['where'] = "p.post_author = u.ID AND p.post_status = 'publish' AND p.post_type = 'post' AND p.post_password = '' " . (floatval($wp_version) < 2.1 ? "AND p.post_date_gmt <= '" . gmdate('Y-m-d H:i:59') . "'" : "") . "";
            $query['order'] = "";
            $query['limit'] = "";
            $query['group'] = "GROUP BY u.ID, u.user_nicename";

            $authors = $wpdb->get_results("SELECT DISTINCT " . $query['what'] . " FROM " . $query['from'] . " WHERE " . $query['where'] . " " . $query['order'] . " " . $query['group'] . " " . $query['limit'] . "");


            if ($authors && is_array($authors)) {
                foreach ($authors as $author) {

                    $author_url = ($linkFunc == 'get_author_posts_url' ? get_author_posts_url($author->ID, $author->user_nicename) : get_author_link(false, $author->ID, $author->user_nicename));
                    $this->addLine($author_url, $this->getTimestamp($author->last_post), $this->opt['author'][1], $this->opt['author'][0]);
                }
            }
        }

        /*         * ******************************************************************** */
        if ($this->IsTaxonomySupported()) {
            $count = 0;
            $tags = get_terms("post_tag", array("hide_empty" => true, "hierarchical" => false));
            if ($tags && is_array($tags) && count($tags) > 0) {
                foreach ($tags AS $tag) {
                    if ($count > 3000)
                        break; //not to have a memory break
                    $this->addLine(get_tag_link($tag->term_id), strtotime(get_lastpostmodified()), $this->opt['tag'][1], $this->opt['tag'][0]);
                    $count++;
                }
            }
        }

        return $this->render();
    }

# end function

    /**
     * Push new info to array
     *
     * @param string $link
     * @param string $timestamp
     * @param string $changefreq
     * @param string $priority
     */
    private function addLine($link, $timestamp, $changefreq, $priority) {

        array_push($this->data, array(
            'loc' => $link,
            'lastmod' => date('Y-m-d\TH:i:s+00:00', $timestamp),
            'changefreq' => $changefreq,
            'priority' => $priority
        ));
    }

    /**
     * Create the XML sitemap
     * @return string
     */
    private function render() {
        $content = '';
        try {
            @ini_set('memory_limit', apply_filters('admin_memory_limit', WP_MAX_MEMORY_LIMIT));

            $content .= '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            $content .= '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
                http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd"
                xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";


            if (is_array($this->data) && count($this->data) > 0)
                foreach ($this->data as $data) {
                    $content .= "\t" . '<url>' . "\n";
                    $content .= "\t\t" . '<loc>' . $data['loc'] . '</loc>' . "\n";
                    $content .= "\t\t" . '<lastmod>' . $data['lastmod'] . '</lastmod>' . "\n";
                    $content .= "\t\t" . '<changefreq>' . $data['changefreq'] . '</changefreq>' . "\n";
                    $content .= "\t\t" . '<priority>' . $data['priority'] . '</priority>' . "\n";
                    $content .= "\t" . '</url>' . "\n";
                } # end foreach
            $content .= '</urlset>';
        } catch (Exception $e) {

        }

        $this->saveSitemap($content);

        $this->doPing();
        return $content;
    }

    private function doPing() {
        //Ping Google
        $google_url = "http://www.google.com/webmasters/sitemaps/ping?sitemap=" . urlencode($this->getXmlUrl());
        SQ_Tools::sq_remote_get($google_url, $this->args);

        //Ping Bing
        $bing_url = "http://www.bing.com/webmaster/ping.aspx?siteMap=" . urlencode($this->getXmlUrl());
        SQ_Tools::sq_remote_get($bing_url, $this->args);
    }

    /**
     * Converts into a unix timestamp
     *
     * @param string time
     * @return string
     */
    private function getTimestamp($time) {

        $time = date('Y-m-d H:i:s');
        list($date, $hours) = explode(' ', $time);
        list($year, $month, $day) = explode('-', $date);
        list($hour, $min, $sec) = explode(':', $hours);

        return mktime(intval($hour), intval($min), intval($sec), intval($month), intval($day), intval($year));
    }

    /**
     * Function to save the sitemap data to file as either XML or XML.GZ format
     * @param string $data XML data
     * @param string $filename file path
     *
     * @return boolean
     */
    private function saveSitemap($data) {
        if (function_exists('gzopen') && function_exists('gzwrite') && function_exists('gzclose')) {
            if (function_exists('file_exists') && file_exists($this->file . '.gz'))
                @unlink($this->file . '.gz');

            if (function_exists('file_exists') && !file_exists($this->file . '.gz'))
                if ($gz = @gzopen($this->file . '.gz', 'wb9')) {
                    @gzwrite($gz, $data);
                    @gzclose($gz);
                }
        }

        if (function_exists('fopen'))
            if ($fp = @fopen($this->file, 'w+')) {
                fwrite($fp, $data);
                fclose($fp);
                return true;
            }

        return false;
    }

# end function

    /**
     * Returns the URL for the XML sitemap file
     *
     * @return string
     */
    public function getXmlUrl() {
        return trailingslashit(get_bloginfo('url')) . $this->filename;
    }

    /**
     * Returns if this version of WordPress supports the new taxonomy system
     *
     * @since 3.0b8
     * @access private
     * @author Arne Brachhold
     * @return true if supported
     */
    private function IsTaxonomySupported() {
        return (function_exists("get_taxonomy") && function_exists("get_terms"));
    }

}

?>