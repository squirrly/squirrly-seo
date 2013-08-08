<?php

class SQ_Error extends SQ_FrontController {

    /** @var array */
    private static $errors, $switch_off;

    /**
     * The error controller for Squirrly
     */
    function __construct() {
        parent::__construct();

        /* Verify dependences */
        if (!function_exists('get_class')) {
            self::setError(__('Function get_class does not exists! Is required for Squirrly to work properly.', _PLUGIN_NAME_));
        }
        if (!function_exists('file_exists')) {
            self::setError(__('Function file_exists does not exists! Is required for Squirrly to work properly.', _PLUGIN_NAME_));
        }

        if (!defined('ABSPATH'))
            self::setError(__('The home directory is not set!', _PLUGIN_NAME_), 'fatal');

        /* Check the PHP version */
        if (PHP_VERSION_ID < 5000) {
            self::setError(__('The PHP version has to be greater then 4.0', _PLUGIN_NAME_), 'fatal');
        }
    }

    /**
     * Show the error in wrodpress
     *
     * @param string $error
     * @param boolean $stop
     *
     * @return void;
     */
    public static function setError($error = '', $type = 'notice', $id = '') {
        self::$errors[] = array('id' => $id,
            'type' => $type,
            'text' => $error);
    }

    /**
     * This hook will show the error in WP header
     */
    function hookNotices() {
        if (is_array(self::$errors))
            foreach (self::$errors as $error) {

                switch ($error['type']) {
                    case 'fatal':
                        self::showError(ucfirst(_PLUGIN_NAME_ . " " . $error['type']) . ': ' . $error['text'], $error['id']);
                        die();
                        break;
                    case 'settings':
                        if (isset(SQ_Tools::$options['ignore_warn']) && SQ_Tools::$options['ignore_warn'] == 1)
                            break;

                        /* switch off option for notifications */
                        self::$switch_off = "<a href=\"javascript:void(0);\" onclick=\"jQuery.post( ajaxurl, {action: 'sq_warnings_off', nonce: '" . wp_create_nonce('sq_none') . "'}, function(data) { if (data) { jQuery('#sq_ignore_warn').attr('checked', true); jQuery('.sq_message').hide(); jQuery('#toplevel_page_squirrly .awaiting-mod').fadeOut('slow'); } });\" >" . __("Turn off warnings!", _PLUGIN_NAME_) . "</a>";
                        self::showError(ucfirst(_PLUGIN_NAME_) . " " . __('Notice: ', _PLUGIN_NAME_) . $error['text'] . " " . self::$switch_off, $error['id']);
                        break;
                    default:

                        self::showError(ucfirst(_PLUGIN_NAME_) . " " . __('Note: ', _PLUGIN_NAME_) . $error['text'], $error['id']);
                }
            }
        self::$errors = array();
    }

    /**
     * Show the notices to WP
     *
     * @return void
     */
    public static function showError($message, $id = '') {
        $type = 'sq_error';

        if (file_exists(_SQ_THEME_DIR_ . 'SQ_notices.php')) {
            include (_SQ_THEME_DIR_ . 'SQ_notices.php');
        } else {
            echo $message;
        }
    }

}

?>