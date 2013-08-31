<?php

class Model_SQ_Menu {

    /** @var array with the menu content
     *
     * $page_title (string) (required) The text to be displayed in the title tags of the page when the menu is selected
     * $menu_title (string) (required) The on-screen name text for the menu
     * $capability (string) (required) The capability required for this menu to be displayed to the user. User levels are deprecated and should not be used here!
     * $menu_slug (string) (required) The slug name to refer to this menu by (should be unique for this menu). Prior to Version 3.0 this was called the file (or handle) parameter. If the function parameter is omitted, the menu_slug should be the PHP file that handles the display of the menu page content.
     * $function The function that displays the page content for the menu page. Technically, the function parameter is optional, but if it is not supplied, then WordPress will basically assume that including the PHP file will generate the administration screen, without calling a function. Most plugin authors choose to put the page-generating code in a function within their main plugin file.:In the event that the function parameter is specified, it is possible to use any string for the file parameter. This allows usage of pages such as ?page=my_super_plugin_page instead of ?page=my-super-plugin/admin-options.php.
     * $icon_url (string) (optional) The url to the icon to be used for this menu. This parameter is optional. Icons should be fairly small, around 16 x 16 pixels for best results. You can use the plugin_dir_url( __FILE__ ) function to get the URL of your plugin directory and then add the image filename to it. You can set $icon_url to "div" to have wordpress generate <br> tag instead of <img>. This can be used for more advanced formating via CSS, such as changing icon on hover.
     * $position (integer) (optional) The position in the menu order this menu should appear. By default, if this parameter is omitted, the menu will appear at the bottom of the menu structure. The higher the number, the lower its position in the menu. WARNING: if 2 menu items use the same position attribute, one of the items may be overwritten so that only one item displays!
     *
     * */
    public $menu = array();

    /** @var array with the menu content
     * $id (string) (required) HTML 'id' attribute of the edit screen section
     * $title (string) (required) Title of the edit screen section, visible to user
     * $callback (callback) (required) Function that prints out the HTML for the edit screen section. Pass function name as a string. Within a class, you can instead pass an array to call one of the class's methods. See the second example under Example below.
     * $post_type (string) (required) The type of Write screen on which to show the edit screen section ('post', 'page', 'link', or 'custom_post_type' where custom_post_type is the custom post type slug)
     * $context (string) (optional) The part of the page where the edit screen section should be shown ('normal', 'advanced', or 'side'). (Note that 'side' doesn't exist before 2.7)
     * $priority (string) (optional) The priority within the context where the boxes should show ('high', 'core', 'default' or 'low')
     * $callback_args (array) (optional) Arguments to pass into your callback function. The callback will receive the $post object and whatever parameters are passed through this variable.
     *
     * */
    public $meta = array();

    function __construct() {

    }

    /**
     * Add a menu in WP admin page
     *
     * @param array $param
     *
     * @return void
     */
    public function addMenu($param = null) {
        if ($param)
            $this->menu = $param;

        if (is_array($this->menu)) {

            if ($this->menu[0] <> '' && $this->menu[1] <> '') {
                /* add the translation */
                $this->menu[0] = __($this->menu[0], _PLUGIN_NAME_);
                $this->menu[1] = __($this->menu[1], _PLUGIN_NAME_);

                if (!isset($this->menu[5]))
                    $this->menu[5] = null;
                if (!isset($this->menu[6]))
                    $this->menu[6] = null;
                if (!isset($this->menu[7]))
                    $this->menu[7] = null;

                /* add the menu with WP */
                add_menu_page($this->menu[0], $this->menu[1], $this->menu[2], $this->menu[3], $this->menu[4], $this->menu[5], $this->menu[6], $this->menu[7]);
            }
        }
    }

    /**
     * Add a submenumenu in WP admin page
     *
     * @param array $param
     *
     * @return void
     */
    public function addSubmenu($param = null) {
        if ($param)
            $this->menu = $param;

        if (is_array($this->menu)) {

            if ($this->menu[0] <> '' && $this->menu[1] <> '') {
                /* add the translation */
                $this->menu[0] = __($this->menu[0], _PLUGIN_NAME_);
                $this->menu[1] = __($this->menu[1], _PLUGIN_NAME_);

                if (!isset($this->menu[5]))
                    $this->menu[5] = null;
                if (!isset($this->menu[6]))
                    $this->menu[6] = null;
                if (!isset($this->menu[7]))
                    $this->menu[7] = null;

                /* add the menu with WP */
                add_submenu_page($this->menu[0], $this->menu[1], $this->menu[2], $this->menu[3], $this->menu[4], $this->menu[5], $this->menu[6], $this->menu[7]);
            }
        }
    }

