<?php
/**
 * Created by PhpStorm.
 * User: vural
 * Date: 13.08.2018
 * Time: 17:50
 */

require_once('config/config.php');

/**
 * @param $class_name
 */
spl_autoload_register(function($class_name) {
    if (file_exists(HOME_DIRECTORY . "/" . str_replace('\\', '/', lcfirst($class_name)) . '.php')) {
        require_once HOME_DIRECTORY . "/" . str_replace('\\', '/', lcfirst($class_name)) . '.php';
    }
});

use \App\Library\DB;
use \App\Library\Router;

require_once('config/routes.php');
require_once(APP_DIRECTORY . '/Helper/helper.php');

// Start Database Connection
DB::getConnection();

// Start the router
Router::init();
