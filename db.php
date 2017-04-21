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
        $user = $this->select('user', ['id'], ['openid[=]' => $this->open_id, 'deleted[=]' => 0]);

        if (empty($user)) {
            return false;
        }

        return true;
    }

    public function is_have_phone($phone)
    {
        $user = $this->select('user', ['id'], ['telephone[=]' => $phone, 'deleted[=]' => 0]);

        if (empty($user)) {
            return false;
        }

        return true;
    }

    public function add($nickname, $image)
    {
        $user_id = $this->insert("user", [
                     "uuid" => $this->open_id,
                 "nickname" => $nickname,
                   "openid" => $this->open_id,
                    "image" => $image,
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
