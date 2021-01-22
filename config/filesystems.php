<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DRIVER', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
        ],
        'qiniu' => [
            'driver'  => 'qiniu',
            'domains' => [
                'default'   =>  env('QINIU_DOMAIN', 'xxx.clouddn.com'), //你的七牛域名
                'https'     =>  env('QINIU_HTTPS_DOMAIN', 'xxx.clouddn.com'),    //你的HTTPS域名
                'custom'    =>  env('QINIU_CUSTOM_DOMAIN', 'xxx.clouddn.com'),    //你的自定义域名
            ],
            'access_key'=> env('QINIU_ACCESS_KEY', 'xxxxxxxxxxxxxxxx'),  //AccessKey
            'secret_key'=> env('QINIU_SECRET_KEY', 'xxxxxxxxxxxxxxxx'),  //SecretKey
            'bucket'    => env('QINIU_BUCKET', 'xxxxxxxxxxxxxxxx'),  //Bucket名字
            'notify_url'=>  null,  //持久化处理回调地址
            'access'    => 'public',  //空间访问控制 public 或 private
            'hotlink_prevention_key' =>  null, // CDN 时间戳防盗链的 key。 设置为 null 则不启用本功能。
//            'hotlink_prevention_key' => 'cbab68a279xxxxxxxxxxab509a', // 同上，备用
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
