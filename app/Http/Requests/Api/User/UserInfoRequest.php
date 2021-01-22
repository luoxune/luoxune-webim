<?php


namespace App\Http\Requests\Api\User;

use App\Http\Requests\Api\FormRequest;

class UserInfoRequest extends FormRequest
{
    public function rules()
    {
        return [
            'user_id' => 'required|exists:users,id|integer',
        ];
    }

    public function attributes()
    {
        return [
            'user_id' => '好友',
        ];
    }
}