    /**
     * Add a box Meta in WP
     *
     * @param array $param
     *
     * @return void
     */
    public function addMeta($param = null) {
        if ($param)
            $this->meta = $param;


        if (is_array($this->meta)) {

            if ($this->meta[0] <> '' && $this->meta[1] <> '') {
                /* add the translation */
                $this->meta[1] = __($this->meta[1], _PLUGIN_NAME_);

                if (!isset($this->meta[5]))
                    $this->meta[5] = null;
                if (!isset($this->meta[6]))
                    $this->meta[6] = null; //no arg










//print_r($this->meta);
                /* add the box content with WP */
                add_meta_box($this->meta[0], $this->meta[1], $this->meta[2], $this->meta[3], $this->meta[4], $this->meta[5]);
                //add_meta_box('post'._PLUGIN_NAME_, __(ucfirst(_PLUGIN_NAME_),_PLUGIN_NAME_), array($this, 'showMenu'), 'post', 'side', 'high');
            }
        }
    }

    /**
     * Check the google code saved at settings
     *
     * @return string
     */
    public function checkGoogleWTCode($code) {
        if ($code <> '') {
            if (strpos($code, 'content') !== false) {
                preg_match('/content\\s*=\\s*[\'\"]([^\'\"]+)[\'\"]/i', $code, $result);
                if (isset($result[1]) && !empty($result[1]))
                    $code = $result[1];
            }

            if (strpos($code, '"') !== false) {
                preg_match('/[\'\"]([^\'\"]+)[\'\"]/i', $code, $result);
                if (isset($result[1]) && !empty($result[1]))
                    $code = $result[1];
            }

            if ($code == '')
                SQ_Error::setError(__("The code for Google Webmaster Tool is incorrect.", _PLUGIN_NAME_));
        }
        return $code;
    }

    /**
     * Check the google code saved at settings
     *
     * @return string
     */
    public function checkGoogleAnalyticsCode($code) {
        //echo $code;
        if ($code <> '') {
            if (strpos($code, '_gaq.push') !== false) {
                preg_match('/_gaq.push\(\[[\'\"]_setAccount[\'\"],\\s?[\'\"]([^\'\"]+)[\'\"]\]\)/i', $code, $result);
                if (isset($result[1]) && !empty($result[1]))
                    $code = $result[1];
            }

            if (strpos($code, '"') !== false) {
                preg_match('/[\'\"]([^\'\"]+)[\'\"]/i', $code, $result);
                if (isset($result[1]) && !empty($result[1]))
                    $code = $result[1];
            }

            if (strpos($code, 'UA-') === false) {
                $code = '';
                SQ_Error::setError(__("The code for Google Analytics is incorrect.", _PLUGIN_NAME_));
            }
        }
        return $code;
    }

    /**
     * Check the Facebook code saved at settings
     *
     * @return string
     */
    public function checkFavebookInsightsCode($code) {
        if ($code <> '') {
            if (strpos($code, 'content') !== false) {
                preg_match('/content\\s*=\\s*[\'\"]([^\'\"]+)[\'\"]/i', $code, $result);
                if (isset($result[1]) && !empty($result[1]))
                    $code = $result[1];
            }

            if (strpos($code, '"') !== false) {
                preg_match('/[\'\"]([^\'\"]+)[\'\"]/i', $code, $result);
                if (isset($result[1]) && !empty($result[1]))
                    $code = $result[1];
            }

            if ($code == '')
                SQ_Error::setError(__("The code for Facebook is incorrect.", _PLUGIN_NAME_));
        }
        return $code;
    }

    /**
     * Check the Bing code saved at settings
     *
     * @return string
     */
    public function checkBingWTCode($code) {
        if ($code <> '') {
            if (strpos($code, 'content') !== false) {
                preg_match('/content\\s*=\\s*[\'\"]([^\'\"]+)[\'\"]/i', $code, $result);
                if (isset($result[1]) && !empty($result[1]))
                    $code = $result[1];
            }

            if (strpos($code, '"') !== false) {
                preg_match('/[\'\"]([^\'\"]+)[\'\"]/i', $code, $result);
                if (isset($result[1]) && !empty($result[1]))
                    $code = $result[1];
            }

            if ($code == '')
                SQ_Error::setError(__("The code for Bing is incorrect.", _PLUGIN_NAME_));
        }
        return $code;
    }

