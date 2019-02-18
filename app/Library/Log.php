<?php
/**
 * Created by PhpStorm.
 * User: vural
 * Date: 15.08.2018
 * Time: 17:22
 */

namespace App\Library;


class Log
{
    /**
     * @param null $text
     */
    public static function error($text = null)
    {
        $output = "[" . date("Y-m-d H:i:s") . "]: " . $text;

        file_put_contents(
            LOG_DIRECTORY . "/error.log",
            $output . "\r\n",
            FILE_APPEND
        );
    }
}