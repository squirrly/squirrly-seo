<?php

/**
 * Handles the parameters and url
 *
 * @author Squirrly
 */
class SQ_Tools extends SQ_FrontController {
    /** @var options Array of options from database */
    public static $options = array();
    public $flash_data = null;
    public $showNote = array();
    static $errors_count = 0;
    private static $debug;


    function __construct() {
        parent::__construct();

        self::$options = $this->getOptions();

        $this->checkDebug(); //Check for debug
    }

    public static function getUserID(){
        global $current_user;
        return $current_user->ID;
    }

    /**
     * Check if debug is called
     */
    private function checkDebug(){
        //if debug is called
        if (self::getIsset('sq_debug')){


            if(self::getValue('sq_debug') == self::$options['sq_api'])
                $_GET['sq_debug'] = 'on';
            elseif(is_admin())
                $_GET['sq_debug'] = 'on';
            else
                $_GET['sq_debug'] = 'off';

            if(self::getValue('sq_debug') === 'on'){
                if (function_exists('register_shutdown_function'))
                    register_shutdown_function(array($this, 'showDebug'));
            }
        }
    }


    /**
    * This hook will save the current version in database and load the messages from usermeta
    *
    * @return void
    */
    function hookInit(){
        global $sq_showNote;

        //TinyMCE editor required
        set_user_setting('editor', 'tinymce');

        $this->showNote = $sq_showNote;
        $this->loadMultilanguage();
        $this->checkPluginUpdated();
        $this->load_flashdata();

        //add setting link in plugin
        add_filter('plugin_action_links', array($this, 'hookActionlink'), 5, 2 );
    }

    /**
     * Add a link to settings in the plugin list
     *
     * @param array $links
     * @param type $file
     * @return array
     */
    public function hookActionlink($links, $file) {
          if ($file == _PLUGIN_NAME_. '/squirrly.php'){
            $link = '<a href="' . admin_url('admin.php?page=squirrly') . '">' . __('Settings', _PLUGIN_NAME_) . '</a>';
            array_unshift($links, $link);
            if(SQ_Tools::$options['sq_howto'] == 1){
              $link = '<a href="' . admin_url('admin.php?page=sq_howto') . '">' . __('Getting started', _PLUGIN_NAME_) . '</a>';
              array_unshift($links, $link);
            }
          }

          return $links;
    }

    /**
    * This hook will output the message to WP header
    *
    * @return void
    */
    function hookNotices(){
        global $pagenow;

        $message = $this->flashdata('plugin_update_notice');

        if($message)
        {
            // keep update message on update and plugins page because they do many redirects,
            // so we never know whether user seen the message or not
            if($pagenow == 'update.php' || ($pagenow == 'plugins.php' && isset($_GET['action'])))
                    $this->keep_flashdata('plugin_update_notice');

            echo $this->showNotices($message);
        }
    }

    /**
    * This hook will save the new# sign notices in the usermeta table in database
    *
    * @return void
    */
    function hookShutdown(){
         global $user_ID;
        $new_data = array();

        if(is_array($this->flash_data)) {
            foreach($this->flash_data as $k => $v) {
                    if(substr($k, 0, 4) == 'new#')
                           $new_data['old#' . substr($k, 4)] = $v;
            }

            update_user_option($user_ID, SQ_META, $new_data, false);
        }

        return;
    }

    /**
    * Load the Options from user option table in DB
    *
    * @return void
    */
    public static function getOptions(){
        $default = array(
            'sq_beginner_user' => 1,
            'sq_api' => '',
            'sq_use' => 1,
            'sq_howto' => 1,
            'sq_auto_canonical' => 1,
            'sq_auto_sitemap' => 1,
            'sq_auto_meta' => 1,
            'sq_auto_favicon' => 1,
            'sq_auto_twitter' => 1,
            'sq_auto_facebook' => 1,
            'sq_twitter_account' => '',

            'sq_auto_seo' => 1,
            'sq_auto_title' => 1,
            'sq_auto_description' => 1,
            'sq_fp_title' => '',
            'sq_fp_description' => '',
            'sq_fp_keywords' => '',

            'sq_google_plus' => '',
            'sq_google_wt' => '',
            'sq_google_analytics' => '',
            'sq_facebook_insights' => '',
            'sq_bing_wt' => '',

            'ignore_warn' => 0,
            'sq_keyword_help' => 1,
            'sq_keyword_information' => 0,
            'sq_advance_user' => 0,
            'sq_affiliate_link' => ''
        );
        $options = json_decode(get_option(SQ_OPTION),true);

        if (is_array($options)){
            $options = @array_merge($default, $options);
            return $options;
        }

        return $default;
    }


