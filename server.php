<?php

include __DIR__ . '/vendor/autoload.php'; // 引入 composer 入口文件

use EasyWeChat\Foundation\Application;

$options = [
    'debug'  => true,
    'app_id' => 'wx3f57772b43b05ba5',
    'secret' => '98926008d074d0ead28018fa8c686d32',
    'token'  => '5MhSkoNZCxC8GNhmMlwITdNyXuebyxeF',
    'aes_key' => 'tldoRFpZaPndNYMkEY00nmoGCZsKm5W79N9oJBAns8F', // 可选
    'log' => [
        'level' => 'debug',
        'file'  => '/alidata/www/s_weixin/log/s_weixin.log', // XXX: 绝对路径！！！！
    ],
    //...
];

$app      = new Application($options);
$response = $app->server->serve();

$response->send();
