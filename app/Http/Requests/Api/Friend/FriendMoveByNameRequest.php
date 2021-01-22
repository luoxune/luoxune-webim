<?php

namespace App\Http\Requests\Api\Friend;

use App\Http\Requests\Api\FormRequest;

class FriendMoveByNameRequest extends FormRequest
{
    public function rules()
    {
        return [
            'divide' => 'required|exists:divides,name|string',
            'friend' => 'required|exists:users,id|integer',
        ];
    }

    public function attributes()
    {
        return [
            'divide' => '好友分组',
            'friend' => '好友',
        ];
    }
}
