<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\UserResource;
use App\Models\Message;
use App\Models\Record;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\OSS;
use GatewayClient\Gateway;
class AppController extends Controller
{
    //
    public function list(Request $request)
    {
        $user = $request->user();
        $divides = $user->divides;
        $groups = $user->groups;
        $friend = null;
        $friendList = null;
        if (!empty($divides)) {
            foreach ($divides as $key => $v) {
                $friendList = null;
                if (!$v->friends->isEmpty()) {
                    foreach ($v->friends as $key2 => $v2) {
                        $friendList[$key2] = User::find($v2->friend_id);
                    }
                }
                $friend[$key] = [
                    'groupname' => $v->name,
                    'id' => $v->id,
                    'list' => $friendList
                ];
            }
        }
        return response()->json([
            'code' => 0,
            'msg' => '',
            'data' => [
                'mine' => (new UserResource($user))->showSensitiveFields(),
                'friend' => $friend,
                'group' => $groups,
            ],
        ]);
    }
    public function msgboxJson(Request $request)
    {
        $user = $request->user();
        $msgList = null;
        $msg = Message::where('uid', $user->id)->orderBy('updated_at','dasc')->get();
        if (!$msg->isEmpty()) {
            foreach ($msg as $key => $v) {
                $v['user'] = User::find($v->from);
                $v['time'] = $v->updated_at->format('Y-m-d H:i:s');
                $msgList[$key] = $v;
            }
        }
        return response()->json([
            'code' => 0,
            'msg' => '',
            'data' => $msgList
        ]);
    }

    public function msgboxRead(Request $request)
    {
        $user = $request->user();
        return (Message::where('uid',$user->id)->where('read',0)->update(['read' => 1]));
    }
    public function msgboxNoread(Request $request)
    {
        return Message::where('uid',$request->user()->id)->where('read',0)->count();
    }

    public function chatlogs(Request $request)
    {
        $user = $request->user();
        if($request->input('type') == 'friend'){
            $window_id = (int)$request->input('id');
            return response()->json([
                'code' => 0,
                'msg' => '',
                'data' => Record::where([
                    ['from_id', '=', $window_id],
                    ['to_id', '=', $user->id],
                    ['type', '=', 'friend'],
                ])->orwhere([
                    ['from_id', '=', $user->id],
                    ['to_id', '=', $window_id],
                    ['type', '=', 'friend'],
                ]) ->orderBy('updated_at', 'desc')->paginate()
            ]);
        }else if ($request->input('type') == 'group'){
            $window_id = (int)$request->input('id');
            return response()->json([
                'code' => 0,
                'msg' => '',
                'data' => Record::where([
                    ['to_id', '=', $window_id],
                    ['type', '=', 'group'],
                ])->orderBy('updated_at', 'desc')->paginate()
            ]);
        }else{
            abort(403);
        }

    }
    public function files(Request $request)
    {
        $user = $request->user();
        $prefix = $user? $user->id : 'mobile';

        $file = $request->file;
        //dd($file);
        $original_name = $file->getClientOriginalName();
        $filePath = $file->getRealPath();
        $extension = $file->extension();
        $realType = $file->getMimeType();
        $file_name = 'uploads/files/'.date("Ym/d", time())."/$prefix".'_'.Str::random(random_int(10,20)) . '.'.$extension;
        if (!OSS::publicUpload(env('ALIOSS_BUCKET'), $file_name, $filePath,[
            'ContentType' => $realType,
        ])){
            abort(500,'上传图片失败，请稍后重试');
        }
        return response()->json([
            'code' => 0,
            'msg' => '',
            'data' => [
                'src' => env('ALIOSS_URL').'/'.$file_name,
                'name' => $original_name
            ],
        ]);
    }
}
