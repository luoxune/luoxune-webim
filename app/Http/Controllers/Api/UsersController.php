<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\User\ChangeSignRequest;
use App\Http\Requests\Api\User\UserFindRequest;
use App\Http\Requests\Api\User\UserInfoRequest;
use App\Http\Requests\Api\User\UserRequest;
use App\Models\Divide;
use App\Models\Friend;
use App\Models\GNumber;
use App\Models\Member;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Auth\AuthenticationException;

class UsersController extends Controller
{
    public function store(UserRequest $request)
    {
        $verifyData = \Cache::get($request->verification_key);

        if (!$verifyData) {
            abort(403, '验证码已失效');
        }

        if (!hash_equals($verifyData['code'], $request->verification_code)) {
            // 返回401
            throw new AuthenticationException('验证码错误');
        }
        $number = GNumber::Where('used',0)->get()->random();
        //$number =  Number::Where('used',0)->take(1)->get()[0];
        //return $number->id;
        $number->used = 1;
        $number->save();
        $user = User::create([
            'number' => $number->id,
            'avatar' => '//t.cn/RCzsdCq',
            'username' => $request->name,
            'phone' => $verifyData['phone'],
            'password' => \Hash::make($request->password),
        ]);
        //
        $divide = Divide::create([
            'user_id' => $user->id,
            'name' => '我的好友',
        ]);
        $friend = Friend::create([
            'user_id' => $user->id,
            'friend_id' => $user->id,
            'divide_id' => $divide->id,
            'dividename' => $divide->name,
        ]);
        // 清除验证码缓存
        \Cache::forget($request->verification_key);

        return (new UserResource($user))->showSensitiveFields();
    }
    public function show(UserInfoRequest $request)
    {
        $user=User::find($request->user_id);
        return new UserResource($user);
    }

    public function me(Request $request)
    {
        return (new UserResource($request->user()))->showSensitiveFields();
    }

    public function update(UserRequest $request)
    {
        $user = $request->user();
        $result = $user->update([
            'username' => $request->name,
            'password' => \Hash::make($request->password),
            'sign' => $request->sign,
            'avatar' => $request->avatar,
            'QQ' => $request->QQ,
            'sex' => $request->sex,
            'birth' => $request->birth,
            'blt' => $request->blt,
            'email' => $request->email,
        ]);
        Member::where('user_id',$user->id)->update(['user_avatar' => $user->avatar]);
        if($result){
            return (new UserResource($request->user()))->showSensitiveFields();
        }else{
            abort(500);
        }

    }
    public function find(UserFindRequest $request)
    {
        $username = $request->number;
        strlen($username) > 6 ? $key = 'phone' : $key = 'number';
        if ($result = User::where($key, $username)->first()) {
            return new UserResource($result);
        } else {
            abort('404', '查询无结果');
        }
    }
    public function changeSign(ChangeSignRequest $request)
    {
        $user = $request->user();
        $user->sign = $request->sign;
        $user->save();
    }
}
