<?php

class SQ_BlockSupport extends SQ_BlockController {

    /**
     * Called when Post action is triggered
     *
     * @return void
     */
    public function action() {
        parent::action();

        switch (SQ_Tools::getValue('action')) {
            case 'sq_feedback':
                global $current_user;
                $return = array();

                SQ_Tools::saveOptions('sq_feedback', 1);

                $line = "\n" . "________________________________________" . "\n";
                $from = $current_user->user_email;
                $subject = __('Plugin Feedback', _SQD_PLUGIN_NAME_);
                $face = SQ_Tools::getValue('feedback');
                $message = SQ_Tools::getValue('message');

                if ($message <> '' || (int) $face > 0) {
                    switch ($face) {
                        case 1:
                            $face = 'Angry';
                            break;
                        case 2:
                            $face = 'Sad';
                            break;
                        case 3:
                            $face = 'Happy';
                            break;
                        case 4:
                            $face = 'Excited';
                            break;
                        case 5:
                            $face = 'Love it';
                            break;
                    }
                    if ($message <> '')
                        $message = $message . $line;

                    if ($face <> '') {
                        $message .= 'Url:' . get_bloginfo('wpurl') . "\n";
                        $message .= 'Face:' . $face;
                    }



                    $headers[] = 'From: ' . $current_user->display_name . ' <' . $from . '>';

                    //$this->error='buuum';
                    if (wp_mail(_SQ_SUPPORT_EMAIL_, $subject, $message, $headers))
                        $return['message'] = __('Thank you for your feedback', _PLUGIN_NAME_);
                    else {
                        $return['message'] = __('Could not send the email...', _PLUGIN_NAME_);
                    }
                } else {
                    $return['message'] = __('No message.', _SQD_PLUGIN_NAME_);
                }

                SQ_Tools::setHeader('json');
                echo json_encode($return);
                break;

            case 'sq_support':
                global $current_user;
                $return = array();
                $versions = '';

                $versions .= 'Url:' . get_bloginfo('wpurl') . "\n";
                $versions .= 'Squirrly version: ' . SQ_VERSION_ID . "\n";
                $versions .= 'Wordpress version: ' . WP_VERSION_ID . "\n";
                $versions .= 'PHP version: ' . PHP_VERSION_ID . "\n";

                $line = "\n" . "________________________________________" . "\n";
                $from = $current_user->user_email;
                $subject = __('Plugin Support', _SQD_PLUGIN_NAME_);
                $message = SQ_Tools::getValue('message');

                if ($message <> '') {
                    $message .= $line;
                    $message .= $versions;

                    $headers[] = 'From: ' . $current_user->display_name . ' <' . $from . '>';

                    //$this->error='buuum';
                    if (wp_mail(_SQ_SUPPORT_EMAIL_, $subject, $message, $headers))
                        $return['message'] = __('Message sent...', _PLUGIN_NAME_);
                    else {
                        $return['message'] = __('Could not send the email...', _PLUGIN_NAME_);
                    }
                } else {
                    $return['message'] = __('No message.', _SQD_PLUGIN_NAME_);
                }

                header('Content-Type: application/json');
                echo json_encode($return);
                break;
        }
        exit();
    }

}

?>