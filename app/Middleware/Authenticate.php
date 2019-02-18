<?php
/**
 * Created by PhpStorm.
 * User: vural
 * Date: 16.08.2018
 * Time: 14:10
 */

namespace app\Middleware;

use App\Library\Request;

class Authenticate
{
    private $token;
    private $allowed_secret_keys = [
        //null,
        'jUKNaCyDu4xQx71WOS5WgbRHnMXQz8Kl'
    ];


    /**
	 * Allow only requests having secret key and token 
     * @return bool
     */
    public function handle()
    {
        $this->token = md5(date('Y/m/d'));
        $token = Request::header('X-App-Token');
        $secret_key = Request::header('X-App-Secret');

        if ((ENVIRONMENT != 'prod') || in_array($secret_key, $this->allowed_secret_keys) && $token == $this->token) {
            return true;
        }

        return false;
    }

}