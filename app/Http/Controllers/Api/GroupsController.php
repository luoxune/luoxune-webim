<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\Group\GroupAddRequest;
use App\Http\Requests\Api\Group\GroupAgreeRequest;
use App\Http\Requests\Api\Group\GroupFindRequest;
use App\Http\Requests\Api\Group\GroupMemberRequest;
use App\Http\Requests\Api\Group\GroupRefuseRequest;
use App\Http\Requests\Api\Group\GroupRequest;
use App\Http\Resources\GroupResource;
use App\Models\GNumber;
use App\Models\Group;
use App\Models\Member;
use App\Models\Message;
use App\Models\User;
use GatewayClient\Gateway;
use Illuminate\Http\Request;

class GroupsController extends Controller
{

    public function store(GroupRequest $request)
    {
        $user = $request->user();
        $group = Group::create([
            'number' => GNumber::Where('used',0)->get()->random()->id,
            'user_id' => $user->id,
            'groupname' => $request->groupname,
            'avatar' => $request->avatar,
            'sign' => $request->sign,
            'status' => 0,
            'setting' => 0
        ]);
        $member = Member::create([
            'id' => $group->id,
            'user_id' => $user->id,
            'username' => $user->username,
            'groupname' => $group->groupname,
            'avatar' =>  $request->avatar,
            'user_avatar' => $user->avatar,
            'sign' => $request->sign,
        ]);
        if($group && $member){
            $client_id = Gateway::getClientIdByUid($user->id);
            if(!empty($client_id)){
                $add_message = [
                    'type' => 'addGroup',
                    'data' => [
                        'type'=> 'group' ,//列表类型，只支持friend和group两种
                        'avatar'=>  $group->avatar ,//群组头像
                        'groupname'=> $group->groupname ,//群组名称
                        'id' => $group->id //群组id
                    ]
                ];
                Gateway::sendToClient($client_id['0'], json_encode($add_message));
            }
            return response()->json([
                'code' => 0,
                'msg' => '创建成功',
            ]);
        }else{
            abort(400,"创建失败，请稍后重试");
        }
    }
    public function show(GroupRequest $request)
    {
        $group=Group::find($request->group_id);
        if($group != ''){
            return new GroupResource($group);
        }else{
            abort(404,"加载失败，请稍后重试");
        }

    }
    public function update(GroupRequest $request)
    {
        $group = Group::find($request->group_id);
        if($request->user()->id != $group->user_id){
            abort(403,"更新失败，请稍后重试");
        }
        $group ->groupname = $request->groupname;
        $group -> sign = $request ->sign;
        $group -> avatar = $request -> avatar;
        $resultg = $group ->save();
        $resultm = Member::where('id',$group->id)->update(['groupname'=> $group->groupname,'avatar'=>$group->avatar,'sign' => $group->sign]);
        if($resultg && $resultm){
            return response()->json([
                'code' => 0,
                'msg' => '更新成功',
            ]);
        }else{
            abort(404,"更新失败，请稍后重试");
        }
    }

    public function delete(GroupRequest $request)
    {
        $user = $request->user();
        $group = Group::find($request->group_id);
        if($group->user_id == $user->id){
            abort(403,"群主不可退群");
        }
        $resultm = Member::where('id',(int)$request->group_id)
            ->where('user_id',$user->id)
            ->delete();
        if($resultm){
            $client_id = Gateway::getClientIdByUid($user->id);
            if(!empty($client_id)){
                $add_message = [
                    'type' => 'delGroup',
                    'id' => $group->id
                ];
                Gateway::sendToClient($client_id['0'], json_encode($add_message));
                Gateway::leaveGroup($client_id['0'], $group->id);
            }
            Message::create([
                'from' => null,
                'uid' => $group->user_id,
                'from_group' => $group->id,
                'content' =>"$user->username 退出群组$group->groupname($group->number)",
                'remark' => null,
                'type' => 2,
                'read' => 0,
                'agree' => null,
            ]);
            $this->sendMessage($group->user_id);
            return response()->json([
                'code' => 0,
                'msg' => '已退群',
            ]);
        }else{
            abort(400,"退群失败，请稍后重试");
        }
    }

