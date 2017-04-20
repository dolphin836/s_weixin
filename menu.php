<?php

include "wechat.class.php";

$options = array(
		'token'=>'5MhSkoNZCxC8GNhmMlwITdNyXuebyxeF',
        'encodingaeskey'=>'tldoRFpZaPndNYMkEY00nmoGCZsKm5W79N9oJBAns8F',
		'appid'=>'wx3f57772b43b05ba5',
		'appsecret'=>'98926008d074d0ead28018fa8c686d32'
);

$weObj = new Wechat($options);

$json =  array(
    "button" => array(
        array('type' => 'view', 'name' => '所有项目', 'url' => 'http://mobie.hbdx.cc/product'),
        array('type' => 'view', 'name' => '我的票码', 'url' => 'http://mobie.hbdx.cc/ticket'),
        array('type' => 'view', 'name' => '个人中心', 'url' => 'http://mobie.hbdx.cc/center')
    )
);

$req = $weObj->createMenu($json);

var_dump($req);