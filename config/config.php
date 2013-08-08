<?php

/**
 * The configuration file
 */
define('_SQ_SUPPORT_EMAIL_', 'support@squirrly.co');
define('_SQ_NONCE_ID_', 'sq_none');

if (!defined('PHP_VERSION_ID')) {
    $version = explode('.', PHP_VERSION);
    define('PHP_VERSION_ID', ((int) @$version[0] * 1000 + (int) @$version[1] * 100 + ((isset($version[2])) ? ((int) $version[2] * 10) : 0)));
}
if (!defined('WP_VERSION_ID') && isset($wp_version)) {
    $version = explode('.', $wp_version);
    define('WP_VERSION_ID', ((int) @$version[0] * 1000 + (int) @$version[1] * 100 + ((isset($version[2])) ? ((int) $version[2] * 10) : 0)));
}
if (!defined('WP_VERSION_ID'))
    define('WP_VERSION_ID', '3000');

if (!defined('SQ_VERSION_ID')) {
    $version = explode('.', SQ_VERSION);
    define('SQ_VERSION_ID', ((int) @$version[0] * 1000 + (int) @$version[1] * 100 + ((isset($version[2])) ? ((int) $version[2] * 10) : 0)));
}

/* No path file? error ... */
require_once(dirname(__FILE__) . '/paths.php');

/* Define the record name in the Option and UserMeta tables */
define('SQ_OPTION', 'sq_options');
define('SQ_META', 'sq_plugin_flash');
?>