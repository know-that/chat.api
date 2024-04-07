<?php

namespace App\Services\Upload;


use App\Enums\Model\FileUploadFromEnum;
use App\Exceptions\ErrorException;
use App\Models\Upload;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Qiniu\Auth;

class QiNiuKoDo implements AsyncUploadInterface
{
    /**
     * 配置信息
     * @var array
     */
    private array $config;

    public function __construct()
    {
        $this->config = Config::get('qi-niu.koDo');
    }

    /**
     * 授权信息
     * @return array
     */
    public function credentials(): array
    {
        $auth = new Auth($this->config['accessKey'], $this->config['secretKey']);
        $policy = $this->policy();

        $upToken = $auth->uploadToken(
            $this->config['bucket'],
            null,
            $this->config['expires'],
            $policy
        );
        return [
            'token' => $upToken
        ];
    }

    /**
     * 回调
     *
     * @param array $params
     * @return Upload
     * @throws ErrorException
     */
    public function callback(array $params): Upload
    {
        $pathInfo = pathinfo($params['origin_name']);

        // 文件标识
        $marker = strtoupper($params['etag']);

        // 写入数据库
        $file = Upload::updateOrCreate(
            [
                'marker' => $marker
            ],
            [
                'from'       => FileUploadFromEnum::QiNiu->value,
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
            throw new ErrorException("文件写入失败");
        }

        return $file;
    }


    /**
     * 上传策略
     *
     * @param array $params
     * @return string[]
     */
    private function policy(array $params = []): array
    {
        $host = Config::get('app.url');

        return [
            'saveKey'   => $this->config['uploadPath'] . Str::uuid() . '$(ext)',
            'callbackUrl' => $host . ($params['callbackUrl'] ?? $this->config['callbackUrl']),
            'callbackBody' => '{"bucket":"$(bucket)","etag":"$(etag)","mimeType":"$(mimeType)","object":"$(key)","size":$(fsize),"origin_name":"$(fname)"}',
            'callbackBodyType' => 'application/json'
        ];
    }
}
