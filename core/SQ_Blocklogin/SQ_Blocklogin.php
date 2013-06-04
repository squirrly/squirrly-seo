<?php
class SQ_Blocklogin extends SQ_BlockController {

    function init() {
        /* If logged in then return */
        if (SQ_Tools::$options['sq_api'] <> '') return;
        parent::init();
    }

    /**
     * Called for sq_login
     * Login or register a user
     */
    function action(){
        parent::action();
        switch (SQ_Tools::getValue('action')){
            case 'sq_login':
                $this->squirrlyLogin();
                break;
            case 'sq_register':
                $this->squirrlyRegister();
                break;
            case 'sq_reset':
                SQ_Tools::saveOptions('sq_api', '');
                $return = array();
                $return['reset'] = 'success';

                SQ_Tools::setHeader('json');
                echo json_encode($return);
                exit();
                break;
        }
    }

    /**
     * Register a new user to Squirrly and get the token
     * @global string $current_user
     */
    function squirrlyRegister(){
        global $current_user;

        if (SQ_Tools::getValue('email') <> ''){
            $args['name'] = '';
            $args['user'] = SQ_Tools::getValue('email');
            $args['email'] = SQ_Tools::getValue('email');
        }

        if($args['email'] <> '' ){

            $responce = SQ_Action::apiCall('sq/register',$args);
            $return = json_decode($responce);

            if (!is_object($return))
               $return = (object) NULL;

            $return->msg = $responce;

            if (isset($return->token)){
                SQ_Tools::saveOptions('sq_api', $return->token);

            }elseif(!empty($return->error)){
                switch ($return->error){
                    case 'alreadyregistered':
                        $return->info = sprintf(__('We found your email, so it means you already have a Squirrly.co account. Please login with your Squirrly Email. If you forgot your password click %shere%s',_PLUGIN_NAME_),'<a href="'._SQ_DASH_URL_ .'login/?action=lostpassword" target="_blank">','</a>');
                        break;
                }
            }else{
                $return->error = __('An error occured. Mabe a network error :(',_PLUGIN_NAME_);

            }
        }else
            $return->error = sprintf(__('Could not send your informations to squirrly. Please register %smanually%s.',_PLUGIN_NAME_),'<a href="'._SQ_DASH_URL_ .'login/?action=register" target="_blank">','</a>');

        SQ_Tools::setHeader('json');
        echo json_encode($return);
        exit();
    }

    /**
     * Login a user to Squirrly and get the token
     */
    function squirrlyLogin(){

        $args['user'] = SQ_Tools::getValue('user');
        $args['password'] = SQ_Tools::getValue('password');
        if($args['user'] <> '' && $args['password'] <> ''){
            /*if(function_exists('mcrypt_create_iv') && function_exists('mcrypt_encrypt') && function_exists('hash')){
                $args['password'] = $this->sq_crypt($args['user'], $args['password']);
            }else {
                $args['encrypted'] = '0';
            }*/
            $args['encrypted'] = '0';
            $responce = SQ_Action::apiCall('sq/login',$args);
            $return = json_decode($responce);
            $return->msg = $responce;

            if (isset($return->token)){
                SQ_Tools::saveOptions('sq_api', $return->token);

            }elseif(!empty($return->error)){
                switch ($return->error){
                    case 'badlogin':
                        $return->error = __('Wrong email or password!',_PLUGIN_NAME_);
                        break;
                    case 'multisite':
                        $return->error = __('You can use this account only for the URL you registered first!',_PLUGIN_NAME_);
                        break;
                }
            }else
                $return->error = __('An error occured.',_PLUGIN_NAME_);
        }else
            $return->error = __('Both fields are required.',_PLUGIN_NAME_);

        SQ_Tools::setHeader('json');
        echo json_encode($return);
        exit();
    }

    /**
     * Scrypt the password
     *
     * @param string $user
     * @param string $password
     * @return string
     */
    private function sq_crypt($user, $password){

        $iv = mcrypt_create_iv(
            mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC),
            MCRYPT_DEV_URANDOM
        );

        $encrypted = base64_encode(
            $iv .
            mcrypt_encrypt(
                MCRYPT_RIJNDAEL_256,
                hash('sha256', $user, true),
                $password,
                MCRYPT_MODE_CBC,
                $iv
            )
        );
        //echo $encrypted ."\n";
        return $encrypted;
    }
}
?>