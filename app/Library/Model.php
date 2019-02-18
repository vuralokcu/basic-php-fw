<?php
/**
 * Created by PhpStorm.
 * User: vural
 * Date: 16.08.2018
 * Time: 16:00
 */

class Model
{
    protected static $query = null;
    protected static $instance = null;
    protected $table;
    protected $primary_key;

    private function __construct()
    {
    }


    /**
     * @return Model|null
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new Model();
        }

        return self::$instance;
    }


    public static function where()
    {
		// will be completed...
    }
}