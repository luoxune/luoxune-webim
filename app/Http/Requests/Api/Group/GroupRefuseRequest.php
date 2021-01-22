<?php

namespace App\Http\Requests\Api\Group;

use App\Http\Requests\Api\FormRequest;

class GroupRefuseRequest extends FormRequest
{
    public function rules()
    {
        return [
            'group_id' => 'required|exists:groups,id|integer',
            'uid' => 'required|exists:users,id|integer',
        ];
    }

    public function attributes()
    {
        return [
            'uid' => '用户',
            'group_id' => '群组',
        ];
    }
}
