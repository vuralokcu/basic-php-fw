<?php
/**
 * Created by PhpStorm.
 * User: vural
 * Date: 16.08.2018
 * Time: 14:06
 */

namespace App\Library;

use App\Middleware\Authenticate;

class Controller
{
    public function __construct()
    {
        $authenticate = new Authenticate();
        if (! $authenticate->handle()) {
            abort(405);
            return Response::setOutput(
                [
                    'result'    => false,
                    'message'   => 'Bu sayfasyı kullanmanıza izin verilmemektedir.',
                    'code'      => 'NOT_AUTHORIZED'
                ]
            )->json();
        }
    }
}