    /**
     * Add the image to the root path
     *
     * @param string $file
     * @param string $path
     * @return array [name (the name of the file), favicon (the path of the ico), message (the returned message)]
     *
     */
    public function addFavicon($file, $path = ABSPATH) {
        $out = array();
        $out['name'] = strtolower(basename($file['name']));
        $out['tmp'] = _SQ_CACHE_DIR_ . strtolower(basename($file['name']));
        $out['favicon'] = $path . "/" . 'favicon.ico';
        $file_err = $file['error'];
        $img = new Model_SQ_Image();

        /* get the file extension */
        $file_name = explode('.', $file['name']);
        $file_type = strtolower($file_name[count($file_name) - 1]);

        /* if the file has a name */
        if (!empty($file['name'])) {
            /* Check the extension */
            $file_type = strtolower($file_type);
            $files = array('ico', 'jpeg', 'jpg', 'gif', 'png');
            $key = in_array($file_type, $files);

            if (!$key) {
                SQ_Error::setError(__("File type error: Only ICO, JPEG, JPG, GIF or PNG files are allowed.", _PLUGIN_NAME_));
                return;
            }

            /* Check for error messages */
            $error_count = count($file_error);
            if ($error_count > 0) {
                for ($i = 0; $i <= $error_count; ++$i) {
                    SQ_Error::setError($file['error'][$i]);
                    return;
                }
            } elseif (!$img->checkFunctions()) {
                SQ_Error::setError(__("GD error: The GD library must be installed on your server.", _PLUGIN_NAME_));
                return;
            } else {
                /* Delete the previous file if exists */
                if (is_file($out['tmp'])) {
                    if (!unlink($out['tmp'])) {
                        SQ_Error::setError(__("Delete error: Could not delete the old favicon.", _PLUGIN_NAME_));
                        return;
                    }
                }

                /* Upload the file */
                if (!move_uploaded_file($file['tmp_name'], $out['tmp'])) {
                    SQ_Error::setError(__("Upload error: Could not upload the favicon.", _PLUGIN_NAME_));
                    return;
                }

                /* Change the permision */
                if (!chmod($out['tmp'], 0755)) {
                    SQ_Error::setError(__("Permission error: Could not change the favicon permissions.", _PLUGIN_NAME_));
                    return;
                }

                if ($file_type <> 'ico') {
                    /* Transform the image into icon */
                    $img->openImage($out['tmp']);
                    $img->resizeImage(32, 32);
                    $img->saveImage($out['tmp']);

                    switch ($file_type) {
                        case "jpeg":
                        case "jpg":
                            $im = @imagecreatefromjpeg($out['tmp']);
                            break;
                        case "gif":
                            $im = @imagecreatefromgif($out['tmp']);
                            break;
                        case "png":
                            $im = @imagecreatefrompng($out['tmp']);
                            break;
                    }

                    /* Save the file */
                    if ($im)
                        new Model_SQ_Icon($im, $out['favicon']);
                    else
                        SQ_Error::setError(__("ICO Error: Could not create the ICO from file. Try with another file type.", _PLUGIN_NAME_));
                }else {
                    copy($out['tmp'], $out['favicon']);
                }

                $out['message'] .= __("The favicon has been updated.", _PLUGIN_NAME_);

                return $out;
            }
        }
    }

}

/**
 * Upload the image to the server
 */
class Model_SQ_Image {

    var $imageType;
    var $imgH;
    var $image;
    var $quality = 100;

    function openImage($image) {
        $this->image = $image;

        if (!file_exists($image))
            return false;

        $imageData = getimagesize($image);

        if (!$imageData) {
            return false;
        } else {
            $this->imageType = image_type_to_mime_type($imageData[2]);

            switch ($this->imageType) {
                case 'image/gif':
                    $this->imgH = imagecreatefromgif($image);
                    imagealphablending($this->imgH, true);
                    break;
                case 'image/png':
                    $this->imgH = imagecreatefrompng($image);
                    imagealphablending($this->imgH, true);
                    break;
                case 'image/jpg':
                case 'image/jpeg':
                    $this->imgH = imagecreatefromjpeg($image);
                    break;

                // CHANGED EXCEPTION TO RETURN FALSE
                default: return false; // throw new Exception('Unknown image format!');
            }
        }
    }

