<?php

namespace App\Http\Requests\Api\User;

use App\Http\Requests\Api\FormRequest;

class UserFindRequest extends FormRequest
{
    public function rules()
    {
        return [
            'number' => 'required|string|max:25',
        ];
    }

    public function attributes()
    {
        return [
            'verification_key' => '短信验证码 key',
            'verification_code' => '短信验证码',
        ];
    }
}
