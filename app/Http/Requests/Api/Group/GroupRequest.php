<?php

namespace App\Http\Requests\Api\Group;

use App\Http\Requests\Api\FormRequest;

class GroupRequest extends FormRequest
{
    public function rules()
    {
        switch($this->method()) {
            case 'PUT':
                return [
                    'groupname' => 'required|string|max:30',
                    'sign' => 'string|max:30|nullable',
                    'avatar' => 'string|nullable'
                ];
                break;
            case 'PATCH':
                return [
                    'group_id' => 'required|exists:groups,id|integer',
                    'groupname' => 'required|string|max:30',
                    'sign' => 'string|max:30|nullable',
                    'avatar' => 'string|nullable'
                ];
                break;
            case 'POST':
                return [
                    'group_id' => 'required|exists:groups,id|integer',
                ];
                break;
            case 'DELETE':
                return [
                    'group_id' => 'required|exists:groups,id|integer',
                ];
                break;
            default:
                break;
        }
    }
}