    function saveImage() {
        switch ($this->imageType) {
            case 'image/jpg':
            case 'image/jpeg':
                return @imagejpeg($this->imgH, $this->image, $this->quality);
                break;
            case 'image/gif':
                return @imagegif($this->imgH, $this->image);
                break;
            case 'image/png':
                return @imagepng($this->imgH, $this->image);
                break;
            default:
                return @imagejpeg($this->imgH, $this->image);
        }
    }

    function resizeImage($maxwidth, $maxheight, $preserveAspect = true) {
        $width = @imagesx($this->imgH);
        $height = @imagesy($this->imgH);

        if ($width > $maxwidth && $height > $maxheight) {
            $oldprop = round($width / $height, 2);
            $newprop = round($maxwidth / $maxheight, 2);
            $preserveAspectx = round($width / $maxwidth, 2);
            $preserveAspecty = round($height / $maxheight, 2);

            if ($preserveAspect) {
                if ($preserveAspectx < $preserveAspecty) {
                    $newwidth = $width / ($height / $maxheight);
                    $newheight = $maxheight;
                } else {
                    $newwidth = $maxwidth;
                    $newheight = $height / ($width / $maxwidth);
                }

                $dest = imagecreatetruecolor($newwidth, $newheight);
                $this->applyTransparency($dest);
                // CHANGED EXCEPTION TO RETURN FALSE
                if (imagecopyresampled($dest, $this->imgH, 0, 0, 0, 0, $newwidth, $newheight, $width, $height) == false)
                    return false; // throw new Exception('Couldn\'t resize image!');
            }else {
                $dest = imagecreatetruecolor($maxwidth, $maxheight);
                $this->applyTransparency($dest);
                // CHANGED EXCEPTION TO RETURN FALSE
                if (imagecopyresampled($dest, $this->imgH, 0, 0, 0, 0, $maxwidth, $maxheight, $width, $height) == false)
                    return false; // throw new Exception('Couldn\'t resize image!') ;
            }
            $this->imgH = $dest;
        }
    }

    function applyTransparency($imgH) {
        if ($this->imageType == 'image/png' || $this->imageType == 'image/gif') {
            imagealphablending($imgH, false);
            $col = imagecolorallocatealpha($imgH, 255, 255, 255, 127);
            imagefilledrectangle($imgH, 0, 0, 485, 500, $col);
            imagealphablending($imgH, true);
        }
    }

    function checkFunctions() {
        return function_exists('gd_info');
    }

}

/**
 * Transform the image to ico
 */
class Model_SQ_Icon {

