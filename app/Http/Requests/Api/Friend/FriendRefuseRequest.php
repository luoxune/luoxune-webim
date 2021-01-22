<?php

namespace App\Http\Requests\Api\Friend;

use App\Http\Requests\Api\FormRequest;

class FriendRefuseRequest extends FormRequest
{
    public function rules()
    {
        return [
            'uid' => 'required|exists:users,id|integer',
            'from_divide' => 'required|exists:divides,id|integer',
        ];
    }

    public function attributes()
    {
        return [
            'from_divide' => '好友分组',
            'uid' => '用户',
        ];
    }
}
