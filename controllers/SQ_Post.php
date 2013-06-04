<?php
class SQ_Post extends SQ_FrontController {

    function hookInit(){
        add_filter( 'tiny_mce_before_init', array(&$this->model,'setCallback') );
        add_filter('mce_external_plugins',  array(&$this->model,'addHeadingButton') );
        add_filter('mce_buttons', array(&$this->model,'registerButton'));

        if (SQ_Tools::$options['sq_api'] == '') return;

        add_action('save_post', array($this, 'hookSavePost'), 10);

        //For Shopp plugin - product
        add_action('shopp_product_saved',array($this,'hookShopp'),10);

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
        if ((int)$post_ID == 0){
           if (SQ_Tools::getIsset('id')) $GLOBALS['sq_postID'] = (int)SQ_Tools::getValue('id');
        }else{
           $GLOBALS['sq_postID'] = $post_ID;
        }
        /*********************************/

        echo '<script type="text/javascript">(function() {this.sq_tinymce = { callback: function () {}, setup: function(ed){} } })(window);</script>';
    }

    /**
     * Hook the Shopp plugin save product
     */
    function hookShopp($Product){
        $this->checkSeo($Product->id);
    }

    /**
     * Hook the post save/update
     * @param type $post_id
     */
    function hookSavePost($post_id){
        $file_name = false;
         // unhook this function so it doesn't loop infinitely
        remove_action('save_post', array($this, 'hookSavePost'), 10);
        if( (SQ_Tools::getValue('action')) == 'editpost' &&
             wp_is_post_revision($post_id) == '' &&
             wp_is_post_autosave($post_id) == '' &&
             get_post_status($post_id) != 'auto-draft' &&
             SQ_Tools::getValue('autosave') == ''){

            $this->checkSeo($post_id, get_post_status($post_id));
        }
        if( (SQ_Tools::getValue('action')) == 'editpost' &&
             wp_is_post_autosave($post_id) == '' &&
             get_post_status($post_id) != 'auto-draft' &&
             SQ_Tools::getValue('autosave') == ''){

            $this->checkImage($post_id);
        }
        add_action('save_post', array($this, 'hookSavePost'), 10);

    }

    function checkImage($post_id){

        $img_dir = $this->model->getImgDir();
        $img_url = $this->model->getImgUrl();

        if(!$img_dir || !$img_url)
            return;

        $content = stripslashes(SQ_Tools::getValue('post_content'));
        $tmpcontent = trim($content, "\n");
        $urls = array();

        if (function_exists('preg_match_all')){
            @preg_match_all('/<img[^>]+src="([^"]+)"[^>]+>/i', $tmpcontent, $out);

            if (is_array($out)){
              if(!is_array($out[1]) || count($out[1]) == 0)
                 return;

              foreach ($out[1] as $row){
                 if (strpos($row,basename($_SERVER['HTTP_HOST'])) === false && strpos($row,'http') !== false){
                    if(!in_array($row, $urls)){
                        //echo $row;
                        $urls[] = $row;
                    }
                 }
              }
            }
        }

        if (!is_array($urls))
           return;

        if(is_array($seo) && count($urls) == 0)
           return;


        foreach ($urls as $url){
            //if (strpos($url, $this->model->getImgUrl()) !== false) continue;

            $file_name = $this->model->upload_image($url);
            if ($file_name !== false) {
                $localurl = $img_url . $file_name;
                //echo '$localurl: '.$localurl;
                $localfile = $img_dir . $file_name;
                $wp_filetype = wp_check_filetype($file_name, null);

                $content = str_replace($url, $localurl, $content);

                $attach_id = wp_insert_attachment(array(
                        'post_mime_type' => $wp_filetype['type'],
                        'post_title' => preg_replace('/\.[^.]+$/', '', $file_name),
                        'post_content' => '',
                        'post_status' => 'inherit',
                        'guid' => $localurl
                ), $localfile, $post_id);

                $attach_data = wp_generate_attachment_metadata($attach_id, $localfile);
                wp_update_attachment_metadata($attach_id, $attach_data);
            }
        }


        if ($file_name !== false){
            $_POST['post_content'] = addslashes($content);

            // update post in database
            wp_update_post(array(
                    'ID' => $post_id,
                    'post_content' => $content)
            );
        }
    }
    function checkSeo($post_id, $status =''){
        $args = array();

        $seo = SQ_Tools::getValue('sq_seo');

        if(is_array($seo) && count($seo)>0)
           $args['seo'] = implode (',', $seo);

        $args['keyword'] = SQ_Tools::getValue('sq_keyword');

        $args['status'] = $status;
        $args['permalink'] = get_permalink($post_id);
        $args['permalink'] = $this->getPaged($args['permalink']);
        $args['permalink'] = urlencode($args['permalink']);
        $args['author'] = (int)SQ_Tools::getUserID();
        $args['post_id'] = $post_id;

        SQ_Action::apiCall('sq/seo/post',$args);
    }

