<?php
/**
 * Created by PhpStorm.
 * User: vural
 * Date: 14.08.2018
 * Time: 17:43
 */

use \App\Library\Response;

if (! function_exists('abort')) {
    /**
     * @param int $http_status_code
     * @return mixed
     */
    function abort($http_status_code = 404)
    {
        return Response::abort($http_status_code);
    }
}


if (! function_exists('redirect')) {
    /**
     * @param null $route
     * @param array $params
     */
    function redirect($route = null, $params = [])
    {
        Response::redirect($route, $params);
    }
}


if (! function_exists('view')) {
    /**
     * @param null $view
     * @param array $data
     */
    function view($view = null, $data = [])
    {
        Response::view($view, $data);
    }
}

if (! function_exists('clearText')) {
    /**
     * @param null $param
     * @return string
     */
    function clearText($param = null)
    {
        return strip_tags(trim($param));
    }
}