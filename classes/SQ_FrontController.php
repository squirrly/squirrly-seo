<?php

/**
 * The main class for controllers
 *
 */
class SQ_FrontController {

    /** @var object of the model class */
    public $model;

    /** @var boolean */
    private $flush = true;

    /** @var object of the view class */
    public $view;

    /** @var name of the  class */
    private $name;

    public function __construct() {

        /* get the name of the current class */
        $this->name = get_class($this);


        /* load the model and hooks here for wordpress actions to take efect */
        /* create the model and view instances */
        $this->model = SQ_ObjController::getModel($this->name);

        //IMPORTANT TO LOAD HOOKS HERE
        /* check if there is a hook defined in the controller clients class */
        SQ_ObjController::getController('SQ_HookController', false)
                ->setAdminHooks($this);
    }

    /**
     * load sequence of classes
     * Function called usualy when the controller is loaded in WP
     *
     * @return void
     */
    public function init() {

        $this->view = SQ_ObjController::getController('SQ_DisplayController', false);

        if ($this->flush)
            $this->output();

        /* load the blocks for this controller */
        SQ_ObjController::getController('SQ_ObjController', false)->getBlocks($this->name);
    }

    protected function output() {
        /* view is called from theme directory with the class name by defauls */
        $this->view->output($this->name, $this);
    }

    /**
     * initialize settings
     * Called from index
     *
     * @return void
     */
    public function run() {
        /** check the admin condition */
        if (!is_admin())
            return;

        /* Load error class */
        SQ_ObjController::getController('SQ_Error', false);
        /* Load Tools */
        SQ_ObjController::getController('SQ_Tools', false);



        /* Load the Submit Actions Handler */
        SQ_ObjController::getController('SQ_Action', false);

        SQ_ObjController::getController('SQ_DisplayController', false);

        /* show the admin menu and post actions */
        $this->loadMenu();

        /* Load the Sitemap Generator */
        SQ_ObjController::getController('SQ_Sitemap', false);
    }

    /**
     * initialize menu
     *
     * @return void
     */
    private function loadMenu() {
        /* get the menu from controller */
        SQ_ObjController::getController('SQ_Menu');
    }

    /**
     * first function call for any class
     *
     */
    protected function action() {
        // check to see if the submitted nonce matches with the
        // generated nonce we created
        if (class_exists('wp_verify_nonce'))
            if (!wp_verify_nonce(SQ_Tools::getValue(_SQ_NONCE_ID_), _SQ_NONCE_ID_))
                die('Invalid request!');
    }

    /**
     * This function will load the media in the header for each class
     *
     * @return void
     */
    public function hookHead() {
        if (is_admin())
            SQ_ObjController::getController('SQ_DisplayController', false)->init();

        SQ_ObjController::getController('SQ_DisplayController', false)
                ->loadMedia($this->name);
    }

}

?>