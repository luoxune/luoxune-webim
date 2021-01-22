<?php

namespace App\Http\Requests\Api\User;

use App\Http\Requests\Api\FormRequest;

class UserRequest extends FormRequest
{
    public function rules()
    {
        switch($this->method()) {
            case 'PUT':
                return [
                    'name' => 'required|string|max:25',
                    'password' => 'required|alpha_dash|min:6',
                    'verification_key' => 'required|string',
                    'verification_code' => 'required|string',
                ];
                break;
            case 'PATCH':
                return [
                    'name' => 'required|string|max:25',
                    'password' => 'required|alpha_dash|min:6',
                    'sign' => 'string|max:30|nullable',
                    'avatar' => 'string|nullable',
                    'QQ' => 'integer|nullable',
                    'sex' => 'string|nullable|in:男,女,保密',
                    'birth' => 'string|nullable',
                    'blt' => 'string|nullable',
                    'email' => 'email|nullable',
                ];
                break;
        }

    }

    public function attributes()
    {
        return [
            'verification_key' => '短信验证码 key',
            'verification_code' => '短信验证码',
        ];
    }
}
