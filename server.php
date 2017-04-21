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
				$returnText = '更新手机失败';
			}
		}

		if ($command == '*') { //获取推荐二维码
			$ticket     = $weObj->getQRCode($OpenID, 2);
			$qrcode     = $weObj->getQRUrl($ticket['ticket']);
			$returnText = $qrcode;
		}
		break;
	case Wechat::MSGTYPE_EVENT: //事件消息
		$eventType  = $weObj->getRevEvent();
		switch ($eventType['event']) {
			case Wechat::EVENT_SUBSCRIBE: //订阅
				$key        = $eventType['key'];
				if ( ! $db->is_have() ) { //如果系统中不存在则新增用户
					$userinfo    = $weObj->getUserInfo($OpenID);
					$rfcode   = '';
					if ($key != '') { //推荐人
						$rfcode  = substr($key, 0 , 8);; 
					}
					$user_id     = $db->add($userinfo['nickname'], $userinfo['headimgurl'], $rfcode);
					if ($user_id && $rfcode != '') {
						$returnText  .= "你是由 " . $rfcode . "推荐的";
					}
				} else {
					$returnText = "欢迎回来。";
				}
				break;
			case Wechat::EVENT_SCAN: //扫描带参二维码
				$key        = $eventType['key'];
				$returnText = "欢迎回来，推荐功能只对新用户有效哦。";
				break;
			default:
				break;
		}
		break;
	default:
}

$weObj->text($returnText);

$weObj->reply();