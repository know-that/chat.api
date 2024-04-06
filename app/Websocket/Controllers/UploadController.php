<?php

namespace App\Websocket\Controllers;

use App\Enums\Model\FileUploadFromEnum;
use App\Services\AliYun\AliYunService;
use App\Models\Upload;
use App\Exceptions\ErrorException;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

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
        $res = AliYunService::getInstance()->getAssumeRole();
        $data = $res->toArray();

        // 获取 aliyun oss 配置
        $aliYunOSS = Config::get('ali-yun.OSS');
        $data['Credentials']['Region'] = $aliYunOSS['region'];
        $data['Credentials']['Bucket'] = $aliYunOSS['bucket'];

        return $this->response($data['Credentials']);
    }

    /**
     * 阿里云上传回调
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ErrorException
     */
    public function callback(Request $request): JsonResponse
    {
        $params = $request->only(['bucket', 'etag', 'imageInfo_format', 'imageInfo_height', 'imageInfo_width', 'mimeType', 'object', 'size', 'origin_name']);
        $pathInfo = pathinfo($params['origin_name']);

        // 文件标识
        $marker = strtoupper($params['etag']);

        // 写入数据库
        $file = Upload::firstOrCreate(
            [
                'marker' => $marker
            ],
            [
                'from'       => FileUploadFromEnum::AliYunOss->value,
                'marker'     => $marker,
                'name'       => $params['origin_name'],
                'mime'       => $params['mimeType'],
                'suffix'     => $pathInfo['extension'] ?? '',
                'url'        => $params['object'],
                'size'       => $params['size'],
                'created_at' => date("Y-m-d H:i:s")
            ]
        );
        if (!$file) {
            throw new ErrorException();
        }

        return $this->response($file->setVisible(["id","name","suffix","mime","size","url"]));
    }
}
