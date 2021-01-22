<?php

namespace App\Http\Requests\Api\Group;

use App\Http\Requests\Api\FormRequest;

class GroupDestroyRequest extends FormRequest
{
    public function rules()
    {
        return [
            'group_id' => 'required|exists:groups,id|integer',
        ];
    }

    public function attributes()
    {
        return [
            'group_id' => '群组',
        ];
    }
}
