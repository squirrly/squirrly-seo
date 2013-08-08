<?php

/**
 * The class handles the theme part in WP
 */
class SQ_DisplayController {

    private static $name;
    private static $cache;

    public function init() {
        /* Load the global CSS file */
        self::loadMedia('sq_global');
    }

    /**
     * echo the css link from theme css directory
     *
     * @param string $uri The name of the css file or the entire uri path of the css file
     * @param string $media
     *
     * @return string
     */
    public static function loadMedia($uri = '', $media = 'all', $params = null) {
        $css_uri = '';
        $js_uri = '';

        if (isset(self::$cache[$uri]))
            return;
        self::$cache[$uri] = true;

        /* if is a custom css file */
        if (strpos($uri, '/') === false) {
            if (file_exists(_SQ_THEME_DIR_ . 'css/' . strtolower($uri) . '.css')) {
                $css_uri = _SQ_THEME_URL_ . 'css/' . strtolower($uri) . '.css?ver=' . SQ_VERSION_ID;
            }
            if (file_exists(_SQ_THEME_DIR_ . 'js/' . strtolower($uri) . '.js')) {
                $js_uri = _SQ_THEME_URL_ . 'js/' . strtolower($uri) . '.js?ver=' . SQ_VERSION_ID;
            }
        } else {

            if (strpos($uri, '.css') !== FALSE)
                $css_uri = $uri;
            elseif (strpos($uri, '.js') !== FALSE) {
                $js_uri = $uri;
            }
        }



        if ($css_uri <> '')
            echo "<link rel='stylesheet' id='sq_menu.css-css'  href='" . $css_uri . "' type='text/css' media='all' />" . "\n";

        if ($js_uri <> '')
            echo '<script type="text/javascript" src="' . $js_uri . '">' . (isset($params) ? $params : '') . '</script>' . "\n";

        //wp_enqueue_style(basename($css_uri), $css_uri,null, SQ_VERSION);
    }

    /**
     * Called for any class to show the block content
     *
     * @param string $block the name of the block file in theme directory (class name by default)
     *
     * @return string of the current class view
     */
    public function output($block, $obj) {
        self::$name = $block;
        echo $this->echoBlock($obj);
    }

    /**
     * echo the block content from theme directory
     *
     * @return string
     */
    public static function echoBlock($view) {
        global $post_ID;
        if (file_exists(_SQ_THEME_DIR_ . self::$name . '.php')) {
            ob_start();

            /* includes the block from theme directory */
            include(_SQ_THEME_DIR_ . self::$name . '.php');
            $block_content = ob_get_contents();
            ob_end_clean();

            return $block_content;
        }
    }

}

?>