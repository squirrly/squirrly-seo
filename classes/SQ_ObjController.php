<?php

/**
 * The class creates object for plugin classes
 */
class SQ_ObjController {

    /** @var array of instances */
    public static $instances;

    /** @var array from core config */
    private static $config;

    private static function includeController($className, $core) {
        /* check if class is already defined */
        if (!class_exists($className, false))
        /* if $core == true then call the class from core directory */
            try {
                if (file_exists(($core ? _SQ_CONTROLLER_DIR_ : _SQ_CLASSES_DIR_) . $className . '.php'))
                    include_once(($core ? _SQ_CONTROLLER_DIR_ : _SQ_CLASSES_DIR_) . $className . '.php');
            } catch (Exception $e) {
                echo 'Controller Error: ' . $e->getMessage();
            }
    }

    /**
     * Get the instance of the specified class
     *
     * @param string $className
     * @param bool $core TRUE is the class is a core class or FALSE if it is from classes directory
     *
     * @return object of the class|false
     */
    public static function getController($className, $core = true) {
        if (!isset(self::$instances[$className])) {
            /* if $core == true then call the class from core directory */
            self::includeController($className, $core);

            if (class_exists($className)) {
                self::$instances[$className] = new $className;
                return self::$instances[$className];
            }
        }
        else
            return self::$instances[$className];

        return false;
    }

    private static function includeModel($className) {

        /* check if class is already defined */
        if (file_exists(_SQ_MODEL_DIR_ . $className . '.php'))
            try {
                include_once(_SQ_MODEL_DIR_ . $className . '.php');
            } catch (Exception $e) {
                echo 'Model Error: ' . $e->getMessage();
            }
    }

    /**
     * Get the instance of the specified model class
     *
     * @param string $className
     *
     * @return object of the class
     */
    public static function getModel($className) {
        /* add Model prefix */
        $prefix = 'Model_';

        if (!isset(self::$instances[$prefix . $className])) {
            /* if $core == true then call the class from core directory */
            self::includeModel($className);

            //echo $className . '<br />';
            if (class_exists($prefix . $className)) {
                $classModel = $prefix . $className;
                self::$instances[$prefix . $className] = new $classModel;
                return self::$instances[$prefix . $className];
            }
        }
        else
            return self::$instances[$prefix . $className];

        return;
    }

    private static function includeBlock($className) {

        /* check if class is already defined */
        try {
            require_once(_SQ_CORE_DIR_ . $className . '/' . $className . '.php');
        } catch (Exception $e) {
            echo 'Model Error: ' . $e->getMessage();
        }
    }

    /**
     * Get the instance of the specified block from core directory
     *
     * @param string $className
     *
     * @return object of the class
     */
    public static function getBlock($className) {

        if (!isset(self::$instances[$className])) {
            /* if $core == true then call the class from core directory */
            self::includeBlock($className);

            //echo $className . '<br />';
            if (class_exists($className)) {
                self::$instances[$className] = new $className;
                return self::$instances[$className];
            }
            else
                exit("Block error: Can't call $className class");
        }
        else
            return self::$instances[$className];

        return;
    }

    /**
     * Get all core classes from config.xml in core directory
     *
     * @param string $for
     */
    public function getBlocks($for) {
        /* if config allready in cache */
        if (!isset(self::$config)) {
            $config_file = _SQ_CORE_DIR_ . 'config.xml';
            if (!file_exists($config_file))
                return;

            /* load configuration blocks data from core config files */
            $data = file_get_contents($config_file);
            self::$config = json_decode(json_encode((array) simplexml_load_string($data)), 1);
            ;
        }
        //print_r(self::$config);
        if (is_array(self::$config))
            foreach (self::$config['block'] as $block) {
                if ($block['active'] == 1)
                    if (isset($block['controllers']['controller']))
                        if (!is_array($block['controllers']['controller'])) {
                            /* if the block should load for the current controller */
                            if ($for == $block['controllers']['controller']) {
                                SQ_ObjController::getBlock($block['name'])->init();
                            }
                        } else {
                            foreach ($block['controllers']['controller'] as $controller) {
                                /* if the block should load for the current controller */
                                if ($for == $controller) {
                                    SQ_ObjController::getBlock($block['name'])->init();
                                }
                            }
                        }
            }
    }

}

?>