    /**
     * Creates ico file from image resource(s)
     * @param sring $image
     *
     * @return string
     */
    function __construct($image, $newfile) {
        $ret = "";

        $ret.= $this->jpexs_inttoword(0); //PASSWORD
        $ret.=$this->jpexs_inttoword(1); //SOURCE
        $ret.=$this->jpexs_inttoword(1); //ICONCOUNT



        $width = imagesx($image);
        $height = imagesy($image);

        $color_count = imagecolorstotal($image);

        $transparent = imagecolortransparent($image);
        $is_transparent = ($transparent != -1);
        if ($is_transparent)
            $color_count--;

        if ($color_count == 0) {
            $color_count = 0;
            $bit_count = 24;
        };
        if (($color_count > 0) and ($color_count <= 2)) {
            $color_count = 2;
            $bit_count = 1;
        };
        if (($color_count > 2) and ($color_count <= 16)) {
            $color_count = 16;
            $bit_count = 4;
        };
        if (($color_count > 16) and ($color_count <= 256)) {
            $color_count = 0;
            $bit_count = 8;
        };


        //ICONINFO:
        $ret.=$this->jpexs_inttobyte($width); //
        $ret.=$this->jpexs_inttobyte($height); //
        $ret.=$this->jpexs_inttobyte($color_count); //
        $ret.=$this->jpexs_inttobyte(0); //RESERVED

        $planes = 0;
        if ($bit_count >= 8)
            $planes = 1;

        $ret.=$this->jpexs_inttoword($f, $planes); //PLANES
        if ($bit_count >= 8)
            ($w_bit_count = $bit_count);
        if ($bit_count == 4)
            $w_bit_count = 0;
        if ($bit_count == 1)
            $w_bit_count = 0;
        $ret.=$this->jpexs_inttoword($w_bit_count); //BITS

        $zbytek = (4 - ($width / (8 / $bit_count)) % 4) % 4;
        $zbytek_mask = (4 - ($width / 8) % 4) % 4;

        $size = 40 + ($width / (8 / $bit_count) + $zbytek) * $height + (($width / 8 + $zbytek_mask) * $height);
        if ($bit_count < 24)
            $size+=pow(2, $bit_count) * 4;

        $ret.=$this->jpexs_inttodword($size); //SIZE
        $OffSet = 6 + 16 + $full_size;
        $ret.=$this->jpexs_inttodword(6 + 16 + $full_size); //OFFSET
        $full_size+=$size;
        //-------------


        $width = imagesx($image);
        $height = imagesy($image);
        $color_count = imagecolorstotal($image);

        $transparent = imagecolortransparent($image);
        $is_transparent = ($transparent != -1);

        if ($is_transparent)
            $color_count--;
        if ($color_count == 0) {
            $color_count = 0;
            $bit_count = 24;
        };
        if (($color_count > 0) and ($color_count <= 2)) {
            $color_count = 2;
            $bit_count = 1;
        };
        if (($color_count > 2) and ($color_count <= 16)) {
            $color_count = 16;
            $bit_count = 4;
        };
        if (($color_count > 16) and ($color_count <= 256)) {
            $color_count = 0;
            $bit_count = 8;
        };



        //ICONS
        $ret.=$this->jpexs_inttodword(40); //HEADSIZE
        $ret.=$this->jpexs_inttodword($width); //
        $ret.=$this->jpexs_inttodword(2 * $height); //
        $ret.=$this->jpexs_inttoword(1); //PLANES
        $ret.=$this->jpexs_inttoword($bit_count);   //
        $ret.=$this->jpexs_inttodword(0); //Compress method


        $zbytek_mask = ($width / 8) % 4;

        $zbytek = ($width / (8 / $bit_count)) % 4;
        $size = ($width / (8 / $bit_count) + $zbytek) * $height + (($width / 8 + $zbytek_mask) * $height);

        $ret.=$this->jpexs_inttodword($size); //SIZE
        $ret.=$this->jpexs_inttodword(0); //HPIXEL_M
        $ret.=$this->jpexs_inttodword(0); //V_PIXEL_M
        $ret.=$this->jpexs_inttodword($color_count); //UCOLORS
        $ret.=$this->jpexs_inttodword(0); //DCOLORS
        //---------------


        $cc = $color_count;
        if ($cc == 0)
            $cc = 256;

        if ($bit_count < 24) {
            $color_total = imagecolorstotal($image);
            if ($is_transparent)
                $color_total--;

            for ($p = 0; $p < $color_total; $p++) {
                $color = imagecolorsforindex($image, $p);
                $ret.=$this->jpexs_inttobyte($color["blue"]);
                $ret.=$this->jpexs_inttobyte($color["green"]);
                $ret.=$this->jpexs_inttobyte($color["red"]);
                $ret.=$this->jpexs_inttobyte(0); //RESERVED
            };

            $CT = $color_total;
            for ($p = $color_total; $p < $cc; $p++) {
                $ret.=$this->jpexs_inttobyte(0);
                $ret.=$this->jpexs_inttobyte(0);
                $ret.=$this->jpexs_inttobyte(0);
                $ret.=$this->jpexs_inttobyte(0); //RESERVED
            };
        };

        if ($bit_count <= 8) {
            for ($y = $height - 1; $y >= 0; $y--) {
                $b_write = "";
                for ($x = 0; $x < $width; $x++) {
                    $color = imagecolorat($image, $x, $y);
                    if ($color == $transparent)
                        $color = imagecolorexact($image, 0, 0, 0);

                    if ($color == -1)
                        $color = 0;
                    if ($color > pow(2, $bit_count) - 1)
                        $color = 0;

                    $b_write.=$this->jpexs_decbinx($color, $bit_count);
                    if (strlen($b_write) == 8) {
                        $ret.=$this->jpexs_inttobyte(bindec($b_write));
                        $b_write = "";
                    }
                }

                if ((strlen($b_write) < 8) and (strlen($b_write) != 0)) {
                    $sl = strlen($b_write);
                    for ($t = 0; $t < 8 - $sl; $t++)
                        $sl.="0";
                    $ret.=$this->jpexs_inttobyte(bindec($b_write));
                }

                for ($z = 0; $z < $zbytek; $z++)
                    $ret.=$this->jpexs_inttobyte(0);
            }
        }



        if ($bit_count >= 24) {
            for ($y = $height - 1; $y >= 0; $y--) {
                for ($x = 0; $x < $width; $x++) {
                    $color = imagecolorsforindex($image, imagecolorat($image, $x, $y));
                    $ret.=$this->jpexs_inttobyte($color["blue"]);
                    $ret.=$this->jpexs_inttobyte($color["green"]);
                    $ret.=$this->jpexs_inttobyte($color["red"]);
                    if ($bit_count == 32)
                        $ret.=$this->jpexs_inttobyte(0); //Alpha for ICO_XP_COLORS
                }
                for ($z = 0; $z < $zbytek; $z++)
                    $ret.=$this->jpexs_inttobyte(0);
            }
        }


        //MASK
        for ($y = $height - 1; $y >= 0; $y--) {
            $byteCount = 0;
            $bOut = "";
            for ($x = 0; $x < $width; $x++) {
                if (($transparent != -1) and (imagecolorat($image, $x, $y) == $transparent)) {
                    $bOut.="1";
                } else {
                    $bOut.="0";
                }
            }

            for ($p = 0; $p < strlen($bOut); $p+=8) {
                $byte = bindec(substr($bOut, $p, 8));
                $byteCount++;
                $ret.=$this->jpexs_inttobyte($byte);
            }

            $zbytek = $byteCount % 4;

            for ($z = 0; $z < $zbytek; $z++) {
                $ret.=$this->jpexs_inttobyte(0xff);
            }
        }

        if (function_exists('fopen')) {
            $f = @fopen($newfile, "w");
            @fwrite($f, $ret);
            @fclose($f);
        }
    }

