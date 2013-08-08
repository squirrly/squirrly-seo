<?php

$currentDir = dirname(__FILE__);

define('_SQ_NAME_', 'squirrly');
define('_PLUGIN_NAME_', 'squirrly-seo'); //THIS LINE WILL BE CHANGED WITH THE USER SETTINGS
define('_THEME_NAME_', 'default'); //THIS LINE WILL BE CHANGED WITH THE USER SETTINGS

define('_SQ_DASH_URL_', 'https://my.squirrly.co/');
defined('_SQ_API_URL_') ||
        define('_SQ_API_URL_', ((strpos(get_bloginfo('wpurl'), 'https') !== false) ? 'https:' : 'http:') . '//api.squirrly.co/');
define('_SQ_STATIC_API_URL_', ((strpos(get_bloginfo('wpurl'), 'https') !== false) ? 'https:' : 'http:') . '//api.squirrly.co/static/');
defined('_SQ_SUPPORT_URL_') ||
        define('_SQ_SUPPORT_URL_', 'https://www.facebook.com/Squirrly.co');

if (!defined('SQ_URI'))
    if (WP_VERSION_ID >= 3000)
        define('SQ_URI', 'wp350');
    else
        define('SQ_URI', 'wp2');

/* Directories */
define('_SQ_ROOT_DIR_', realpath($currentDir . '/..'));
define('_SQ_CACHE_DIR_', _SQ_ROOT_DIR_ . '/cache/');
define('_SQ_CLASSES_DIR_', _SQ_ROOT_DIR_ . '/classes/');
define('_SQ_CONTROLLER_DIR_', _SQ_ROOT_DIR_ . '/controllers/');
define('_SQ_MODEL_DIR_', _SQ_ROOT_DIR_ . '/models/');
define('_SQ_TRANSLATIONS_DIR_', _SQ_ROOT_DIR_ . '/translations/');
define('_SQ_CORE_DIR_', _SQ_ROOT_DIR_ . '/core/');
define('_SQ_ALL_THEMES_DIR_', _SQ_ROOT_DIR_ . '/themes/');
define('_SQ_THEME_DIR_', _SQ_ROOT_DIR_ . '/themes/' . _THEME_NAME_ . '/');

/* URLS */
define('_SQ_URL_', WP_PLUGIN_URL . '/' . _PLUGIN_NAME_);
define('_SQ_CACHE_URL_', _SQ_URL_ . '/cache/');
define('_SQ_ALL_THEMES_URL_', _SQ_URL_ . '/themes/');
define('_SQ_THEME_URL_', _SQ_URL_ . '/themes/' . _THEME_NAME_ . '/');
?>