<?php

namespace App\Websocket\Controllers;

use App\Services\Upload\UploadService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * 文件上传
 *
 * @package App\Admin\Controllers
 */
class UploadController extends Controller
{
    /**
     * 上传凭证
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function credentials(): JsonResponse
    {
        return $this->response(UploadService::getInstance()->credentials());
    }

    /**
     * 上传回调
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function callback(Request $request): JsonResponse
    {
        $params = $request->only(['bucket', 'etag', 'mimeType', 'object', 'size', 'origin_name']);
        $file = UploadService::getInstance()->callback($params);
        return $this->response($file->setVisible(["id","name","suffix","mime","size","url"]));
    }
}
