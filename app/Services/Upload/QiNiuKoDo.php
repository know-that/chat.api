<?php

namespace App\Services\Upload;


use App\Models\Upload;
use Illuminate\Support\Facades\Config;
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
     * @param array $params
     * @return Upload
     */
    public function callback(array $params): Upload
    {
        // TODO: Implement callback() method.
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
            'callbackUrl' => $host . ($params['callbackUrl'] ?? $this->config['callbackUrl']),
            'callbackBody' => '{"bucket":"$(bucket)","etag":"$(etag)","mimeType":"$(mimeType)","object":"$(key)","size":$(fsize),"origin_name":"$(fname)"}',
            'callbackBodyType' => 'application/json'
        ];
    }
}
