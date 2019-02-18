<?php
/**
 * Created by PhpStorm.
 * User: vural
 * Date: 15.08.2018
 * Time: 17:02
 */

namespace App\Library;


class DB
{
    private static $connection = null;

    private function __construct() {}

    /**
     * @return \mysqli|null
     */
    public static function getConnection()
    {
        if (self::$connection == null) {
            try {
                self::$connection = new \mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

                self::$connection->set_charset("utf8");
                self::$connection->query("SET SQL_MODE = ''");
            } catch (\Exception $e) {
                Log::error("Database Connection Error. Code: " . $e->getCode() . " - Message: " . $e->getMessage());
            }
        }

        return self::$connection;
    }

    /**
     * @param null $sql
     * @return null|\stdClass
     * @throws \Exception
     */
    public static function query($sql = null)
    {
        if (self::$connection && $sql) {
            $query = self::$connection->query($sql);

            if (! self::$connection->errno){
                if (isset($query->num_rows)) {
                    $data = [];

                    while ($row = $query->fetch_assoc()) {
                        $data[] = $row;
                    }

                    $result = new \stdClass();
                    $result->num_rows = $query->num_rows;
                    $result->row = isset($data[0]) ? $data[0] : [];
                    $result->rows = $data;

                    unset($data);

                    $query->close();

                    return $result;
                } else {
                    return null;
                }
            } else {
                Log::error("SQL Sorgu Hatası. Sorgu: " . $sql);
                throw new \Exception('Error : Mysql query error!');
            }
        } else {
            Log::error("SQL Sorgu Hatası. Database'e bağlanılamıyor.");
            throw new \Exception('Error : Can not connect to database!');
        }
    }


    /**
     * @param $value
     * @return mixed
     */
    public static function escape($value)
    {
        return self::$connection->real_escape_string(trim($value));
    }


    /**
     * @return mixed
     */
    public static function countAffected()
    {
        return self::$connection->affected_rows;
    }


    /**
     * @return mixed
     */
    public static function getLastId()
    {
        return self::$connection->insert_id;
    }


    /**
     * Destructor method
     */
    public function __destruct()
    {
        if (self::$connection != null) {
            self::$connection->close();
        }
    }
}