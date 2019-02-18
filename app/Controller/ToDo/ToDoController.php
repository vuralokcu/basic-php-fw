<?php

/**
 * Created by PhpStorm.
 * User: vural
 * Date: 14.12.2018
 * Time: 14:55
 */

namespace App\Controller\ToDo;

use App\Library\Controller;
use App\Library\DB;
use App\Library\Request;
use App\Library\Response;

class ToDoController extends Controller
{

    /**
     * @param int $user_id
     * @return string
     */
    public function create($user_id = 0)
    {
        if ($user_id) {
            $user = DB::query("SELECT * FROM `user` WHERE user_id = '" . (int)$user_id . "'");

            if ($user->num_rows) {
                if (Request::post('title') && Request::post('date')) {
                    $title = clearText(Request::post('title'));
                    $description = clearText(Request::post('description'));
                    $date = clearText(Request::post('date'));

                    if (! isset($title[1])) {
                        $data = [
                            'result'    => false,
                            'message'   => 'Çok kısa bir başlık girdiniz.',
                            'code'      => 'INVALID_PARAMETER'
                        ];
                    } else if (! preg_match('|\d{4}\-\d{2}\-\d{2}\s\d{2}:\d{2}:\d{2}|', $date)) {
                        $data = [
                            'result'    => false,
                            'message'   => 'Hatalı bir tarih girdiniz.',
                            'code'      => 'INVALID_PARAMETER'
                        ];
                    } else {

                        DB::query("INSERT INTO `todo` (title, description, user_id, `date`, created_at) VALUES (
                                  '" . DB::escape($title) . "', '" . DB::escape($description) . "', '" . $user_id . "',
                                  '" . DB::escape($date) . "', NOW()
                                )");

                        $todo_id = DB::getLastId();

                        if ($todo_id && Request::post('users')) {
                            $users = Request::post('users');
                            if (is_array($users)) {
                                foreach ($users as $todo_user) {
                                    $todo_user = (int) $todo_user;
                                    if ($todo_user > 0 && ($todo_user != $user_id)) {
                                        DB::query("INSERT INTO `todo_user` (todo_id, user_id) VALUES (
                                                      '" . (int) $todo_id . "', '" . $todo_user . "')");
                                    }
                                }
                            }
                        }

                        $data = [
                            'result' => true,
                            'message' => 'Yapılacak iş başarıyla oluşturulmuştur.',
                            'code' => 'SUCCESS',
                            'todo_id' => $todo_id
                        ];
                    }
                } else {
                    $data = [
                        'result' => false,
                        'message' => 'Lütfen iş başlığını ve tarihi iletiniz.',
                        'code' => 'MISSING_PARAMETER'
                    ];
                }
            } else {
                $data = [
                    'result'    => false,
                    'message'   => 'Bu kullanıcı ID ile kayıtlı bir kullanıcı bulunamadı.',
                    'code'      => 'NOT_EXISTS'
                ];
            }
        } else {
            $data = [
                'result' => false,
                'message' => 'Lütfen kullanıcı ID değerini iletiniz.',
                'code' => 'MISSING_PARAMETER'
            ];
        }

        return Response::setOutput($data)->json();
    }


