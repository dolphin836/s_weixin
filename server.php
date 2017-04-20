<?php

require 'vendor/autoload.php';

include "wechat.class.php";

use Medoo\Medoo;

$db = new Medoo([
	'database_type' => 'mysql',
	'database_name' => 'tan',
		   'server' => 'localhost',
		 'username' => 'root',
		 'password' => '18133193e0',
	      'charset' => 'utf8'
]);

$log = new Katzgrau\KLogger\Logger(__DIR__ . '/log');

$options = array(
		'token'=>'5MhSkoNZCxC8GNhmMlwITdNyXuebyxeF',
        'encodingaeskey'=>'tldoRFpZaPndNYMkEY00nmoGCZsKm5W79N9oJBAns8F',
		'appid'=>'wx3f57772b43b05ba5',
		'appsecret'=>'98926008d074d0ead28018fa8c686d32'
);

$weObj = new Wechat($options);

// $weObj->valid();

$msgType     = $weObj->getRev()->getRevType();

$log->info('消息类型：' . $msgType);

switch($msgType) {
	case Wechat::MSGTYPE_EVENT:
	
		$OpenID      = $weObj->getRevFrom();

		$log->info('用户标识：' . $OpenID);

		$eventType  = $weObj->getRevEvent();

		$log->debug('事件类型：' . $eventType['event']);
		$log->debug('事件参数：' . $eventType['key']);

		switch ($eventType['event']) {
			case Wechat::EVENT_SUBSCRIBE:
				$log->info('订阅');
				$returnText  = "订阅成功";
				$userinfo    = $weObj->getUserInfo($OpenID);
				$log->info('昵称：' . $userinfo['nickname']);

				$user       = $db->select('user', ['id'], ['openid[=]' => $OpenID]);
				$log->debug('数据库查询结果 ', $user);
				// if (empty($user)) {
				// 	$database->insert("user", [
				// 		"uuid" => $OpenID,
				// 		"nickname" => $userinfo['nickname'],
				// 		"openid" => $OpenID,
				// 		"image" => $userinfo['headimgurl'],
				// 		"register_time" => time()
				// 	]);
				// }
				break;
			case Wechat::EVENT_UNSUBSCRIBE:
				$log->info('取消订阅');
				$returnText = "取消订阅";
				break;
			case Wechat::EVENT_SCAN:
				$log->info('带参二维码');
				$returnText = "扫码成功";
				break;
			default:
				$returnText = "通用回复文本";
				break;
		}
		break;
	default:
		$returnText = "通用回复文本";
}

$weObj->text($returnText);

$weObj->reply();