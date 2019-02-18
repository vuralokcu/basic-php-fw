<?php
/**
 * Created by PhpStorm.
 * User: vural
 * Date: 14.08.2018
 * Time: 13:11
 */

namespace App\Library;


class Request
{
    private static $instance = null;

    /**
     * Constructor
     */
    private function __construct()
    {
    }

    /**
     * @return Request|null
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new Request();
        }

        return self::$instance;
    }

    /**
     * @return mixed
     */
    public static function method()
    {
        return $_SERVER['REQUEST_METHOD'];
    }


    /**
     * @return mixed
     */
    public static function getUrl()
    {
        //return (ENVIRONMENT == 'prod') ? $_SERVER["REQUEST_URI"] : self::get('slug');
        return self::get('slug');
    }


    /**
     * @return string
     */
    public static function getFullUrl()
    {
        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" .
                    $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
    }


    /**
     * @param null $param
     * @return null
     */
    public static function get($param = null) {
        if ($param) {
            return isset($_GET[$param]) ? $_GET[$param] : null;
        }

        return null;
    }


    /**
     * @param null $param
     * @return null
     */
    public static function post($param = null) {
        if ($param) {
            return isset($_POST[$param]) ? $_POST[$param] : null;
        }

        return null;
    }


    /**
     * @param null $param
     * @return null
     */
    public static function header($param = null) {
        if ($param) {
            foreach (getallheaders() as $key => $value) {
                if ($key == $param)
                    return $value;
            }
        }

        return null;
    }
}