    /**
     * @param int $user_id
     * @param int $todo_id
     * @return string
     */
    public function update($user_id = 0, $todo_id = 0)
    {
        if ($user_id && $todo_id) {
            $user = DB::query("SELECT * FROM `user` WHERE user_id = '" . (int)$user_id . "'");

            if ($user->num_rows) {
                if (Request::post('title') && Request::post('date')) {
                    /*
                    $todo = DB::query("SELECT t.* FROM `todo` AS t
                                          INNER JOIN todo_user AS tu ON tu.todo_id = t.todo_id
                                       WHERE todo_id = '" . (int) $todo_id . "' && (t.user_id = '" . (int)$user_id . "' OR
                                        tu.user_id = '" . (int)$user_id . "')
                                        GROUP BY t.todo_id");
                    */

                    $todo = DB::query("SELECT * FROM `todo` WHERE todo_id = '" . (int)$todo_id . "' &&
                                    user_id = '" . (int)$user_id . "'");

                    if ($todo->num_rows) {
                        $title = clearText(Request::post('title'));
                        $description = clearText(Request::post('description'));
                        $date = clearText(Request::post('date'));

                        if (!isset($title[1])) {
                            $data = [
                                'result' => false,
                                'message' => 'Çok kısa bir başlık girdiniz.',
                                'code' => 'INVALID_PARAMETER'
                            ];
                        } else if (!preg_match('|\d{4}\-\d{2}\-\d{2}\s\d{2}:\d{2}:\d{2}|', $date)) {
                            $data = [
                                'result' => false,
                                'message' => 'Hatalı bir tarih girdiniz.',
                                'code' => 'INVALID_PARAMETER'
                            ];
                        } else {
                            DB::query("UPDATE todo SET title = '" . DB::escape($title) . "',
                                        description = '" . DB::escape($description) . "',
                                        `date` = '" . DB::escape($date) . "',
                                        `updated_at` = NOW()
                                    WHERE todo_id = '" . (int)$todo_id . "'");

                            DB::query("DELETE FROM `todo_user` WHERE todo_id = '" . (int)$todo_id . "'");
                            if (Request::post('users')) {
                                $users = Request::post('users');
                                if (is_array($users)) {
                                    foreach ($users as $todo_user) {
                                        $todo_user = (int)$todo_user;
                                        if ($todo_user > 0 && ($todo_user != $user_id)) {
                                            DB::query("INSERT INTO `todo_user` (todo_id, user_id) VALUES (
                                                      '" . (int)$todo_id . "', '" . $todo_user . "')");
                                        }
                                    }
                                }
                            }

                            $data = [
                                'result' => true,
                                'message' => 'Yapılacak iş başarıyla güncellenmiştir.',
                                'code' => 'SUCCESS',
                                'todo_id' => $todo_id
                            ];
                        }
                    } else {
                        $data = [
                            'result' => false,
                            'message' => 'Bu yapılacak iş ID ile kayıtlı bir iş bulunamadı.',
                            'code' => 'NOT_EXISTS'
                        ];
                    }
                } else {
                    $data = [
                        'result' => false,
                        'message' => 'Lütfen iş başlığını ve tarihi iletiniz.',
                        'code' => 'MISSING_PARAMETER'
                    ];
                }
            } else {
                $data = [
                    'result'    => false,
                    'message'   => 'Bu kullanıcı ID ile kayıtlı bir kullanıcı bulunamadı.',
                    'code'      => 'NOT_EXISTS'
                ];
            }
        } else {
            $data = [
                'result' => false,
                'message' => 'Lütfen kullanıcı ID ve yapılacak iş ID değerini iletiniz.',
                'code' => 'MISSING_PARAMETER'
            ];
        }

        return Response::setOutput($data)->json();
    }


    /**
     * @param int $user_id
     * @param int $todo_id
     * @return string
     */
    public function delete($user_id = 0, $todo_id = 0)
    {
        if ($user_id && $todo_id) {
            $user = DB::query("SELECT * FROM `user` WHERE user_id = '" . (int)$user_id . "'");

            if ($user->num_rows) {
                $todo = DB::query("SELECT * FROM `todo` WHERE todo_id = '" . (int)$todo_id . "' &&
                                    user_id = '" . (int)$user_id . "'");

                if ($todo->num_rows) {
                    DB::query("DELETE FROM `todo` WHERE todo_id = '" . (int)$todo_id . "'");
                    DB::query("DELETE FROM `todo_user` WHERE todo_id = '" . (int)$todo_id . "'");

                    $data = [
                        'result' => true,
                        'message' => 'Yapılacak iş başarıyla silinmiştir.',
                        'code' => 'SUCCESS'
                    ];
                } else {
                    $data = [
                        'result'    => false,
                        'message'   => 'Bu yapılacak iş ID ile kayıtlı bir iş bulunamadı.',
                        'code'      => 'NOT_EXISTS'
                    ];
                }
            } else {
                $data = [
                    'result'    => false,
                    'message'   => 'Bu kullanıcı ID ile kayıtlı bir kullanıcı bulunamadı.',
                    'code'      => 'NOT_EXISTS'
                ];
            }
        } else {
            $data = [
                'result' => false,
                'message' => 'Lütfen kullanıcı ID ve yapılacak iş ID değerini iletiniz.',
                'code' => 'MISSING_PARAMETER'
            ];
        }

        return Response::setOutput($data)->json();
    }


    /**
     * @param int $todo_id
     * @return string
     */
    public function getTodo($todo_id = 0)
    {
        $data = [];

        if ($todo_id) {
            $todo = DB::query("SELECT * FROM `todo` WHERE todo_id = '" . (int)$todo_id . "'");

            if ($todo->num_rows) {
                $user_info_arr = [];
                $user_info = DB::query("SELECT * FROM `user` WHERE user_id = '" . $todo->row['user_id'] . "'");

                if ($user_info->num_rows) {
                    $user_info_arr = [
                        'user_id' => $user_info->row['user_id'],
                        'name'  => $user_info->row['name'],
                        'email'  => $user_info->row['email'],
                        'status' => (int) $user_info->row['status'],
                        'created_at' => $user_info->row['created_at']
                    ];
                }

                // To do all users
                $todo_users_arr = [];
                $todo_users = DB::query("SELECT u.* FROM `user` AS u
                                            INNER JOIN todo_user AS tu ON tu.user_id = u.user_id
                                         WHERE tu.todo_id = '" . $todo_id . "' && u.status = '1'");
                if ($todo_users->num_rows) {
                    foreach ($todo_users->rows as $todo_user) {
                        $todo_users_arr[] = [
                            'user_id' => $todo_user['user_id'],
                            'name'  => $todo_user['name'],
                            'email'  => $todo_user['email'],
                            'status' => (int) $todo_user['status'],
                            'created_at' => $todo_user['created_at']
                        ];
                    }
                }

                $data = [
                    'todo_id' => $todo->row['todo_id'],
                    'title' => $todo->row['title'],
                    'description' => $todo->row['description'],
                    'date' => $todo->row['date'],
                    'created_at' => $todo->row['created_at'],
                    'updated_at' => $todo->row['updated_at'],
                    'user' => $user_info_arr,
                    'users' => $todo_users_arr
                ];
            }
        }

        return Response::setOutput($data)->json();
    }


    /**
     * @param int $user_id
     * @param int $todo_id
     * @return string
     */
    public function getUserTodo($user_id = 0, $todo_id = 0)
    {
        $data = [];
        if ($user_id && $todo_id) {
            if ($todo_id) {
                $todo = DB::query("SELECT t.* FROM `todo` AS t
                                          INNER JOIN todo_user AS tu ON tu.todo_id = t.todo_id
                                       WHERE t.todo_id = '" . (int) $todo_id . "' &&
                                        (t.user_id = '" . (int)$user_id . "' OR tu.user_id = '" . (int)$user_id . "')
                                       GROUP BY t.todo_id");

                if ($todo->num_rows) {
                    $user_info_arr = [];
                    $user_info = DB::query("SELECT * FROM `user` WHERE user_id = '" . $todo->row['user_id'] . "'");

                    if ($user_info->num_rows) {
                        $user_info_arr = [
                            'user_id' => $user_info->row['user_id'],
                            'name' => $user_info->row['name'],
                            'email' => $user_info->row['email'],
                            'status' => (int)$user_info->row['status'],
                            'created_at' => $user_info->row['created_at']
                        ];
                    }

                    // To do all users
                    $todo_users_arr = [];
                    $todo_users = DB::query("SELECT u.* FROM `user` AS u
                                            INNER JOIN todo_user AS tu ON tu.user_id = u.user_id
                                         WHERE tu.todo_id = '" . $todo_id . "' && u.status = '1'");

                    if ($todo_users->num_rows) {
                        foreach ($todo_users->rows as $todo_user) {
                            $todo_users_arr[] = [
                                'user_id' => $todo_user['user_id'],
                                'name' => $todo_user['name'],
                                'email' => $todo_user['email'],
                                'status' => (int)$todo_user['status'],
                                'created_at' => $todo_user['created_at']
                            ];
                        }
                    }

                    $data = [
                        'todo_id' => $todo->row['todo_id'],
                        'title' => $todo->row['title'],
                        'description' => $todo->row['description'],
                        'date' => $todo->row['date'],
                        'created_at' => $todo->row['created_at'],
                        'updated_at' => $todo->row['updated_at'],
                        'user' => $user_info_arr,
                        'users' => $todo_users_arr
                    ];
                }
            }
        }

        return Response::setOutput($data)->json();
    }


    /**
     * @param int $user_id
     * @param null $date_start
     * @param null $date_end
     * @return string
     */
    public function getDateTodos($user_id = 0, $date_start = null, $date_end = null)
    {
        $data = [];

        if ($user_id) {
            $user = DB::query("SELECT * FROM `user` WHERE user_id = '" . (int)$user_id . "'");

            if ($user->num_rows) {
                if (! preg_match('|\d{4}\-\d{2}\-\d{2}|', $date_start)) {
                    $date_start = date('Y-m-d');
                }

                if (! preg_match('|\d{4}\-\d{2}\-\d{2}|', $date_end)) {
                    $datetime = new \DateTime('tomorrow');
                    $date_end = $datetime->format('Y-m-d');
                }

                $date_start .= ' 00:00:00';
                $date_end .= ' 23:59:59';

                $todos = DB::query("SELECT t.* FROM `todo` AS t
                                      LEFT JOIN todo_user AS tu ON tu.todo_id = t.todo_id
                                    WHERE (
                                        (t.user_id = '" . (int)$user_id . "' OR tu.user_id = '" . (int)$user_id . "')
                                        AND (`date` BETWEEN '" . DB::escape($date_start) . "' AND
                                        '" . DB::escape($date_end) . "')
                                    )
                                    GROUP BY t.todo_id");

                if ($todos->num_rows) {
                    foreach ($todos->rows as $todo) {
                        $user_info_arr = [];
                        $user_info = DB::query("SELECT * FROM `user` WHERE user_id = '" . $todo['user_id'] . "'");

                        if ($user_info->num_rows) {
                            $user_info_arr = [
                                'user_id' => $user_info->row['user_id'],
                                'name'  => $user_info->row['name'],
                                'email'  => $user_info->row['email'],
                                'status' => (int) $user_info->row['status'],
                                'created_at' => $user_info->row['created_at']
                            ];
                        }

                        // To do all users
                        $todo_users_arr = [];
                        $todo_users = DB::query("SELECT u.* FROM `user` AS u
                                            INNER JOIN todo_user AS tu ON tu.user_id = u.user_id
                                         WHERE tu.todo_id = '" . $todo['todo_id'] . "' && u.status = '1'");

                        if ($todo_users->num_rows) {
                            foreach ($todo_users->rows as $todo_user) {
                                $todo_users_arr[] = [
                                    'user_id' => $todo_user['user_id'],
                                    'name'  => $todo_user['name'],
                                    'email'  => $todo_user['email'],
                                    'status' => (int) $todo_user['status'],
                                    'created_at' => $todo_user['created_at']
                                ];
                            }
                        }

                        $data[] = [
                            'todo_id' => $todo['todo_id'],
                            'title' => $todo['title'],
                            'description' => $todo['description'],
                            'date' => $todo['date'],
                            'created_at' => $todo['created_at'],
                            'updated_at' => $todo['updated_at'],
                            'user' => $user_info_arr,
                            'users' => $todo_users_arr
                        ];
                    }
                }
            }
        }

        return Response::setOutput($data)->json();
    }
}