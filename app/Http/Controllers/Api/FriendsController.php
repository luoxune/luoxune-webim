<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\Friend\FriendMoveByNameRequest;
use App\Http\Requests\Api\Friend\FriendRefuseRequest;
use App\Http\Requests\Api\Friend\FriendRequest;
use App\Models\Divide;
use App\Models\Friend;
use App\Models\Message;
use App\Models\User;
use GatewayClient\Gateway;
use Illuminate\Http\Request;

class FriendsController extends Controller
{

    public function addFriend(FriendRequest $request)
    {
        $user = $request->user();
        $friend_id = (int)$request->uid;
        if(Friend::where('user_id',$user->id)->where('friend_id',$friend_id)->exists()){
            abort(403,'你们已经是好友了');
        }
        if(Message::where('from',$user->id)
            ->where('uid',$friend_id)
            ->where('type',0)
            ->where('agree',0)
            ->exists()
        ){
            abort(403,'您的添加请求已发送成功');
        }
        Message::create([
            'from' => $user->id,
            'uid' => $friend_id,
            'from_group' => (int)$request->divide,
            'content' => "申请添加你为好友",
            'remark' => $request->remark,
            'type' => 0,
            'read' => 0,
            'agree' => 0,

        ]);
        $this->sendMessage($friend_id);
        return response()->json([
            'code' => 0,
            'msg' => '您的添加请求已发送成功',
        ])->setStatusCode(200);
    }
    public function agreeFriend(FriendRequest $request)
    {
        $user = $request->user();
        $friend = User::find($request->uid);
        $divide_id = (int)$request->divide;
        $from_divide_id = (int)$request->from_divide;
        if(Friend::where('user_id',$user->id)->where('friend_id',$friend->id)->exists()){
            abort(400,'你们已经是好友了');
        }
        Friend::create([
            'user_id' => $user->id,
            'friend_id' => $friend->id,
            'divide_id' => $divide_id,
            'dividename' => Divide::find($divide_id)->name,
        ]);

        Friend::create([
            'user_id' => $friend->id,
            'friend_id' => $user->id,
            'divide_id' => $from_divide_id,
            'dividename' => Divide::find($from_divide_id)->name,
        ]);
        Message::create([
            'from' => null,
            'uid' => $friend->id,
            'from_group' => null,
            'content' => $user->username." 已同意你的好友请求",
            'remark' => null,
            'type' => 2,
            'read' => 0,
            'agree' => null,
        ]);
        Message::where('uid',$user->id)->where('from',$friend->id)->where('type',0)->where('from_group',$from_divide_id)->update(['agree' => 1]);
        $client_id = Gateway::getClientIdByUid($friend->id);
        if(!empty($client_id)){
            $add_message = [
                'type' => 'addFriend',
                'data' => [
                    'username' => $user->username,
                    'avatar' => $user->avatar,
                    'id' => $user->id,
                    'type' => 'friend',
                    'sign' => $user->sign,
                    'groupid' => $from_divide_id,
                ]
            ];
            Gateway::sendToClient($client_id['0'], json_encode($add_message));
        }
        $this->sendMessage($friend->id);
    }

    public function deleteFriend(FriendRequest  $request)
    {
        $user = $request->user();
        $friend_id = $request->friend;
        if($user->id == $friend_id){
            abort(403,'删除好友失败');
        }
        if(!User::where('id',$friend_id)->exists()){
            abort(404,'不存在好友信息');
        }
        $deletedUser = Friend::where('user_id',  $user->id)->where('friend_id', $friend_id)->delete();
        $deletedFriend = Friend::where('user_id', $friend_id)->where('friend_id', $user->id)->delete();
        if ($deletedFriend && $deletedUser) {
            $client_id = Gateway::getClientIdByUid($friend_id);
            if(!empty($client_id)){
                $add_message = [
                    'type' => 'delFriend',
                    'data' => [
                        'id' => $user->id
                    ]
                ];
                Gateway::sendToClient($client_id['0'], json_encode($add_message));
            }
            return response()->json([
                'code' => 0,
                'msg' => '删除好友成功',
            ]);
        } else {
            abort(500,'删除好友失败');
        }
    }
    public function refuseFriend(FriendRefuseRequest $request)
    {
        $user = $request->user();
        $friend_id = (int)$request->uid;
        $from_divide_id = (int)$request->from_divide;
        Message::where('uid',$user->id)
            ->where('from',$friend_id)
            ->where('agree',0)
            ->where('type',0)
            ->where('from_group',$from_divide_id)
            ->update(['agree' => 2]);
        Message::create([
            'from' => null,
            'uid' => $friend_id,
            'from_group' => null,
            'content' => $user->username."($user->number) 拒绝了你的好友请求",
            'remark' => null,
            'type' => 2,
            'read' => 0,
            'agree' => null,
        ]);
        $this->sendMessage($friend_id);
    }
    public function moveFriendByID(FriendMoveByNameRequest  $request)
    {
        $user = $request->user();
        $result = Friend::where('user_id',$user->id)
            ->where('friend_id',$request->friend)
            ->update(['divide_id' => $request->divide]);
        if($result){
            return response()->json([
                'code' => 0,
                'msg' => '移动成功，Web客户端刷新浏览器后生效',
            ]);
        }else{
            abort(403,'移动失败，请稍后重试');
        }
    }
    public function moveFriendByName(FriendMoveByNameRequest  $request)
    {
        $user = $request->user();
        $divide_id = Divide::where('name',$request->divide)->where('user_id',$user->id)
            ->first()->id;
        $result = Friend::where('user_id',$user->id)
            ->where('friend_id',$request->friend)
            ->update(['divide_id' => $divide_id]);
        if($result){
            return response()->json([
                'code' => 0,
                'msg' => '移动成功，Web客户端刷新浏览器后生效',
            ]);
        }else{
            abort(403,'移动失败，请稍后重试');
        }
    }
}
