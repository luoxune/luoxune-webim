<?php

namespace App\Http\Controllers\Api;

use App\Models\Friend;
use App\Models\Record;
use Illuminate\Http\Request;
use GatewayClient\Gateway;

class GatewayEventsController extends Controller
{
    public function run(Request $request)
    {
        $msg = $request->all();
        $user = $request->user();
        if(!$user){
            abort(200);
        }
        $uid = $user->id;
        switch ($msg['type']) {
            case 'init' :
                $client_id = $msg['client_id'];
                Gateway::bindUid($client_id, $uid);
                Gateway::sendToUid($uid, json_encode(array(
                    'type' => 'notice',
                    'content' => 'init success ！',
                )));
                // 获取它的所有朋友的id
                $friendsAll = Friend::where([
                    ['user_id', $uid],
                ])->pluck('friend_id');
                if (!empty($friendsAll)) {
                    foreach ($friendsAll as $vo) {
                        $user_client_id = Gateway::getClientIdByUid($vo);
                        if (!empty($user_client_id)) {
                            $online_message = [
                                'type' => 'online',
                                'id' => $uid,
                            ];
                            Gateway::sendToUid($vo, json_encode($online_message));
                        }
                    }
                }
                $ret = $user->groups;
                if (!$ret->isEmpty()) {
                    foreach ($ret as $key => $vo) {
                        Gateway::joinGroup($client_id, $vo->id);  //将登录用户加入群组
                    }
                }
                unset($ret);
                $user->status = "online";
                $user->save();
                break;
            case 'chatMessage':
                // 聊天消息
                $toObj = json_decode($msg['to']);
                $mineObj = json_decode($msg['mine']);
                $type = $toObj->type;
                $to_id = $toObj->id;
                $chat_message = [
                    'type' => 'chatMessage',
                    'data' => [
                        'username' => $mineObj->username,
                        'avatar' => $mineObj->avatar,
                        'id' => $type === 'friend' ? $uid : $to_id,
                        'from_id' => $uid,
                        'type' => $type,
                        'content' => htmlspecialchars($mineObj->content),
                        'timestamp' => time() * 1000,
                    ]
                ];
                // 加入聊天log表

                $param = [
                    'from_id' => (int)$uid,
                    'to_id' => (int)$to_id,
                    'from_name' => $user->username,
                    'from_avatar' => $user->avatar,
                    'content' => htmlspecialchars($mineObj->content),
                    'push' => 0
                ];
                switch ($type) {
                    // 私聊
                    case 'friend':
                        // 插入
                        $param['type'] = 'friend';
                        if (empty(Gateway::getClientIdByUid($to_id))) {
                            $param['push'] = 1;  //用户不在线,标记此消息推送
                            if (Record::where([
                                    ['from_id','=',$uid],
                                    ['to_id','=',$to_id],
                                    ['push','=',1]
                                ])->count() == 0){
                                $system =[
                                    'type' => 'chatMessage',
                                    'data' => [
                                        'system' => true//系统消息
                                        ,'id' => $to_id//聊天窗口ID
                                        ,'type' => "friend"//聊天窗口类型
                                        ,'content' => '对方已掉线'
                                    ]
                                ];
                                Gateway::sendToUid($uid, json_encode($system));
                            }
                        }
                        $post = Record::create($param);
                        if ($post){
                            Gateway::sendToUid($to_id, json_encode($chat_message));
                        }
                        break;
                    // 群聊
                    case 'group':
                        $param['type'] = 'group';
                        $post = Record::create($param);
                        if ($post){
                            Gateway::sendToGroup($to_id, json_encode($chat_message));
                        }
                        break;
                }
                break;
            case 'online':
                //数据库状态保存
                $user->status = $msg['status'];
                //获取它的所有朋友的id
                $friendsAll = Friend::where([
                    ['friend_id', '=', $uid],
                ])->pluck('user_id');
                if (!empty($friendsAll)) {
                    foreach ($friendsAll as $vo) {
                        $user_client_id = Gateway::getClientIdByUid($vo);
                        if (!empty($user_client_id)){
                            $online_message = [
                                'type' => $msg['status'],
                                'id' => $uid,
                            ];
                            Gateway::sendToUid($vo, json_encode($online_message));
                        }
                    }
                }
                return json_encode(['code' => 0, 'data' => '', 'msg' => $msg['status']]);
                break;
            default:break;
        }
    }
}
