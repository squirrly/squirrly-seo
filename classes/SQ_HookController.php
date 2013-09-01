<?php

/**
 * The class handles the actions in WP
 */
class SQ_HookController {

    /** @var array the WP actions list from admin */
    private $admin_hooks = array();
    private $custom_hooks = array();
    private $block_hooks = array();

    public function __construct() {
        $this->admin_hooks = array('init' => 'admin_init',
            'head' => 'admin_head',
            'footer' => 'admin_footer',
            'wmenu' => '_admin_menu',
            'menu' => 'admin_menu',
            'submenu' => 'add_submenu_page',
            'loaded' => 'plugins_loaded',
            'xml' => 'admin_xml_ns',
            'scripts' => 'admin_enqueue_scripts',
            'notices' => 'admin_notices',
            'shutdown' => 'shutdown',
            'fronttitletheme' => 'thematic_doctitle',
            'frontinit' => 'init',
            'fronthead' => 'wp_head',
            'frontfooter' => 'wp_footer',
            'frontcontent' => 'the_content',
        );
        $this->custom_hooks = array();
        $this->block_hooks = array('getContent' => 'getContent');
    }

    /**
     * Calls the specified action in WP
     * @param oject $instance The parent class instance
     *
     * @return void
     */
    public function setAdminHooks($instance) {

        /* for each admin action check if is defined in class and call it */
        foreach ($this->admin_hooks as $hook => $value) {

            if (is_callable(array($instance, 'hook' . ucfirst($hook)))) {
                //echo $value . '<br>';
                //print_r(array($instance, 'hook'.ucfirst($hook)));
                //call the WP add_action function
                add_action($value, array($instance, 'hook' . ucfirst($hook)), 5);
            }
        }

        /* for each custom action check if is defined in class and call it */
        foreach ($this->custom_hooks as $hook => $value) {

            if (is_callable(array($instance, 'hook' . ucfirst($hook)))) {
                //call the controller custom hook function

                call_user_func(array($instance, 'hook' . ucfirst($hook)));
            }
        }
    }

    /**
     * Calls the specified action in WP
     * @param string $action
     * @param array $callback Contains the class name or object and the callback function
     *
     * @return void
     */
    public function setAction($action, $obj, $callback) {

        /* calls the custom action function from WP */
        add_action($action, array($obj, $callback), 10);
    }

    /**
     * Calls the specified action in WP
     * @param oject $instance The parent class instance
     *
     * @return void
     */
    public function setBlockHooks($instance) {
        $param_arr = array();

        /* for each admin action check if is defined in class and call it */
        foreach ($this->block_hooks as $hook => $value)
            if (is_callable(array($instance, 'hook' . ucfirst($hook))))
                call_user_func_array(array($instance, 'hook' . ucfirst($hook)), $param_arr);
    }

}

?>