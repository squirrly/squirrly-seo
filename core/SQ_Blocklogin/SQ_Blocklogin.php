<?php

class SQ_Blocklogin extends SQ_BlockController {

    function init() {
        /* If logged in, then return */
        if (SQ_Tools::$options['sq_api'] <> '')
            return;
        parent::init();
    }

    /**
     * Called for sq_login on Post action
     * Login or register a user
     */
    function action() {
        parent::action();
        switch (SQ_Tools::getValue('action')) {
            //login action
            case 'sq_login':
                $this->squirrlyLogin();
                break;

            //sign-up action
            case 'sq_register':
                $this->squirrlyRegister();
                break;

            //reset the token action
            case 'sq_reset':
                SQ_Tools::saveOptions('sq_api', '');
                $return = array();
                $return['reset'] = 'success';

                //Set the header for json reply
                SQ_Tools::setHeader('json');
                echo json_encode($return);
                //force exit
                exit();
        }
    }

    /**
     * Register a new user to Squirrly and get the token
     * @global string $current_user
     */
    function squirrlyRegister() {
        global $current_user;
        //set return to null object as default
        $return = (object) NULL;
        //api responce variable
        $responce = '';
        //post arguments
        $args = array();

        //Check if email is set
        if (SQ_Tools::getValue('email') <> '') {
            $args['name'] = '';
            $args['user'] = SQ_Tools::getValue('email');
            $args['email'] = SQ_Tools::getValue('email');
        }

        //if email is set
        if ($args['email'] <> '') {
            $responce = SQ_Action::apiCall('sq/register', $args);

            //create an object from json responce
            if (is_object(json_decode($responce)))
                $return = json_decode($responce);

            //add the responce in msg for debugging in case of error
            $return->msg = $responce;

            //check if token is set and save it
            if (isset($return->token)) {
                SQ_Tools::saveOptions('sq_api', $return->token);
            } elseif (!empty($return->error)) {
                //if an error is throw then ...
                switch ($return->error) {
                    case 'alreadyregistered':
                        $return->info = sprintf(__('We found your email, so it means you already have a Squirrly.co account. Please login with your Squirrly Email. If you forgot your password click %shere%s', _PLUGIN_NAME_), '<a href="' . _SQ_DASH_URL_ . 'login/?action=lostpassword" target="_blank">', '</a>');
                        break;
                }
            } else {
                //if unknown error
                $return->error = __('An error occured. Mabe a network error :(', _PLUGIN_NAME_);
            }
        }
        else
            $return->error = sprintf(__('Could not send your informations to squirrly. Please register %smanually%s.', _PLUGIN_NAME_), '<a href="' . _SQ_DASH_URL_ . 'login/?action=register" target="_blank">', '</a>');

        //Set the header to json
        SQ_Tools::setHeader('json');
        echo json_encode($return); //transform object in json and show it

        exit();
    }

    /**
     * Login a user to Squirrly and get the token
     */
    function squirrlyLogin() {
        //set return to null object as default
        $return = (object) NULL;
        //api responce variable
        $responce = '';

        //get the user and password
        $args['user'] = SQ_Tools::getValue('user');
        $args['password'] = SQ_Tools::getValue('password');
        if ($args['user'] <> '' && $args['password'] <> '') {
            $args['encrypted'] = '0';
            //get the responce from server on login call
            $responce = SQ_Action::apiCall('sq/login', $args);

            //create an object from json responce
            if (is_object(json_decode($responce)))
                $return = json_decode($responce);

            //add the responce in msg for debugging in case of error
            $return->msg = $responce;

            //check if token is set and save it
            if (isset($return->token)) {
                SQ_Tools::saveOptions('sq_api', $return->token);
            } elseif (!empty($return->error)) {
                //if an error is throw then ...
                switch ($return->error) {
                    case 'badlogin':
                        $return->error = __('Wrong email or password!', _PLUGIN_NAME_);
                        break;
                    case 'multisite':
                        $return->error = __('You can use this account only for the URL you registered first!', _PLUGIN_NAME_);
                        break;
                }
            }
            else
            //if unknown error
                $return->error = __('An error occured.', _PLUGIN_NAME_);
        }
        else
            $return->error = __('Both fields are required.', _PLUGIN_NAME_);

        //Set the header to json
        SQ_Tools::setHeader('json');
        echo json_encode($return);

        exit();
    }

}

?>