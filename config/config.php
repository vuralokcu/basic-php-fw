<?php
/**
 * Created by PhpStorm.
 * User: vural
 * Date: 13.08.2018
 * Time: 17:22
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
date_default_timezone_set('Europe/Istanbul');

define('SITE_URL', 'http://localhost:8000');
define('HOME_DIRECTORY', $_SERVER['DOCUMENT_ROOT'] . '/projects/basic-php-fw');
define('APP_DIRECTORY', HOME_DIRECTORY . '/app');
define('LOG_DIRECTORY', HOME_DIRECTORY . '/storage/log');
define('ENVIRONMENT', 'test');


#Database Information
define('DB_HOST', 'localhost');
define('DB_NAME', 'basic_php_fw');
define('DB_USER', 'root');
define('DB_PASS', '');