    /*
     * Internal functions:
     * -------------------------
     * jpexs_inttobyte($n) - returns chr(n)
     * jpexs_inttodword($n) - returns dword (n)
     * jpexs_inttoword($n) - returns word(n)
     * jpexs_freadbyte($file) - reads 1 byte from $file
     * jpexs_freadword($file) - reads 2 bytes (1 word) from $file
     * jpexs_freaddword($file) - reads 4 bytes (1 dword) from $file
     * jpexs_freadlngint($file) - same as freaddword($file)
     * jpexs_decbin8($d) - returns binary string of d zero filled to 8
     * jpexs_RetBits($byte,$start,$len) - returns bits $start->$start+$len from $byte
     * jpexs_freadbits($file,$count) - reads next $count bits from $file
     */

    function jpexs_decbin8($d) {
        return $this->jpexs_decbinx($d, 8);
    }

    function jpexs_decbinx($d, $n) {
        $bin = decbin($d);
        $sbin = strlen($bin);
        for ($j = 0; $j < $n - $sbin; $j++)
            $bin = "0$bin";

        return $bin;
    }

    function jpexs_retBits($byte, $start, $len) {
        $bin = $this->jpexs_decbin8($byte);
        $r = bindec(substr($bin, $start, $len));

        return $r;
    }

    function jpexs_freadbits($f, $count) {
        $jpexs_currentBit = 0;
        global $jpexs_currentBit, $jpexs_SMode;

        $Byte = $this->jpexs_freadbyte($f);
        $LastCBit = $jpexs_currentBit;
        $jpexs_currentBit+=$count;

        if ($jpexs_currentBit == 8) {
            $jpexs_currentBit = 0;
        } else {
            fseek($f, ftell($f) - 1);
        }

        return $this->jpexs_retBits($Byte, $LastCBit, $count);
    }

    function jpexs_freadbyte($f) {
        return ord(fread($f, 1));
    }

    function jpexs_freadword($f) {
        $b1 = $this->jpexs_freadbyte($f);
        $b2 = $this->jpexs_freadbyte($f);
        return $b2 * 256 + $b1;
    }

    function jpexs_freadlngint($f) {
        return $this->jpexs_freaddword($f);
    }

    function jpexs_freaddword($f) {
        $b1 = $this->jpexs_freadword($f);
        $b2 = $this->jpexs_freadword($f);
        return $b2 * 65536 + $b1;
    }

    function jpexs_inttobyte($n) {
        return chr($n);
    }

    function jpexs_inttodword($n) {
        return chr($n & 255) . chr(($n >> 8) & 255) . chr(($n >> 16) & 255) . chr(($n >> 24) & 255);
    }

    function jpexs_inttoword($n) {
        return chr($n & 255) . chr(($n >> 8) & 255);
    }

}

?>