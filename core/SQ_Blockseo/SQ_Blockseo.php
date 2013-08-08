<?php

class SQ_Blockseo extends SQ_BlockController {

    function action() {

    }

    function hookHead() {
        parent::hookHead();
        $metas = array();
        $metas = $this->model->getAdvSeo();

        echo '<script type="text/javascript">
             var __snippetsavechanges = "' . __('Save changes', _PLUGIN_NAME_) . '";
             var __snippetsavecancel = "' . __('Cancel', _PLUGIN_NAME_) . '";
             var __snippetreset = "' . __('Reset', _PLUGIN_NAME_) . '";

             var __snippetcustomize = "' . __('Customize Title', _PLUGIN_NAME_) . '";
             var __snippetkeyword = "' . __('manage keywords', _PLUGIN_NAME_) . '";
             var __snippetshort = "' . __('Too short', _PLUGIN_NAME_) . '";
             var __snippetlong = "' . __('Too long', _PLUGIN_NAME_) . '";

             var __snippetname = "' . __('Squirrly Snippet', _PLUGIN_NAME_) . '";
             var __snippetrefresh = "' . __('Update', _PLUGIN_NAME_) . '";
             var __snippetclickrefresh = "' . __('Click the Update button (to the right) to see the snippet from your website.', _PLUGIN_NAME_) . '";
             var __snippetentertitle = "' . __('Enter a title above for the snippet to get data.', _PLUGIN_NAME_) . '";' . "\n";

        if (is_array($metas))
            foreach ($metas as $key => $meta) {
                echo 'var __' . $key . ' = "' . $meta . '";' . "\n";
            }

        echo '</script>';
    }

}

?>