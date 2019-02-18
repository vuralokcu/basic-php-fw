<?php
/**
 * Created by PhpStorm.
 * User: vural
 * Date: 14.08.2018
 * Time: 17:08
 */

namespace App\Library;


class Route
{
    private $http_method;
    private $controller;
    private $method;
    private $url;

    public function __construct() {

    }

    /**
     * @return mixed
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @param mixed $controller
     */
    public function setController($controller)
    {
        $this->controller = $controller;
    }


    /**
     * @return mixed
     */
    public function getHttpMethod()
    {
        return mb_strtoupper($this->http_method);
    }

    /**
     * @param mixed $http_method
     */
    public function setHttpMethod($http_method)
    {
        $this->http_method = mb_strtoupper($http_method);
    }

    /**
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param mixed $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @param $formatted
     * @return mixed
     */
    public function getUrl($formatted = false)
    {
        if ($formatted) {
            return preg_replace('|\{.+?\}|si', '([a-zA-Z0-9\-\_]*)', $this->url);
        }

        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }
}