    /**
    * Save the Options in user option table in DB
    *
    * @return void
    */
    public static function saveOptions($key, $value){
        self::$options[$key] = $value;
        update_option(SQ_OPTION, json_encode(self::$options));
    }

    /**
     * Set the header type
     * @param type $type
     */
    public static function setHeader($type){
        if (SQ_Tools::getValue('sq_debug') == 'on') return;

        switch ($type){
            case 'json':
                header('Content-Type: application/json');
        }
    }
    /**
    * Get a value from $_POST / $_GET
    * if unavailable, take a default value
    *
    * @param string $key Value key
    * @param mixed $defaultValue (optional)
    * @return mixed Value
    */
    public static function getValue($key, $defaultValue = false){
        if (!isset($key) OR empty($key) OR !is_string($key))
                return false;
        $ret = (isset($_POST[$key]) ? $_POST[$key] : (isset($_GET[$key]) ? $_GET[$key] : $defaultValue));

        if (is_string($ret) === true)
                $ret = urldecode(preg_replace('/((\%5C0+)|(\%00+))/i', '', urlencode($ret)));
        return !is_string($ret)? $ret : stripslashes($ret);
    }

    public static function getIsset($key){
        if (!isset($key) OR empty($key) OR !is_string($key))
                return false;
        return isset($_POST[$key]) ? true : (isset($_GET[$key]) ? true : false);
    }

    /**
    * Show the notices to WP
    *
    * @return void
    */
    public static function showNotices($message, $type = 'sq_notices'){
        if (file_exists(_SQ_THEME_DIR_.'SQ_notices.php')){
            ob_start();
                include (_SQ_THEME_DIR_.'SQ_notices.php');
            $message = ob_get_contents();
            ob_end_clean();
        }

        return $message;
    }

    private function loadMultilanguage(){
        if ( !defined('WP_PLUGIN_DIR') ) {
                load_plugin_textdomain( _PLUGIN_NAME_, _PLUGIN_NAME_ . '/languages/' );
	} else {
		load_plugin_textdomain( _PLUGIN_NAME_, null, _PLUGIN_NAME_ . '/languages/' );

	}

    }

    /**
     * Connect remote with CURL if exists
     */
    public static function sq_remote_get($url, $param = array()){
        $cookies = '';
        $post_preview = false;
        $cookies = array();
        $cookie_string = '';

        $url_domain = parse_url($url);
        $url_domain = $url_domain['host'];


        if (isset($param['timeout']))
            $timeout = $param['timeout'];
        else
            $timeout = 30;

        if ($url_domain == $_SERVER['HTTP_HOST'] && strpos($url,'preview=true') !== false) $post_preview = true;

        if($post_preview){
            foreach ( $_COOKIE as $name => $value ) {

                if (strpos($name,'wordpress')!== false || strpos($name,'wpta')!== false){
                    $cookies[] = new WP_Http_Cookie( array( 'name' => $name, 'value' => $value ) );
                    $cookie_string .= "$name=$value;";
                }
            }
            $cookies[] = new WP_Http_Cookie( array( 'name' => 'sq_snippet', 'value' => 1 ) );
            $cookie_string .= "sq_snippet=1;";
        }

        if (function_exists('curl_init')){
            return self::sq_curl($url, array('timeout' => $timeout, 'cookies' => $cookies, 'cookie_string' => $cookie_string ));
        }else{
            return self::sq_wpcall($url, array('timeout' => $timeout, 'cookies' => $cookies));
        }

    }

    /**
     * Call remote UR with CURL
     * @param string $url
     * @param array $param
     * @return string
     */
    private static function sq_curl($url, $param){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch,CURLOPT_TIMEOUT,$param['timeout']);

        if($param['cookie_string'] <> '')
            curl_setopt($ch, CURLOPT_COOKIE, $param['cookie_string']);

        $response = curl_exec($ch);
        $response = self::cleanResponce($response);

        if(curl_errno($ch) == 1){ //if protocol not supported
            self::dump(curl_getinfo($ch), curl_errno($ch), curl_error($ch));
            $response = self::sq_wpcall($url, $param); //use the wordpress call
        }
        self::dump('CURL', $url, $param, $response);//output debug

