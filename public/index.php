<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
        
);
$sourceArr = array(
    // "/home/www/html/ariquiz/29",
   "/home/www/html/j15/administrator/components/com_ariquiz",
   "/home/www/html/j15/components/com_ariquiz",
//    "/var/www/html/quiz-de/administrator/components/com_ariquiz",
//    "/var/www/html/quiz-de//components/com_ariquiz",
//     "/var/www/temp/decode"
);
$targetArr = array();
Zend_Registry::set('sourceArr', $sourceArr);
Zend_Registry::set('targetArr', $targetArr);
$application->bootstrap()
            ->run();