    function getPaged($link) {
        $page = get_query_var('paged');
        if ($page && $page > 1) {
            $link = trailingslashit($link) ."page/". "$page";
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
    public function action(){
      parent::action();

      switch (SQ_Tools::getValue('action')){
       case 'sq_feedback':
            global $current_user;
            $return = array();

            SQ_Tools::saveOptions('sq_feedback', 1);

            $line = "\n"."________________________________________"."\n";
            $from = $current_user->user_email;
            $subject = __('Plugin Feedback',_SQD_PLUGIN_NAME_);
            $face = SQ_Tools::getValue('feedback');
            $message = SQ_Tools::getValue('message');

            if ($message <> '' || (int)$face > 0){
                switch($face){
                    case 1:
                        $face= 'Angry';
                        break;
                    case 2:
                        $face= 'Sad';
                        break;
                    case 3:
                        $face= 'Happy';
                        break;
                    case 4:
                        $face= 'Excited';
                        break;
                    case 5:
                        $face= 'Love it';
                        break;
                }
                if ($message <> '')
                    $message = $message . $line;

                if($face <> '') {
                    $message .= 'Url:' . get_bloginfo('wpurl') . "\n";
                    $message .= 'Face:' .$face ;
                }



                $headers[] = 'From: '.$current_user->display_name.' <'.$from.'>';

                //$this->error='buuum';
                if (wp_mail( _SQ_SUPPORT_EMAIL_, $subject, $message, $headers))
                    $return['message'] = __('Thank you for your feedback',_PLUGIN_NAME_);
                else {
                    $return['message'] = __('Could not send the email...',_PLUGIN_NAME_);
                }
            }else{
                $return['message'] = __('No message.',_SQD_PLUGIN_NAME_);
            }

            SQ_Tools::setHeader('json');
            echo json_encode($return);
            break;

       case 'sq_support':
           global $current_user;
            $return = array();
            $versions = '';

            $versions .= 'Url:' . get_bloginfo('wpurl') . "\n";
            $versions .= 'Squirrly version: ' . SQ_VERSION_ID . "\n";
            $versions .= 'Wordpress version: ' . WP_VERSION_ID . "\n";
            $versions .= 'PHP version: ' . PHP_VERSION_ID . "\n";

            $line = "\n"."________________________________________"."\n";
            $from = $current_user->user_email;
            $subject = __('Plugin Support',_SQD_PLUGIN_NAME_);
            $message = SQ_Tools::getValue('message');

            if ($message <> ''){
                $message .= $line;
                $message .= $versions ;

                $headers[] = 'From: '.$current_user->display_name.' <'.$from.'>';

                //$this->error='buuum';
                if (wp_mail( _SQ_SUPPORT_EMAIL_, $subject, $message, $headers))
                    $return['message'] = __('Message sent...',_PLUGIN_NAME_);
                else {
                    $return['message'] = __('Could not send the email...',_PLUGIN_NAME_);
                }
            }else{
                $return['message'] = __('No message.',_SQD_PLUGIN_NAME_);
            }

            header('Content-Type: application/json');
            echo json_encode($return);
            break;
      }
      exit();

    }


}
?>