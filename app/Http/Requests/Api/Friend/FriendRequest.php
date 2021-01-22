<?php

namespace App\Http\Requests\Api\Friend;

use App\Http\Requests\Api\FormRequest;

class FriendRequest extends FormRequest
{
    public function rules()
    {
        switch($this->method()) {
            case 'POST':
                return [
                    'divide' => 'required|exists:divides,id|integer',
                    'uid' => 'required|exists:users,id|integer',
                    'remark' => 'required|string|max:30',
                ];
                break;
            case 'PUT':
                return [
                    'divide' => 'required|exists:divides,id|integer',
                    'from_divide' => 'required|exists:divides,id|integer',
                    'uid' => 'required|exists:users,id|integer',
                ];
                break;
            case 'DELETE':
                return [
                    'friend' => 'required|exists:users,id|integer',
                ];
                break;
        }
    }

    public function attributes()
    {
        return [
            'divide' => '好友分组',
            'uid' => '用户',
            'from_divide' => '好友分组',
            'friend' => '好友',
            'remark' => '附言',
        ];
    }
}
