<?php

/**
 * Called on plugin uninstall
 */
if (!defined('ABSPATH') && !defined('WP_UNINSTALL_PLUGIN'))
    exit();

/* Call config files */
require(dirname(__FILE__) . '/config/config.php');

/* Delete the record from database */
delete_option(SQ_OPTION);
delete_user_option(SQ_META);

