<?php

ini_set('display_errors', 'on');
error_reporting(E_ERROR);
//error_reporting( E_ALL );

spl_autoload_register("JS_autoload");

/**
 * 自动加载所有module
 * @param type $classname 
 */
function JS_autoload($class_name) {
    //class directories
    $directorys = array(
        'lib/',
        'dao/',
        'module/',
        'module/'
    );

    //for each directory
    foreach ($directorys as $directory) {
        //see if the file exsists
        if (file_exists($directory . $class_name . '.php')) {
            require_once($directory . $class_name . '.php');
            return true;
        }
        if (file_exists($directory . $class_name . '.class.php')) {
            require_once($directory . $class_name . '.class.php');
            return true;
        }
        if (strpos($class_name, "DAO") !== false) {
            $dao_class_name = str_replace("DAO", "", $class_name);
        }
        if (file_exists($directory . $dao_class_name . '.dao.php')) {
            require_once($directory . $dao_class_name . '.dao.php');
            return true;
        }
    }

    return false;
}

require_once('config/config.inc.php');
require_once('config/constant.inc.php');
require_once 'lib/common.php';
