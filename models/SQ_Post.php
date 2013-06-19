<?php
/**
 * Shown in the Post page (called from SQ_Menu)
 *
 */
class Model_SQ_Post {

    /**
     * Set the callback to tinymce with javascript SQ_eventChange function call on event
     * @param string $init
     * @return string
     */
    public function setCallback( $init ) {

            if ( wp_default_editor() == 'tinymce' ){
                    $init['setup'] = 'window.sq_tinymce.setup';
                    $init['onchange_callback'] = 'window.sq_tinymce.callback';
                    $init['content_css'] = ((isset($init['content_css']) && $init['content_css'] <> '') ? $init['content_css'] . ',' : '' ) . _SQ_THEME_URL_.'css/sq_frontend.css';
            }

            return $init;
    }

    /**
     * Register a button in tinymce editor
     */
    public function registerButton($buttons){
        array_push($buttons, "|", "heading");
        return $buttons;
    }

    public function addHeadingButton($plugin_array){
         $plugin_array['heading'] = _SQ_THEME_URL_.'js/tinymce.js';
         return $plugin_array;
    }

    /**
     * Search for posts in the local blog
     *
     * @global object $wpdb
     * @param string $q
     * @return array
     */
    public function searchPost($q, $exclude = array(), $start = 0, $nbr = 8){
        global $wpdb;
        $responce = array();
        if (sizeof($exclude) > 1){
          $exclude = join(',',$exclude);
        }else
          $exclude = (int)$exclude;

        $q = trim ($q,'"');
        //echo "SELECT ID, post_title, post_date_gmt, post_content, post_type FROM $wpdb->posts WHERE post_status = 'publish' AND (post_title LIKE '%$q%' OR post_content LIKE '%$q%') AND ID not in ($exclude) ORDER BY post_title LIMIT " . $start . ',' . ($start + $nbr);
        /* search in wp database */
        $posts = $wpdb->get_results("SELECT ID, post_title, post_date_gmt, post_content, post_type FROM $wpdb->posts WHERE post_status = 'publish' AND (post_title LIKE '%$q%' OR post_content LIKE '%$q%') AND ID not in ($exclude) ORDER BY post_title LIMIT " . $start . ',' . ($start + $nbr));


        if ($posts){

            $responce['total'] = $wpdb->num_rows;
            foreach ($posts as $post) {
                $responce['results'][] = array('id' => $post->ID,
                                              'url' => get_permalink($post->ID),
                                              'title' => $post->post_title,
                                              'content' => $this->truncate($post->post_content, 50),
                                              'date' => $post->post_date_gmt);

            }

        }else{
            $responce['error'] .= __('Squirrly could not find any results for: ') .' "' . stripslashes($q) . '"';
        }
        return json_encode($responce);
    }

    private function truncate($text, $length = 25){
        if (!$length)
            return $text;

        $text = strip_tags($text);
        $words = explode(' ', $text, $length + 1);

        if (count($words) > $length) {
            array_pop($words);
            array_push($words, '...');
            $text = implode(' ', $words);
        }
        return $text;
    }



     /**
    * Upload the image on server from version 2.0.4
    *
    * Add configuration page
    */
    public function upload_image($url){
        $dir = null;
        $file = array();

        $response = wp_remote_get($url, array('timeout' => 30));
        $file = wp_upload_bits( basename( $url ), '', wp_remote_retrieve_body( $response ), $dir);

        $file['type'] = wp_remote_retrieve_header( $response, 'content-type' );

        if (!isset($file['error']) || $file['error'] == '')
            if (isset($file['url']) && $file['url'] <> ''){
                $file['url'] = str_replace(get_bloginfo('url'), '', $file['url']);
                $file['filename'] = basename($file['url']);

                return $file;
            }

        return false;
    }

    /**
     * Save/Update Meta in database
     *
     * @param integer $post_id
     * @param array $metas [key,value]
     * @return true
     */
    public function saveAdvMeta($post_id, $metas){
        global $wpdb;

        if ((int)$post_id == 0 || !is_array($metas)) return;

        foreach ($metas as $meta) {
            $sql = "SELECT `meta_value`
                    FROM `".$wpdb->postmeta."`
                    WHERE `meta_key` = '".$meta['key']."' AND `post_id`=".(int)$post_id  ;

            if($wpdb->get_row($sql)){
                $sql = "UPDATE `".$wpdb->postmeta."`
                       SET `meta_value` = '".$meta['value']."'
                       WHERE `meta_key` = '".$meta['key']."' AND `post_id`=". (int)$post_id;

            }else{
                $sql = "INSERT INTO `".$wpdb->postmeta."`
                    (`post_id`,`meta_key`,`meta_value`)
                    VALUES (".(int)$post_id.",'".$meta['key']."','".$meta['value']."')";
            }
            $wpdb->query($sql) ;
        }

        return $metas ;
    }


}
?>