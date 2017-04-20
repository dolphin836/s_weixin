<?php

include "wechat.class.php";

$options = array(
		'token'=>'5MhSkoNZCxC8GNhmMlwITdNyXuebyxeF',
        'encodingaeskey'=>'tldoRFpZaPndNYMkEY00nmoGCZsKm5W79N9oJBAns8F',
		'appid'=>'wx3f57772b43b05ba5',
		'appsecret'=>'98926008d074d0ead28018fa8c686d32'
);

$weObj = new Wechat($options);

// $weObj->valid();

$msgType     = $weObj->getRevType();

$OpenID      = $weObj->getRevFrom();

$returnText  = '用户标识：' . $OpenID;

$returnText .= ' - 消息类型：' . $msgType;

switch($msgType) {
	case Wechat::MSGTYPE_EVENT:
		$returnText .= "（事件消息）";
		$eventType  = $weObj->getRevEvent();
		$returnText = '事件类型：' . $eventType;

		switch ($eventType) {
			case Wechat::EVENT_SUBSCRIBE:
				$returnText .= "（订阅）";
				break;
			case Wechat::EVENT_UNSUBSCRIBE:
				$returnText .= "（取消订阅）";
				break;
			case Wechat::EVENT_SCAN:
				$returnText .= "（带参二维码）";
				break;
			default:
				break;
		}
		break;
	default:
		$returnText .= "通用回复文本";
}

$weObj->text($returnText);

$weObj->reply();