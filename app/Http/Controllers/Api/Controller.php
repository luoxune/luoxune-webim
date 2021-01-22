<?php

namespace App\Http\Controllers\Api;

use App\Models\Message;
use GatewayClient\Gateway;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller as BaseController;

class Controller extends BaseController
{
    public function __construct()
    {
        Gateway::$registerAddress = '127.0.0.1:1314';

    }
    //
    public function sendMessage($uid)
    {
        $user_client_id = Gateway::getClientIdByUid($uid);
        if (!empty($user_client_id)) {
            $old_message = Message::where('uid',$uid)->where('read',0)->count();
            $online_message = [
                'type' => "message",
                'msg' => "新消息提醒",
                'data' => $old_message+1,
            ];
            Gateway::sendToUid($uid, json_encode($online_message));
        }
    }
}
