<?php

namespace App\Http\Requests\Api\Group;

use App\Http\Requests\Api\FormRequest;

class GroupAddRequest extends FormRequest
{
    public function rules()
    {
        return [
            'group_id' => 'required|exists:groups,id|integer',
            'remark' => 'required|string|max:30',
        ];
    }

    public function attributes()
    {
        return [
            'group' => '群组',
            'friend' => '好友',
            'remark' => '附言',
        ];
    }
}
