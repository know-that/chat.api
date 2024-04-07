<?php

return [
    "accessKeyId" => env('ALI_YUN_ACCESS_KEY_ID', ''),
    "accessKeySecret" => env('ALI_YUN_ACCESS_KEY_SECRET', ''),

    /**
     * RAM 账号 ar
     */
    "roleArn"   => env('ALI_YUN_ROLE_ARN', ''),

    /**
     * 对象存储
     */
    "OSS"   => [
        "endpoint"  => env('ALI_YUN_OSS_ENDPOINT', ''),
        "region"  => env('ALI_YUN_OSS_REGION', ''),
        "bucket"  => env('ALI_YUN_OSS_BUCKET', ''),
        "uploadPath" => "chat/uploads/" . date("Y/m/d/"),
        "staticUrl" => env("ALI_YUN_OSS_STATIC_URL")
    ]
];
