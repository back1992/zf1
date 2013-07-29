<?php

// Define path to application directory
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';

// Custom functions to help development
require_once ('Utils.php');



// Create application, bootstrap, and run
$application = new Zend_Application(
        APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini'
);
$sourceArr = array(
    "/var/www/html/quiz-de/administrator/components/com_ariquiz",
    "/var/www/html/quiz-de//components/com_ariquiz",
//     "/var/www/temp/decode"
);
Zend_Registry::set('sourceArr', $sourceArr);
$comp_sou = array();
$comp_tar = array();
Zend_Registry::set('comp_sou', $comp_sou);
Zend_Registry::set('comp_tar', $comp_tar);
$application->bootstrap()
        ->run();