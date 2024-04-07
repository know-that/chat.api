<?php

return [

    "koDo"  => [
        "accessKey" => env('QIU_NIU_KO_DO_ACCESS_KEY', ''),

        "secretKey" => env('QIU_NIU_KO_DO_SECRET_KEY', ''),

        "expires"   => env('QIU_NIU_KO_DO_EXPIRES', 3600),

        "bucket"   => env('QIU_NIU_KO_DO_BUCKET'),

        "uploadPath" => env("QIU_NIU_KO_DO_UPLOAD_PATH", "chat/uploads/" . date("Y/m/d/")),

        "callbackUrl" => env("QIU_NIU_KO_DO_UPLOAD_CALLBACK_URL"),

        "staticUrl" => env("QIU_NIU_KO_DO_STATIC_URL")
    ],
];
