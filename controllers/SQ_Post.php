<?php

class SQ_Post extends SQ_FrontController {

    var $saved;

    /**
     * Initialize the TinyMCE editor for the current use
     *
     * @return void
     */
    function hookInit() {
        add_filter('tiny_mce_before_init', array(&$this->model, 'setCallback'));
        add_filter('mce_external_plugins', array(&$this->model, 'addHeadingButton'));
        add_filter('mce_buttons', array(&$this->model, 'registerButton'));

        if (SQ_Tools::$options['sq_api'] == '')
            return;

        add_action('save_post', array($this, 'hookSavePost'), 10);

        //For Shopp plugin - product
        add_action('shopp_product_saved', array($this, 'hookShopp'), 10);
    }

    /**
     * hook the Head
     *
     * @global integer $post_ID
     */
    function hookHead() {
        global $post_ID;
        parent::hookHead();

        /**
         * Add the post ID in variable
         * If there is a custom plugin post or Shopp product
         *
         * Set the global variable $sq_postID for cookie and keyword record
         */
        if ((int) $post_ID == 0) {
            if (SQ_Tools::getIsset('id'))
                $GLOBALS['sq_postID'] = (int) SQ_Tools::getValue('id');
        }else {
            $GLOBALS['sq_postID'] = $post_ID;
        }
        /*         * ****************************** */

        echo '<script type="text/javascript">(function() {this.sq_tinymce = { callback: function () {}, setup: function(ed){} } })(window);</script>';
    }

    /**
     * Hook the Shopp plugin save product
     */
    function hookShopp($Product) {
        $this->checkSeo($Product->id);
    }

    /**
     * Hook the post save/update
     * @param type $post_id
     */
    function hookSavePost($post_id) {
        $file_name = false;


        // unhook this function so it doesn't loop infinitely
        remove_action('save_post', array($this, 'hookSavePost'), 10);

        //If the post is a new or edited post
        if ((SQ_Tools::getValue('action')) == 'editpost' &&
                wp_is_post_revision($post_id) == '' &&
                wp_is_post_autosave($post_id) == '' &&
                get_post_status($post_id) != 'auto-draft' &&
                get_post_status($post_id) != 'inherit' &&
                SQ_Tools::getValue('autosave') == '') {
            //echo 'saving';
            //check for custom SEO
            $this->checkAdvMeta($post_id);
            //check the SEO from Squirrly Live Assistant
            $this->checkSeo($post_id, get_post_status($post_id));
        }

        //If the post is not auto-save post
        if ((SQ_Tools::getValue('action')) == 'editpost' &&
                wp_is_post_autosave($post_id) == '' &&
                get_post_status($post_id) != 'auto-draft' &&
                get_post_status($post_id) != 'inherit' &&
                SQ_Tools::getValue('autosave') == '') {

            if (!$this->saved)
            //check the remote images
                $this->checkImage($post_id);
            $this->saved = true;
        }


        add_action('save_post', array($this, 'hookSavePost'), 10);
    }

    /**
     * Check if the image is a remote image and save it locally
     *
     * @param integer $post_id
     * @return false|void
     */
    function checkImage($post_id) {
        @set_time_limit(90);
        $local_file = false;

        $content = stripslashes(SQ_Tools::getValue('post_content'));
        $tmpcontent = trim($content, "\n");
        $urls = array();

        if (function_exists('preg_match_all')) {
            @preg_match_all('/<img[^>]+src="([^"]+)"[^>]+>/i', $tmpcontent, $out);

            if (is_array($out)) {
                if (!is_array($out[1]) || count($out[1]) == 0)
                    return;

                foreach ($out[1] as $row) {
                    if (strpos($row, 'http') !== false &&
                            strpos($row, get_bloginfo('url')) === false) {
                        if (!in_array($row, $urls)) {
                            $urls[] = $row;
                        }
                    }
                }
            }
        }

        if (!is_array($urls) || (is_array($urls) && count($urls) == 0))
            return;
        $urls = @array_unique($urls);
        foreach ($urls as $url) {
            if ($file = $this->model->upload_image($url)) {
                if (!file_is_valid_image($file['file']))
                    continue;

                //encode special characters
                $local_file = str_replace($file['filename'], urlencode($file['filename']), $file['url']);
                if ($local_file !== false) {
                    $content = str_replace($url, $local_file, $content);

                    $attach_id = wp_insert_attachment(array(
                        'post_mime_type' => $file['type'],
                        'post_title' => preg_replace('/\.[^.]+$/', '', $file['filename']),
                        'post_content' => '',
                        'post_status' => 'inherit',
                        'guid' => $local_file
                            ), $file['file'], $post_id);

                    $attach_data = wp_generate_attachment_metadata($attach_id, $file['file']);
                    wp_update_attachment_metadata($attach_id, $attach_data);
                }
            }
        }


        if ($local_file !== false) {
            wp_update_post(array(
                'ID' => $post_id,
                'post_content' => $content)
            );
        }
    }

    /**
     * Check the SEO from Squirrly Live Assistant
     *
     * @param integer $post_id
     * @param void
     */
    function checkSeo($post_id, $status = '') {
        $args = array();

        $seo = SQ_Tools::getValue('sq_seo');

        if (is_array($seo) && count($seo) > 0)
            $args['seo'] = implode(',', $seo);

        $args['keyword'] = SQ_Tools::getValue('sq_keyword');

        $args['status'] = $status;
        $args['permalink'] = get_permalink($post_id);
        $args['permalink'] = $this->getPaged($args['permalink']);
        $args['permalink'] = urlencode($args['permalink']);
        $args['author'] = (int) SQ_Tools::getUserID();
        $args['post_id'] = $post_id;

        SQ_Action::apiCall('sq/seo/post', $args, 5);
    }

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

    /**
     * Called when Post action is triggered
     *
     * @return void
     */
    public function action() {
        parent::action();

        switch (SQ_Tools::getValue('action')) {
            case 'sq_save_meta':
                $return = $this->checkAdvMeta(SQ_Tools::getValue('post_id'));
                echo json_encode($return);
                break;
        }
        exit();
    }

    /**
     * Check if there are advanced settings for meta title, description and keywords
     * @param integer $post_id
     * @return array | false
     *
     */
    private function checkAdvMeta($post_id) {
        $meta = array();
        if (SQ_Tools::getIsset('sq_fp_title') || SQ_Tools::getIsset('sq_fp_description') || SQ_Tools::getIsset('sq_fp_keywords')) {
            if (SQ_Tools::getIsset('sq_fp_title'))
                $meta[] = array('key' => 'sq_fp_title',
                    'value' => SQ_Tools::getValue('sq_fp_title'));

            if (SQ_Tools::getIsset('sq_fp_description'))
                $meta[] = array('key' => 'sq_fp_description',
                    'value' => SQ_Tools::getValue('sq_fp_description'));

            if (SQ_Tools::getIsset('sq_fp_keywords'))
                $meta[] = array('key' => 'sq_fp_keywords',
                    'value' => SQ_Tools::getValue('sq_fp_keywords'));

            $this->model->saveAdvMeta($post_id, $meta);
            return $meta;
        }
        return false;
    }

}

?>