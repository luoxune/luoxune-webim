<?php

namespace App\Http\Requests\Api\Group;

use App\Http\Requests\Api\FormRequest;

class GroupMemberRequest extends FormRequest
{
    public function rules()
    {
        return [
            'id' => 'required|exists:groups,id|integer',
        ];
    }

    public function attributes()
    {
        return [
            'id' => '群组',
        ];
    }
}
