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

$db         = new Db($OpenID);

switch($msgType) {
	case Wechat::MSGTYPE_TEXT: //文本消息
		$content     = $weObj->getRevContent();
		$command     = substr($content, 0 , 1);
		if ($command == '#') { //更新手机
			$phone   = substr($content, 1 , 11);

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
				$returnText = '更新手机识别';
			}
		}
		if ($command == '*') { //获取推荐二维码
			$ticket     = $weObj->getQRCode($scene_str, 2);
			$qrcode     = $weObj->getQRUrl($ticket['ticket']);
			$returnText = $qrcode;
		}
		break;
	case Wechat::MSGTYPE_EVENT: //事件消息
		$eventType  = $weObj->getRevEvent();
		switch ($eventType['event']) {
			case Wechat::EVENT_SUBSCRIBE: //订阅
				if ( ! $db->is_have() ) { //如果系统中不存在则新增用户
					$userinfo    = $weObj->getUserInfo($OpenID);
					$db->add($userinfo['nickname'], $userinfo['headimgurl']);
				}
				break;
			case Wechat::EVENT_SCAN: //扫描带参二维码
				$key        = $eventType['key'];
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