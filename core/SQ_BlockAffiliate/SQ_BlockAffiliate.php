<?php

/**
 * Affiliate settings
 */
class SQ_BlockAffiliate extends SQ_BlockController {

    function action() {
        parent::action();
        switch (SQ_Tools::getValue('action')) {
            //login action
            case 'sq_settings_affiliate':
                //post arguments
                $args = array();
                //set return to null object as default
                $return = (object) NULL;
                //api responce variable
                $responce = '';

                $responce = SQ_Action::apiCall('sq/user/affiliate', $args);

                //create an object from json responce
                if (is_object(json_decode($responce)))
                    $return = json_decode($responce);

                //add the responce in msg for debugging in case of error
                $return->msg = $responce;

                //if the affiliate link is received
                if (isset($return->affiliate_link)) {
                    SQ_Tools::saveOptions('sq_affiliate_link', $return->affiliate_link);
                } elseif (!empty($return->error)) {
                    //if an error is throw then ...
                    $return->info = sprintf(__('Error: %s', _PLUGIN_NAME_), $return->error);
                } else {
                    //if unknown error
                    $return->error = __('An error occured. Mabe a network error :(', _PLUGIN_NAME_);
                }
                break;
        }
    }

    function hookGetContent() {
        $this->options = SQ_Tools::$options;
    }

}

?>