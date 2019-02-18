<?php
/**
 * Created by PhpStorm.
 * User: vural
 * Date: 15.08.2018
 * Time: 15:30
 */

namespace App\Library;


class Response
{
    private static $output = null;
    private static $instance = null;

    private function __construct() {}

    /**
     * @return Response|null
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new Response();
        }

        return self::$instance;
    }


    /**
     * @param null $output
     * @return Response|null
     */
    public static function setOutput($output = null)
    {
        self::$output = $output;
        return self::getInstance();
    }


    /**
     * @return string
     */
    public static function json()
    {
        header('Content-Type: application/json');
        echo json_encode(self::$output);
        exit;
    }


    /**
     * @param int $http_status_code
     * @return int
     */
    public static function abort($http_status_code = 404)
    {
        return http_response_code($http_status_code);
    }


    /**
     * @param null $route
     * @param array $params
     */
    public static function redirect($route = null, $params = [])
    {
        if (preg_match('|[https?]?\:?\/\/.*?|si', $route)) {
            header('Location: ' . $route);
            exit;
        } else {
            $route = Router::getRoute($route);
            if ($route) {
                $route_url = $route->getUrl();
                if ($params) {
                    foreach ($params as $key => $param) {
                        $route_url = str_replace("{" . $key . "}", $param, $route_url);
                    }
                }

                header('Location: ' . SITE_URL . $route_url);
                exit;
            }
        }
    }


    /**
     * @param null $view
     * @param array $data
     */
    public static function view($view = null, $data = [])
    {
        if ($view) {
            $view = str_replace('.', '/', $view);
            if (file_exists(HOME_DIRECTORY . '/resource/view/' . $view . '.php')) {
                extract($data);
                ob_start();
                require(HOME_DIRECTORY . '/resource/view/' . $view . '.php');
                $content = ob_get_clean();
                echo $content;
            }
        }
    }
}