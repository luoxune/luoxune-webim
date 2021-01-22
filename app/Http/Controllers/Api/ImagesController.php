<?php

namespace App\Http\Controllers\Api;

use App\Handlers\ImageUploadHandler;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Resources\GroupResource;
use App\Http\Requests\Api\ImageRequest;


class ImagesController extends Controller
{
    public function store(ImageRequest $request, ImageUploadHandler $uploader)
    {
        $user = $request->user();
        $prefix = !empty($user) ? $user-> id : 'mobile';
        $type = $request->type == 'avatar'? 'avatar' : 'chat';
        $size = 100;
        $result = $uploader->save_to_aliyun($request->file, Str::plural($type), $prefix, $size);
        if($result){
            return response()->json([
                'code' => 0,
                'msg' => '上传成功',
                'data' => [
                    'src' => $result['path']
                ],
            ]);
        }else{
            abort(403,'不支持的图片格式');
        }

    }

}
