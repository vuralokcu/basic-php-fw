<?php
/**
 * Created by PhpStorm.
 * User: vural
 * Date: 14.12.2018
 * Time: 20:10
 */
use \App\Library\Router;
# Route Example
Router::setRoute('route_name', 'HTTP_METHOD', 'ControllerName', 'MethodName', 'URL_Pattern');

# Users
Router::setRoute('user.create', 'POST', 'User\UserController', 'create', 'users/register');
Router::setRoute('user.login', 'POST', 'User\UserController', 'login', 'users/login');

# Error Codes
Router::setRoute('error_code.list', 'GET', 'HomeController', 'getErrorCodes', 'error-codes');