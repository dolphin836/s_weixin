<?php

require 'vendor/autoload.php';

include "wechat.class.php";

include "config.php";

include "db.php";

$log        = new Katzgrau\KLogger\Logger(__DIR__ . '/log');

$weObj      = new Wechat($config['weixin']);

$returnText = $config['default_reply_msg'];

$msgType    = $weObj->getRev()->getRevType();

$OpenID     = $weObj->getRevFrom();

$log->info($OpenID);

$db         = new Db($OpenID);

$log->info($msgType);

switch($msgType) {
	case Wechat::MSGTYPE_TEXT: //文本消息
		$content     = $weObj->getRevContent();
		$log->info($content);
		$command     = substr($content, 0 , 1);
		$log->info($command);
		if ($command == '#') { //更新手机
			$phone   = substr($content, 1 , 11);
			$log->info($phone);
			if ( ! ctype_digit($phone) ) {
				$log->info('无法识别的手机号码');
				$returnText = '无法识别的手机号码';
			}

			if ( $db->is_have_phone($phone) ) {
				$log->info('手机号码已经存在');
				$returnText = '手机号码已经存在';
			}

			if ($db->phone($phone)) {
				$returnText = '更新手机成功';
			} else {
				$returnText = '更新手机失败';
			}
		}
		if ($command == '*') { //获取推荐二维码
			$ticket     = $weObj->getQRCode($OpenID, 2);
			$log->info($ticket);
			$qrcode     = $weObj->getQRUrl($ticket['ticket']);
			$log->info($qrcode);
			$returnText = $qrcode;
		}
		break;
	case Wechat::MSGTYPE_EVENT: //事件消息
		$eventType  = $weObj->getRevEvent();
		$log->info($eventType['event']);
		switch ($eventType['event']) {
			case Wechat::EVENT_SUBSCRIBE: //订阅
				if ( ! $db->is_have() ) { //如果系统中不存在则新增用户
					$userinfo    = $weObj->getUserInfo($OpenID);
					$log->info($userinfo['nickname']);
					$user_id     = $db->add($userinfo['nickname'], $userinfo['headimgurl']);
					$log->info($user_id);
					$returnText = $user_id;
				}
				break;
			case Wechat::EVENT_SCAN: //扫描带参二维码
				$key        = $eventType['key'];
				$log->info($key);
				$returnText = $key;
				break;
			default:
				break;
		}
		break;
	default:
}

$weObj->text($returnText);

$weObj->reply();