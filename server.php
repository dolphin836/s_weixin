<?php

require 'vendor/autoload.php';

include "wechat.class.php";

function microtime_float()
{
    list($usec, $sec)  = explode(" ", microtime());

    list($str1, $str2) = explode(".", $usec);

    $string = $sec . $str2;

    return $string;
}

function GeraHash($qtd)
{ 
    $Caracteres = 'QWERTYUIOPASDFGHJKLZXCVBNMqwertyuioplkjhgfdsazxcvbnm0123456789'; 
    $QuantidadeCaracteres = strlen($Caracteres); 
    $QuantidadeCaracteres--; 

    $Hash=NULL; 

    for($x = 1; $x <= $qtd; $x++)
    { 
        $Posicao = rand(0, $QuantidadeCaracteres); 
        $Hash   .= substr($Caracteres, $Posicao, 1); 
    } 

    return $Hash; 
}

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
	case Wechat::MSGTYPE_TEXT:
		$content     = $weObj->getRevContent();
		$returnText  = $content;
		if ($content == '#') {
			$scene_str  = GeraHash(64);
			$log->info('Scene：' . $scene_str);
			$ticket    = $weObj->getQRCode($scene_str, 2);
			$log->info('Ticket：' . $ticket['ticket']);
			$log->info('Ticket：' . $ticket['expire_seconds']);
			$log->info('Ticket：' . $ticket['url']);
			$qrcode    = $weObj->getQRUrl($ticket['ticket']);
			$log->info('推荐人二维码地址：' . $qrcode);
		}
		break;
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
				if (empty($user)) {
					$db->insert("user", [
						"uuid" => $OpenID,
						"nickname" => $userinfo['nickname'],
						"openid" => $OpenID,
						"image" => $userinfo['headimgurl'],
						"register_time" => time()
					]);
				}
				break;
			case Wechat::EVENT_UNSUBSCRIBE:
				$log->info('取消订阅');
				$returnText = "取消订阅";
				break;
			case Wechat::EVENT_SCAN:
				$log->info('带参二维码');
				$log->info($eventType['key']);
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