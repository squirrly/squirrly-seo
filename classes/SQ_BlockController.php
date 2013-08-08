<?php

/**
 * The main class for core blocks
 *
 */
class SQ_BlockController {

    /** @var object of the model class */
    protected $model;

    /** @var boolean */
    private $flush = true;

    /** @var object of the view class */
    protected $view;

    /** @var name of the  class */
    private $name;

    public function __construct() {
        /** check the admin condition */
        if (!is_admin())
            return;

        /* get the name of the current class */
        $this->name = get_class($this);

        /* create the model and view instances */
        $this->model = SQ_ObjController::getModel($this->name);
    }

    /**
     * load sequence of classes
     *
     * @return void
     */
    public function init() {

        $this->view = SQ_ObjController::getController('SQ_DisplayController', false);

        /* check if there is a hook defined in the block class */
        SQ_ObjController::getController('SQ_HookController', false)
                ->setBlockHooks($this);

        if ($this->flush)
            $this->output();
    }

    protected function output() {
        $this->hookHead();

        /* view is called from theme directory with the class name by default */
        $this->view->output($this->name, $this);
    }

    /**
     * This function is called from Ajax class as a wp_ajax_action
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
    protected function hookHead() {

        SQ_ObjController::getController('SQ_DisplayController', false)
                ->loadMedia($this->name);
    }

    /** @todo _ GASESTE O CALE SA INCARC CSS PENTRU BLOCURI */
}

?>