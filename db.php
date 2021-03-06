<?php

use Medoo\Medoo;

class Db extends Medoo
{
    private $open_id;

    function __construct($open_id)
    {
        $config = [
            'database_type' => 'mysql',
            'database_name' => 'tan',
                   'server' => 'localhost',
                 'username' => 'root',
                 'password' => '18133193e0',
                  'charset' => 'utf8'
        ];

        parent::__construct($config);

        $this->open_id = $open_id;
    }

    // public function user()
    // {
    //     $user = $this->select('user', ['id', 'uuid', 'nickname'], ['openid[=]' => $this->open_id, 'deleted[=]' => 0]);

    //     return $user;
    // }

    public function is_have()
    {
        $user = $this->select('user', ['id'], ['openid[=]' => $this->open_id]);

        if (empty($user)) {
            return false;
        }

        return true;
    }

    public function is_have_phone($phone)
    {
        $user = $this->select('user', ['id'], ['telephone[=]' => $phone]);

        if (empty($user)) {
            return false;
        }

        return true;
    }

    public function add($nickname, $image, $rfcode)
    {
        if ($rfcode == $this->open_id) {
            $rfcode = '';
        }

        $password    = "12345678";
        $en_password = password_hash($password, PASSWORD_DEFAULT);

        $user_id = $this->insert("user", [
                     "uuid" => $this->open_id,
                 "nickname" => $nickname,
                   "openid" => $this->open_id,
              'en_password' => $en_password,
                 'password' => $password,
                    "image" => $image,
                     "type" => 1,
             "referee_uuid" => $rfcode,
            "register_time" => time(),
               "login_time" => time()
        ]);

        return $user_id;
    }

    public function phone($phone)
    {
        $query = $this->update('user', ['telephone' => $phone], ['openid[=]' => $this->open_id]);

        return $query;
    }
}