    public function destroy(GroupRequest $request)
    {
        $user = $request->user();
        $group = Group::where('user_id',$user->id)->where('id',$request->group_id)->first();
        if($group == '' || $group->groupname != $request->groupname){
            abort(403,"解散失败，请稍后重试");
        }
        $groupTmpID = $group->id;
        $members = $group->members;
        if(!$members->isEmpty()){
            foreach($members as $key => $v){
                $client_id = Gateway::getClientIdByUid($v->user_id);
                if(!empty($client_id)){
                    $message = [
                        'type' => 'delGroup',
                        'data' => [
                            'id' => $groupTmpID
                        ],
                    ];
                    Gateway::sendToClient($client_id['0'], json_encode($message));
                }
                $client_id = null;

                Message::create([
                    'from' => null,
                    'uid' => $v->user_id,
                    'from_group' => $group->id,
                    'content' =>"群组 $group->groupname($group->number)已解散",
                    'remark' => null,
                    'type' => 2,
                    'read' => 0,
                    'agree' => null,
                ]);
                $this->sendMessage($v->user_id);
            }
        }
        $resultg = $group->delete();
        $resultm = Member::where('id',$group->id)->delete();
        if($resultg && $resultm){
            return response()->json([
                'code' => 0,
                'msg' => '已解散',
            ]);
        }else{
            abort(400,"解散失败，请稍后重试");
        }
    }
    public function addGroup(GroupAddRequest $request)
    {
        $user = $request->user();
        if(Member::where('user_id',$user->id)->where('id',(int)$request->group_id)->exists()){
            abort(403,'您已经是此群成员了');
        }
        $Group = Group::find($request->group_id);
        $owner = $Group->user_id;
        if(Message::where('from',$user->id)
            ->where('uid',$owner)
            ->where('type',1)
            ->where('agree',0)
            ->exists()
        ){
            abort(403,'您的添加请求已发送成功');
        }
        Message::create([
            'from' => $user->id,
            'uid' => $owner,
            'from_group' => $Group->id,
            'content' => "申请加群 ".$Group->groupname."($Group->number)",
            'remark' => $request->remark,
            'type' => 1,
            'read' => 0,
            'agree' => 0,

        ]);
        $this->sendMessage($owner);
        return response()->json([
            'code' => 0,
            'msg' => '您的添加请求已发送成功',
        ])->setStatusCode(200);
    }
    public function findGroup(GroupFindRequest $request)
    {
        $result = Group::where('number',$request->group_number)->first();
        if (!$result == null) {
            return new GroupResource($result);
        } else {
            abort('404', '查询无结果');
        }
    }
    public function agreeGroup(GroupAgreeRequest $request)
    {

        $user = $request->user();
        $frien_id = (int)$request->uid;
        $group_id = (int)$request->group_id;
        $friend = User::find($frien_id);
        $Group = Group::find($group_id);
        if(Member::where('user_id',$friend->id)->where('id',(int)$group_id)->exists()){
            abort(400,'对方已经是此群成员了');
        }
        Member::create([
            'id' => $Group->id,
            'user_id' => $friend->id,
            'username' => $friend->username,
            'groupname' => $Group->groupname,
            'user_avatar' => $friend->avatar,
            'avatar' => $Group->avatar,
            'sign' =>   $Group->sign,
        ]);
        Message::create([
            'from' => null,
            'uid' => $friend->id,
            'from_group' => $Group->id,
            'content' => $user->username." 已同意你的加群请求",
            'remark' => null,
            'type' => 2,
            'read' => 0,
            'agree' => null,
        ]);
        Message::where('uid',$user->id)->where('from',$friend->id)->where('type',1)->where('from_group',$Group->id)->update(['agree' => 1]);
        $client_id = Gateway::getClientIdByUid($friend->id);
        if(!empty($client_id)){
            $add_message = [
                'type' => 'addGroup',
                'data' => [
                    'type'=> 'group' ,//列表类型，只支持friend和group两种
                    'avatar'=>  $Group->avatar ,//群组头像
                    'groupname'=> $Group->groupname ,//群组名称
                    'id' => $Group->id //群组id
                ]
            ];
            Gateway::joinGroup($client_id['0'],$Group->id);
            Gateway::sendToClient($client_id['0'], json_encode($add_message));
        }
        $this->sendMessage($friend->id);
    }
    public function refuseGroup(GroupRefuseRequest $request)
    {
        $user = $request->user();
        $friend_id = (int)$request->uid;
        Message::where('uid',$user->id)
            ->where('from',$friend_id)
            ->where('agree',0)
            ->where('type',1)
            ->where('from_group',(int)$request->from_group)
            ->update(['agree' => 2]);
        Message::create([
            'from' => null,
            'uid' => $friend_id,
            'from_group' => null,
            'content' => $user->username." 拒绝了你的加群请求",
            'remark' => null,
            'type' => 2,
            'read' => 0,
            'agree' => null,
        ]);
        $this->sendMessage($friend_id);

    }
    public function members(GroupMemberRequest $request)
    {
        return response()->json([
            'code' => 0,
            'msg' => '',
            'data' => [
                'list' => Group::find($request->id)->members
            ]
        ]);
    }
}
