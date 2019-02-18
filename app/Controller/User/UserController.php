<?php
/**
 * Created by PhpStorm.
 * User: vural
 * Date: 15.08.2018
 * Time: 10:26
 */

namespace App\Controller\User;

use App\Library\Controller;
use App\Library\DB;
use App\Library\Request;
use App\Library\Response;

class UserController extends Controller
{
    /**
	 * User registration method
     * @return string
     * @throws \Exception
     */
    public function create()
    {
        if (Request::post('name') && Request::post('email') && Request::post('password') && Request::post('code')) {
            $email = clearText(Request::post('email'));

            $user = DB::query("SELECT user_id FROM `user` WHERE email = '" . DB::escape($email) . "'");

            if (! $user->num_rows) {
                $name = clearText(Request::post('name'));
                $password = clearText(Request::post('password'));
                $invite_code = clearText(Request::post('code'));

                if (isset($email[128]) || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $data = [
                        'result'    => false,
                        'message'   => 'Hatalı bir e-posta adresi girdiniz.',
                        'code'      => 'INVALID_PARAMETER'
                    ];
                } else if (isset($name[128])) {
                    $data = [
                        'result'    => false,
                        'message'   => 'Hatalı bir isim girdiniz.',
                        'code'      => 'INVALID_PARAMETER'
                    ];
                } else if (isset($password[128])) {
                    $data = [
                        'result'    => false,
                        'message'   => 'Hatalı bir şifre girdiniz.',
                        'code'      => 'INVALID_PARAMETER'
                    ];
                } else {

                    $check_code = $this->getInviteCode($invite_code, $email);

                    if ($check_code) {
                        DB::query("INSERT INTO `user` (email, `name`, password, invite_code_id, status, created_at)
                                    VALUES (
                                      '" . DB::escape($email) . "', '" . DB::escape($name) . "',
                                      '" . DB::escape(md5($password)) . "',
                                      '" . (int) $check_code['invite_code_id'] . "', '1', NOW()
                                    )");

                        $user_id = DB::getLastId();

                        // SET invite code status to disable
                        DB::query("UPDATE invite_code SET status = '0'
                                    WHERE invite_code_id = '" . (int) $check_code['invite_code_id'] . "'");

                        $data = [
                            'result' => true,
                            'message' => 'Kullanıcı hesabı başarıyla oluşturulmuştur.',
                            'code' => 'SUCCESS',
                            'user_id' => $user_id
                        ];
                    } else {
                        $data = [
                            'result'    => false,
                            'message'   => 'Geçersiz bir davet kodu girdiniz.',
                            'code'      => 'INVALID_PARAMETER'
                        ];
                    }
                }
            } else {
                $data = [
                    'result'    => false,
                    'message'   => 'Bu e-posta adresi ile kayıtlı bir kullanıcı zaten bulunmaktadır.',
                    'code'      => 'ALREADY_EXISTS'
                ];
            }
        } else {
            $data = [
                'result'    => false,
                'message'   => 'Lütfen isim, e-posta ve şifre bilgisini iletiniz.',
                'code'      => 'MISSING_PARAMETER'
            ];
        }

        return Response::setOutput($data)->json();
    }


    /**
     * User Login
     * @return string
     */
    public function login()
    {
        if (Request::post('email') && Request::post('password')) {
            $email = clearText(Request::post('email'));
            $password = clearText(Request::post('password'));

            $user_login = DB::query("SELECT * FROM `user` WHERE email = '" . DB::escape($email) . "'
                  && password = '" . md5(DB::escape($password)) . "'");

            if ($user_login->num_rows) {
                $user_login->row['status'] = (int) $user_login->row['status'];
                if ($user_login->row['status']) {
                    $data = [
                        'result' => true,
                        'message' => 'Kullanıcı bilgileri doğrudur.',
                        'code' => 'SUCCESS',
                        'user_id' => $user_login->row['user_id']
                    ];
                } else {
                    $data = [
                        'result'    => false,
                        'message'   => 'Kullanıcı hesabı kapatılmıştır.',
                        'code'      => 'NOT_AUTHORIZED'
                    ];
                }
            } else {
                $data = [
                    'result'    => false,
                    'message'   => 'Hatalı bir e-posta adresi veya şifre girdiniz.',
                    'code'      => 'INVALID_PARAMETER'
                ];
            }
        } else {
            $data = [
                'result'    => false,
                'message'   => 'Lütfen e-posta ve şifre bilgisini iletiniz.',
                'code'      => 'MISSING_PARAMETER'
            ];
        }

        return Response::setOutput($data)->json();
    }

}