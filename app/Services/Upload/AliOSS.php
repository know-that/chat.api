<?php

namespace App\Services\Upload;

use App\Enums\Model\FileUploadFromEnum;
use App\Exceptions\ErrorException;
use App\Models\UploadModel;
use App\Services\AliYun\AliYunService;
use Exception;
use Illuminate\Support\Facades\Config;

class AliOSS implements AsyncUploadInterface
{
    /**
     * 配置信息
     * @var array
     */
    private array $config;

    public function __construct()
    {
        $this->config = Config::get('ali-yun.OSS');
    }

    /**
     * 授权信息
     *
     * @return array
     * @throws Exception
     */
    public function credentials(): array
    {
        $res = AliYunService::getInstance()->getAssumeRole();
        $data = $res->toArray();

        // 获取 aliyun oss 配置
        $data['Credentials']['Region'] = $this->config['region'];
        $data['Credentials']['Bucket'] = $this->config['bucket'];

        return $data['Credentials'];
    }

    /**
     * 回调
     *
     * @param array $params
     * @return UploadModel
     * @throws ErrorException
     */
    public function callback(array $params): UploadModel
    {
        $pathInfo = pathinfo($params['origin_name']);

        // 文件标识
        $marker = strtoupper($params['etag']);

        // 写入数据库
        $file = UploadModel::firstOrCreate(
            [
                'marker' => $marker
            ],
            [
                'from'       => FileUploadFromEnum::AliYun->value,
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
}
