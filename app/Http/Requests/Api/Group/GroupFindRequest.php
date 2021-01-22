<?php

namespace App\Http\Requests\Api\Group;

use App\Http\Requests\Api\FormRequest;

class GroupFindRequest extends FormRequest
{
    public function rules()
    {
        return [
            'group_number' => 'required|',
        ];
    }

    public function attributes()
    {
        return [
            'group_id' => '群组',
        ];
    }
}
