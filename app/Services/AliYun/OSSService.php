<?php

namespace App\Services\AliYun;

use App\Services\AliYun\Trait\OSSAboutModelTrait;
use App\Services\Trait\InstanceTrait;
use App\Exceptions\ErrorException;
use OSS\Core\OssException;
use OSS\OssClient;
use Throwable;

class OSSService extends AliYun
{
    use InstanceTrait, OSSAboutModelTrait;

    /**
     * OSS 客户端
     * @var OssClient
     */
    public OssClient $client;

    /**
     * constructor
     * @throws ErrorException
     */
    public function __construct()
    {
        parent::__construct();

        try {
            $this->client = new OssClient(
                $this->config['accessKeyId'],
                $this->config['accessKeySecret'],
                $this->config['OSS']['endpoint']
            );
        } catch (\Exception $e) {
            throw new ErrorException(previous: $e);
        }
    }

    /**
     * 删除
     *
     * @param string $object
     * @return bool
     */
    public function delete(string $object): bool
    {
        $this->client->deleteObject($this->config['OSS']['bucket'], $object);
        return true;
    }

    /**
     * 字符串上传
     *
     * @param string $object
     * @param string $content
     * @return bool
     */
    public function uploadString(string $object, string $content): bool
    {
        $this->client->putObject($this->config['OSS']['bucket'], $object, $content);
        return true;
    }

    /**
     * 文件列表
     *
     * @param string $prefix 文件路径前缀
     * @param int $max 最大获取多少个文件
     * @param bool $isSelf 是否包含本身
     * @return array
     * @throws OssException
     */
    public function list(string $prefix, int $max = 1000, bool $isSelf = false): array
    {
        $result = $this->client->listObjectsV2($this->config['OSS']['bucket'], [
            'prefix'                => $prefix,
            OssClient::OSS_MAX_KEYS => $max
        ]);
        $data = [];
        foreach ($result->getObjectList() as $item) {
            $key = $item->getKey();
            if (!$isSelf && $key === $prefix) {
                continue;
            }

            $data[] = [
                'key'           =>  $key,
                'lastModified'  =>  $item->getLastModified(),
                'eTag'          =>  str_replace('"', '', $item->getETag()),
                'type'          =>  $item->getType(),
                'size'          =>  $item->getSize()
            ];
        }

        return $data;
    }
}
