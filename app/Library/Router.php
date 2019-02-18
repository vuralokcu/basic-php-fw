<?php
/**
 * Created by PhpStorm.
 * User: vural
 * Date: 13.08.2018
 * Time: 17:42
 */

namespace App\Library;


class Router
{
    private static $routes = [];
    private static $router = null;

    private function __construct() {}

    /**
     * @return null
     */
    public static function getInstance()
    {
        if (self::$router == null) {
            self::$router = new Router();
        }

        return self::$router;
    }


    /**
     * @param null $route_name
     * @param null $http_method
     * @param null $controller
     * @param null $method
     * @param null $url
     */
    public static function setRoute($route_name = null, $http_method = null, $controller = null, $method = null, $url = null)
    {
        $route_bean = new Route();
        $route_bean->setHttpMethod(isset($http_method) ? $http_method : null);
        $route_bean->setController(isset($controller) ? $controller : null);
        $route_bean->setMethod(isset($method) ? $method : null);
        $route_bean->setUrl(isset($url) ? $url : null);

        if ($route_name) {
            self::$routes[$route_name] = $route_bean;
        } else {
            self::$routes[] = $route_bean;
        }
        unset($route_bean);
    }


    /**
     * @param null $route_name
     * @return Route|null
     */
    public static function getRoute($route_name = null)
    {
        if ($route_name) {
            return isset(self::$routes[$route_name]) ? self::$routes[$route_name] : null;
        }

        return null;
    }


    /**
     * @return array
     */
    public static function getRoutes()
    {
        return self::$routes;
    }


    /**
     * Init the router
     */
    public static function init()
    {
        $slug = Request::getUrl();
        $slug = trim($slug, '/');
        if (! $slug) $slug = '/';

        if (self::$routes) {
            foreach (self::$routes as $route) {
                if ($route->getHttpMethod() == Request::method() && preg_match('|^' . $route->getUrl(true) . '$|si', $slug)) {
                    $controller_name = "\\App\\Controller\\" . $route->getController();
                    $controller = new $controller_name();

                    if (method_exists($controller, $route->getMethod())) {
                        preg_match('|^' . $route->getUrl(true) . '|si', $slug, $params);

                        if (isset($params[1])) {
                            unset($params[0]);
                            return call_user_func_array([$controller, $route->getMethod()], $params);
                        } else {
                            return $controller->{$route->getMethod()}();
                        }
                    }
                }
            }
        }

        Response::setOutput(
            [
                'result'    => false,
                'message'   => 'Page not found.',
                'code'      => 'PAGE_NOT_FOUND'
            ]
        );
        return abort(404);
    }
}