        curl_close($ch);
        return $response;
    }

    /**
     * Use the WP remote call
     * @param string $url
     * @param array $param
     * @return string
     */
    private static function sq_wpcall($url, $param){
        $response = wp_remote_get($url, $param);
        $response = self::cleanResponce(wp_remote_retrieve_body($response)); //clear and get the body
        return $response;
    }

    /**
     * Connect remote with CURL if exists
     */
    public static function sq_remote_head($url){
        $response = array();

        if (isset($param['timeout']))
            $timeout = $param['timeout'];
        else
            $timeout = 30;

        if (function_exists('curl_exec')){
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            curl_exec($ch);

            $response['headers']['content-type'] = curl_getinfo($ch, CURLINFO_CONTENT_TYPE );
            $response['response']['code'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            return $response;
        }else{
            return wp_remote_head($url, array('timeout'=>$timeout));
        }

        return false;
    }

    private static function cleanResponce($response){

       if (function_exists('substr_count'))
           if (substr_count($response,'(') > 1) return $response;

       if (strpos($response,'(') !== false && strpos($response,')') !== false)
          $response = substr ($response, (strpos($response,'(') + 1),(strpos($response,')')-1));

       return $response;
   }

    /**
    * checkPluginUpdated
    *
    * Checks whether plugin update happened and triggers update notice
    *
    */
    private function checkPluginUpdated(){
        if (isset(self::$options['version']))
            $saved_version = self::$options['version'];

        // setup current version for new plugin installations
        if(!isset($saved_version) && !isset(self::$options['apiID'])) {
            $this->saveOptions('version', SQ_VERSION);
        }

        // it'll trigger only if different version of plugin was installed before
        if(!isset($saved_version) || version_compare($saved_version, SQ_VERSION, '!='))
        {
            // save new version string to database to avoid event doubling
            $this->saveOptions('version', SQ_VERSION);

            // setup flashdata so admin_notices hook could pick it up next time it will be displayed
            if(isset($this->showNote[SQ_VERSION])){
                $this->set_flashdata('plugin_update_notice', $this->showNote[SQ_VERSION]);

            }
        }
    }

    /**
    * Check for SEO blog bad settings
    */
    public static function checkErrorSettings($count_only = false) {


        if ( function_exists( 'is_network_admin' ) && is_network_admin() )
                return;

        if ( isset( self::$options['ignore_warn'] ) && self::$options[ 'ignore_warn' ] == 1 )
                return;

        $fixit = "<a href=\"javascript:void(0);\"  onclick=\"%s jQuery(this).closest('div').fadeOut('slow'); if(parseInt(jQuery('.sq_count').html())>0) { var notif = (parseInt(jQuery('.sq_count').html()) - 1); if (notif > 0) {jQuery('.sq_count').html(notif); }else{ jQuery('.sq_count').html(notif); jQuery('.sq_count').hide(); } } jQuery.post(ajaxurl, { action: '%s', nonce: '".wp_create_nonce( 'sq_none' )."'});\" >" . __( "Fix it for me!", _PLUGIN_NAME_ ) . "</a>";

        /* IF SEO INDEX IS OFF*/
        if ( self::getAutoSeoSquirrly() ){
            if ($count_only)
                self::$errors_count ++;
            else SQ_Error::setError(__('Let Squirrly optimize your SEO automatically (recommended)', _PLUGIN_NAME_) . " <br />" . sprintf( $fixit, "jQuery('#sq_use_on').attr('checked', true); jQuery('#sq_use_on').attr('checked',true);", "sq_fixautoseo") . " | ", 'settings', 'sq_fix_auto');
        }

        /* IF SEO INDEX IS OFF*/
        if ( self::getPrivateBlog() ){

            if ($count_only)
                self::$errors_count ++;
            else SQ_Error::setError(__('You\'re blocking google from indexing your site!', _PLUGIN_NAME_) . " <br />" . sprintf( $fixit, "jQuery('#sq_google_index1').attr('checked',true);", "sq_fixprivate") . " | ", 'settings','sq_fix_private');
        }

        if ( self::getBadLinkStructure() ){
            if ($count_only)
                self::$errors_count ++;
            else SQ_Error::setError(__('It is highly recommended that you include the %postname% variable in the permalink structure. <br />Go to Settings > Permalinks and add /%postname%/ in Custom Structure', _PLUGIN_NAME_)  . " <br /> ", 'settings');
        }

    }

    /**
     * Check if the blog is in private mode
     * @return bool
     */
    private static function getAutoSeoSquirrly() {
        if(isset(self::$options['sq_use']))
            return ((int)self::$options['sq_use'] == 0 );

        return true;
    }

    /**
     * Check if the blog is in private mode
     * @return bool
     */
    public static function getPrivateBlog() {
        return ((int)get_option( 'blog_public' ) == 0 );
    }

    /**
     * Check if the blog comments is in private mode
     * @return bool
     */
    private static function getCommentsNotification() {
        return ((int)get_option( 'comments_notify' ) == 1 );
    }

    /**
     * Check if the blog has a bad link structure
     * @return bool
     */
    private static function getBadLinkStructure() {
        global $wp_rewrite;
        if(function_exists('apache_get_modules') ){
            //Check if mod_rewrite is installed in apache
            if(!in_array('mod_rewrite',apache_get_modules()))
                return false;
        }

        $home_path = get_home_path();
	$htaccess_file = $home_path.'.htaccess';

        if ((!file_exists($htaccess_file) && is_writable($home_path) && $wp_rewrite->using_mod_rewrite_permalinks()) || is_writable($htaccess_file)) {
                $link = get_option('permalink_structure');
                if ($link == '')
                    return true;
        }
    }

    /**
    * Get flashdata by key and wipes it immidiately
    *
    * @return void
    */
    protected function flashdata($key) {
        if(isset($this->flash_data['new#' . $key]))
            return $this->flash_data['new#' . $key];
        else if(isset($this->flash_data['old#' . $key]))
            return $this->flash_data['old#' . $key];

        return null;
    }

    /**
    * Load flashdata that used to be available once and then wiped
    *
    * @return void
     */
    private function load_flashdata() {
        global $user_ID;

        if (is_array($this->flash_data))
            $this->flash_data = array_merge ($this->flash_data,get_user_option('sq_plugin_flash', $user_ID));
        else
            $this->flash_data = get_user_option(SQ_META, $user_ID);

        if(!is_array($this->flash_data))
                $this->flash_data = array();

        return;
    }


    /**
    * Keep flashdata key till next time
    *
    * @return void
     */
    private function keep_flashdata($key) {
        $val = $this->flashdata($key);

        if(!is_null($val))
                $this->flash_data['new#' . $key] = $val;

        return;
    }

    /**
    * Set flashdata value by key, pass null value to unset flashdata
    *
    * @return void
     */
    private function set_flashdata($key, $value) {
        if(is_null($value)) {
            if(isset($this->flash_data['new#' . $key]))
                    unset($this->flash_data['new#' . $key]);
            if(isset($this->flash_data['old#' . $key]))
                    unset($this->flash_data['old#' . $key]);

            return;
        }

        $this->flash_data['new#' . $key] = $value;

        return;
    }


    /**
    * Support for i18n with wpml, polyglot or qtrans
    *
    * @param string $in
    * @return string $in localized
    */
    public static function i18n($in) {
        if (function_exists('langswitch_filter_langs_with_message')) {
            $in = langswitch_filter_langs_with_message($in);
        }
        if (function_exists('polyglot_filter')) {
            $in = polyglot_filter($in);
        }
        if (function_exists('qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage')) {
            $in = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($in);
        }
        $in = apply_filters('localization', $in);
        return $in;
    }

    /**
     * Convert integer on the locale format.
     *
     * @param int $number The number to convert based on locale.
     * @param int $decimals Precision of the number of decimal places.
     * @return string Converted number in string format.
     */
    public static function i18n_number_format( $number, $decimals = 0 ) {
            global $wp_locale;
            $formatted = number_format( $number, absint( $decimals ), $wp_locale->number_format['decimal_point'], $wp_locale->number_format['thousands_sep'] );
            return apply_filters( 'number_format_i18n', $formatted );
    }


    public static function getBrowserInfo(){
        $u_agent = $_SERVER['HTTP_USER_AGENT'];
        $bname = 'Unknown';
        $platform = 'Unknown';
        $version= "";
        if (!function_exists('preg_match'))
            return false;

        //First get the platform?
        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'linux';
        }
        elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'mac';
        }
        elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'windows';
        }

        // Next get the name of the useragent yes seperately and for good reason
        if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent))
        {
            $bname = 'IE';
            $ub = "MSIE";
        }
        elseif(preg_match('/Firefox/i',$u_agent))
        {
            $bname = 'Mozilla Firefox';
            $ub = "Firefox";
        }
        elseif(preg_match('/Chrome/i',$u_agent))
        {
            $bname = 'Google Chrome';
            $ub = "Chrome";
        }
        elseif(preg_match('/Safari/i',$u_agent))
        {
            $bname = 'Apple Safari';
            $ub = "Safari";
        }
        elseif(preg_match('/Opera/i',$u_agent))
        {
            $bname = 'Opera';
            $ub = "Opera";
        }
        elseif(preg_match('/Netscape/i',$u_agent))
        {
            $bname = 'Netscape';
            $ub = "Netscape";
        }

        // finally get the correct version number
        $known = array('Version', $ub, 'other');
        $pattern = '#(?' . join('|', $known) .')[/ ]+(?[0-9.|a-zA-Z.]*)#';

        if (strpos($u_agent, 'MSIE 7.0;') !== false){
                $version = 7.0;
        }

        if ($version==null || $version=="") {
            $version="0";
        }

        return array(
            'userAgent' => $u_agent,
            'name'      => $bname,
            'version'   => $version,
            'platform'  => $platform,
            'pattern'    => $pattern
        );
    }
    /**
     *
     * @param string $url
     * @return array
     */
    public static function getSnippet($url){
        if ($url == '' || !function_exists('preg_match')) return;
        $snippet = array();
        $length = array('title' => 66,
                        'description' => 240,
                        'url' => 45);

        $content = self::sq_remote_get($url,array('timeout' => 10));

        $title_regex = "/<title[^>]*>([^<>]*)<\/title>/si";
        preg_match($title_regex, $content, $title);

        if (is_array($title) && count($title) > 0){
            $snippet['title'] = $title[1];
            $snippet['title'] = self::i18n(trim(strip_tags($snippet['title'])));
        }

        $description_regex = '/<meta[^<>]*description[^<>]*content="([^"<>]+)"[^<>]*>/si';
        preg_match($description_regex, $content, $description);
        if (is_array($description) && count($description) > 0){
            $snippet['description'] = trim(strip_tags(htmlspecialchars($description[1])));

            if (strlen($snippet['description']) > $length['description'])
                $snippet['description'] = substr($snippet['description'], 0, ($length['description'] -1) ). '...';
        }

        $snippet['url'] = $url;
        if (strlen($snippet['url']) > $length['url'])
                $snippet['url'] = substr($snippet['url'], 0, ($length['url'] -1) ) . '...';

        return $snippet;

    }

    /**
     * Store the debug for a later view
     */
    public static function dump(){
        $output = '';
        $callee = array('file' => '', 'line' => '');
        if (function_exists('func_get_args')){
            $arguments = func_get_args();
            $total_arguments = count( $arguments );
        }else
            $arguments = array();



        if (function_exists('debug_backtrace'))
            list( $callee ) 	= debug_backtrace();

        $output .= '<fieldset style="background: #FFFFFF; border: 1px #CCCCCC solid; padding: 5px; font-size: 9pt; margin: 0;">';
        $output .= '<legend style="background: #EEEEEE; padding: 2px; font-size: 8pt;">' . $callee['file'] . ' @ line: ' . $callee['line']
                . '</legend><pre style="margin: 0; font-size: 8pt; text-align: left;">';

        $i = 0;
        foreach ( $arguments as $argument )
        {
                if ( count( $arguments ) > 1 ) $output .= "\n" . '<strong>#' . ( ++$i ) . ' of ' . $total_arguments . '</strong>: ';

                // if argument is boolean, false value does not display, so ...
                if ( is_bool( $argument ) ) $argument = ( $argument ) ? 'TRUE' : 'FALSE';
                else
                   if ( is_object( $argument ) && function_exists('array_reverse') && function_exists('class_parents'))
                        $output .= implode( "\n" . '|' . "\n", array_reverse( class_parents( $argument ) ) ) . "\n" . '|' . "\n";

                $output .= htmlspecialchars( print_r( $argument, TRUE ) )
                .( ( is_object( $argument ) && function_exists('spl_object_hash') ) ? spl_object_hash( $argument ) : '' );
        }
        $output .= "</pre>";
    	$output .= "</fieldset>";

        self::$debug[] = $output;
    }

    /**
     * Show the debug dump
     */
    public static function showDebug(){
            echo "Debug result: <br />".@implode( '<br />', self::$debug );
    }
}

?>
