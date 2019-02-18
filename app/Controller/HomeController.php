<?php
/**
 * Created by PhpStorm.
 * User: vural
 * Date: 15.08.2018
 * Time: 15:18
 */

namespace App\Controller;

use App\Library\DB;
use App\Library\Request;
use App\Library\Response;
use App\Library\Controller;

class HomeController extends Controller
{
    /**
     * @return string
     */
    public function index()
    {
        return Response::setOutput([])->json();
    }


    /**
     * @return string
     */
    public function getErrorCodes()
    {
        $error_codes = [
            'SUCCESS' => 'İşlem başarıyla gerçekleştirilmiştir.',
            'NOT_AUTHORIZED' => 'Sayfaya erişim izniniz bulunmamaktadır.',
            'PAGE_NOT_FOUND' => 'Aradığınız sayfa bulunmamaktadır.',
            'MISSING_PARAMETER' => 'Lütfen sayfada istenen tüm parametreleri iletiniz.',
            'INVALID_PARAMETER' => 'Hatalı parametre gönderdiniz.',
            'ALREADY_EXISTS' => 'Söz konusu bilgi zaten mevcut.',
            'NOT_EXISTS' => 'Söz konusu bilgi mevcut değil.',
            'SYSTEM_ERROR' => 'Sistem hatası meydana geldi.'
        ];

        return Response::setOutput($error_codes)->json();
    }


    /**
     * @return string
     */
    public function getSystemTime()
    {
        $timezone = null;
        if (date_default_timezone_get()) {
            $timezone = date_default_timezone_get();
        } else if (ini_get('date.timezone')) {
            $timezone = ini_get('date.timezone');
        }

        $data = [
            'result' => true,
            'time' => date('Y-m-d H:i:s'),
            'timezone' => $timezone
        ];

        return Response::setOutput($data)->json();
    }

}