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
                    $init['content_css'] = (($init['content_css'] <> '') ? $init['content_css'] . ',' : '' ) . _SQ_THEME_URL_.'css/sq_frontend.css';
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
    * Upload the image on server
    *
    * Add configuration page
    */
    public function upload_image($url){
            global $wp_filesystem;

            $upload_dir = $this->getImgDir();

            $file_name = wp_unique_filename($upload_dir, basename($url));
            $file_path = $upload_dir . $file_name;

            if(!file_exists($file_path))
            {
                    $http_response = wp_remote_get($url, array('timeout' => 10));

                    if(is_wp_error($http_response))
                            return false;

                    $data = wp_remote_retrieve_body($http_response);

                    add_filter('filesystem_method', array($this, 'filesystem_method'));

                    WP_Filesystem();

                    if (!$wp_filesystem->put_contents($file_path, $data, FS_CHMOD_FILE)) {
                            return false;
                    }

                    return $file_name;
            }

            return false;
    }

    /**
    * filesystem_method
    *
    * Change WP_Filesystem method to direct for this plugin
    *
    * @param string $method File System Method
    */
    public function filesystem_method($method){
            return 'direct';
    }
    /**
    * Get the upload path
    */
    public function getImgDir(){
        return ABSPATH . '/wp-content/uploads/';
    }

    /**
    * Get the upload url
    */
    public function getImgUrl() {
        $url = parse_url(get_bloginfo('wpurl'));
        $url = $url['scheme'] . '://'. $url['host'];
        $wpurl = str_replace($url,'',get_bloginfo('wpurl'));

        return $wpurl. '/wp-content/uploads/';
    }


}
?>