<?php

//error_reporting(E_ALL);
define('debug', true);

define('ROOT_DIR', dirname(dirname(__FILE__)) . '/');
define('LIB_DIR', ROOT_DIR . 'lib/');
define('SMARTY_DIR', ROOT_DIR . 'smarty/');
define('TMP_DIR', ROOT_DIR . 'tmp/');

//static content
if (debug) {
    define('HOST', 'http://localhost/justsing/');
    
    // formal DB
    define('DB_HOST', '127.0.0.1');
    define('DB_PORT', '3306');
    define('DB_USERNAME', 'root');
    define('DB_PASSWORD', 'root');
    define('DB_SCHEMA', 'gameserver');
    
    define('REDIS_SERVER', '127.0.0.1');
    define('REDIS_PORT', 6379);
} else {
    define('HOST', 'http://线上域名/');
    
    // formal DB
    define('DB_HOST', '127.0.0.1');
    define('DB_PORT', '3306');
    define('DB_USERNAME', 'root');
    define('DB_PASSWORD', 'root');
    define('DB_SCHEMA', 'gameserver');
    
    define('REDIS_SERVER', '127.0.0.1');
    define('REDIS_PORT', 6379);
}

define("STATIC_DOMAIN", HOST."static/");
define("JS_DIR", STATIC_DOMAIN."js/");
define("CSS_DIR", STATIC_DOMAIN."css/");
define("IMG_DIR", STATIC_DOMAIN."images/");

// initialize Smarty variable
if (!isset($__noSmarty) && !isset($TPL)) {
    require_once( SMARTY_DIR . 'Smarty.class.php' );
    $TPL = & new Smarty;

    $TPL->template_dir = ROOT_DIR . '/tpl';
    $TPL->compile_dir = TMP_DIR . '/tpl_c';
    $TPL->cache_dir = TMP_DIR . '/cache';

    //assign constant to all page
    $TPL->assign('constant', get_defined_constants());
}

define("PRIVATE_KEY", "JUST_SING_KEY");

define('VALIDATE_ERROR_BASE', -1000);
define('VALIDATE_ERROR_EXIST', VALIDATE_ERROR_BASE - 1);
define('VALIDATE_ERROR_MIN', VALIDATE_ERROR_BASE - 2);
define('VALIDATE_ERROR_MAX', VALIDATE_ERROR_BASE - 3);
define('VALIDATE_ERROR_MINLEN', VALIDATE_ERROR_BASE - 4);
define('VALIDATE_ERROR_MAXLEN', VALIDATE_ERROR_BASE - 5);
define('VALIDATE_ERROR_WRONG_FORMAT', VALIDATE_ERROR_BASE - 6);
define('VALIDATE_ERROR_INCLUDE_VALUE', VALIDATE_ERROR_BASE - 7);
define('VALIDATE_ERROR_EXCLUDE_VALUE', VALIDATE_ERROR_BASE - 8);
define('VALIDATE_ERROR_CUSTOM', VALIDATE_ERROR_BASE - 9);

define('VALIDATE_DATATYPE_BASE', 1000);
define('VALIDATE_DATATYPE_INT', VALIDATE_DATATYPE_BASE + 1);
define('VALIDATE_DATATYPE_STRING', VALIDATE_DATATYPE_BASE + 2);
define('VALIDATE_DATATYPE_UIN', VALIDATE_DATATYPE_BASE + 3);
define('VALIDATE_DATATYPE_EMAIL', VALIDATE_DATATYPE_BASE + 4);
define('VALIDATE_DATATYPE_URL', VALIDATE_DATATYPE_BASE + 5);
define('VALIDATE_DATATYPE_TEL', VALIDATE_DATATYPE_BASE + 6);
define('VALIDATE_DATATYPE_MOBILE', VALIDATE_DATATYPE_BASE + 7);
define('VALIDATE_DATATYPE_ZIPCODE', VALIDATE_DATATYPE_BASE + 8);
define('VALIDATE_DATATYPE_NUMBER', VALIDATE_DATATYPE_BASE + 9);
define('VALIDATE_DATATYPE_DATETIME', VALIDATE_DATATYPE_BASE + 10);
define('VALIDATE_DATATYPE_CUSTOM', VALIDATE_DATATYPE_BASE + 11);
define('VALIDATE_DATATYPE_CUSTOM_BASE', VALIDATE_DATATYPE_BASE + 12);